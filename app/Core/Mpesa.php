<?php
namespace App\Core;

use App\Models\Setting;
use RuntimeException;

/**
 * Safaricom Daraja (M-Pesa) — Lipa na M-Pesa Online, a.k.a. STK push.
 *
 * Flow:
 *   1. stkPush()  → customer gets a PIN prompt on their handset
 *   2. Safaricom POSTs the result to /checkout/mpesa/callback
 *   3. query()    → fallback poll, for when the callback cannot reach us
 *
 * Credentials live in Admin → Settings → mpesa.
 */
class Mpesa
{
    private const SANDBOX = 'https://sandbox.safaricom.co.ke';
    private const LIVE    = 'https://api.safaricom.co.ke';

    private string $base;
    private string $shortcode;
    private string $consumerKey;
    private string $consumerSecret;
    private string $passkey;

    public function __construct()
    {
        $this->base           = Setting::get('mpesa_env', 'sandbox') === 'production' ? self::LIVE : self::SANDBOX;
        $this->shortcode      = trim((string) Setting::get('mpesa_shortcode', ''));
        $this->consumerKey    = trim((string) Setting::get('mpesa_consumer_key', ''));
        $this->consumerSecret = trim((string) Setting::get('mpesa_consumer_secret', ''));
        $this->passkey        = trim((string) Setting::get('mpesa_passkey', ''));
    }

    public static function enabled(): bool
    {
        return Setting::get('mpesa_enabled', '0') === '1'
            && Setting::get('mpesa_shortcode', '') !== ''
            && Setting::get('mpesa_consumer_key', '') !== ''
            && Setting::get('mpesa_consumer_secret', '') !== ''
            && Setting::get('mpesa_passkey', '') !== '';
    }

    /**
     * Normalise a Kenyan number to the 2547XXXXXXXX / 2541XXXXXXXX form
     * Daraja expects.
     *
     * @throws RuntimeException when the number is not usable
     */
    public static function normalisePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if (str_starts_with($digits, '254')) {
            $national = substr($digits, 3);
        } elseif (str_starts_with($digits, '0')) {
            $national = substr($digits, 1);
        } elseif (strlen($digits) === 9) {
            $national = $digits;
        } else {
            throw new RuntimeException('That does not look like a Kenyan mobile number.');
        }

        if (!preg_match('/^[71]\d{8}$/', $national)) {
            throw new RuntimeException('Enter a Safaricom or Airtel number, for example 0722 123 456.');
        }

        return '254' . $national;
    }

    /**
     * Trigger the PIN prompt on the customer's handset.
     *
     * @return array{MerchantRequestID:string, CheckoutRequestID:string, CustomerMessage:string}
     * @throws RuntimeException
     */
    public function stkPush(string $phone, int $amount, string $reference, string $description, string $callbackUrl): array
    {
        if (!self::enabled()) {
            throw new RuntimeException('M-Pesa is not configured.');
        }

        if ($amount < 1) {
            throw new RuntimeException('The payment amount must be at least KSh 1.');
        }

        $timestamp = date('YmdHis');
        $password  = base64_encode($this->shortcode . $this->passkey . $timestamp);

        $response = $this->post('/mpesa/stkpush/v1/processrequest', [
            'BusinessShortCode' => $this->shortcode,
            'Password'          => $password,
            'Timestamp'         => $timestamp,
            'TransactionType'   => 'CustomerPayBillOnline',
            'Amount'            => $amount,
            'PartyA'            => $phone,
            'PartyB'            => $this->shortcode,
            'PhoneNumber'       => $phone,
            'CallBackURL'       => $callbackUrl,
            'AccountReference'  => mb_substr($reference, 0, 12),
            'TransactionDesc'   => mb_substr($description, 0, 13),
        ], $this->token());

        if (!isset($response['CheckoutRequestID'])) {
            $message = $response['errorMessage'] ?? $response['ResponseDescription'] ?? 'M-Pesa did not accept the request.';
            throw new RuntimeException($message);
        }

        return [
            'MerchantRequestID' => (string) ($response['MerchantRequestID'] ?? ''),
            'CheckoutRequestID' => (string) $response['CheckoutRequestID'],
            'CustomerMessage'   => (string) ($response['CustomerMessage'] ?? 'Check your phone to complete the payment.'),
        ];
    }

    /** Poll the status of a push, for when the callback never arrives. */
    public function query(string $checkoutRequestId): array
    {
        $timestamp = date('YmdHis');

        return $this->post('/mpesa/stkpushquery/v1/query', [
            'BusinessShortCode' => $this->shortcode,
            'Password'          => base64_encode($this->shortcode . $this->passkey . $timestamp),
            'Timestamp'         => $timestamp,
            'CheckoutRequestID' => $checkoutRequestId,
        ], $this->token());
    }

    /**
     * Pull the useful fields out of a Daraja callback body.
     *
     * @return array{checkout_id:string, code:string, desc:string, receipt:?string, amount:?float, phone:?string}
     */
    public static function parseCallback(array $payload): array
    {
        $stk = $payload['Body']['stkCallback'] ?? [];

        $parsed = [
            'checkout_id' => (string) ($stk['CheckoutRequestID'] ?? ''),
            'code'        => (string) ($stk['ResultCode'] ?? ''),
            'desc'        => (string) ($stk['ResultDesc'] ?? ''),
            'receipt'     => null,
            'amount'      => null,
            'phone'       => null,
        ];

        foreach ($stk['CallbackMetadata']['Item'] ?? [] as $item) {
            $value = $item['Value'] ?? null;

            switch ($item['Name'] ?? '') {
                case 'MpesaReceiptNumber': $parsed['receipt'] = (string) $value; break;
                case 'Amount':             $parsed['amount']  = (float) $value;  break;
                case 'PhoneNumber':        $parsed['phone']   = (string) $value; break;
            }
        }

        return $parsed;
    }

    // ---- HTTP ----------------------------------------------------------

    private function token(): string
    {
        static $cached = null;

        if ($cached !== null) {
            return $cached;
        }

        $url = $this->base . '/oauth/v1/generate?grant_type=client_credentials';
        $ch  = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['Authorization: Basic ' . base64_encode($this->consumerKey . ':' . $this->consumerSecret)],
            CURLOPT_TIMEOUT        => 25,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $body   = curl_exec($ch);
        $errno  = curl_errno($ch);
        $error  = curl_error($ch);
        curl_close($ch);

        if ($errno !== 0) {
            throw new RuntimeException('Could not reach M-Pesa: ' . $error);
        }

        $data = json_decode((string) $body, true);

        if (!is_array($data) || empty($data['access_token'])) {
            self::log('Token request failed: ' . (string) $body);
            throw new RuntimeException('M-Pesa rejected the API credentials.');
        }

        $cached = (string) $data['access_token'];
        return $cached;
    }

    private function post(string $path, array $payload, string $token): array
    {
        $ch = curl_init($this->base . $path);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token,
            ],
            CURLOPT_TIMEOUT        => 40,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $body  = curl_exec($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($errno !== 0) {
            self::log("POST {$path} transport error: {$error}");
            throw new RuntimeException('Could not reach M-Pesa. Please try again, or pay on collection.');
        }

        self::log("POST {$path} -> " . (string) $body);

        $data = json_decode((string) $body, true);

        return is_array($data) ? $data : [];
    }

    public static function log(string $line): void
    {
        $file = dirname(__DIR__, 2) . '/storage/logs/mpesa.log';
        $dir  = dirname($file);

        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        @file_put_contents($file, '[' . date('Y-m-d H:i:s') . '] ' . $line . PHP_EOL, FILE_APPEND);
    }
}
