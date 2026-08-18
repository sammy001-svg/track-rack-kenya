<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Mailer;
use App\Core\Session;
use App\Models\Order;
use App\Models\Payment;

class OrderController extends Controller
{
    public function index(): void
    {
        $model = new Order();

        $filters = [
            'status'         => array_key_exists((string) ($_GET['status'] ?? ''), Order::STATUSES) ? $_GET['status'] : null,
            'payment_status' => array_key_exists((string) ($_GET['payment'] ?? ''), Order::PAYMENT_STATUSES) ? $_GET['payment'] : null,
            'q'              => trim((string) ($_GET['q'] ?? '')),
        ];

        $result = $model->paginate($filters, max(1, (int) ($_GET['page'] ?? 1)), (int) config('per_page.admin', 20));

        $this->view('admin.orders.index', [
            'pageTitle'    => 'Orders',
            'orders'       => $result['items'],
            'total'        => $result['total'],
            'pages'        => $result['pages'],
            'page'         => $result['page'],
            'filters'      => $filters,
            'statusCounts' => $model->countByStatus(),
            'revenue'      => $model->revenue(),
        ], 'layouts.admin');
    }

    public function show(string $id): void
    {
        $model = new Order();
        $order = $model->find((int) $id);

        if ($order === null) {
            Session::flash('error', 'That order no longer exists.');
            $this->redirect('/admin/orders');
        }

        $this->view('admin.orders.show', [
            'pageTitle' => 'Order ' . $order['reference'],
            'order'     => $order,
            'items'     => $model->items((int) $order['id']),
            'payments'  => $model->payments((int) $order['id']),
        ], 'layouts.admin');
    }

    public function update(string $id): void
    {
        $orderId = (int) $id;
        $model   = new Order();
        $order   = $model->find($orderId);

        if ($order === null) {
            Session::flash('error', 'That order no longer exists.');
            $this->redirect('/admin/orders');
        }

        $status = (string) ($_POST['status'] ?? 'pending');
        $status = array_key_exists($status, Order::STATUSES) ? $status : 'pending';

        $model->updateById($orderId, [
            'status'      => $status,
            'admin_notes' => trim((string) ($_POST['admin_notes'] ?? '')) ?: null,
        ]);

        if (isset($_POST['notify']) && $order['status'] !== $status) {
            $this->notifyStatusChange($order, $status);
            Session::flash('success', 'Order updated and the customer has been emailed.');
        } else {
            Session::flash('success', 'Order updated.');
        }

        $this->redirect('/admin/orders/' . $orderId);
    }

    /** Record a bank transfer or cash payment taken outside the site. */
    public function recordPayment(string $id): void
    {
        $orderId = (int) $id;
        $model   = new Order();
        $order   = $model->find($orderId);

        if ($order === null) {
            Session::flash('error', 'That order no longer exists.');
            $this->redirect('/admin/orders');
        }

        $amount = trim((string) ($_POST['amount'] ?? ''));
        $method = (string) ($_POST['method'] ?? 'bank');

        if (!is_numeric($amount) || (float) $amount <= 0) {
            Session::flash('error', 'Enter the amount received.');
            $this->redirect('/admin/orders/' . $orderId);
        }

        if (!array_key_exists($method, Payment::METHODS)) {
            $method = 'bank';
        }

        (new Payment())->recordManual(
            $orderId,
            $method,
            (float) $amount,
            trim((string) ($_POST['note'] ?? '')) ?: null
        );

        Session::flash('success', 'Payment of ' . money($amount) . ' recorded.');
        $this->redirect('/admin/orders/' . $orderId);
    }

    public function destroy(string $id): void
    {
        (new Order())->deleteById((int) $id);

        Session::flash('success', 'Order deleted.');
        $this->redirect('/admin/orders');
    }

    public function printable(string $id): void
    {
        $model = new Order();
        $order = $model->find((int) $id);

        if ($order === null) {
            Session::flash('error', 'That order no longer exists.');
            $this->redirect('/admin/orders');
        }

        $this->view('admin.orders.print', [
            'pageTitle' => 'Order ' . $order['reference'],
            'order'     => $order,
            'items'     => $model->items((int) $order['id']),
            'payments'  => $model->payments((int) $order['id']),
        ], 'layouts.blank');
    }

    // =================================================================

    private function notifyStatusChange(array $order, string $status): void
    {
        $name = e(explode(' ', $order['customer_name'])[0]);
        $ref  = e($order['reference']);

        [$subject, $body] = match ($status) {
            'processing' => [
                'Your order is being prepared — ' . $order['reference'],
                "<h2 style=\"font-family:Georgia,serif;font-weight:normal;\">We are preparing your order</h2>"
                    . "<p>Hello {$name},</p><p>Order <strong>{$ref}</strong> is being put together now.</p>",
            ],
            'dispatched' => [
                'Your order is on its way — ' . $order['reference'],
                "<h2 style=\"font-family:Georgia,serif;font-weight:normal;\">On its way</h2>"
                    . "<p>Hello {$name},</p><p>Order <strong>{$ref}</strong> has been dispatched"
                    . ($order['delivery_town'] ? ' to ' . e($order['delivery_town']) : '') . '.</p>'
                    . ($order['admin_notes'] ? '<p>' . nl2br(e($order['admin_notes'])) . '</p>' : ''),
            ],
            'completed' => [
                'Your order is complete — ' . $order['reference'],
                "<h2 style=\"font-family:Georgia,serif;font-weight:normal;\">Thank you</h2>"
                    . "<p>Hello {$name},</p><p>Order <strong>{$ref}</strong> is complete. "
                    . 'We hope it serves you and your horse well.</p>',
            ],
            'cancelled' => [
                'Your order has been cancelled — ' . $order['reference'],
                "<h2 style=\"font-family:Georgia,serif;font-weight:normal;\">Order cancelled</h2>"
                    . "<p>Hello {$name},</p><p>Order <strong>{$ref}</strong> has been cancelled. "
                    . 'If anything was paid, we will arrange a refund.</p>',
            ],
            default => [
                'Update on your order — ' . $order['reference'],
                "<h2 style=\"font-family:Georgia,serif;font-weight:normal;\">Order update</h2>"
                    . "<p>Hello {$name},</p><p>Order <strong>{$ref}</strong> is now "
                    . e(Order::STATUSES[$status]) . '.</p>',
            ],
        };

        Mailer::send($order['email'], $subject, $body);
    }
}
