<?php
namespace App\Core;

class Session
{
    public static function start(array $config): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        session_name($config['name']);
        session_set_cookie_params([
            'lifetime' => $config['lifetime'],
            'path'     => '/',
            'secure'   => (bool) $config['secure'],
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();
    }

    public static function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function forget(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /** Read a value once, then remove it. */
    public static function pull(string $key, $default = null)
    {
        $value = $_SESSION[$key] ?? $default;
        unset($_SESSION[$key]);
        return $value;
    }

    public static function regenerate(): void
    {
        session_regenerate_id(true);
    }

    public static function destroy(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }

        session_destroy();
    }

    // ---- Flash messages -------------------------------------------------

    public static function flash(string $type, string $message): void
    {
        $_SESSION['_flash'][] = ['type' => $type, 'message' => $message];
    }

    public static function flashes(): array
    {
        $messages = $_SESSION['_flash'] ?? [];
        unset($_SESSION['_flash']);
        return $messages;
    }

    // ---- Form repopulation ----------------------------------------------

    public static function flashInput(array $input): void
    {
        unset($input['csrf_token'], $input['password'], $input['password_confirm']);
        $_SESSION['_old'] = $input;
    }

    public static function old(string $key, $default = '')
    {
        return $_SESSION['_old'][$key] ?? $default;
    }

    public static function clearOld(): void
    {
        unset($_SESSION['_old']);
    }

    public static function flashErrors(array $errors): void
    {
        $_SESSION['_errors'] = $errors;
    }

    public static function errors(): array
    {
        $errors = $_SESSION['_errors'] ?? [];
        unset($_SESSION['_errors']);
        return $errors;
    }
}
