<?php
/**
 * Bundle named migrations into one file that can be imported through
 * phpMyAdmin, for hosts with no command line to run bin/migrate.php on.
 *
 *   php bin/export-migrations-sql.php 2026_08_18_000003_location_and_logo.sql ...
 *
 * The bundle keeps the given order, and records each file in the `migrations`
 * table afterwards so bin/migrate.php agrees about what has been applied if
 * the site ever does get a command line.
 *
 * It refuses to bundle a migration that is not safe to run twice. Some create
 * rows without a guard — 2026_08_18_000001_phase_two.sql inserts the services
 * — and importing one of those onto a database that already has it would
 * duplicate the data rather than fail. Only re-runnable migrations belong in a
 * file a person is going to import by hand, possibly twice.
 */

require __DIR__ . '/../app/bootstrap.php';

$names = array_slice($argv, 1);

if ($names === []) {
    exit("Usage: php bin/export-migrations-sql.php <migration.sql> [...]\n");
}

$dir = BASE_PATH . '/database/migrations';
$out = BASE_PATH . '/database/apply-pending.sql';

$bundled = [];

foreach ($names as $name) {
    $path = $dir . '/' . basename($name);

    if (!is_file($path)) {
        exit("No such migration: {$name}\n");
    }

    $sql = file_get_contents($path);

    // An INSERT with no ON DUPLICATE KEY UPDATE and no IGNORE would duplicate
    // its rows on a second import.
    $inserts = preg_match_all('/\bINSERT\s+(?!IGNORE)INTO\b/i', $sql);
    $guards  = preg_match_all('/\bON\s+DUPLICATE\s+KEY\s+UPDATE\b/i', $sql);

    if ($inserts > $guards) {
        exit("Refusing to bundle {$name}: it has {$inserts} unguarded INSERT(s) and would\n"
            . "duplicate rows if the file were imported twice. Run it once, deliberately.\n");
    }

    $bundled[$name] = $sql;
}

$stamp = date('Y-m-d H:i');
$list  = implode("\n--   ", array_keys($bundled));

$header = <<<HEAD
-- Tack Rack — pending database migrations
-- Generated {$stamp} by bin/export-migrations-sql.php. Do not edit by hand.
--
-- Import through phpMyAdmin: select the site database, open the Import tab,
-- choose this file, and run it. It contains, in this order:
--
--   {$list}
--
-- Every statement in here is safe to run more than once, so importing it twice
-- changes nothing the second time.
--
-- It only touches the `settings` table and the `migrations` bookkeeping table.
-- No products, orders, customers or quotes are read or written.

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `migrations` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `filename`   VARCHAR(190) NOT NULL,
  `applied_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_migrations_filename` (`filename`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


HEAD;

$body = '';

foreach ($bundled as $name => $sql) {
    $body .= "-- =====================================================================\n";
    $body .= "--  {$name}\n";
    $body .= "-- =====================================================================\n\n";
    $body .= trim($sql) . "\n\n\n";
}

$body .= "-- Record what was applied, so bin/migrate.php does not offer these again.\n";
$body .= "INSERT IGNORE INTO `migrations` (`filename`) VALUES\n";
$body .= implode(",\n", array_map(
    static fn (string $name): string => "  (" . var_export($name, true) . ")",
    array_keys($bundled)
)) . ";\n";

file_put_contents($out, $header . $body);

foreach (array_keys($bundled) as $name) {
    echo "  bundled  {$name}\n";
}

printf("\n%s  (%s KB)\n", $out, round(strlen($header . $body) / 1024, 1));
