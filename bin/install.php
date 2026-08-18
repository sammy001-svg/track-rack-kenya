<?php
/**
 * One-shot installer.
 *
 *   php bin/install.php            # create the database, schema and seed data
 *   php bin/install.php --fresh    # drop and recreate everything (DESTRUCTIVE)
 *   php bin/install.php --schema   # schema only, no demo catalog
 *
 * Reads its connection details from config/config.php.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("This script can only be run from the command line.\n");
}

$root = dirname(__DIR__);
require $root . '/app/Core/helpers.php';

define('CONFIG_PATH', $root . '/config');

$options   = array_slice($argv, 1);
$fresh     = in_array('--fresh', $options, true);
$schemaOnly = in_array('--schema', $options, true);

$db       = config('db');
$database = $db['database'];

function out(string $line): void
{
    echo $line . PHP_EOL;
}

out('');
out('  Tack Rack Kenya — installer');
out('  ' . str_repeat('=', 40));
out('');

// ---- Connect without selecting a database ------------------------------
$dsn = sprintf('mysql:host=%s;port=%d;charset=%s', $db['host'], $db['port'], $db['charset']);

try {
    $pdo = new PDO($dsn, $db['username'], $db['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
} catch (PDOException $e) {
    out('  ERROR  Could not connect to MySQL at ' . $db['host'] . ':' . $db['port']);
    out('         ' . $e->getMessage());
    out('');
    out('         Check that MySQL/MariaDB is running and that the credentials in');
    out('         config/config.php are correct.');
    out('');
    exit(1);
}

out('  Connected to MySQL at ' . $db['host'] . ':' . $db['port']);

// ---- Create (or recreate) the database --------------------------------
if ($fresh) {
    $pdo->exec("DROP DATABASE IF EXISTS `{$database}`");
    out('  Dropped existing database `' . $database . '`');
}

$pdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
out('  Database `' . $database . '` ready');

$pdo->exec("USE `{$database}`");

// ---- Run the SQL files -------------------------------------------------
$run = static function (PDO $pdo, string $file, string $label): void {
    if (!is_file($file)) {
        out('  ERROR  Missing SQL file: ' . $file);
        exit(1);
    }

    $sql = file_get_contents($file);

    try {
        $pdo->exec($sql);
        out('  ' . $label . ' applied');
    } catch (PDOException $e) {
        out('  ERROR  ' . $label . ' failed');
        out('         ' . $e->getMessage());
        exit(1);
    }
};

$run($pdo, $root . '/database/schema.sql', 'Schema');

if (!$schemaOnly) {
    $run($pdo, $root . '/database/seed.sql', 'Seed data');
}

// ---- Writable directories ---------------------------------------------
foreach (['/public/uploads', '/public/uploads/products', '/public/uploads/brands', '/public/uploads/categories', '/storage/logs'] as $path) {
    $dir = $root . $path;
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }
    out(is_writable($dir) ? '  Writable  ' . $path : '  WARNING   not writable: ' . $path);
}

// ---- Summary -----------------------------------------------------------
$counts = [];
foreach (['products', 'categories', 'brands', 'quotes', 'pages', 'users'] as $table) {
    $counts[$table] = (int) $pdo->query("SELECT COUNT(*) FROM `{$table}`")->fetchColumn();
}

out('');
out('  ' . str_repeat('-', 40));
foreach ($counts as $table => $count) {
    out(sprintf('  %-12s %d', $table, $count));
}
out('  ' . str_repeat('-', 40));
out('');

if (!$schemaOnly) {
    out('  Admin sign-in');
    out('    URL       /admin/login');
    out('    Email     admin@tackrack.co.ke');
    out('    Password  TackRack@2026');
    out('');
    out('  >> Change this password immediately under Admin > My account.');
    out('');
}

out('  Start a local server with:');
out('    php -S localhost:8000 -t public bin/router.php');
out('');
out('  Done.');
out('');
