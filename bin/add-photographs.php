<?php
/**
 * Add photographs to products that already exist.
 *
 *   php bin/add-photographs.php            # say what it would do
 *   php bin/add-photographs.php --yes      # do it
 *
 * Takes the loose photographs that arrive between shoots — a supplier's
 * phone picture of something we had never photographed — and puts them
 * through exactly the treatment the catalogue import gives a studio shot:
 * the backdrop trimmed away, the long edge brought down to 1400, and a JPEG
 * and a WebP written side by side.
 *
 * Needs GD, which the winget PHP does not load. Run it with XAMPP's:
 *
 *   /c/xampp/php/php.exe bin/add-photographs.php --yes
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("This script can only be run from the command line.\n");
}

require dirname(__DIR__) . '/app/bootstrap.php';

use App\Core\Database;

const LONG_EDGE = 1400;
const JPEG_Q    = 82;
const WEBP_Q    = 78;

$apply  = in_array('--yes', array_slice($argv, 1), true);
$source = BASE_PATH . '/public/assets/img/New Products';
$outDir = PUBLIC_PATH . '/uploads/products';

/**
 * What each photograph is, and where it belongs.
 *
 *   file    — the name as it arrived
 *   product — the slug of the product it belongs to
 *   alt     — what a reader who cannot see it should be told
 *   lead    — true to make it the product's first image
 */
$manifest = [
    ['file' => 'WhatsApp Image 2026-10-16 at 12.02.30 PM.jpeg',
     'product' => 'coligone-calm-down-paste',
     'alt' => 'The 50g syringe of Coligone Calm Down paste',
     'lead' => true],

    ['file' => 'WhatsApp Image 2026-10-16 at 12.02.31 PM.jpeg',
     'product' => 'coligone-pretravel-paste',
     'alt' => 'The 50g syringe of Coligone PreTravel paste',
     'lead' => true],

    ['file' => 'WhatsApp Image 2026-10-16 at 12.03.24 PM.jpeg',
     'product' => 'cotton-pimple-grip-riding-gloves',
     'alt' => 'A pair of black cotton riding gloves, palms up, showing the pimple grip',
     'lead' => true],

    ['file' => 'WhatsApp Image 2026-10-16 at 12.03.25 PM.jpeg',
     'product' => 'hook-polo-grip-pro-gloves',
     'alt' => 'The Hook Polo Grip Pro glove, showing the leather palm and the printed back',
     'lead' => true],

    ['file' => 'WhatsApp Image 2026-10-16 at 12.04.46 PM.jpeg',
     'product' => 'red-gorilla-feed-tub',
     'alt' => 'The flexible tub in black, with both handles',
     'lead' => true],

    ['file' => 'WhatsApp Image 2026-10-16 at 12.06.15 PM.jpeg',
     'product' => 'wintec-500-all-purpose-saddle',
     'alt' => 'The Wintec 500 flap and girth billets, with the 42cm seat marker',
     'lead' => true],

    ['file' => 'WhatsApp Image 2026-10-16 at 12.06.16 PM.jpeg',
     'product' => 'wintec-2000-all-purpose-saddle',
     'alt' => 'The Wintec 2000 flap badge and girth billets from beneath',
     'lead' => false],

    ['file' => 'WhatsApp Image 2026-10-16 at 12.06.17 PM.jpeg',
     'product' => 'arena-leather-general-purpose-saddle',
     'alt' => 'Full side profile of the Arena leather general purpose saddle',
     'lead' => true],

    ['file' => 'equimins-biotin-15.jpg',
     'product' => 'equimins-biotin-15',
     'alt' => 'The 3kg tub of Equimins Biotin 15, showing the hoof label',
     'lead' => true],

    ['file' => 'stirrup-leathers.png',
     'product' => 'stirrup-leathers',
     'alt' => 'A pair of black leather stirrup leathers, coiled, with the buckles showing',
     'lead' => true],

    ['file' => 'WhatsApp Image 2026-10-16 at 12.06.18 PM.jpeg',
     'product' => 'arena-leather-general-purpose-saddle',
     'alt' => 'The Arena Saddles stamp and girth billets beneath the flap',
     'lead' => false],
];

function out(string $line = ''): void { echo $line . PHP_EOL; }

/** Trim the backdrop away, exactly as the catalogue import does. */
function productBox($img, int $tolerance = 10): ?array
{
    $w = imagesx($img);
    $h = imagesy($img);

    $bg = [0, 0, 0];

    foreach ([[2, 2], [$w - 3, 2], [2, $h - 3], [$w - 3, $h - 3]] as [$cx, $cy]) {
        $c = imagecolorat($img, $cx, $cy);
        $bg[0] += ($c >> 16) & 0xFF;
        $bg[1] += ($c >> 8) & 0xFF;
        $bg[2] += $c & 0xFF;
    }

    $bg = array_map(static fn ($v) => (int) round($v / 4), $bg);

    $step = max(1, (int) floor(min($w, $h) / 500));
    $minX = $w; $minY = $h; $maxX = -1; $maxY = -1;

    for ($y = 0; $y < $h; $y += $step) {
        for ($x = 0; $x < $w; $x += $step) {
            $c = imagecolorat($img, $x, $y);

            if (abs((($c >> 16) & 0xFF) - $bg[0]) <= $tolerance
                && abs((($c >> 8) & 0xFF) - $bg[1]) <= $tolerance
                && abs(($c & 0xFF) - $bg[2]) <= $tolerance) {
                continue;
            }

            if ($x < $minX) { $minX = $x; }
            if ($x > $maxX) { $maxX = $x; }
            if ($y < $minY) { $minY = $y; }
            if ($y > $maxY) { $maxY = $y; }
        }
    }

    if ($maxX < 0) {
        return null;
    }

    $bw  = $maxX - $minX + 1;
    $bh  = $maxY - $minY + 1;
    $pad = (int) round(max($bw, $bh) * 0.02);

    $minX = max(0, $minX - $pad);
    $minY = max(0, $minY - $pad);
    $bw   = min($w - $minX, $bw + 2 * $pad);
    $bh   = min($h - $minY, $bh + 2 * $pad);

    $area = ($bw * $bh) / ($w * $h);

    // A phone picture is rarely shot on a clean backdrop, so a crop that
    // would take nearly everything or nearly nothing is not to be trusted.
    return ($area > 0.97 || $area < 0.02) ? null : [$minX, $minY, $bw, $bh];
}

function processImage(string $srcPath, string $destBase): ?array
{
    $info = @getimagesize($srcPath);

    if ($info === false) {
        return null;
    }

    $img = match ($info['mime']) {
        'image/png'  => @imagecreatefrompng($srcPath),
        'image/webp' => @imagecreatefromwebp($srcPath),
        default      => @imagecreatefromjpeg($srcPath),
    };

    if ($img === false) {
        return null;
    }

    [$sx, $sy, $w, $h] = productBox($img) ?? [0, 0, imagesx($img), imagesy($img)];

    $scale = min(1.0, LONG_EDGE / max($w, $h));
    $nw = max(1, (int) round($w * $scale));
    $nh = max(1, (int) round($h * $scale));

    $dst = imagecreatetruecolor($nw, $nh);
    imagefilledrectangle($dst, 0, 0, $nw, $nh, imagecolorallocate($dst, 255, 255, 255));
    imagealphablending($dst, true);
    imagecopyresampled($dst, $img, 0, 0, $sx, $sy, $nw, $nh, $w, $h);

    imagejpeg($dst, $destBase . '.jpg', JPEG_Q);

    if (function_exists('imagewebp')) {
        imagewebp($dst, $destBase . '.webp', WEBP_Q);
    }

    imagedestroy($img);
    imagedestroy($dst);
    clearstatcache();

    return ['w' => $nw, 'h' => $nh, 'bytes' => filesize($destBase . '.jpg')];
}

out();
out('  Tack Rack Kenya — add photographs');
out('  ' . str_repeat('=', 54));
out();

if (!function_exists('imagecreatefromjpeg')) {
    out('  ERROR  GD is not loaded in this PHP.');
    out('         Run it with XAMPP\'s: /c/xampp/php/php.exe bin/add-photographs.php');
    out();
    exit(1);
}

if (!is_dir($outDir) && !mkdir($outDir, 0775, true) && !is_dir($outDir)) {
    out("  ERROR  could not create {$outDir}");
    exit(1);
}

$db      = Database::instance();
$missing = [];
$done    = 0;
// How many we have already allocated to a product in this run, so a second
// photograph for the same product does not reuse the first one's number.
$added   = [];

foreach ($manifest as $row) {
    $src = $source . '/' . $row['file'];

    if (!is_file($src)) {
        out('  !! not found: ' . $row['file']);
        continue;
    }

    $product = $db->all('SELECT id, name FROM products WHERE slug = :s', ['s' => $row['product']])[0] ?? null;

    if ($product === null) {
        $missing[$row['product']][] = $row;
        continue;
    }

    $id   = (int) $product['id'];
    $have = $db->all('SELECT id, path, alt FROM product_images WHERE product_id = :i ORDER BY sort_order', ['i' => $id]);

    /*
     * Already on the product? Leave it alone. Without this, running the
     * script a second time files every photograph again under the next
     * free number and hands the lead to the copy — which is exactly what
     * it did once. The caption identifies the photograph: two frames of
     * the same product never carry the same one.
     */
    foreach ($have as $img) {
        if ($img['alt'] === $row['alt']) {
            out(sprintf('  already on  %-42s %s', $product['name'], basename($img['path'])));
            continue 2;
        }
    }

    // Carry on from whatever numbering the product already uses.
    $next = 1;

    foreach ($have as $img) {
        if (preg_match('/-(\d+)\.[a-z]+$/', $img['path'], $m)) {
            $next = max($next, (int) $m[1] + 1);
        }
    }

    $next += $added[$row['product']] ?? 0;
    $added[$row['product']] = ($added[$row['product']] ?? 0) + 1;

    $base = sprintf('%s-%02d', $row['product'], $next);
    $dest = $outDir . '/' . $base;

    if (!$apply) {
        out(sprintf('  would add  %-44s -> %s.jpg%s', $product['name'], $base, $row['lead'] ? '  (as the first image)' : ''));
        $done++;
        continue;
    }

    $made = processImage($src, $dest);

    if ($made === null) {
        out('  !! could not read ' . $row['file']);
        continue;
    }

    if ($row['lead']) {
        // Everything already on the product steps back one place.
        $db->run('UPDATE product_images SET sort_order = sort_order + 1, is_primary = 0 WHERE product_id = :i', ['i' => $id]);
    }

    $db->run(
        'INSERT INTO product_images (product_id, path, alt, sort_order, is_primary)
         VALUES (:p, :path, :alt, :sort, :primary)',
        [
            'p'       => $id,
            'path'    => '/uploads/products/' . $base . '.jpg',
            'alt'     => $row['alt'],
            'sort'    => $row['lead'] ? 0 : count($have) + (($added[$row['product']] ?? 1) - 1),
            'primary' => $row['lead'] ? 1 : 0,
        ]
    );

    out(sprintf('  %-44s %s.jpg  %dx%d  %dKB%s',
        $product['name'], $base, $made['w'], $made['h'],
        (int) round($made['bytes'] / 1024),
        $row['lead'] ? '  (now the first image)' : ''));

    $done++;
}

out();
out(sprintf('  %d photograph(s) %s.', $done, $apply ? 'added' : 'ready to add'));

if ($missing !== []) {
    out();
    out('  These products do not exist yet, so their photographs were skipped:');

    foreach ($missing as $slug => $rows) {
        out(sprintf('    %-44s %d photograph(s)', $slug, count($rows)));
    }
}

out();
