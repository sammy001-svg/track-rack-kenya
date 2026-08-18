<?php
namespace App\Core;

class Csrf
{
    private const KEY = '_csrf_token';

    public static function token(): string
    {
        if (!Session::has(self::KEY)) {
            Session::set(self::KEY, bin2hex(random_bytes(32)));
        }
        return Session::get(self::KEY);
    }

    public static function field(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . e(self::token()) . '">';
    }

    public static function check(?string $token): bool
    {
        $stored = Session::get(self::KEY);
        return is_string($stored) && is_string($token) && hash_equals($stored, $token);
    }

    /**
     * Verify the token on the current POST request, aborting with 419 if it
     * does not match. Called from the front controller for every POST.
     */
    public static function verify(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            return;
        }

        if (self::check($_POST['csrf_token'] ?? null)) {
            return;
        }

        http_response_code(419);

        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        if (str_contains($accept, 'application/json')) {
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'error' => 'Your session expired. Please reload the page.']);
            exit;
        }

        echo '<!doctype html><meta charset="utf-8"><title>Session expired</title>'
            . '<div style="font:16px/1.6 system-ui;max-width:38rem;margin:20vh auto;padding:0 1.5rem">'
            . '<h1 style="font-weight:600">Session expired</h1>'
            . '<p>Your session token was missing or out of date, so the form was not submitted. '
            . 'Please go back, reload the page and try again.</p></div>';
        exit;
    }
}
