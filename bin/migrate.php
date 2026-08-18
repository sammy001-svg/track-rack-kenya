<?php
/**
 * Migration runner.
 *
 *   php bin/migrate.php            # apply any pending migrations
 *   php bin/migrate.php --status   # list applied and pending, change nothing
 *
 * Migrations live in database/migrations/ and are applied in filename order.
 * Each is recorded in the `migrations` table so it is never applied twice.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("This script can only be run from the command line.\n");
}

$root = dirname(__DIR__);
require $root . '/app/Core/helpers.php';
define('CONFIG_PATH', $root . '/config');

$statusOnly = in_array('--status', array_slice($argv, 1), true);
$db = config('db');

function out(string $line = ''): void { echo $line . PHP_EOL; }

out();
out('  Tack Rack Kenya — migrations');
out('  ' . str_repeat('=', 44));
out();

try {
    $pdo = new PDO(
        sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $db['host'], $db['port'], $db['database'], $db['charset']),
        $db['username'],
        $db['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    out('  ERROR  ' . $e->getMessage());
    out('         Run php bin/install.php first.');
    out();
    exit(1);
}

$pdo->exec(
    'CREATE TABLE IF NOT EXISTS `migrations` (
        `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `filename`   VARCHAR(190) NOT NULL,
        `applied_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `uq_migrations_filename` (`filename`)
     ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
);

$applied = $pdo->query('SELECT `filename` FROM `migrations`')->fetchAll(PDO::FETCH_COLUMN);

$files = glob($root . '/database/migrations/*.sql') ?: [];
sort($files);

if ($files === []) {
    out('  No migration files found.');
    out();
    exit(0);
}

$pending = [];

foreach ($files as $file) {
    $name = basename($file);
    if (in_array($name, $applied, true)) {
        out('  applied  ' . $name);
    } else {
        out('  PENDING  ' . $name);
        $pending[] = $file;
    }
}

out();

if ($statusOnly) {
    out('  ' . count($pending) . ' pending. Run without --status to apply.');
    out();
    exit(0);
}

if ($pending === []) {
    out('  Nothing to do — the database is up to date.');
    out();
    exit(0);
}

foreach ($pending as $file) {
    $name = basename($file);

    try {
        $pdo->exec(file_get_contents($file));

        $stmt = $pdo->prepare('INSERT INTO `migrations` (`filename`) VALUES (?)');
        $stmt->execute([$name]);

        out('  OK       ' . $name);
    } catch (PDOException $e) {
        out('  FAILED   ' . $name);
        out('           ' . $e->getMessage());
        out();
        out('  Migrations stopped. Fix the error and run again;');
        out('  already-applied migrations will be skipped.');
        out();
        exit(1);
    }
}

out();
out('  ' . count($pending) . ' migration(s) applied.');
out();
