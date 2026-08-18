<?php
namespace App\Core;

use App\Models\User;

class Auth
{
    private const KEY = '_admin_user_id';

    public static function attempt(string $email, string $password): bool
    {
        $user = (new User())->findBy('email', $email);

        if ($user === null || (int) $user['is_active'] !== 1) {
            // Equalise timing against a valid-account guess.
            password_verify($password, '$2y$10$invalidinvalidinvalidinvalidinvalidinvalidinvalidinvalidi');
            return false;
        }

        if (!password_verify($password, $user['password_hash'])) {
            return false;
        }

        if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
            (new User())->updateById((int) $user['id'], [
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            ]);
        }

        Session::regenerate();
        Session::set(self::KEY, (int) $user['id']);

        (new User())->updateById((int) $user['id'], ['last_login_at' => date('Y-m-d H:i:s')]);

        return true;
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

        $id = Session::get(self::KEY);
        if ($id === null) {
            $resolved = true;
            return null;
        }

        $user = (new User())->find((int) $id);

        if ($user === null || (int) $user['is_active'] !== 1) {
            Session::forget(self::KEY);
            $user = null;
        } else {
            unset($user['password_hash']);
        }

        $cached   = $user;
        $resolved = true;

        return $cached;
    }

    public static function id(): ?int
    {
        $user = self::user();
        return $user === null ? null : (int) $user['id'];
    }

    public static function isAdmin(): bool
    {
        $user = self::user();
        return $user !== null && $user['role'] === 'admin';
    }

    public static function logout(): void
    {
        Session::forget(self::KEY);
        Session::regenerate();
    }

    /**
     * Route middleware: send guests to the login screen.
     * Returns false to tell the router to stop dispatching.
     */
    public static function requireLogin(): bool
    {
        if (self::check()) {
            return true;
        }

        Session::set('_intended', $_SERVER['REQUEST_URI'] ?? '/admin');
        header('Location: ' . url('/admin/login'));
        return false;
    }

    /** Route middleware: admin role only. */
    public static function requireAdmin(): bool
    {
        if (!self::requireLogin()) {
            return false;
        }

        if (self::isAdmin()) {
            return true;
        }

        Session::flash('error', 'That area is restricted to administrators.');
        header('Location: ' . url('/admin'));
        return false;
    }
}
