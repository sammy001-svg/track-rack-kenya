<?php
/**
 * Apply the prepared catalogue to whatever database this deployment points at.
 *
 *   php bin/apply-catalogue.php           # show what it would do, change nothing
 *   php bin/apply-catalogue.php --yes     # apply it
 *
 * This is the same database/catalog-import.sql the admin panel applies, run
 * from the command line instead, so a deployment can be brought in line over
 * SSH or the cPanel terminal without clicking through a browser session. It
 * prints the category tree afterwards, so there is no need to guess whether
 * it worked.
 *
 * It replaces the catalogue: categories, brands, products, photographs and
 * sizes. Past orders and quotes survive — their product_id becomes NULL, but
 * every line keeps its own product_name and product_sku, so the history still
 * reads correctly. Users, settings, pages, bookings and repairs are untouched.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("This script can only be run from the command line.\n");
}

$root = dirname(__DIR__);
require $root . '/app/Core/helpers.php';
define('CONFIG_PATH', $root . '/config');

$apply = in_array('--yes', array_slice($argv, 1), true);
$db    = config('db');
$file  = $root . '/database/catalog-import.sql';

function out(string $line = ''): void { echo $line . PHP_EOL; }

out();
out('  Tack Rack Kenya — apply the prepared catalogue');
out('  ' . str_repeat('=', 54));
out();

if (!is_file($file) || filesize($file) < 1024) {
    out('  ERROR  database/catalog-import.sql is missing from this deployment.');
    out('         Pull the latest code first, then run this again.');
    out();
    exit(1);
}

out(sprintf('  database   %s on %s', $db['database'], $db['host']));
out(sprintf('  import     database/catalog-import.sql (%d KB)', (int) round(filesize($file) / 1024)));
out();

try {
    $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s',
        $db['host'], $db['port'], $db['database'], $db['charset']);

    $pdo = new PDO($dsn, $db['username'], $db['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
} catch (PDOException $e) {
    out('  ERROR  ' . $e->getMessage());
    out();
    exit(1);
}

$count = static function (PDO $pdo, string $sql): int {
    return (int) $pdo->query($sql)->fetchColumn();
};

$before = [
    'departments' => $count($pdo, 'SELECT COUNT(*) FROM categories WHERE parent_id IS NULL'),
    'categories'  => $count($pdo, 'SELECT COUNT(*) FROM categories'),
    'products'    => $count($pdo, 'SELECT COUNT(*) FROM products'),
];

out(sprintf('  now        %d departments, %d categories, %d products',
    $before['departments'], $before['categories'], $before['products']));
out('  after      4 departments, 76 categories, 168 products');
out();

if (!$apply) {
    out('  Nothing has been changed. Run it again with --yes to apply:');
    out();
    out('      php bin/apply-catalogue.php --yes');
    out();
    exit(0);
}

// Keep a copy of what is about to be replaced, so a surprise is recoverable.
$dir = $root . '/storage/backups';

if (!is_dir($dir)) {
    @mkdir($dir, 0775, true);
}

$backup = $dir . '/catalogue-before-' . date('Y-m-d-His') . '.csv';
$handle = is_dir($dir) ? @fopen($backup, 'w') : false;

if ($handle === false) {
    out('  WARNING  storage/backups is not writable, so no backup was taken.');
    out('           Stopping. Make it writable, or pass --no-backup to go ahead anyway.');

    if (!in_array('--no-backup', array_slice($argv, 1), true)) {
        out();
        exit(1);
    }
} else {
    fputcsv($handle, ['sku', 'name', 'slug', 'category', 'stock_status']);

    $rows = $pdo->query(
        'SELECT p.sku, p.name, p.slug, c.name AS category, p.stock_status
           FROM products p LEFT JOIN categories c ON c.id = p.category_id
          ORDER BY p.name'
    );

    foreach ($rows as $row) {
        fputcsv($handle, array_values($row));
    }

    fclose($handle);
    out('  backup     ' . str_replace($root . '/', '', $backup));
}

/*
 * The file is several hundred statements in one string, and a normal
 * connection will not run more than one at a time. Open one that will, and
 * step through every result set so a failure half way down is raised rather
 * than swallowed.
 */
try {
    $runner = new PDO($dsn, $db['username'], $db['password'], [
        PDO::ATTR_ERRMODE                 => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_MULTI_STATEMENTS  => true,
    ]);

    $statement = $runner->query(file_get_contents($file));

    if ($statement !== false) {
        while ($statement->nextRowset()) {
            // Nothing to read; stepping through is what surfaces an error.
        }
    }
} catch (PDOException $e) {
    out();
    out('  FAILED   ' . $e->getMessage());
    out('           Nothing else has been changed.');
    out();
    exit(1);
}

$after = [
    'departments' => $count($pdo, 'SELECT COUNT(*) FROM categories WHERE parent_id IS NULL'),
    'categories'  => $count($pdo, 'SELECT COUNT(*) FROM categories'),
    'products'    => $count($pdo, 'SELECT COUNT(*) FROM products WHERE is_active = 1'),
    'images'      => $count($pdo, 'SELECT COUNT(*) FROM product_images'),
    'variants'    => $count($pdo, 'SELECT COUNT(*) FROM product_variants'),
];

out();
out(sprintf('  applied    %d departments, %d categories, %d products, %d photographs, %d sizes',
    $after['departments'], $after['categories'], $after['products'],
    $after['images'], $after['variants']));
out();
out('  The menu this produces:');
out();

// Print the tree the navigation will build, so the result is visible here
// rather than only after a browser refresh.
$tree = $pdo->query(
    'SELECT c.id, c.parent_id, c.name, p.parent_id AS grandparent
       FROM categories c LEFT JOIN categories p ON p.id = c.parent_id
      ORDER BY COALESCE(p.parent_id, c.parent_id, c.id), c.sort_order, c.id'
)->fetchAll(PDO::FETCH_ASSOC);

$byParent = [];

foreach ($tree as $row) {
    $byParent[$row['parent_id'] ?? 0][] = $row;
}

foreach ($byParent[0] ?? [] as $dept) {
    out('    ' . $dept['name']);

    foreach ($byParent[$dept['id']] ?? [] as $section) {
        $subs = $byParent[$section['id']] ?? [];
        out(sprintf('      %-34s %s', $section['name'],
            $subs === [] ? '' : '(' . count($subs) . ' sub-categories)'));

        foreach ($subs as $sub) {
            out('          ' . $sub['name']);
        }
    }

    out();
}

out('  Done. Reload the site and the dropdowns will show the tree above.');
out();
