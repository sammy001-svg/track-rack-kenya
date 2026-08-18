<?php
/**
 * Application configuration.
 *
 * For a real deployment, copy this file to config.local.php and override
 * anything environment-specific there. config.local.php is git-ignored and
 * is merged over these values automatically.
 */

$config = [

    // ---- Application ------------------------------------------------
    'app' => [
        'name'      => 'Tack Rack',
        'env'       => 'local',              // local | production
        'debug'     => true,                 // set false in production
        'url'       => '',                   // e.g. https://tackrack.co.ke - blank = auto-detect
        'timezone'  => 'Africa/Nairobi',
        'locale'    => 'en_KE',
        'currency'  => 'KSh',
    ],

    // ---- Database ---------------------------------------------------
    'db' => [
        'host'     => '127.0.0.1',
        'port'     => 3306,
        'database' => 'tackrack',
        'username' => 'root',
        'password' => '',
        'charset'  => 'utf8mb4',
    ],

    // ---- Sessions ---------------------------------------------------
    'session' => [
        'name'     => 'tackrack_session',
        'lifetime' => 7200,                  // 2 hours
        'secure'   => false,                 // true once served over HTTPS
    ],

    // ---- Mail -------------------------------------------------------
    // Uses PHP mail(). On XAMPP this silently fails unless sendmail is
    // configured - quote requests are always persisted to the database
    // regardless, so nothing is lost.
    'mail' => [
        'enabled'    => false,
        'from_email' => 'no-reply@tackrack.co.ke',
        'from_name'  => 'Tack Rack Website',
    ],

    // ---- Uploads ----------------------------------------------------
    'uploads' => [
        'path'       => __DIR__ . '/../public/uploads',
        'url'        => '/uploads',
        'max_bytes'  => 5 * 1024 * 1024,     // 5 MB
        'mimes'      => ['image/jpeg', 'image/png', 'image/webp', 'image/gif'],
        'extensions' => ['jpg', 'jpeg', 'png', 'webp', 'gif'],
    ],

    // ---- Pagination -------------------------------------------------
    'per_page' => [
        'shop'  => 12,
        'admin' => 20,
    ],
];

$localConfig = __DIR__ . '/config.local.php';
if (is_file($localConfig)) {
    $overrides = require $localConfig;
    if (is_array($overrides)) {
        foreach ($overrides as $section => $values) {
            $config[$section] = is_array($values) && isset($config[$section]) && is_array($config[$section])
                ? array_replace($config[$section], $values)
                : $values;
        }
    }
}

return $config;
