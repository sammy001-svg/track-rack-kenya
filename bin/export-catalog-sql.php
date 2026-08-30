<?php
/**
 * Dump the real catalogue — categories, brands, products and product images —
 * as a plain SQL file that can be imported through phpMyAdmin.
 *
 * This exists because the live host has no CLI, so bin/import-products.php
 * (which needs GD to resize the studio originals) cannot be run there. The
 * resized photographs are already version controlled under
 * public/uploads/products and therefore already deployed by cPanel's Git
 * checkout — only the database rows are missing. This file supplies them.
 *
 * The output deletes the existing catalogue first. That is deliberate: the
 * live site is still carrying the placeholder seed data. Deleting is safe for
 * order history because order_items.product_id is ON DELETE SET NULL and the
 * row keeps its own product_name and product_sku snapshot, so past orders and
 * quotes still read correctly afterwards.
 *
 * It never touches users, settings, pages, orders, quotes, bookings or repairs.
 *
 *   php bin/export-catalog-sql.php
 */

require __DIR__ . '/../app/bootstrap.php';

use App\Core\Database;

$db  = Database::instance();
$pdo = $db->pdo();
$out = __DIR__ . '/../database/catalog-import.sql';

/** Render one value as a SQL literal. */
function lit(?string $v, PDO $pdo): string
{
    return $v === null ? 'NULL' : $pdo->quote($v);
}

/**
 * Build a multi-row INSERT for a table, taking the column list from the rows
 * themselves so a later migration adding a column needs no change here.
 */
function insert(string $table, array $rows, PDO $pdo): string
{
    if ($rows === []) {
        return "-- {$table}: nothing to insert\n";
    }

    $cols = array_keys($rows[0]);
    $sql  = "INSERT INTO `{$table}` (`" . implode('`, `', $cols) . "`) VALUES\n";

    $values = [];
    foreach ($rows as $row) {
        $cells = [];
        foreach ($cols as $c) {
            $cells[] = lit($row[$c] === null ? null : (string) $row[$c], $pdo);
        }
        $values[] = '  (' . implode(', ', $cells) . ')';
    }

    return $sql . implode(",\n", $values) . ";\n";
}

$categories = $db->all('SELECT * FROM categories ORDER BY id');
$brands     = $db->all('SELECT * FROM brands ORDER BY id');
$products   = $db->all('SELECT * FROM products ORDER BY id');
$images     = $db->all('SELECT * FROM product_images ORDER BY product_id, sort_order');

// Guard against exporting a half-built local database over the live one.
if (count($products) === 0 || count($images) === 0) {
    fwrite(STDERR, "Refusing to export: the local catalogue is empty.\n");
    exit(1);
}

$orphans = [];
foreach ($images as $img) {
    if (!is_file(__DIR__ . '/../public' . $img['path'])) {
        $orphans[] = $img['path'];
    }
}

if ($orphans !== []) {
    fwrite(STDERR, 'Refusing to export: ' . count($orphans) . " image rows have no file on disk.\n");
    foreach (array_slice($orphans, 0, 10) as $o) {
        fwrite(STDERR, "  {$o}\n");
    }
    exit(1);
}

$stamp = date('Y-m-d H:i');

$sql = <<<HEAD
-- Tack Rack — real catalogue
-- Generated {$stamp} by bin/export-catalog-sql.php. Do not edit by hand.
--
-- Import through phpMyAdmin: select the site database, open the Import tab,
-- choose this file, and run it. The product photographs are already on the
-- server (they ship in the repository under public/uploads/products), so
-- nothing else needs uploading.
--
-- This replaces the placeholder seed catalogue with the real one. Past orders
-- and quotes survive: their product_id becomes NULL but each line keeps its own
-- product_name and product_sku, so the history still reads correctly.
--
-- It does NOT touch users, settings, pages, orders, quotes, bookings or repairs.

SET NAMES utf8mb4;
SET SESSION sql_mode = '';

START TRANSACTION;

-- Out with the placeholder catalogue. DELETE rather than TRUNCATE: TRUNCATE is
-- refused on a table another table points at, which is how this failed before.
DELETE FROM `product_images`;
DELETE FROM `product_variants`;
DELETE FROM `products`;
DELETE FROM `categories`;
DELETE FROM `brands`;


HEAD;

$sql .= "-- Categories (" . count($categories) . ")\n";
$sql .= insert('categories', $categories, $pdo) . "\n\n";

$sql .= "-- Brands (" . count($brands) . ")\n";
$sql .= insert('brands', $brands, $pdo) . "\n\n";

$sql .= "-- Products (" . count($products) . ")\n";
$sql .= insert('products', $products, $pdo) . "\n\n";

$sql .= "-- Product images (" . count($images) . ")\n";
$sql .= insert('product_images', $images, $pdo) . "\n\n";

$sql .= "COMMIT;\n";

file_put_contents($out, $sql);

printf("%-18s %d\n", 'categories', count($categories));
printf("%-18s %d\n", 'brands', count($brands));
printf("%-18s %d\n", 'products', count($products));
printf("%-18s %d\n", 'images', count($images));
printf("\n%s  (%s KB)\n", realpath($out), round(strlen($sql) / 1024));
