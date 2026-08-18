<?php
namespace App\Models;

use App\Core\Model;

class Payment extends Model
{
    protected string $table = 'payments';

    public const METHODS = [
        'mpesa' => 'M-Pesa',
        'bank'  => 'Bank transfer',
        'cash'  => 'Cash on collection',
        'card'  => 'Card',
    ];

    public function start(int $orderId, string $method, float $amount, ?string $phone = null): int
    {
        return $this->create([
            'order_id' => $orderId,
            'method'   => $method,
            'amount'   => round($amount, 2),
            'status'   => 'pending',
            'phone'    => $phone,
        ]);
    }

    public function attachCheckout(int $paymentId, string $merchantRequestId, string $checkoutRequestId): void
    {
        $this->updateById($paymentId, [
            'merchant_request_id' => $merchantRequestId,
            'checkout_request_id' => $checkoutRequestId,
        ]);
    }

    public function findByCheckoutId(string $checkoutRequestId): ?array
    {
        return $this->db()->one(
            'SELECT * FROM `payments` WHERE `checkout_request_id` = :id LIMIT 1',
            ['id' => $checkoutRequestId]
        );
    }

    /**
     * Apply a Daraja callback. Idempotent — a repeated callback for an
     * already-settled payment is ignored, which matters because Safaricom
     * retries.
     *
     * @return bool True when this call changed the payment
     */
    public function settle(array $parsed, string $rawBody): bool
    {
        $payment = $this->findByCheckoutId($parsed['checkout_id']);

        if ($payment === null) {
            return false;
        }

        if ($payment['status'] !== 'pending') {
            return false; // already settled
        }

        $success = $parsed['code'] === '0';

        $update = [
            'status'       => $success ? 'success' : ($parsed['code'] === '1032' ? 'cancelled' : 'failed'),
            'result_code'  => $parsed['code'],
            'result_desc'  => mb_substr($parsed['desc'], 0, 255),
            'raw_callback' => mb_substr($rawBody, 0, 60000),
        ];

        if ($parsed['receipt'] !== null) {
            $update['mpesa_receipt'] = $parsed['receipt'];
        }

        // Trust the amount Safaricom actually collected over what we asked for.
        if ($success && $parsed['amount'] !== null && $parsed['amount'] > 0) {
            $update['amount'] = round($parsed['amount'], 2);
        }

        $this->updateById((int) $payment['id'], $update);
        (new Order())->recalculatePayment((int) $payment['order_id']);

        return true;
    }

    /** Manually record an off-platform payment (bank transfer, cash). */
    public function recordManual(int $orderId, string $method, float $amount, ?string $note = null): int
    {
        $id = $this->create([
            'order_id'    => $orderId,
            'method'      => $method,
            'amount'      => round($amount, 2),
            'status'      => 'success',
            'result_desc' => $note,
        ]);

        (new Order())->recalculatePayment($orderId);

        return $id;
    }

    public function markFailed(int $paymentId, string $reason): void
    {
        $this->updateById($paymentId, [
            'status'      => 'failed',
            'result_desc' => mb_substr($reason, 0, 255),
        ]);
    }
}
