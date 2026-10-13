<?php
/**
 * Turn the sizes & colours sheet into a file that can be imported through
 * phpMyAdmin without touching anything else.
 *
 * database/catalog-import.sql replaces the whole catalogue: it deletes every
 * product and writes ours back. That is the right tool when the live database
 * is still carrying placeholder data, and the wrong one once somebody has been
 * editing products in the admin console, because their work goes with it.
 *
 * This file only ever writes to `product_variants`. It matches products by
 * slug rather than by id, so it does not care that the two databases number
 * their rows differently, and database/variants.php lists both sites' slugs
 * for each row of the sheet. A product that is not there is skipped rather
 * than created, and running the file twice leaves the same rows behind.
 *
 *   php bin/export-variants-sql.php
 */

require __DIR__ . '/../app/bootstrap.php';

use App\Core\Database;

$sheet = require __DIR__ . '/../database/variants.php';
$pdo   = Database::instance()->pdo();

$q = static fn (string $s): string => $pdo->quote($s);

$slugs = [];
$rows  = [];

foreach ($sheet as $label => $row) {
    foreach ($row['products'] as $slug) {
        $slugs[$slug] = true;
        $sort = 0;

        foreach ($row['variants'] as $group => $values) {
            foreach ($values as $value) {
                $rows[] = [$slug, $group, $value, $sort++];
            }
        }
    }
}

if ($rows === []) {
    fwrite(STDERR, "Refusing to export: the sheet produced no options.\n");
    exit(1);
}

$slugs = array_keys($slugs);
sort($slugs);

$stamp  = date('Y-m-d H:i');
$count  = count($rows);
$items  = count($slugs);
$sheetN = count($sheet);

$sql = <<<HEAD
-- Tack Rack — sizes, colours and packs
-- Generated {$stamp} by bin/export-variants-sql.php. Do not edit by hand.
--
-- {$count} options across {$items} products, from {$sheetN} rows of the
-- client's sizes & colours sheet (database/variants.php).
--
-- Import through phpMyAdmin: select the site database, open the Import tab,
-- choose this file, and run it. It is safe to run on a live site.
--
-- This file is additive. It writes to `product_variants` and to nothing else:
-- no product, category, image, order, quote, booking or setting is created,
-- changed or removed by it. Products are matched on their slug, and the slug
-- list covers both this catalogue and the live site's, which names a number of
-- things differently. A product that is not present is skipped rather than
-- guessed at, and running the file a second time replaces its own rows instead
-- of duplicating them.

SET NAMES utf8mb4;

START TRANSACTION;

-- Clear only the rows this file manages, so a second run replaces them.
DELETE v FROM `product_variants` v
  JOIN `products` p ON p.`id` = v.`product_id`
 WHERE p.`slug` IN (

HEAD;

$sql .= '  ' . implode(",\n  ", array_map($q, $slugs)) . "\n);\n\n";

$sql .= "-- The options themselves, joined onto whatever id each product has here.\n";
$sql .= "INSERT INTO `product_variants` (`product_id`, `label`, `value`, `sort_order`)\n";
$sql .= "SELECT p.`id`, v.`label`, v.`value`, v.`sort_order`\n";
$sql .= "  FROM `products` p\n";
$sql .= "  JOIN (\n";

$lines = [];
foreach ($rows as $i => [$slug, $group, $value, $sort]) {
    $lines[] = sprintf(
        '    %s %s AS `slug`, %s AS `label`, %s AS `value`, %d AS `sort_order`',
        $i === 0 ? 'SELECT' : 'UNION ALL SELECT',
        $q($slug),
        $q($group),
        $q($value),
        $sort
    );
}

$sql .= implode("\n", $lines) . "\n";
$sql .= "  ) v ON v.`slug` = p.`slug`;\n\n";
$sql .= "COMMIT;\n";

$out = __DIR__ . '/../database/add-variants.sql';
file_put_contents($out, $sql);

printf("%-18s %d\n", 'sheet rows', $sheetN);
printf("%-18s %d\n", 'products', $items);
printf("%-18s %d\n", 'options', $count);
printf("\n%s  (%s KB)\n", realpath($out), round(strlen($sql) / 1024));
