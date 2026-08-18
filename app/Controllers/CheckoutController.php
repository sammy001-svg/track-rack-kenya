<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\CustomerAuth;
use App\Core\Mailer;
use App\Core\Mpesa;
use App\Core\QuoteList;
use App\Core\Session;
use App\Core\Validator;
use App\Models\Order;
use App\Models\Payment;
use RuntimeException;

class CheckoutController extends Controller
{
    /** GET /checkout */
    public function index(): void
    {
        $split = QuoteList::split();

        if ($split['buyable'] === []) {
            Session::flash('error', 'There is nothing on your list that can be bought directly. Send a quote request instead.');
            $this->redirect('/quote');
        }

        $customer = CustomerAuth::user();

        $this->view('site.checkout', [
            'pageTitle'    => 'Checkout',
            'bodyClass'    => 'page-checkout',
            'noindex'      => true,
            'items'        => $split['buyable'],
            'quoteItems'   => $split['quote'],
            'subtotal'     => $split['subtotal'],
            'deliveryCost' => $this->deliveryCost('collect', $split['subtotal']),
            'customer'     => $customer,
            'mpesaEnabled' => Mpesa::enabled(),
            'errors'       => Session::errors(),
        ]);

        Session::clearOld();
    }

    /** POST /checkout — create the order, then send them to payment. */
    public function place(): void
    {
        $split = QuoteList::split();

        if ($split['buyable'] === []) {
            Session::flash('error', 'Your basket is empty.');
            $this->redirect('/quote');
        }

        $validator = new Validator($_POST);
        $validator->honeypot('website')
            ->require('name', 'Your name')->max('name', 150, 'Your name')
            ->require('email', 'Email address')->email('email')->max('email', 190, 'Email address')
            ->require('phone', 'Phone number')->phone('phone')->max('phone', 60, 'Phone number')
            ->in('delivery_method', array_keys(Order::DELIVERY_METHODS), 'Delivery method')
            ->max('notes', 2000, 'Notes');

        $method = $validator->value('delivery_method', 'collect');

        if ($method !== 'collect') {
            $validator->require('delivery_address', 'Delivery address')
                ->max('delivery_address', 500, 'Delivery address')
                ->require('delivery_town', 'Town or area')->max('delivery_town', 120, 'Town or area');
        }

        if ($validator->fails()) {
            Session::flashErrors($validator->errors());
            Session::flashInput($_POST);
            Session::flash('error', 'Please correct the highlighted fields.');
            $this->redirect('/checkout');
        }

        $deliveryCost = $this->deliveryCost($method, $split['subtotal']);

        $result = (new Order())->createWithItems([
            'customer_id'      => CustomerAuth::id(),
            'name'             => $validator->value('name'),
            'email'            => $validator->value('email'),
            'phone'            => $validator->value('phone'),
            'delivery_method'  => $method,
            'delivery_address' => $method === 'collect' ? null : $validator->value('delivery_address'),
            'delivery_town'    => $method === 'collect' ? null : $validator->value('delivery_town'),
            'notes'            => $validator->value('notes') ?: null,
            'ip_address'       => $_SERVER['REMOTE_ADDR'] ?? null,
        ], $split['buyable'], $deliveryCost);

        // The purchased lines leave the list; anything quote-only stays put.
        QuoteList::removeBuyable();

        Session::clearOld();
        Session::set('_order_' . $result['reference'], true);

        $this->notifyOrder($result['reference']);

        $this->redirect('/checkout/pay/' . $result['reference']);
    }

    /** GET /checkout/pay/{reference} */
    public function pay(string $reference): void
    {
        $order = $this->authorisedOrder($reference);

        if ($order['payment_status'] === 'paid') {
            $this->redirect('/checkout/done/' . $reference);
        }

        $model = new Order();

        $this->view('site.checkout-pay', [
            'pageTitle'    => 'Payment',
            'bodyClass'    => 'page-checkout',
            'noindex'      => true,
            'order'        => $order,
            'items'        => $model->items((int) $order['id']),
            'payments'     => $model->payments((int) $order['id']),
            'mpesaEnabled' => Mpesa::enabled(),
            'bankDetails'  => setting('bank_details', ''),
        ]);
    }

    /** POST /checkout/mpesa — trigger the STK push. */
    public function mpesa(): void
    {
        $reference = (string) ($_POST['reference'] ?? '');
        $order     = $this->authorisedOrder($reference);

        if (!Mpesa::enabled()) {
            $this->fail($reference, 'M-Pesa is not available at the moment. Please use another method.');
        }

        if ($order['payment_status'] === 'paid') {
            $this->redirect('/checkout/done/' . $reference);
        }

        $outstanding = round((float) $order['total'] - (float) $order['amount_paid'], 2);

        if ($outstanding < 1) {
            $this->redirect('/checkout/done/' . $reference);
        }

        try {
            $phone = Mpesa::normalisePhone((string) ($_POST['phone'] ?? $order['phone']));
        } catch (RuntimeException $e) {
            $this->fail($reference, $e->getMessage());
        }

        $paymentModel = new Payment();
        $paymentId    = $paymentModel->start((int) $order['id'], 'mpesa', $outstanding, $phone);

        try {
            $push = (new Mpesa())->stkPush(
                $phone,
                (int) ceil($outstanding),
                (string) setting('mpesa_account_ref', 'TACKRACK'),
                $order['reference'],
                url('/checkout/mpesa/callback')
            );

            $paymentModel->attachCheckout($paymentId, $push['MerchantRequestID'], $push['CheckoutRequestID']);

            Session::flash('success', $push['CustomerMessage']);
            $this->redirect('/checkout/pay/' . $reference . '?awaiting=' . urlencode($push['CheckoutRequestID']));
        } catch (RuntimeException $e) {
            $paymentModel->markFailed($paymentId, $e->getMessage());
            $this->fail($reference, $e->getMessage());
        }
    }

    /**
     * POST /checkout/mpesa/callback — Safaricom calls this, not a browser.
     * No CSRF token, so it is exempted in the front controller.
     */
    public function mpesaCallback(): void
    {
        $raw = file_get_contents('php://input') ?: '';
        Mpesa::log('Callback received: ' . $raw);

        $payload = json_decode($raw, true);

        if (is_array($payload)) {
            $parsed = Mpesa::parseCallback($payload);

            if ($parsed['checkout_id'] !== '') {
                $changed = (new Payment())->settle($parsed, $raw);

                if ($changed && $parsed['code'] === '0') {
                    $this->notifyPaid($parsed['checkout_id']);
                }
            }
        }

        // Safaricom retries anything that is not a 200 with this body.
        header('Content-Type: application/json');
        echo json_encode(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
        exit;
    }

    /**
     * GET /checkout/status/{reference} — polled by the payment page while
     * the customer is entering their PIN.
     */
    public function status(string $reference): void
    {
        $model = new Order();
        $order = $model->findBy('reference', $reference);

        if ($order === null || !$this->owns($order)) {
            $this->json(['ok' => false, 'error' => 'Unknown order.'], 404);
        }

        $latest = $model->payments((int) $order['id'])[0] ?? null;

        $this->json([
            'ok'             => true,
            'payment_status' => $order['payment_status'],
            'status'         => $order['status'],
            'amount_paid'    => (float) $order['amount_paid'],
            'total'          => (float) $order['total'],
            'latest'         => $latest === null ? null : [
                'status'  => $latest['status'],
                'method'  => $latest['method'],
                'receipt' => $latest['mpesa_receipt'],
                'message' => $latest['result_desc'],
            ],
            'redirect' => $order['payment_status'] === 'paid' ? url('/checkout/done/' . $reference) : null,
        ]);
    }

    /** GET /checkout/done/{reference} */
    public function done(string $reference): void
    {
        $model = new Order();
        $order = $this->authorisedOrder($reference);

        $this->view('site.checkout-done', [
            'pageTitle' => 'Order ' . $order['reference'],
            'bodyClass' => 'page-checkout',
            'noindex'   => true,
            'order'     => $order,
            'items'     => $model->items((int) $order['id']),
            'payments'  => $model->payments((int) $order['id']),
        ]);
    }

    // =================================================================
    //  Helpers
    // =================================================================

    /** Delivery pricing, with a free-delivery threshold. */
    private function deliveryCost(string $method, float $subtotal): float
    {
        if ($method === 'collect') {
            return 0.0;
        }

        $freeOver = (float) setting('free_delivery_over', '0');

        if ($freeOver > 0 && $subtotal >= $freeOver) {
            return 0.0;
        }

        return $method === 'nairobi'
            ? (float) setting('delivery_nairobi', '0')
            : (float) setting('delivery_courier', '0');
    }

    /**
     * An order may be viewed by the account that owns it, or by the guest
     * session that placed it. Nothing else.
     */
    private function owns(array $order): bool
    {
        $customerId = CustomerAuth::id();

        if ($customerId !== null && (int) $order['customer_id'] === $customerId) {
            return true;
        }

        return Session::get('_order_' . $order['reference']) === true;
    }

    private function authorisedOrder(string $reference): array
    {
        $order = (new Order())->findBy('reference', $reference);

        if ($order === null) {
            $this->notFound('We could not find that order.');
        }

        if (!$this->owns($order)) {
            Session::flash('error', 'Please sign in to view that order.');
            Session::set('_customer_intended', '/account/orders');
            $this->redirect('/account/login');
        }

        return $order;
    }

    private function fail(string $reference, string $message): void
    {
        Session::flash('error', $message);
        $this->redirect('/checkout/pay/' . $reference);
    }

    private function notifyOrder(string $reference): void
    {
        $model = new Order();
        $order = $model->findBy('reference', $reference);

        if ($order === null) {
            return;
        }

        $rows = '';
        foreach ($model->items((int) $order['id']) as $item) {
            $rows .= '<tr>'
                . '<td style="padding:8px 12px;border-bottom:1px solid #eee;">' . e($item['product_name'])
                . ($item['variant'] ? '<br><small style="color:#777">' . e($item['variant']) . '</small>' : '') . '</td>'
                . '<td style="padding:8px 12px;border-bottom:1px solid #eee;text-align:right;">' . (int) $item['quantity'] . '</td>'
                . '<td style="padding:8px 12px;border-bottom:1px solid #eee;text-align:right;">' . e(money($item['line_total'])) . '</td>'
                . '</tr>';
        }

        $summary = '<table style="border-collapse:collapse;width:100%;margin:18px 0;">'
            . '<thead><tr>'
            . '<th style="text-align:left;padding:8px 12px;border-bottom:2px solid #14110E;">Item</th>'
            . '<th style="text-align:right;padding:8px 12px;border-bottom:2px solid #14110E;">Qty</th>'
            . '<th style="text-align:right;padding:8px 12px;border-bottom:2px solid #14110E;">Total</th>'
            . '</tr></thead><tbody>' . $rows . '</tbody></table>'
            . '<p style="text-align:right;margin:0;">Subtotal: ' . e(money($order['subtotal']))
            . '<br>Delivery: ' . e(money($order['delivery_cost']))
            . '<br><strong style="font-size:16px;">Total: ' . e(money($order['total'])) . '</strong></p>';

        Mailer::send(
            $order['email'],
            'Your Tack Rack order ' . $order['reference'],
            '<h2 style="font-family:Georgia,serif;font-weight:normal;">Thank you for your order</h2>'
                . '<p>We have reserved these items. Your reference is <strong>' . e($order['reference']) . '</strong>.</p>'
                . $summary
                . '<p><strong>' . e(Order::DELIVERY_METHODS[$order['delivery_method']]) . '</strong></p>'
                . Mailer::button('View your order', url('/checkout/pay/' . $order['reference']))
        );

        $staff = (string) setting('contact_email', '');
        if ($staff !== '') {
            Mailer::send(
                $staff,
                'New order ' . $order['reference'] . ' — ' . money($order['total']),
                '<h2 style="font-family:Georgia,serif;font-weight:normal;">New order</h2>'
                    . '<p><strong>' . e($order['customer_name']) . '</strong><br>'
                    . e($order['email']) . '<br>' . e($order['phone']) . '</p>'
                    . '<p>' . e(Order::DELIVERY_METHODS[$order['delivery_method']])
                    . ($order['delivery_address'] ? '<br>' . nl2br(e($order['delivery_address'])) : '') . '</p>'
                    . $summary,
                $order['email']
            );
        }
    }

    /** Receipt email, sent when a payment actually clears. */
    private function notifyPaid(string $checkoutId): void
    {
        $paymentModel = new Payment();
        $payment      = $paymentModel->findByCheckoutId($checkoutId);

        if ($payment === null) {
            return;
        }

        $order = (new Order())->find((int) $payment['order_id']);

        if ($order === null) {
            return;
        }

        Mailer::send(
            $order['email'],
            'Payment received — ' . $order['reference'],
            '<h2 style="font-family:Georgia,serif;font-weight:normal;">Payment received</h2>'
                . '<p>Thank you. We have received ' . e(money($payment['amount'])) . ' for order '
                . '<strong>' . e($order['reference']) . '</strong>.</p>'
                . ($payment['mpesa_receipt']
                    ? '<p>M-Pesa receipt: <strong>' . e($payment['mpesa_receipt']) . '</strong></p>'
                    : '')
                . '<p>' . e(Order::DELIVERY_METHODS[$order['delivery_method']]) . '. '
                . 'We will be in touch as soon as it is ready.</p>'
        );

        $staff = (string) setting('contact_email', '');
        if ($staff !== '') {
            Mailer::send(
                $staff,
                'Payment received ' . $order['reference'] . ' — ' . money($payment['amount']),
                '<p>' . e($order['customer_name']) . ' has paid ' . e(money($payment['amount']))
                    . ' for order ' . e($order['reference']) . '.'
                    . ($payment['mpesa_receipt'] ? '<br>Receipt: ' . e($payment['mpesa_receipt']) : '') . '</p>'
            );
        }
    }
}
