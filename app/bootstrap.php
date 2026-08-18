<?php
/**
 * Application bootstrap: paths, autoloading, error handling, session, DB.
 * Required by public/index.php and by CLI scripts under bin/.
 */

define('BASE_PATH',   dirname(__DIR__));
define('APP_PATH',    BASE_PATH . '/app');
define('CONFIG_PATH', BASE_PATH . '/config');
define('PUBLIC_PATH', BASE_PATH . '/public');
define('STORAGE_PATH', BASE_PATH . '/storage');

// ---- PSR-4 style autoloader for the App\ namespace ---------------------
spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';

    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relative = str_replace('\\', '/', substr($class, strlen($prefix)));
    $file     = APP_PATH . '/' . $relative . '.php';

    if (is_file($file)) {
        require $file;
    }
});

require APP_PATH . '/Core/helpers.php';

// ---- Environment -------------------------------------------------------
date_default_timezone_set(config('app.timezone', 'Africa/Nairobi'));
mb_internal_encoding('UTF-8');

$debug = (bool) config('app.debug', false);
ini_set('display_errors', $debug ? '1' : '0');
ini_set('log_errors', '1');
ini_set('error_log', STORAGE_PATH . '/logs/php-error.log');
error_reporting(E_ALL);

if (!is_dir(STORAGE_PATH . '/logs')) {
    @mkdir(STORAGE_PATH . '/logs', 0775, true);
}

// ---- Base URL / current path ------------------------------------------
if (PHP_SAPI === 'cli') {
    define('BASE_URL', rtrim((string) config('app.url', ''), '/'));
    define('CURRENT_PATH', '/');
} else {
    $configuredUrl = rtrim((string) config('app.url', ''), '/');

    if ($configuredUrl !== '') {
        define('BASE_URL', $configuredUrl);
    } else {
        $https  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
        $scheme = $https ? 'https' : 'http';
        $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';

        // Sub-directory installs (e.g. http://localhost/tackrack/public)
        $scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
        $scriptDir = $scriptDir === '/' ? '' : $scriptDir;

        // When the document root could not be pointed at public/ and the root
        // .htaccess rewrites into it instead, SCRIPT_NAME ends in /public.
        // Drop it so generated URLs stay clean.
        if (str_ends_with($scriptDir, '/public')) {
            $scriptDir = substr($scriptDir, 0, -strlen('/public'));
        }

        define('BASE_URL', $scheme . '://' . $host . $scriptDir);
    }

    $requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
    $basePath    = parse_url(BASE_URL, PHP_URL_PATH) ?? '';

    if ($basePath !== '' && $basePath !== '/' && str_starts_with($requestPath, $basePath)) {
        $requestPath = substr($requestPath, strlen($basePath));
    }

    define('CURRENT_PATH', '/' . trim($requestPath, '/'));
}

// ---- Session -----------------------------------------------------------
if (PHP_SAPI !== 'cli') {
    App\Core\Session::start(config('session'));
}

// ---- Database ----------------------------------------------------------
App\Core\Database::instance(config('db'));

// ---- Uncaught exception handling --------------------------------------
set_exception_handler(static function (Throwable $e) use ($debug): void {
    error_log($e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());

    if (PHP_SAPI === 'cli') {
        fwrite(STDERR, 'Error: ' . $e->getMessage() . PHP_EOL);
        exit(1);
    }

    http_response_code(500);

    if ($debug) {
        echo '<!doctype html><meta charset="utf-8"><title>Application error</title>';
        echo '<div style="font:14px/1.6 ui-monospace,Menlo,Consolas,monospace;max-width:60rem;margin:6vh auto;padding:0 1.5rem;color:#1a1a1a">';
        echo '<p style="font:600 13px/1 system-ui;letter-spacing:.12em;text-transform:uppercase;color:#9a3412">Application error</p>';
        echo '<h1 style="font:600 24px/1.3 system-ui;margin:.5rem 0 1.5rem">' . e($e->getMessage()) . '</h1>';
        echo '<p style="color:#666">' . e($e->getFile()) . ' line ' . (int) $e->getLine() . '</p>';
        echo '<pre style="background:#f6f6f4;padding:1.25rem;overflow:auto;border-radius:8px;font-size:12px">' . e($e->getTraceAsString()) . '</pre>';
        echo '</div>';
        return;
    }

    echo '<!doctype html><meta charset="utf-8"><title>Something went wrong</title>';
    echo '<div style="font:16px/1.7 Georgia,serif;max-width:34rem;margin:20vh auto;padding:0 1.5rem;text-align:center">';
    echo '<h1 style="font-weight:400;letter-spacing:-.02em">Something went wrong</h1>';
    echo '<p style="color:#555">We have logged the problem. Please try again, or call us on '
        . e(setting('contact_phone', '+254 722 763 279')) . '.</p>';
    echo '<p><a href="' . e(url('/')) . '" style="color:#8a6234">Return to the homepage</a></p></div>';
});
