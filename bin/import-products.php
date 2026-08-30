<?php
/**
 * Import the real catalogue defined in database/catalog.php.
 *
 *   php bin/import-products.php            # process images and import
 *   php bin/import-products.php --dry-run  # report only, change nothing
 *   php bin/import-products.php --keep     # keep existing products
 *
 * Source photographs are 6000x4000 originals; each is resized to a web-sized
 * JPEG plus a WebP sibling and written to public/uploads/products/.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("Command line only.\n");
}

ini_set('memory_limit', '1024M');
set_time_limit(0);

require dirname(__DIR__) . '/app/bootstrap.php';

use App\Core\Database;

$options = array_slice($argv, 1);
$dryRun  = in_array('--dry-run', $options, true);
$keep    = in_array('--keep', $options, true);

$db      = Database::instance();
$catalog = require BASE_PATH . '/database/catalog.php';
$seoCopy = require BASE_PATH . '/database/seo-copy.php';
$srcDir  = PUBLIC_PATH . '/assets/img/Products';
$outDir  = PUBLIC_PATH . '/uploads/products';

if (!is_dir($outDir) && !mkdir($outDir, 0775, true) && !is_dir($outDir)) {
    exit("Could not create {$outDir}\n");
}

const LONG_EDGE = 1400;   // display is ~900px; 1400 covers retina
const JPEG_Q    = 82;
const WEBP_Q    = 78;

function out(string $line = ''): void { echo $line . PHP_EOL; }

out();
out('  Tack Rack — catalogue import' . ($dryRun ? '  (DRY RUN)' : ''));
out('  ' . str_repeat('=', 56));
out();

// ---------------------------------------------------------------------
//  1. Make sure every category used by the catalogue exists
// ---------------------------------------------------------------------
$needed = [
    'helmets-head-protection' => [
        'parent'  => 'rider',
        'name'    => 'Helmets & Head Protection',
        'tagline' => 'Hats, Skull Caps & Silks',
        'desc'    => 'Riding hats, skull caps and silks — fitted properly, because a hat that moves cannot do its job.',
        'meta_title' => 'Riding Hats, Helmets & Skull Caps',
        'meta_desc'  => 'Riding helmets, velvet show hats, skull caps and hat silks, fitted in person at Tack Rack, Ngong Road, Nairobi.',
        'sort'    => 5,
    ],
];

foreach ($needed as $slug => $spec) {
    $exists = $db->one('SELECT id FROM categories WHERE slug = :s', ['s' => $slug]);

    if ($exists !== null) {
        out("  category ok        {$slug}");
        continue;
    }

    if ($dryRun) {
        out("  category WOULD ADD {$slug}");
        continue;
    }

    $parent = $db->one('SELECT id FROM categories WHERE slug = :s', ['s' => $spec['parent']]);

    $db->insert('categories', [
        'parent_id'   => $parent['id'] ?? null,
        'name'        => $spec['name'],
        'slug'        => $slug,
        'tagline'     => $spec['tagline'],
        'description' => $spec['desc'],
        'meta_title'  => $spec['meta_title'],
        'meta_desc'   => $spec['meta_desc'],
        'sort_order'  => $spec['sort'],
        'is_active'   => 1,
    ]);

    out("  category ADDED     {$slug}");
}

// ---------------------------------------------------------------------
//  1b. Meta titles and descriptions for the category pages
//
//  Categories are not rebuilt on every import the way products are, so this
//  fills in the ones that have no meta of its own rather than overwriting
//  copy someone has since edited in the admin console.
// ---------------------------------------------------------------------

foreach ($seoCopy['categories'] as $slug => $meta) {
    $category = $db->one('SELECT id, meta_title, meta_desc FROM categories WHERE slug = :s', ['s' => $slug]);

    if ($category === null) {
        out("  category meta SKIPPED {$slug} (no such category)");
        continue;
    }

    if (($category['meta_title'] ?? '') !== '' && ($category['meta_desc'] ?? '') !== '') {
        continue;
    }

    if ($dryRun) {
        out("  category meta WOULD SET {$slug}");
        continue;
    }

    $db->run(
        'UPDATE categories SET meta_title = :t, meta_desc = :d WHERE id = :id',
        ['t' => $meta['title'], 'd' => $meta['desc'], 'id' => $category['id']]
    );

    out("  category meta SET  {$slug}");
}

// ---------------------------------------------------------------------
//  2. Brands actually named on the products
// ---------------------------------------------------------------------
$brandNames = [];
foreach ($catalog as $item) {
    if (!empty($item['brand'])) { $brandNames[$item['brand']] = true; }
}

$brandIds = [];
$sort = 1;

foreach (array_keys($brandNames) as $name) {
    $row = $db->one('SELECT id FROM brands WHERE name = :n', ['n' => $name]);

    if ($row !== null) {
        $brandIds[$name] = (int) $row['id'];
        continue;
    }

    if ($dryRun) { out("  brand WOULD ADD    {$name}"); continue; }

    $brandIds[$name] = $db->insert('brands', [
        'name'       => $name,
        'slug'       => slugify($name),
        'sort_order' => $sort++,
        'is_active'  => 1,
    ]);

    out("  brand ADDED        {$name}");
}

// ---------------------------------------------------------------------
//  3. Clear the demo catalogue
// ---------------------------------------------------------------------
if (!$keep) {
    $existing = (int) $db->value('SELECT COUNT(*) FROM products');

    if ($dryRun) {
        out("  WOULD REMOVE       {$existing} existing product(s)");
    } elseif ($existing > 0) {
        // product_images and product_variants cascade on delete.
        $db->run('DELETE FROM products');
        out("  removed            {$existing} existing product(s)");
    }
}

out();

// ---------------------------------------------------------------------
//  4. Image processing
// ---------------------------------------------------------------------
function processImage(string $srcPath, string $destBase): ?array
{
    $info = @getimagesize($srcPath);
    if ($info === false) { return null; }

    $img = $info['mime'] === 'image/png'
        ? @imagecreatefrompng($srcPath)
        : @imagecreatefromjpeg($srcPath);

    if ($img === false) { return null; }

    $w = imagesx($img);
    $h = imagesy($img);
    $scale = min(1.0, LONG_EDGE / max($w, $h));
    $nw = max(1, (int) round($w * $scale));
    $nh = max(1, (int) round($h * $scale));

    $dst = imagecreatetruecolor($nw, $nh);
    // Product shots are on white; flatten any transparency onto white.
    imagefilledrectangle($dst, 0, 0, $nw, $nh, imagecolorallocate($dst, 255, 255, 255));
    imagealphablending($dst, true);
    imagecopyresampled($dst, $img, 0, 0, 0, 0, $nw, $nh, $w, $h);

    imagejpeg($dst, $destBase . '.jpg', JPEG_Q);
    if (function_exists('imagewebp')) {
        imagewebp($dst, $destBase . '.webp', WEBP_Q);
    }

    imagedestroy($img);
    imagedestroy($dst);

    clearstatcache();
    return ['w' => $nw, 'h' => $nh, 'bytes' => filesize($destBase . '.jpg')];
}

// ---------------------------------------------------------------------
//  5. Import
// ---------------------------------------------------------------------
$stats = ['products' => 0, 'images' => 0, 'missing' => [], 'bytes' => 0];

foreach ($catalog as $item) {
    $category = $db->one('SELECT id FROM categories WHERE slug = :s', ['s' => $item['category']]);

    if ($category === null) {
        out("  !! category not found: {$item['category']} (for {$item['name']})");
        continue;
    }

    // Confirm every referenced photograph is present before creating anything.
    $files = [];
    foreach ($item['images'] as [$file, $caption]) {
        if (!is_file("{$srcDir}/{$file}")) {
            $stats['missing'][] = "{$item['name']}: {$file}";
            continue;
        }
        $files[] = [$file, $caption];
    }

    if ($files === []) {
        out("  !! no usable images for {$item['name']} — skipped");
        continue;
    }

    if ($dryRun) {
        out(sprintf('  %-52s %2d image(s)', $item['name'], count($files)));
        $stats['products']++;
        $stats['images'] += count($files);
        continue;
    }

    $slug = (new App\Models\Product())->uniqueSlug($item['name']);

    $productId = $db->insert('products', [
        'category_id'    => (int) $category['id'],
        'brand_id'       => isset($item['brand']) ? ($brandIds[$item['brand']] ?? null) : null,
        'name'           => $item['name'],
        'slug'           => $slug,
        'sku'            => $item['sku'] ?? null,
        'short_desc'     => $item['short'],
        'description'    => $item['description'],
        'specifications' => $item['specs'] ?? null,
        'sizing_guide'   => $item['sizing'] ?? null,
        'price'          => null,
        'price_visible'  => 0,
        'buyable'        => 0,
        'stock_status'   => $item['stock'] ?? 'in_stock',
        'is_featured'    => !empty($item['featured']) ? 1 : 0,
        'is_new'         => 0,
        'is_active'      => 1,
        'sort_order'     => 0,
        // Hand-written where we have it. Left null otherwise, which lets
        // ProductController fall back to composing one from the product name.
        'meta_title'     => $seoCopy['products'][$item['name']]['title'] ?? null,
        'meta_desc'      => $seoCopy['products'][$item['name']]['desc'] ?? null,
    ]);

    $position = 0;

    foreach ($files as [$file, $caption]) {
        $destName = sprintf('%s-%02d', $slug, $position + 1);
        $result   = processImage("{$srcDir}/{$file}", "{$outDir}/{$destName}");

        if ($result === null) {
            $stats['missing'][] = "{$item['name']}: {$file} (could not process)";
            continue;
        }

        $db->insert('product_images', [
            'product_id' => $productId,
            'path'       => '/uploads/products/' . $destName . '.jpg',
            'alt'        => $caption,
            'is_primary' => $position === 0 ? 1 : 0,
            'sort_order' => $position,
        ]);

        $stats['images']++;
        $stats['bytes'] += $result['bytes'];
        $position++;
    }

    $stats['products']++;
    out(sprintf('  %-52s %2d image(s)', $item['name'], $position));
}

// ---------------------------------------------------------------------
out();
out('  ' . str_repeat('-', 56));
out(sprintf('  products   %d', $stats['products']));
out(sprintf('  images     %d  (%s MB written)', $stats['images'], number_format($stats['bytes'] / 1048576, 1)));

if ($stats['missing'] !== []) {
    out();
    out('  MISSING OR UNREADABLE:');
    foreach ($stats['missing'] as $m) { out('    ' . $m); }
}

out('  ' . str_repeat('-', 56));
out();
