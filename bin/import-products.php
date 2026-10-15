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
    'girths' => ['horse', 5, 'Girths', 'Standard & Dressage',
        'Fleece lined, elastic and anti-chafe girths in the standard run, and short girths for a dressage saddle.'],
    'numnahs-saddle-pads' => ['horse', 6, 'Numnahs & Saddle Pads', 'Shaped, Square & Non-Slip',
        'Shaped GP numnahs, dressage squares, non-slip and Prolite pads, and fleece blankets.'],
    'headcollars-lead-ropes' => ['horse', 7, 'Headcollars & Lead Ropes', 'Foal, Pony, Cob & Full',
        'Nylon and fleece-lined headcollars, lead ropes, lunge cavessons and lunge reins for the yard and the lorry.'],
    'horse-boots' => ['horse', 8, 'Horse Boots', 'Brushing, Overreach & Bandages',
        'Brushing and overreach boots sized pony, cob and full, with bandages, pads and the sausage boot that saves an elbow.'],

    // ---- Yard ----
    'shampoo-skin-care' => ['yard', 1, 'Shampoo & Skin Care', 'Washing, Detangling & Sun',
        'Shampoos, detanglers, mane and tail lotions, soothing gels and sunscreen for the Kenyan sun.'],
    'fly-repellent' => ['yard', 2, 'Fly Repellent', 'Sprays, Masks & Traps',
        'Fly and midge repellent sprays, fine mesh fly masks, and outdoor fly traps and bait for the yard.'],
    'joint-muscle-care' => ['yard', 3, 'Joint & Muscle Care', 'Supplements, Gels & Clays',
        'Joint supplements and the gels, clays and creams that go on afterwards — devils claw, arnica, MSM and cooling clay.'],
    'gut-health' => ['yard', 4, 'Gut Health', 'Balancers & Soothers',
        'Gut balancers, soothers and yeast cultures for horses that need settling from the inside.'],
    'calming' => ['yard', 5, 'Calming', 'Focus & Temperament',
        'Powders, solutions and pastes to take the edge off a nervous or moody horse without dulling it.'],
    'feed-supplements' => ['yard', 6, 'Feed Supplements', 'Vitamins, Minerals & Electrolytes',
        'Electrolytes, vitamin and mineral premixes, garlic, limestone, salts and licks for the feed room.'],
    'hoof-care' => ['yard', 7, 'Hoof Care', 'Oils, Dressings & Farriery',
        'Hoof oils, dressings, moisturisers and repair compounds, treatment boots and poultices, and shoes and nails for the farrier.'],
    'grooming-equipment' => ['yard', 9, 'Grooming Equipment', 'Brushes, Combs & Boxes',
        'Body and dandy brushes, rubber and plastic curry combs, mane combs, sweat scrapers, plaiting bands and tack boxes.'],
    'leather-care' => ['yard', 10, 'Leather Care', 'Soaps, Dressings & Dubbin',
        'Saddle soaps, leather dressings and dubbin that keep tack alive in a dry, high-altitude climate.'],
    'yard-equipment' => ['yard', 11, 'Yard Equipment', 'Buckets, Haynets & Tubs',
        'Feed buckets and tubs, haynets and the everyday kit that keeps a yard running.'],

    // ---- Sub-categories, under the eight largest sections ----
    // The shop's stock sheets stop at section level; these split the long
    // ones so no page runs to twenty-odd products in a single list.

    // Hoof Care (21)
    'hoof-oils-dressings' => ['hoof-care', 1, 'Oils & Dressings', 'Daily Conditioning',
        'Hoof oils, moisturisers and dressings brushed on to keep the wall and sole in condition.'],
    'frog-sole-care' => ['hoof-care', 2, 'Frog & Sole Care', 'Cleansers, Pastes & Paints',
        'Cleansers, clay putties and sole paints for the frog and the underside of the hoof.'],
    'hoof-repair' => ['hoof-care', 3, 'Hoof Repair', 'Cavities, Cracks & Nail Holes',
        'Waxes and compounds that seal cavities, rebuild missing horn and repair damage around nail holes.'],
    'poultice-treatment-boots' => ['hoof-care', 4, 'Poultices & Treatment Boots', 'Soaking & Dressing',
        'Ready-made poultices, and the rigid and fabric boots that hold a dressing on the hoof.'],
    'hoof-supplements' => ['hoof-care', 6, 'Hoof Supplements', 'Biotin, Fed Daily',
        'Biotin and the hoof supplements fed in the feed to improve horn quality from the inside out.'],

    // Feed Supplements (17)
    'vitamins-minerals' => ['feed-supplements', 1, 'Vitamins & Minerals', 'Premixes, Salts & Licks',
        'Vitamin and mineral premixes, limestone, salts, bicarbonate and mineral licks for the feed room.'],
    'electrolytes' => ['feed-supplements', 2, 'Electrolytes', 'Powders & Pastes',
        'Electrolytes to replace the salts lost in sweat, in a tub, a bag or a ready-to-use syringe.'],
    'herbs-tonics' => ['feed-supplements', 3, 'Herbs & Tonics', 'Garlic, Iron & Respiratory',
        'Garlic, iron tonics and respiratory support, fed daily or through a hard spell.'],

    // Joint & Muscle Care (13)
    'joint-supplements' => ['joint-muscle-care', 1, 'Joint Supplements', 'Fed Daily',
        'Glucosamine, devils claw and the rest of the joint range, fed in the feed.'],
    'gels-clays-creams' => ['joint-muscle-care', 2, 'Gels, Clays & Creams', 'Applied to the Leg',
        'Cooling gels, clays, arnica and MSM creams that go on the leg after work.'],

    // Grooming Equipment (13)
    'curry-combs' => ['grooming-equipment', 4, 'Curry Combs', 'Rubber & Plastic',
        'Rubber and plastic curry combs, used in a circular motion to lift dust and loose hair.'],
    'combs-plaiting' => ['grooming-equipment', 5, 'Combs & Plaiting', 'Manes & Tails',
        'Metal mane combs and plaiting bands, for pulling, plaiting and turning out tidily.'],
    'grooming-tools' => ['grooming-equipment', 6, 'Grooming Tools', 'Scrapers, Scoops & Boxes',
        'Sweat scrapers, feed scoops and the tack box that keeps a grooming kit together.'],

    // Clothing (10)
    'breeches-jodhpurs' => ['clothing', 1, 'Breeches & Jodhpurs', 'Show & Schooling',
        'Show breeches, schooling jodhpurs and full-seat tights, sized in person at the shop.'],
    'footwear-chaps' => ['clothing', 2, 'Footwear & Chaps', 'Boots, Chaps & Gaiters',
        'Jodhpur boots, half chaps and leather gaiters, fitted to the foot and the calf.'],
    'hat-silks-numbers' => ['clothing', 3, 'Hat Silks & Numbers', 'Finishing the Turnout',
        'Hat silks and competition number holders — the small things that finish a turnout.'],

    // Numnahs & Saddlepads (9)
    'numnahs' => ['numnahs-saddle-pads', 1, 'Numnahs', 'Shaped & Square Cut',
        'Quilted GP numnahs shaped to follow the saddle, and square-cut dressage pads.'],
    'saddle-pads' => ['numnahs-saddle-pads', 2, 'Saddle Pads', 'Riser & Fleece',
        'Pads that sit between the saddle and the horse, and fleece for after work.'],
    'non-slip-gel-pads' => ['numnahs-saddle-pads', 3, 'Non-Slip Gel Pads', 'Grip Both Sides',
        'Dimpled gel pads that grip on both sides, to stop a saddle sliding and take the jar out of the ride.'],

    // Safety Equipment (5) — split by what the kit protects
    'riding-helmets' => ['safety-equipment', 1, 'Riding Helmets', 'Velvet & Vented',
        'Velvet show hats and vented schooling helmets, fitted on the head before you ride in them.'],
    'skull-caps' => ['safety-equipment', 2, 'Skull Caps', 'Cross Country & Racing',
        'Jockey skull caps, worn with a silk for cross country and racing.'],
    'body-protectors' => ['safety-equipment', 3, 'Body Protectors', 'BETA Level 3',
        'BETA 2018 Level 3 body protectors for cross country and eventing, fitted to the torso.'],

    // Saddles (5) — grouped by maker, as the shop sells them
    'saddles-wintec' => ['saddles', 1, 'Wintec', 'Synthetic, Changeable Gullet',
        'Wintec synthetic saddles with changeable gullets — washable, light and suited to the climate.'],
    'saddles-jeffries' => ['saddles', 2, 'Jeffries', 'English Leather',
        'Jeffries English leather saddles, hand finished and fitted on the horse.'],
    'saddles-thorowgood' => ['saddles', 3, 'Thorowgood', 'Soft Leather & Synthetic',
        'Thorowgood saddles, soft in the hand and forgiving on a changing back.'],

    // Bridles & Reins (3)
    'bridles' => ['bridles-reins', 1, 'Bridles', 'Black & Havana',
        'Leather snaffle bridles in black and havana, supplied with a pair of reins.'],
    'reins' => ['bridles-reins', 2, 'Reins', 'Leather, Rubber & Draw',
        'Leather, rubber and webbed reins, and schooling draw reins.'],

    // Bits & Accessories (5)
    'bits' => ['bits-accessories', 1, 'Bits', 'Snaffles, Lozenges & Training',
        'Loose ring and jointed snaffles, copper lozenge bits and training bits.'],
    'bit-accessories' => ['bits-accessories', 2, 'Bit Guards & Keepers', 'Protecting the Mouth',
        'Rubber bit guards and the keepers that stop a bit rubbing the corners of the mouth.'],

    // Martingales, Stirrups & Leathers (5)
    'martingales' => ['martingales-stirrups-leathers', 1, 'Martingales & Guards', 'Bib, Running & Rubber',
        'Bib and running martingales, with the rubber guards that keep girth buckles off the saddle flap.'],
    'stirrup-irons' => ['martingales-stirrups-leathers', 2, 'Stirrup Irons', 'Steel, Safety & Composite',
        'Stainless steel, peacock safety and lightweight composite stirrup irons.'],
    'stirrup-treads' => ['martingales-stirrups-leathers', 3, 'Stirrup Treads', 'Sold in Pairs',
        'Rubber treads that grip the boot, sold in pairs to replace worn or missing ones.'],

    // Girths (4) — one sub-category per kind of girth
    'dressage-girths' => ['girths', 1, 'Dressage', 'Short, 22" to 30"',
        'Short girths for a dressage saddle, 22 to 30 inches, so the buckles sit clear of the leg.'],
    'girths-anti-chafe' => ['girths', 2, 'Anti-Chafe', 'Padded Behind the Elbow',
        'Girths shaped and padded to sit clear of the elbow on a horse that rubs.'],
    'girths-fleece-lined' => ['girths', 3, 'Fleece-Lined', 'Soft Against the Skin',
        'Girths lined with fleece, for thin skin and for horses in hard work.'],
    'girths-elastic' => ['girths', 4, 'Elastic', 'Give at the Buckle',
        'Girths with elastic ends, so the horse can fill its chest without the girth fighting it.'],

    // Horse Boots (7)
    'brushing-boots' => ['horse-boots', 1, 'Brushing Boots', 'Pony, Cob & Full',
        'Boots with a moulded strike guard, protecting the inside of the leg from the opposite hoof.'],
    'overreach-boots' => ['horse-boots', 2, 'Overreach Boots', 'Neoprene & Rubber',
        'Neoprene and rubber overreach boots, guarding the heel and coronet from a hind foot.'],
    'bandages' => ['horse-boots', 3, 'Bandages', 'Fleece, Cohesive & Pads',
        'Exercise and stable bandages with the quilted pads that go underneath them.'],

    // Grooming: the brushes, split by what each one is for
    'body-brushes' => ['grooming-equipment', 1, 'Body Brushes', 'For the Shine',
        'Soft-bristled brushes used with a curry comb to lift dust and bring up a coat.'],
    'dandy-brushes' => ['grooming-equipment', 2, 'Dandy Brushes', 'For the Mud',
        'Stiff-bristled brushes for dried mud and sweat, used before the body brush.'],
    'mane-tail-brushes' => ['grooming-equipment', 3, 'Mane & Tail Brushes', 'Without Pulling',
        'Brushes that work through a mane and tail without tearing the hair out.'],

    // Shampoo & Skin Care (9)
    'shampoo' => ['shampoo-skin-care', 1, 'Shampoo', 'Washing Down',
        'Aloe vera, lemon and neem oil shampoos and conditioners for washing horses down.'],
    'detanglers' => ['shampoo-skin-care', 2, 'Detanglers', 'Mane & Tail',
        'Detanglers, sheen sprays and mane and tail lotions, for shows and for every day.'],
    'skin-care' => ['shampoo-skin-care', 3, 'Skin Care', 'Soothing & Protecting',
        'Sunscreen for pink noses, soothing gels for itching, and barrier creams for heels.'],
    'shoeing-equipment' => ['yard', 12, 'Shoeing Equipment', 'Shoes, Nails & Tools',
        'What goes under the hoof and the tools that put it there: shoes, nails and the farrier\'s own kit.'],
    'horseshoes' => ['shoeing-equipment', 1, 'Shoes', 'Steel & Aluminium',
        'Steel shoes and aluminium racing plates, bundled by size.'],
    'horseshoe-nails' => ['shoeing-equipment', 2, 'Nails', 'By Head and Gauge',
        'Farrier nails in the head patterns and gauges the shoes take, by the packet or the box.'],
    'farrier-tools' => ['shoeing-equipment', 3, 'Farrier Tools', 'For the Shoeing Itself',
        'The tools a farrier works with: pincers, rasps, knives, nippers and the rest of the kit.'],

    'first-aid' => ['shampoo-skin-care', 4, 'First Aid', 'Wounds & Dressings',
        'Wound sprays, gels, gamgee and dressings for the tack room first aid box.'],
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
/**
 * The product's bounding box within the studio backdrop, or null to leave the
 * frame alone.
 *
 * The margin around each product varies from frame to frame, so on a card one
 * product floats small while the next fills the tile. Trimming the backdrop
 * here means every card can give the product the same breathing room.
 *
 * The backdrop colour is sampled from the four corners rather than assumed
 * white — some frames sit on a light grey sweep. A result covering nearly the
 * whole frame or nearly none of it is refused, so a photograph that does not
 * fit the assumption keeps its original framing.
 */
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

    // A little of the backdrop is kept so nothing looks guillotined.
    $bw  = $maxX - $minX + 1;
    $bh  = $maxY - $minY + 1;
    $pad = (int) round(max($bw, $bh) * 0.02);

    $minX = max(0, $minX - $pad);
    $minY = max(0, $minY - $pad);
    $bw   = min($w - $minX, $bw + 2 * $pad);
    $bh   = min($h - $minY, $bh + 2 * $pad);

    $area = ($bw * $bh) / ($w * $h);

    return ($area > 0.97 || $area < 0.02) ? null : [$minX, $minY, $bw, $bh];
}

function processImage(string $srcPath, string $destBase): ?array
{
    $info = @getimagesize($srcPath);
    if ($info === false) { return null; }

    $img = $info['mime'] === 'image/png'
        ? @imagecreatefrompng($srcPath)
        : @imagecreatefromjpeg($srcPath);

    if ($img === false) { return null; }

    // Crop the studio margin away before resizing, so the stored image is the
    // product and little else.
    [$sx, $sy, $w, $h] = productBox($img) ?? [0, 0, imagesx($img), imagesy($img)];

    $scale = min(1.0, LONG_EDGE / max($w, $h));
    $nw = max(1, (int) round($w * $scale));
    $nh = max(1, (int) round($h * $scale));

    $dst = imagecreatetruecolor($nw, $nh);
    // Product shots are on white; flatten any transparency onto white.
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

// ---------------------------------------------------------------------
//  Sizes, colours and packs
//
//  These come from the client's sizes & colours sheet rather than from each
//  product's own entry, because the sheet describes types of product — every
//  girth takes the same size run — and because the live site, whose products
//  are named differently, has to be able to apply the same list. The one copy
//  lives in database/variants.php and is keyed by slug.
// ---------------------------------------------------------------------
$sheet = require __DIR__ . '/../database/variants.php';

foreach ($sheet as $label => $row) {
    foreach ($row['products'] as $slug) {
        $product = $db->one('SELECT id FROM products WHERE slug = :s', ['s' => $slug]);

        if ($product === null) {
            continue;   // a slug that belongs to the live catalogue, not ours
        }

        if ($dryRun) {
            continue;
        }

        $sort = 0;

        foreach ($row['variants'] as $group => $values) {
            foreach ($values as $value) {
                $db->insert('product_variants', [
                    'product_id' => (int) $product['id'],
                    'label'      => $group,
                    'value'      => $value,
                    'sort_order' => $sort++,
                ]);
                $stats['variants'] = ($stats['variants'] ?? 0) + 1;
            }
        }
    }
}

out(sprintf('  variants          %d option(s) from the sizes sheet', $stats['variants'] ?? 0));
out();

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
