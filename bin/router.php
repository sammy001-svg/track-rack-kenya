<?php
/**
 * Router for PHP's built-in development server.
 *
 *   php -S localhost:8080 -t public bin/router.php
 *
 * Serves real files straight from public/ and sends everything else to
 * the front controller, mirroring what public/.htaccess does on Apache.
 */

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
$file = __DIR__ . '/../public' . $path;

if ($path !== '/' && is_file($file)) {
    return false; // Let the built-in server handle the static asset.
}

require __DIR__ . '/../public/index.php';
