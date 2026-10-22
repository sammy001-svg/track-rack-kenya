<?php
/**
 * Write database/photographs-wanted.csv — what the photographer still owes us.
 *
 *   php bin/photographs-wanted.php
 *
 * In the order it costs the site most: listings with no photograph at all,
 * then the ones whose lead frame is a side, rear or close detail where a
 * forward-facing shot is wanted, then the ones carrying a single frame.
 *
 * Regenerate it after any shoot, so the list handed to the client is what is
 * actually missing rather than what was missing last time.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("This script can only be run from the command line.\n");
}

require dirname(__DIR__) . '/app/bootstrap.php';

use App\Core\Database;

$db  = Database::instance();
$out = BASE_PATH . '/database/photographs-wanted.csv';

$rows = $db->all(
    "SELECT p.sku, p.name, c.name AS section,
            (SELECT COUNT(*) FROM `product_images` i WHERE i.`product_id` = p.`id`) AS shots,
            (SELECT i.`alt` FROM `product_images` i WHERE i.`product_id` = p.`id`
              ORDER BY i.`is_primary` DESC, i.`sort_order` ASC LIMIT 1) AS lead_alt
       FROM `products` p
       LEFT JOIN `categories` c ON c.`id` = p.`category_id`
      WHERE p.`is_active` = 1
      ORDER BY c.`name`, p.`name`"
);

$none = $sideOn = $single = [];

foreach ($rows as $r) {
    if ((int) $r['shots'] === 0) {
        $none[] = $r;
        continue;
    }

    // A lead frame that announces itself as a side, rear or close view is one
    // the review asked to replace with a forward-facing shot.
    if (preg_match('/\b(from the side|side view|rear view|from behind|close detail|close-up|from above)\b/i',
                   (string) $r['lead_alt'])) {
        $sideOn[] = $r;
        continue;
    }

    if ((int) $r['shots'] === 1) {
        $single[] = $r;
    }
}

$handle = fopen($out, 'w');
fputcsv($handle, ['priority', 'sku', 'product', 'section', 'what is needed', 'shots on file']);

foreach ($none as $r) {
    fputcsv($handle, ['1 no photograph', $r['sku'], $r['name'], $r['section'], 'Any photograph at all', 0]);
}

foreach ($sideOn as $r) {
    fputcsv($handle, ['2 leads with a side or detail view', $r['sku'], $r['name'], $r['section'],
        'A forward-facing shot to lead with (currently: ' . $r['lead_alt'] . ')', $r['shots']]);
}

foreach ($single as $r) {
    fputcsv($handle, ['3 only one frame', $r['sku'], $r['name'], $r['section'], 'A second angle', 1]);
}

fclose($handle);

printf("\n  Tack Rack Kenya — photographs wanted\n  %s\n\n", str_repeat('=', 44));
printf("  %d with no photograph at all\n", count($none));
printf("  %d leading with a side or detail view\n", count($sideOn));
printf("  %d carrying a single frame\n\n", count($single));

if ($none !== []) {
    printf("  Nothing at all:\n");
    foreach ($none as $r) {
        printf("    %-11s %-38s %s\n", $r['sku'], $r['name'], $r['section']);
    }
    printf("\n");
}

if ($sideOn !== []) {
    printf("  Wanted forward-facing:\n");
    foreach ($sideOn as $r) {
        printf("    %-11s %-38s %-20s %s\n", $r['sku'], $r['name'], $r['section'], $r['lead_alt']);
    }
    printf("\n");
}

printf("  written to database/photographs-wanted.csv\n\n");
