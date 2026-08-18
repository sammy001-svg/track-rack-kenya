<?php
namespace App\Core;

use App\Models\Customer;

/**
 * Storefront authentication. Deliberately separate from App\Core\Auth so a
 * customer session can never be mistaken for a staff session — different
 * session key, different model, different guard.
 */
class CustomerAuth
{
    private const KEY = '_customer_id';
    private const MAX_ATTEMPTS = 8;
    private const LOCKOUT_SECONDS = 900;

    public static function attempt(string $email, string $password): bool
    {
        $model    = new Customer();
        $customer = $model->findBy('email', $email);

        if ($customer === null || (int) $customer['is_active'] !== 1) {
            // Equalise timing so a valid address cannot be probed.
            password_verify($password, '$2y$10$invalidinvalidinvalidinvalidinvalidinvalidinvalidinvalidi');
            return false;
        }

        if (!password_verify($password, $customer['password_hash'])) {
            return false;
        }

        if (password_needs_rehash($customer['password_hash'], PASSWORD_DEFAULT)) {
            $model->updateById((int) $customer['id'], [
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            ]);
        }

        self::login((int) $customer['id']);
        $model->updateById((int) $customer['id'], ['last_login_at' => date('Y-m-d H:i:s')]);

        return true;
    }

    public static function login(int $customerId): void
    {
        Session::regenerate();
        Session::set(self::KEY, $customerId);
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function user(): ?array
    {
        static $cached = null;
        static $resolved = false;

        if ($resolved) {
            return $cached;
        }

        $resolved = true;
        $id       = Session::get(self::KEY);

        if ($id === null) {
            return null;
        }

        $customer = (new Customer())->find((int) $id);

        if ($customer === null || (int) $customer['is_active'] !== 1) {
            Session::forget(self::KEY);
            return null;
        }

        unset($customer['password_hash'], $customer['reset_token']);

        $cached = $customer;
        return $cached;
    }

    public static function id(): ?int
    {
        $customer = self::user();
        return $customer === null ? null : (int) $customer['id'];
    }

    public static function logout(): void
    {
        Session::forget(self::KEY);
        Session::regenerate();
    }

    /** Route middleware: send guests to the sign-in page. */
    public static function requireLogin(): bool
    {
        if (self::check()) {
            return true;
        }

        Session::set('_customer_intended', $_SERVER['REQUEST_URI'] ?? '/account');
        Session::flash('error', 'Please sign in to continue.');
        header('Location: ' . url('/account/login'));
        return false;
    }

    // ---- Throttling ----------------------------------------------------

    public static function isLockedOut(): bool
    {
        $attempts = Session::get('_cust_attempts', 0);
        $last     = Session::get('_cust_last_fail', 0);

        if ($attempts < self::MAX_ATTEMPTS) {
            return false;
        }

        if (time() - $last > self::LOCKOUT_SECONDS) {
            self::clearFailures();
            return false;
        }

        return true;
    }

    public static function recordFailure(): void
    {
        Session::set('_cust_attempts', Session::get('_cust_attempts', 0) + 1);
        Session::set('_cust_last_fail', time());
    }

    public static function clearFailures(): void
    {
        Session::forget('_cust_attempts');
        Session::forget('_cust_last_fail');
    }
}
