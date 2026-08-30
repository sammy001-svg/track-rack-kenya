<?php
/**
 * Build every logo and icon derivative from a single source file.
 *
 *   php bin/make-logo.php [path/to/source.png]
 *
 * Defaults to public/assets/img/logo-source.png. Drop a higher-resolution
 * logo in at that path, re-run this, and the header, footer, favicons and
 * structured data all pick it up — nothing else needs editing.
 *
 * The source may sit on a white background; it is knocked out to transparency
 * automatically. Colour in the artwork (the gold wordline) is preserved.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("Command line only.\n");
}

if (!extension_loaded('gd')) {
    fwrite(STDERR, "The gd extension is required.\n");
    exit(1);
}

$root   = dirname(__DIR__);
$imgDir = $root . '/public/assets/img';
$source = $argv[1] ?? ($imgDir . '/logo-source.png');

if (!is_file($source)) {
    fwrite(STDERR, "Source logo not found: {$source}\n");
    fwrite(STDERR, "Place your logo there (PNG or JPG) and run again.\n");
    exit(1);
}

/** Bone, used for the version that sits on dark backgrounds. */
const LIGHT = [247, 244, 239];

function loadImage(string $path)
{
    $raw = file_get_contents($path);
    $img = @imagecreatefromstring($raw);

    if ($img === false) {
        fwrite(STDERR, "Could not read {$path} as an image.\n");
        exit(1);
    }

    imagepalettetotruecolor($img);
    return $img;
}

/**
 * Knock a white background out to transparency.
 *
 * alpha = 255 - min(r,g,b) turns pure white fully transparent, keeps solid
 * ink fully opaque, and feathers anti-aliased edges — while leaving saturated
 * colours (the gold) close to opaque so they survive.
 */
function knockOutWhite($src)
{
    $w = imagesx($src);
    $h = imagesy($src);

    $out = imagecreatetruecolor($w, $h);
    imagealphablending($out, false);
    imagesavealpha($out, true);
    imagefilledrectangle($out, 0, 0, $w, $h, imagecolorallocatealpha($out, 0, 0, 0, 127));

    for ($y = 0; $y < $h; $y++) {
        for ($x = 0; $x < $w; $x++) {
            $rgba = imagecolorat($src, $x, $y);
            $r = ($rgba >> 16) & 0xFF;
            $g = ($rgba >> 8) & 0xFF;
            $b = $rgba & 0xFF;
            $a = ($rgba >> 24) & 0x7F;

            // Respect transparency the source already has.
            if ($a >= 127) {
                continue;
            }

            $ink = 255 - min($r, $g, $b);       // 0 on white, 255 on black
            if ($ink <= 6) {
                continue;                        // background
            }

            $alpha = (int) round(127 - ($ink / 255) * 127);
            imagesetpixel($out, $x, $y, imagecolorallocatealpha($out, $r, $g, $b, max(0, min(127, $alpha))));
        }
    }

    return $out;
}

/** Bounding box of everything that is not fully transparent. */
function contentBox($img): array
{
    $w = imagesx($img);
    $h = imagesy($img);
    $minX = $w; $minY = $h; $maxX = -1; $maxY = -1;

    for ($y = 0; $y < $h; $y++) {
        for ($x = 0; $x < $w; $x++) {
            if ((((imagecolorat($img, $x, $y)) >> 24) & 0x7F) < 120) {
                if ($x < $minX) $minX = $x;
                if ($x > $maxX) $maxX = $x;
                if ($y < $minY) $minY = $y;
                if ($y > $maxY) $maxY = $y;
            }
        }
    }

    return $maxX < 0 ? [0, 0, $w, $h] : [$minX, $minY, $maxX - $minX + 1, $maxY - $minY + 1];
}

function crop($img, array $box)
{
    [$x, $y, $w, $h] = $box;

    $out = imagecreatetruecolor($w, $h);
    imagealphablending($out, false);
    imagesavealpha($out, true);
    imagefilledrectangle($out, 0, 0, $w, $h, imagecolorallocatealpha($out, 0, 0, 0, 127));
    imagecopy($out, $img, 0, 0, $x, $y, $w, $h);

    return $out;
}

/** Recolour near-greyscale ink to bone, leaving saturated colour untouched. */
function toLight($src)
{
    $w = imagesx($src);
    $h = imagesy($src);

    $out = imagecreatetruecolor($w, $h);
    imagealphablending($out, false);
    imagesavealpha($out, true);
    imagefilledrectangle($out, 0, 0, $w, $h, imagecolorallocatealpha($out, 0, 0, 0, 127));

    for ($y = 0; $y < $h; $y++) {
        for ($x = 0; $x < $w; $x++) {
            $rgba = imagecolorat($src, $x, $y);
            $a = ($rgba >> 24) & 0x7F;

            if ($a >= 127) {
                continue;
            }

            $r = ($rgba >> 16) & 0xFF;
            $g = ($rgba >> 8) & 0xFF;
            $b = $rgba & 0xFF;

            $saturation = max($r, $g, $b) - min($r, $g, $b);

            if ($saturation < 45) {
                [$r, $g, $b] = LIGHT;            // ink -> bone
            }

            imagesetpixel($out, $x, $y, imagecolorallocatealpha($out, $r, $g, $b, $a));
        }
    }

    return $out;
}

/** Scale into a square canvas, preserving aspect and adding even padding. */
function square($src, int $size, float $inset = 0.94)
{
    $w = imagesx($src);
    $h = imagesy($src);

    $out = imagecreatetruecolor($size, $size);
    imagealphablending($out, false);
    imagesavealpha($out, true);
    imagefilledrectangle($out, 0, 0, $size, $size, imagecolorallocatealpha($out, 0, 0, 0, 127));
    imagealphablending($out, true);

    $scale = min(($size * $inset) / $w, ($size * $inset) / $h);
    $dw = max(1, (int) round($w * $scale));
    $dh = max(1, (int) round($h * $scale));

    imagecopyresampled($out, $src, (int) (($size - $dw) / 2), (int) (($size - $dh) / 2),
        0, 0, $dw, $dh, $w, $h);

    imagesavealpha($out, true);
    return $out;
}

/**
 * Push partially-transparent pixels towards opaque, so hairline strokes still
 * register after a heavy downscale. Gamma below 1 strengthens.
 */
function strengthen($img, float $gamma)
{
    $w = imagesx($img);
    $h = imagesy($img);

    imagealphablending($img, false);

    for ($y = 0; $y < $h; $y++) {
        for ($x = 0; $x < $w; $x++) {
            $rgba = imagecolorat($img, $x, $y);
            $a = ($rgba >> 24) & 0x7F;

            if ($a === 0 || $a >= 127) {
                continue;
            }

            $opacity = (127 - $a) / 127;
            $a = (int) round(127 - (min(1.0, $opacity ** $gamma) * 127));

            imagesetpixel($img, $x, $y, imagecolorallocatealpha(
                $img, ($rgba >> 16) & 0xFF, ($rgba >> 8) & 0xFF, $rgba & 0xFF, max(0, $a)
            ));
        }
    }

    imagealphablending($img, true);
    imagesavealpha($img, true);

    return $img;
}

function resizeToWidth($src, int $width)
{
    $w = imagesx($src);
    $h = imagesy($src);
    $height = max(1, (int) round($h * ($width / $w)));

    $out = imagecreatetruecolor($width, $height);
    imagealphablending($out, false);
    imagesavealpha($out, true);
    imagefilledrectangle($out, 0, 0, $width, $height, imagecolorallocatealpha($out, 0, 0, 0, 127));
    imagealphablending($out, true);
    imagecopyresampled($out, $src, 0, 0, 0, 0, $width, $height, $w, $h);
    imagesavealpha($out, true);

    return $out;
}

function savePng($img, string $path): void
{
    imagesavealpha($img, true);
    imagepng($img, $path, 9);
    clearstatcache(true, $path);
}

/** Minimal multi-size .ico writer — no dependency needed. */
function writeIco(array $pngPaths, string $out): void
{
    $entries = [];
    $offset  = 6 + (16 * count($pngPaths));

    foreach ($pngPaths as $path) {
        $data = file_get_contents($path);
        [$w, $h] = getimagesize($path);

        $entries[] = [
            'w'    => $w >= 256 ? 0 : $w,
            'h'    => $h >= 256 ? 0 : $h,
            'size' => strlen($data),
            'off'  => $offset,
            'data' => $data,
        ];

        $offset += strlen($data);
    }

    $ico = pack('vvv', 0, 1, count($entries));

    foreach ($entries as $entry) {
        $ico .= pack('CCCCvvVV', $entry['w'], $entry['h'], 0, 0, 1, 32, $entry['size'], $entry['off']);
    }

    foreach ($entries as $entry) {
        $ico .= $entry['data'];
    }

    file_put_contents($out, $ico);
}

// =====================================================================

echo PHP_EOL, '  Building logo assets from ', basename($source), PHP_EOL;
echo '  ', str_repeat('-', 52), PHP_EOL;

$raw     = loadImage($source);
$knocked = knockOutWhite($raw);
$logo    = crop($knocked, contentBox($knocked));

$logoW = imagesx($logo);
$logoH = imagesy($logo);
printf("  source %dx%d, trimmed to %dx%d%s", imagesx($raw), imagesy($raw), $logoW, $logoH, PHP_EOL);

// ---- Wordmark, dark and light -----------------------------------------
$targetWidth = min(900, max(420, $logoW));

$dark  = resizeToWidth($logo, $targetWidth);
$light = toLight($dark);

savePng($dark,  $imgDir . '/logo.png');
savePng($light, $imgDir . '/logo-light.png');

printf("  logo.png            %dx%d  %s KB%s", imagesx($dark), imagesy($dark),
    round(filesize($imgDir . '/logo.png') / 1024), PHP_EOL);
printf("  logo-light.png      %dx%d  %s KB%s", imagesx($light), imagesy($light),
    round(filesize($imgDir . '/logo-light.png') / 1024), PHP_EOL);

// ---- Icon mark ---------------------------------------------------------
// The horse head is the recognisable part; a full wordmark is unreadable at
// 32px. Take the leftmost portion of the artwork, which is where it sits.
$headFraction = (float) ($argv[2] ?? 0.58);
$headSource   = crop($logo, [0, 0, (int) round($logoW * $headFraction), (int) round($logoH * 0.74)]);
$headSource   = crop($headSource, contentBox($headSource));

printf("  icon mark cropped to %dx%d%s", imagesx($headSource), imagesy($headSource), PHP_EOL);

// Line art this fine disappears when downscaled to 16px, so the icon is the
// mark in bone on the brand ink rather than black on transparent: far higher
// contrast in a browser tab, and it reads as deliberate.
$headLight = toLight($headSource);
$icoParts  = [];

foreach ([16, 32, 48, 64, 180, 192, 512] as $size) {
    $tile = imagecreatetruecolor($size, $size);
    imagealphablending($tile, false);
    imagesavealpha($tile, true);
    imagefilledrectangle($tile, 0, 0, $size, $size, imagecolorallocate($tile, 20, 17, 14));
    imagealphablending($tile, true);

    $mark = square($headLight, $size, $size <= 32 ? 0.98 : 0.82);

    // Strengthen thin strokes at the sizes where they would otherwise vanish.
    if ($size <= 64) {
        $mark = strengthen($mark, $size <= 32 ? 0.40 : 0.60);
    }

    imagecopy($tile, $mark, 0, 0, 0, 0, $size, $size);
    imagedestroy($mark);

    $path = $imgDir . '/icon-' . $size . '.png';
    savePng($tile, $path);

    if (in_array($size, [16, 32, 48], true)) {
        $icoParts[] = $path;
    }

    imagedestroy($tile);
}

copy($imgDir . '/icon-180.png', $imgDir . '/apple-touch-icon.png');
writeIco($icoParts, $imgDir . '/favicon.ico');

printf("  favicon.ico         16+32+48  %s KB%s", round(filesize($imgDir . '/favicon.ico') / 1024), PHP_EOL);
printf("  apple-touch-icon    180x180%s", PHP_EOL);
printf("  icon-192 / icon-512 for install prompts%s", PHP_EOL);

// ---- Share image -------------------------------------------------------
// Logo centred on the brand ink, for WhatsApp/Facebook link previews where a
// photograph is not appropriate.
$share = imagecreatetruecolor(1200, 630);
imagefill($share, 0, 0, imagecolorallocate($share, 20, 17, 14));
imagealphablending($share, true);

$scaled = resizeToWidth($light, 620);
imagecopy($share, $scaled, (int) ((1200 - imagesx($scaled)) / 2), (int) ((630 - imagesy($scaled)) / 2),
    0, 0, imagesx($scaled), imagesy($scaled));

imagejpeg($share, $imgDir . '/og-logo.jpg', 88);
printf("  og-logo.jpg         1200x630  %s KB%s", round(filesize($imgDir . '/og-logo.jpg') / 1024), PHP_EOL);

echo PHP_EOL, '  Done.', PHP_EOL, PHP_EOL;
