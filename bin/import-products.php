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
// Studio originals, one folder per shoot, searched in this order. The second
// folder also carries copies of the first shoot; those are byte-identical, so
// whichever folder a file is found in first makes no difference.
$srcDirs = [
    PUBLIC_PATH . '/assets/img/Products',
    PUBLIC_PATH . '/uploads/Product 2',
];

/** Full path to a studio original, or null if no shoot folder has it. */
function findSource(array $dirs, string $file): ?string
{
    foreach ($dirs as $dir) {
        if (is_file("{$dir}/{$file}")) {
            return "{$dir}/{$file}";
        }
    }

    return null;
}
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
//  1. The category tree
//
//  This mirrors Tack Rack's own stock sheets, which are organised into three
//  departments — HORSE, RIDER and YARD — each with its own sections. The
//  sheets photographed for us stop part way down the yard list, so the four
//  sections they do not reach (hoof care, feed supplements, first aid and
//  yard equipment) are ours, added so nothing is left without a home.
//
//  Products are assigned to these by slug in database/catalog.php. Meta
//  titles and descriptions come from database/seo-copy.php.
// ---------------------------------------------------------------------

// The shop calls the third department the yard, not the stable.
$pillarRenames = ['stable' => ['slug' => 'yard', 'name' => 'Yard']];

foreach ($pillarRenames as $from => $to) {
    $row = $db->one('SELECT id, slug FROM categories WHERE slug = :s', ['s' => $from]);

    if ($row === null) {
        continue;
    }

    if ($dryRun) {
        out("  pillar WOULD RENAME {$from} -> {$to['slug']}");
        continue;
    }

    // The old meta described a "stable" department, so it is replaced rather
    // than left to the fill-only pass below.
    $meta = $seoCopy['categories'][$to['slug']] ?? null;

    $db->run(
        'UPDATE categories
            SET slug = :slug, name = :name, meta_title = :mt, meta_desc = :md
          WHERE id = :id',
        [
            'slug' => $to['slug'],
            'name' => $to['name'],
            'mt'   => $meta['title'] ?? null,
            'md'   => $meta['desc'] ?? null,
            'id'   => $row['id'],
        ]
    );

    out("  pillar RENAMED     {$from} -> {$to['slug']}");
}

$tree = [
    // ---- Rider ----
    'clothing' => ['rider', 1, 'Clothing', 'Jodhpurs, Boots & Chaps',
        'Breeches and jodhpurs, jodhpur boots, half chaps and gaiters, hat silks and competition numbers.'],
    'safety-equipment' => ['rider', 2, 'Safety Equipment', 'Helmets & Body Protectors',
        'Riding hats, skull caps and BETA-certified body protectors — fitted in person, because kit that moves cannot do its job.'],
    'whips' => ['rider', 3, 'Whips', 'Schooling, Lunge & Short',
        'Dressage and schooling whips, lunge whips and short whips, balanced to carry without moving the hand.'],

    // ---- Horse ----
    'saddles' => ['horse', 1, 'Saddles', 'Fitted on the Horse',
        'Leather and synthetic saddles for every discipline, fitted on the horse by our Society of Master Saddlers qualified fitter.'],
    'bridles-reins' => ['horse', 2, 'Bridles & Reins', 'Leather & Webbed',
        'Leather snaffle bridles supplied with reins, plus webbed, rubber and leather reins and schooling draw reins.'],
    'bits-accessories' => ['horse', 3, 'Bits & Accessories', 'Snaffles, Gags & Guards',
        'Loose ring and jointed snaffles, lozenge and training bits, and the guards and keepers that go with them.'],
    'martingales-stirrups-leathers' => ['horse', 4, 'Martingales, Stirrups & Leathers', 'Irons, Treads & Straps',
        'Stirrup irons including safety and composite patterns, rubber treads, stirrup leathers and martingales.'],
    'girths' => ['horse', 5, 'Girths', 'Fleece, Elastic & Dressage',
        'Fleece lined, elastic, anti-chafe and short dressage girths, with leather buckle guards to protect the saddle flap.'],
    'numnahs-saddlepads' => ['horse', 6, 'Numnahs & Saddlepads', 'Shaped, Square & Blankets',
        'Shaped GP numnahs, dressage squares, non-slip and Prolite pads, and fleece blankets.'],
    'headcollars-lead-ropes' => ['horse', 7, 'Headcollars & Lead Ropes', 'Foal, Pony, Cob & Full',
        'Nylon and fleece-lined headcollars, lead ropes, lunge cavessons and lunge reins for the yard and the lorry.'],
    'horse-boots' => ['horse', 8, 'Horse Boots', 'Brushing, Overreach & Bandages',
        'Brushing and overreach boots, fetlock rings and temporary shoe boots, with bandages, leg pads and cohesive wrap.'],

    // ---- Yard ----
    'shampoo-skin-care' => ['yard', 1, 'Shampoo & Skin Care', 'Washing, Detangling & Sun',
        'Shampoos, detanglers, mane and tail lotions, soothing gels and sunscreen for the Kenyan sun.'],
    'fly-repellent' => ['yard', 2, 'Fly Repellent', 'Sprays, Masks & Traps',
        'Fly and midge repellent sprays, fine mesh fly masks, and outdoor fly traps and bait for the yard.'],
    'joint-muscle-care' => ['yard', 3, 'Joint & Muscle Care', 'Supplements, Gels & Clays',
        'Joint supplements and the gels, clays and creams that go on afterwards — devils claw, arnica, MSM and cooling clay.'],
    'digestive' => ['yard', 4, 'Digestive', 'Gut Balancers & Soothers',
        'Digestive balancers, soothers and yeast cultures for horses that need settling from the inside.'],
    'calming' => ['yard', 5, 'Calming', 'Focus & Temperament',
        'Powders, solutions and pastes to take the edge off a nervous or moody horse without dulling it.'],
    'feed-supplements' => ['yard', 6, 'Feed Supplements', 'Vitamins, Minerals & Electrolytes',
        'Electrolytes, vitamin and mineral premixes, biotin, garlic, limestone, salts and licks for the feed room.'],
    'hoof-care' => ['yard', 7, 'Hoof Care', 'Oils, Dressings & Farriery',
        'Hoof oils, dressings, moisturisers and repair compounds, treatment boots and poultices, and shoes and nails for the farrier.'],
    'first-aid' => ['yard', 8, 'First Aid', 'Wound & Skin Care',
        'Wound sprays, dressings and gels for the tack room first aid box.'],
    'grooming-equipment' => ['yard', 9, 'Grooming Equipment', 'Brushes, Combs & Boxes',
        'Body and dandy brushes, rubber and plastic curry combs, mane combs, sweat scrapers, plaiting bands and tack boxes.'],
    'leather-care' => ['yard', 10, 'Leather Care', 'Soaps, Dressings & Dubbin',
        'Saddle soaps, leather dressings and dubbin that keep tack alive in a dry, high-altitude climate.'],
    'yard-equipment' => ['yard', 11, 'Yard Equipment', 'Buckets, Haynets & Tubs',
        'Feed buckets and tubs, haynets and the everyday kit that keeps a yard running.'],
];

foreach ($tree as $slug => [$parentSlug, $sort, $name, $tagline, $desc]) {
    $parent = $db->one('SELECT id FROM categories WHERE slug = :s', ['s' => $parentSlug]);

    if ($parent === null) {
        out("  !! parent not found: {$parentSlug} (for {$slug})");
        continue;
    }

    $meta     = $seoCopy['categories'][$slug] ?? null;
    $existing = $db->one('SELECT id, parent_id, sort_order FROM categories WHERE slug = :s', ['s' => $slug]);

    if ($existing !== null) {
        // Keep the placing right without touching copy anyone may have edited.
        if ((int) $existing['parent_id'] !== (int) $parent['id'] || (int) $existing['sort_order'] !== $sort) {
            if ($dryRun) {
                out("  category WOULD MOVE {$slug}");
            } else {
                $db->run(
                    'UPDATE categories SET parent_id = :p, sort_order = :o WHERE id = :id',
                    ['p' => $parent['id'], 'o' => $sort, 'id' => $existing['id']]
                );
                out("  category MOVED     {$slug}");
            }
        } else {
            out("  category ok        {$slug}");
        }

        continue;
    }

    if ($dryRun) {
        out("  category WOULD ADD {$slug}");
        continue;
    }

    $db->insert('categories', [
        'parent_id'   => $parent['id'],
        'name'        => $name,
        'slug'        => $slug,
        'tagline'     => $tagline,
        'description' => $desc,
        'meta_title'  => $meta['title'] ?? null,
        'meta_desc'   => $meta['desc'] ?? null,
        'sort_order'  => $sort,
        'is_active'   => 1,
    ]);

    out("  category ADDED     {$slug}");
}

// ---------------------------------------------------------------------
//  1b. Meta titles and descriptions for the category pages
//
//  Fills in any category that has no meta of its own rather than overwriting
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
        $path = findSource($srcDirs, $file);

        if ($path === null) {
            $stats['missing'][] = "{$item['name']}: {$file}";
            continue;
        }
        $files[] = [$path, $file, $caption];
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

    foreach ($files as [$path, $file, $caption]) {
        $destName = sprintf('%s-%02d', $slug, $position + 1);
        $result   = processImage($path, "{$outDir}/{$destName}");

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
//  6. Retire sections the catalogue has outgrown
//
//  Regrouping the catalogue leaves the old sections behind, empty. This drops
//  any sub-category the tree above no longer defines, but only when nothing
//  is filed under it — a category with products is never touched, so a
//  section added by hand in the admin survives.
// ---------------------------------------------------------------------

$obsolete = $db->all(
    'SELECT c.id, c.slug, (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id) AS n
       FROM categories c
      WHERE c.parent_id IS NOT NULL'
);

foreach ($obsolete as $row) {
    if (isset($tree[$row['slug']])) {
        continue;
    }

    if ((int) $row['n'] > 0) {
        out("  category KEPT      {$row['slug']} ({$row['n']} product(s) still in it)");
        continue;
    }

    if ($dryRun) {
        out("  category WOULD DROP {$row['slug']}");
        continue;
    }

    $db->run('DELETE FROM categories WHERE id = :id', ['id' => $row['id']]);
    out("  category DROPPED   {$row['slug']}");
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
