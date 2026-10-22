<?php
/**
 * Write a migration that attaches the photographs added since the last one.
 *
 *   php bin/export-photographs-sql.php <product-slug> [<product-slug> ...]
 *
 * The image files themselves travel in the repository under
 * public/uploads/products, so a deployment only needs the rows that point at
 * them. Written as a migration so it reaches a live site the same way
 * everything else does, on the first request after the deploy.
 *
 * Idempotent: a row is inserted only where that path is not already on the
 * product, and the lead image is set by path rather than by shuffling
 * sort_order, so running it twice leaves the same result.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("This script can only be run from the command line.\n");
}

require dirname(__DIR__) . '/app/bootstrap.php';

use App\Core\Database;

$slugs = array_slice($argv, 1);

if ($slugs === []) {
    exit("Give it the product slugs whose photographs should be exported.\n");
}

$db  = Database::instance();
$pdo = $db->pdo();
$out = BASE_PATH . '/database/migrations/2026_10_22_000012_new_photographs.sql';

$q = static fn ($v) => $v === null ? 'NULL' : $pdo->quote((string) $v);

$stamp = date('Y-m-d H:i');
$sql   = <<<HEAD
-- =====================================================================
--  Photographs for the listings added alongside them
--
--  Generated {$stamp} by bin/export-photographs-sql.php. Do not edit by hand.
--
--  The image files travel in the repository under public/uploads/products;
--  these are only the rows that point at them. Idempotent: a row goes in
--  only where that path is not already on the product, and the lead image
--  is chosen by path, so running this twice changes nothing.
-- =====================================================================

SET NAMES utf8mb4;


HEAD;

$total = 0;

foreach ($slugs as $slug) {
    $product = $db->all('SELECT id, name FROM products WHERE slug = :s', ['s' => $slug])[0] ?? null;

    if ($product === null) {
        fwrite(STDERR, "  !! no product with slug {$slug}\n");
        continue;
    }

    $images = $db->all(
        'SELECT path, alt, is_primary, sort_order FROM product_images
          WHERE product_id = :i ORDER BY is_primary DESC, sort_order',
        ['i' => (int) $product['id']]
    );

    if ($images === []) {
        continue;
    }

    $sql .= "-- {$product['name']}\n";
    $lead = null;

    foreach ($images as $img) {
        if ((int) $img['is_primary'] === 1) {
            $lead = $img['path'];
        }

        $sql .= sprintf(
            "INSERT INTO `product_images` (`product_id`, `path`, `alt`, `is_primary`, `sort_order`)\n"
          . "SELECT p.`id`, %s, %s, %d, %d FROM `products` p\n"
          . " WHERE p.`slug` = %s\n"
          . "   AND NOT EXISTS (SELECT 1 FROM `product_images` i\n"
          . "                    WHERE i.`product_id` = p.`id` AND i.`path` = %s);\n",
            $q($img['path']), $q($img['alt']), (int) $img['is_primary'], (int) $img['sort_order'],
            $q($slug), $q($img['path'])
        );

        $total++;
    }

    if ($lead !== null) {
        // Set the lead by name rather than by shuffling the order, so this
        // lands the same way however many times it runs.
        $sql .= sprintf(
            "UPDATE `product_images` i JOIN `products` p ON p.`id` = i.`product_id`\n"
          . "   SET i.`is_primary` = IF(i.`path` = %s, 1, 0)\n"
          . " WHERE p.`slug` = %s;\n",
            $q($lead), $q($slug)
        );
    }

    $sql .= "\n";
}

file_put_contents($out, $sql);

printf("\n  %d image row(s) across %d product(s)\n", $total, count($slugs));
printf("  %s  (%d KB)\n\n", str_replace(BASE_PATH . DIRECTORY_SEPARATOR, '', $out), (int) round(strlen($sql) / 1024));
