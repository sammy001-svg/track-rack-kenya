<?php
/**
 * Write database/migrations/… — the category tree as a migration.
 *
 *   php bin/export-category-tree-sql.php
 *
 * The full catalogue import replaces everything, which is fine for a fresh
 * install but heavy-handed for a site already carrying orders: it empties the
 * tables and every order line loses the product it pointed at. This writes the
 * same tree as an additive migration instead.
 *
 * Everything is matched on slug, so existing rows keep their ids and the
 * orders and quotes that reference them stay intact. It only ever inserts,
 * updates, or hides — it never deletes. Running it twice changes nothing the
 * second time, which is what lets the application apply it unattended.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("This script can only be run from the command line.\n");
}

require dirname(__DIR__) . '/app/bootstrap.php';

use App\Core\Database;

$db  = Database::instance();
$pdo = $db->pdo();
$out = BASE_PATH . '/database/migrations/2026_10_21_000010_category_tree.sql';

function q(PDO $pdo, $value): string
{
    return $value === null || $value === '' ? 'NULL' : $pdo->quote((string) $value);
}

$categories = $db->all(
    'SELECT c.slug, c.name, c.tagline, c.description, c.meta_title, c.meta_desc,
            c.image, c.sort_order, c.is_active, p.slug AS parent_slug
       FROM categories c
       LEFT JOIN categories p ON p.id = c.parent_id
      ORDER BY COALESCE(c.parent_id, c.id), c.sort_order, c.id'
);

$products = $db->all(
    'SELECT p.slug, c.slug AS category_slug
       FROM products p
       JOIN categories c ON c.id = p.category_id
      WHERE p.is_active = 1
      ORDER BY p.slug'
);

$stamp = date('Y-m-d H:i');
$sql   = <<<HEAD
-- =====================================================================
--  The category tree — four departments, their sections, and the
--  sub-categories beneath them.
--
--  Generated {$stamp} by bin/export-category-tree-sql.php. Do not edit by hand.
--
--  Additive and idempotent. Categories are matched on slug, so rows that
--  already exist keep their ids and nothing that points at them is
--  disturbed: orders, quotes and their line items are untouched, as are
--  users, settings, pages, bookings and repairs.
--
--  Nothing is deleted. A category that is not part of this tree is hidden
--  by clearing is_active, so it drops out of the navigation while its rows
--  and history remain.
-- =====================================================================

SET NAMES utf8mb4;

-- ---------------------------------------------------------------------
--  The meta columns arrive with an earlier migration rather than with
--  schema.sql. Add them if this database has never had them, in the same
--  guarded form the other migrations use.
-- ---------------------------------------------------------------------
SET @has := (SELECT COUNT(*) FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'categories' AND COLUMN_NAME = 'meta_title');
SET @sql := IF(@has = 0,
  'ALTER TABLE `categories`
     ADD COLUMN `meta_title` VARCHAR(190) NULL AFTER `description`,
     ADD COLUMN `meta_desc`  VARCHAR(300) NULL AFTER `meta_title`',
  'SELECT "categories meta columns already present"');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;


HEAD;

// ---- 1. The categories themselves -------------------------------------
$sql .= "-- ---------------------------------------------------------------------\n";
$sql .= "--  1. Every category in the tree (" . count($categories) . "), matched on slug.\n";
$sql .= "--     parent_id is deliberately left alone here and set below, because a\n";
$sql .= "--     parent may not exist yet on the first pass.\n";
$sql .= "-- ---------------------------------------------------------------------\n";
$sql .= "INSERT INTO `categories`\n";
$sql .= "  (`slug`, `name`, `tagline`, `description`, `meta_title`, `meta_desc`, `image`, `sort_order`, `is_active`)\nVALUES\n";

$rows = [];

foreach ($categories as $c) {
    $rows[] = sprintf('  (%s, %s, %s, %s, %s, %s, %s, %d, 1)',
        q($pdo, $c['slug']), q($pdo, $c['name']), q($pdo, $c['tagline']),
        q($pdo, $c['description']), q($pdo, $c['meta_title']), q($pdo, $c['meta_desc']),
        q($pdo, $c['image']), (int) $c['sort_order']);
}

$sql .= implode(",\n", $rows) . "\n";
$sql .= "ON DUPLICATE KEY UPDATE\n"
      . "  `name`       = VALUES(`name`),\n"
      . "  `tagline`    = VALUES(`tagline`),\n"
      . "  `description`= VALUES(`description`),\n"
      . "  `meta_title` = VALUES(`meta_title`),\n"
      . "  `meta_desc`  = VALUES(`meta_desc`),\n"
      . "  `image`      = VALUES(`image`),\n"
      . "  `sort_order` = VALUES(`sort_order`),\n"
      . "  `is_active`  = 1;\n\n";

// ---- 2. Parents, by slug ----------------------------------------------
$departments = array_values(array_filter($categories, static fn($c) => $c['parent_slug'] === null));
$children    = array_values(array_filter($categories, static fn($c) => $c['parent_slug'] !== null));

$sql .= "-- ---------------------------------------------------------------------\n";
$sql .= "--  2. Who sits under whom. Resolved by slug so existing ids survive.\n";
$sql .= "-- ---------------------------------------------------------------------\n";
$sql .= 'UPDATE `categories` SET `parent_id` = NULL WHERE `slug` IN ('
      . implode(', ', array_map(static fn($d) => q($pdo, $d['slug']), $departments)) . ");\n\n";

foreach ($children as $c) {
    $sql .= sprintf(
        "UPDATE `categories` c JOIN `categories` p ON p.`slug` = %s SET c.`parent_id` = p.`id` WHERE c.`slug` = %s;\n",
        q($pdo, $c['parent_slug']), q($pdo, $c['slug'])
    );
}

// ---- 3. Products onto their shelf -------------------------------------
$sql .= "\n-- ---------------------------------------------------------------------\n";
$sql .= "--  3. Each product onto its shelf (" . count($products) . "). A product the live\n";
$sql .= "--     site does not have is simply not matched, and nothing happens.\n";
$sql .= "-- ---------------------------------------------------------------------\n";

foreach ($products as $p) {
    $sql .= sprintf(
        "UPDATE `products` pr JOIN `categories` c ON c.`slug` = %s SET pr.`category_id` = c.`id` WHERE pr.`slug` = %s;\n",
        q($pdo, $p['category_slug']), q($pdo, $p['slug'])
    );
}

// ---- 4. Hide anything left over ---------------------------------------
$slugs = implode(', ', array_map(static fn($c) => q($pdo, $c['slug']), $categories));

$sql .= "\n-- ---------------------------------------------------------------------\n";
$sql .= "--  4. A category from an older arrangement that is not part of this tree\n";
$sql .= "--     is hidden rather than dropped, so nothing that points at it breaks.\n";
$sql .= "-- ---------------------------------------------------------------------\n";
$sql .= "UPDATE `categories` SET `is_active` = 0 WHERE `slug` NOT IN (\n  {$slugs}\n);\n";

file_put_contents($out, $sql);

printf("\n  Tack Rack Kenya — category tree migration\n  %s\n\n", str_repeat('=', 44));
printf("  departments   %d\n", count($departments));
printf("  categories    %d\n", count($categories));
printf("  products      %d\n\n", count($products));
printf("  %s  (%d KB)\n\n", str_replace(BASE_PATH . DIRECTORY_SEPARATOR, '', $out), (int) round(strlen($sql) / 1024));
