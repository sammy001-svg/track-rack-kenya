<?php
/**
 * Verify database/seo-copy.php against the limits App\Core\Seo enforces, and
 * against the catalogue it is keyed to.
 *
 * Catches the three ways this file goes stale:
 *   - a title that no longer fits once " | Tack Rack Kenya" is appended, so
 *     Google truncates it;
 *   - a description over the 158 characters Seo will clamp it to;
 *   - copy for a product that has been renamed or removed, which would
 *     otherwise just silently stop being used.
 *
 *   php bin/check-seo-copy.php
 *
 * Exits non-zero if anything is wrong, so it can gate a deploy.
 */

require __DIR__ . '/../app/bootstrap.php';

use App\Core\Seo;

$copy    = require __DIR__ . '/../database/seo-copy.php';
$catalog = require __DIR__ . '/../database/catalog.php';

$suffix     = ' | Tack Rack Kenya';
$titleBudget = Seo::TITLE_MAX - mb_strlen($suffix);

$problems = [];

// --- Lengths ---------------------------------------------------------------

foreach (['categories', 'products'] as $group) {
    foreach ($copy[$group] as $key => $entry) {
        $titleLen = mb_strlen($entry['title']);
        $descLen  = mb_strlen($entry['desc']);

        if ($titleLen > $titleBudget) {
            $problems[] = sprintf(
                'title too long (%d > %d, tag would be %d): %s',
                $titleLen,
                $titleBudget,
                $titleLen + mb_strlen($suffix),
                $key
            );
        }

        if ($descLen > Seo::DESC_MAX) {
            $problems[] = sprintf('description too long (%d > %d): %s', $descLen, Seo::DESC_MAX, $key);
        }

        // A description under about 70 characters wastes the snippet.
        if ($descLen < 70) {
            $problems[] = sprintf('description very short (%d): %s', $descLen, $key);
        }
    }
}

// --- Keys still match the catalogue ----------------------------------------

$catalogNames = [];
foreach ($catalog as $item) {
    $catalogNames[$item['name']] = true;
}

foreach (array_keys($copy['products']) as $name) {
    if (!isset($catalogNames[$name])) {
        $problems[] = "copy for a product not in catalog.php: {$name}";
    }
}

$missing = array_diff(array_keys($catalogNames), array_keys($copy['products']));
foreach ($missing as $name) {
    $problems[] = "catalogue product with no SEO copy: {$name}";
}

// --- Report ----------------------------------------------------------------

$longestTitle = 0;
$longestDesc  = 0;
foreach (['categories', 'products'] as $group) {
    foreach ($copy[$group] as $entry) {
        $longestTitle = max($longestTitle, mb_strlen($entry['title']));
        $longestDesc  = max($longestDesc, mb_strlen($entry['desc']));
    }
}

printf("categories   %d\n", count($copy['categories']));
printf("products     %d of %d in the catalogue\n", count($copy['products']), count($catalog));
printf("longest      title %d/%d, description %d/%d\n\n", $longestTitle, $titleBudget, $longestDesc, Seo::DESC_MAX);

if ($problems === []) {
    echo "All entries fit and every catalogue product is covered.\n";
    exit(0);
}

echo count($problems) . " problem(s):\n";
foreach ($problems as $problem) {
    echo "  - {$problem}\n";
}

exit(1);
