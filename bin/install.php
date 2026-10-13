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

// ---- Migrations --------------------------------------------------------
// The base schema is Phase 1. Everything since lives in database/migrations
// and is applied here so a fresh install matches an upgraded one exactly.
//
// They run in two passes, either side of the seed, because the two kinds want
// opposite things: the early ones add tables the seed writes to, so they have
// to come first or the seed fails outright; the later ones amend seeded copy —
// a page's wording, a setting — so they have to come after, or they update
// nothing and the seed then puts the old wording back.
//
// PRE_SEED names the structural ones. Anything not named there runs after the
// seed, which is the right default: almost everything since has been copy.
$pdo->exec(
    'CREATE TABLE IF NOT EXISTS `migrations` (
        `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `filename`   VARCHAR(190) NOT NULL,
        `applied_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `uq_migrations_filename` (`filename`)
     ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
);

const PRE_SEED = [
    '2026_08_18_000001_phase_two.sql',
    '2026_08_18_000002_seo.sql',
];

$applied    = $pdo->query('SELECT `filename` FROM `migrations`')->fetchAll(PDO::FETCH_COLUMN);
$migrations = glob($root . '/database/migrations/*.sql') ?: [];
sort($migrations);

$appliedNow = 0;

$apply = static function (array $files) use ($pdo, &$applied, &$appliedNow): void {
    foreach ($files as $file) {
        $name = basename($file);

        if (in_array($name, $applied, true)) {
            continue;
        }

        try {
            $pdo->exec(file_get_contents($file));
            $pdo->prepare('INSERT INTO `migrations` (`filename`) VALUES (?)')->execute([$name]);
            $applied[] = $name;
            $appliedNow++;
        } catch (PDOException $e) {
            out('  ERROR  Migration failed: ' . $name);
            out('         ' . $e->getMessage());
            exit(1);
        }
    }
};

// The structural ones first: the seed writes to tables they create.
$apply(array_filter($migrations, static fn (string $f): bool => in_array(basename($f), PRE_SEED, true)));

if (!$schemaOnly) {
    $run($pdo, $root . '/database/seed.sql', 'Seed data');
}

// Then the rest, which amend seeded copy and would find nothing to amend if
// they ran before it.
$apply(array_filter($migrations, static fn (string $f): bool => !in_array(basename($f), PRE_SEED, true)));

out($appliedNow > 0
    ? '  Migrations applied (' . $appliedNow . ')'
    : '  Migrations up to date');

// ---- Writable directories ---------------------------------------------
foreach ([
    '/public/uploads',
    '/public/uploads/products',
    '/public/uploads/brands',
    '/public/uploads/categories',
    '/public/uploads/services',
    '/public/uploads/repairs',
    '/public/uploads/site',
    '/storage/logs',
] as $path) {
    $dir = $root . $path;
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }
    out(is_writable($dir) ? '  Writable  ' . $path : '  WARNING   not writable: ' . $path);
}

// ---- Summary -----------------------------------------------------------
$counts = [];
foreach (['products', 'categories', 'brands', 'services', 'pages', 'users', 'customers', 'quotes', 'orders', 'bookings'] as $table) {
    try {
        $counts[$table] = (int) $pdo->query("SELECT COUNT(*) FROM `{$table}`")->fetchColumn();
    } catch (PDOException $e) {
        $counts[$table] = 0;
    }
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

    /*
     * The password hash in database/seed.sql is published with this repository,
     * so an account still carrying it is a published credential. It is replaced
     * here with one generated for this install and shown once.
     *
     * The installer rebuilds from schema.sql, which drops the tables, so in
     * practice the seeded row comes back on every run and every run prints a
     * new password. The check is there so that an account which already has a
     * password of its own is never overwritten.
     */
    $seededHash = '$2y$10$XAH0rk0QSalvKPYGNt1XUuCP7lXZfdBMWVDnbSNERGNCYlye97SEq';

    $admin = $pdo
        ->query("SELECT `id`, `password_hash` FROM `users` WHERE `email` = 'admin@tackrack.co.ke'")
        ->fetch(PDO::FETCH_ASSOC);

    if ($admin !== false && hash_equals($seededHash, (string) $admin['password_hash'])) {
        $password = bin2hex(random_bytes(9));

        $pdo->prepare('UPDATE `users` SET `password_hash` = ? WHERE `id` = ?')
            ->execute([password_hash($password, PASSWORD_DEFAULT), $admin['id']]);

        out('    Password  ' . $password);
        out('');
        out('  >> Generated for this install and shown once. Write it down now,');
        out('     then change it under Admin > My account.');
    } else {
        out('    Password  unchanged \— this account already has its own.');
    }

    out('');
}

out('  Start a local server with:');
out('    php -S localhost:8000 -t public bin/router.php');
out('');
out('  Done.');
out('');
