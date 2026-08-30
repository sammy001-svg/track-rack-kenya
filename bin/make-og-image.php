<?php
/**
 * Build the default social share card — the picture that appears when the site
 * is posted to WhatsApp, Facebook, X or LinkedIn.
 *
 * The previous one was the logo alone on black, which said nothing about what
 * the business sells or where it is. This puts the logo, a strapline and the
 * location beside a real product photograph from the studio shoot.
 *
 * Everything here is Tack Rack's own except the two typefaces, which are the
 * ones the site already uses (Fraunces and Inter, both SIL Open Font License).
 *
 *   php bin/make-og-image.php
 *
 * Writes public/assets/img/og-default.jpg and .webp at 1200x630 — the size
 * every major platform crops from. The finished card is committed, so this
 * only needs running if the design or the photograph changes.
 *
 * The two fonts are not committed — no point vendoring a typeface to redraw
 * one picture. Fetch them into build/fonts/ first:
 *
 *   curl -o build/fonts/Fraunces-Light.ttf "$(curl -s -A Mozilla \
 *     'https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@144,300' \
 *     | grep -o 'https://[^)]*\.ttf')"
 *   curl -o build/fonts/Inter-SemiBold.ttf "$(curl -s -A Mozilla \
 *     'https://fonts.googleapis.com/css2?family=Inter:wght@600' \
 *     | grep -o 'https://[^)]*\.ttf' | head -1)"
 */

require __DIR__ . '/../app/bootstrap.php';

const W = 1200;
const H = 630;

$base    = dirname(__DIR__);
$display = $base . '/build/fonts/Fraunces-Light.ttf';
$sans    = $base . '/build/fonts/Inter-SemiBold.ttf';

// The saddle: the single most recognisable thing they sell, and the item their
// saddle fitting service is built around.
$photo   = $base . '/public/uploads/products/thorowgood-leather-saddle-01.jpg';
$logo    = $base . '/public/assets/img/logo-light.png';
$out     = $base . '/public/assets/img/og-default';

foreach ([$display, $sans, $photo, $logo] as $required) {
    if (!is_file($required)) {
        $hint = str_contains($required, '/build/fonts/')
            ? "\nThe fonts are not committed — see the header of this file for the two curl commands.\n"
            : '';

        exit("Missing: {$required}\n{$hint}");
    }
}

// Site palette, from public/assets/css/main.css.
$canvas = imagecreatetruecolor(W, H);
imageantialias($canvas, true);

$ink   = imagecolorallocate($canvas, 0x14, 0x11, 0x0E);
$bone  = imagecolorallocate($canvas, 0xF7, 0xF4, 0xEF);
$brass = imagecolorallocate($canvas, 0xB9, 0x91, 0x49);
$muted = imagecolorallocate($canvas, 0x9A, 0x93, 0x8A);

imagefilledrectangle($canvas, 0, 0, W, H, $ink);

// ---------------------------------------------------------------------
// Right third: the product photograph, on the white it was shot against.
// ---------------------------------------------------------------------

$panelX = 760;
$panelW = W - $panelX;

imagefilledrectangle($canvas, $panelX, 0, W, H, imagecolorallocate($canvas, 255, 255, 255));

/**
 * Bounding box of everything that is not near-white, sampled on a grid.
 *
 * The studio frames carry wide white margins of their own. Fitted as-is the
 * saddle sat marooned in the middle of the panel at about half the size it
 * should be, so the margins come off first.
 */
function trimWhite($img, int $threshold = 244): array
{
    $w    = imagesx($img);
    $h    = imagesy($img);
    $step = max(1, (int) floor(min($w, $h) / 400));

    $minX = $w; $minY = $h; $maxX = -1; $maxY = -1;

    for ($y = 0; $y < $h; $y += $step) {
        for ($x = 0; $x < $w; $x += $step) {
            $c = imagecolorat($img, $x, $y);

            if ((($c >> 16) & 0xFF) >= $threshold
                && (($c >> 8) & 0xFF) >= $threshold
                && ($c & 0xFF) >= $threshold) {
                continue;
            }

            if ($x < $minX) { $minX = $x; }
            if ($x > $maxX) { $maxX = $x; }
            if ($y < $minY) { $minY = $y; }
            if ($y > $maxY) { $maxY = $y; }
        }
    }

    return $maxX < 0 ? [0, 0, $w, $h] : [$minX, $minY, $maxX - $minX + 1, $maxY - $minY + 1];
}

$src = imagecreatefromjpeg($photo);
[$sx, $sy, $sw, $sh] = trimWhite($src);

// Contain, not cover: cropping a cut-out product just lops the ends off it.
$inset = 0.82;
$scale = min(($panelW * $inset) / $sw, (H * $inset) / $sh);
$dw    = (int) round($sw * $scale);
$dh    = (int) round($sh * $scale);

imagecopyresampled(
    $canvas,
    $src,
    $panelX + (int) (($panelW - $dw) / 2),
    (int) ((H - $dh) / 2),
    $sx,
    $sy,
    $dw,
    $dh,
    $sw,
    $sh
);
imagedestroy($src);

// A hair rule where the two halves meet, so the join reads as deliberate.
imagefilledrectangle($canvas, $panelX, 0, $panelX, H, imagecolorallocate($canvas, 0x2A, 0x25, 0x20));

// ---------------------------------------------------------------------
// Left: logo, strapline, location.
// ---------------------------------------------------------------------

$margin = 72;

$mark  = imagecreatefrompng($logo);
$mw    = imagesx($mark);
$mh    = imagesy($mark);
$markW = 330;
$markH = (int) round($mh * ($markW / $mw));

imagealphablending($canvas, true);
imagecopyresampled($canvas, $mark, $margin, 78, 0, 0, $markW, $markH, $mw, $mh);
imagedestroy($mark);

/** Draw one line of text at a baseline. */
function line($img, string $font, float $size, int $x, int $y, int $colour, string $text): void
{
    imagettftext($img, $size, 0, $x, $y, $colour, $font, $text);
}

/**
 * Letter-space a string for use as an eyebrow.
 *
 * GD has no tracking property. Drawing glyph by glyph and advancing by each
 * one's measured width is the usual workaround, but it loses the font's
 * kerning — it left a visible hole before the A in KENYA. Real thin spaces
 * keep the string a single run, so the font still shapes and kerns it.
 */
function tracked(string $text): string
{
    return implode("\u{2009}", preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY));
}

$y = 78 + $markH + 96;

line($canvas, $sans, 12.5, $margin, $y, $brass, tracked('NAIROBI, KENYA'));

$y += 62;
line($canvas, $display, 43, $margin, $y, $bone, 'Equestrian Supplies');

$y += 60;
line($canvas, $display, 43, $margin, $y, $bone, '& Saddlery');

$y += 58;
line($canvas, $sans, 15.5, $margin, $y, $muted, 'Saddle fitting · Workshop repairs · Since 1997');

// A short brass rule under the copy, echoing the eyebrow rule on the site.
imagefilledrectangle($canvas, $margin, H - 96, $margin + 64, H - 94, $brass);

line($canvas, $sans, 13, $margin, H - 58, $muted, 'tackrack.co.ke');

// ---------------------------------------------------------------------

imagejpeg($canvas, $out . '.jpg', 88);

if (function_exists('imagewebp')) {
    imagewebp($canvas, $out . '.webp', 82);
}

imagedestroy($canvas);

clearstatcache();
printf("og-default.jpg   %d x %d   %s KB\n", W, H, round(filesize($out . '.jpg') / 1024));

if (is_file($out . '.webp')) {
    printf("og-default.webp  %d x %d   %s KB\n", W, H, round(filesize($out . '.webp') / 1024));
}
