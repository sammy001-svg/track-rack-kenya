<?php
/**
 * Hand-written meta titles and descriptions for the catalogue.
 *
 * Without these the site falls back to composing a title from the product and
 * category name, which overran Google's ~60 character limit on 47 of the 58
 * products and got truncated mid-phrase. These are written to fit.
 *
 * Budgets, enforced by App\Core\Seo:
 *   title  — the site appends " | Tack Rack Kenya" (18 characters), so keep
 *            each title at 42 or under and the finished tag lands inside 60.
 *   desc   — 158 characters.
 *
 * bin/check-seo-copy.php verifies both, and reports any entry here whose
 * product no longer exists in catalog.php — renaming a product silently
 * orphans its copy otherwise.
 *
 * Products are keyed by their exact name in database/catalog.php; categories
 * by slug. Staff can override any of this per-product in the admin console;
 * these are the starting values, not a lock.
 */

return [

    // ---------------------------------------------------------------
    // Categories. Only the eight that had no meta of their own — the
    // other eight were written earlier and are left alone.
    // ---------------------------------------------------------------
    'categories' => [
        'grooming-kits-supplies' => [
            'title' => 'Grooming Kits, Brushes & Supplies',
            'desc'  => 'Body brushes, dandy brushes, curry combs, mane combs and tack boxes. Everything for a proper grooming kit, stocked in Nairobi since 1997.',
        ],
        'riding-jackets-vests' => [
            'title' => 'Body Protectors & Riding Jackets',
            'desc'  => 'BETA-certified body protectors, competition jackets and schooling vests, fitted in person at Tack Rack, Ngong Road, Nairobi.',
        ],
        'stable-equipment' => [
            'title' => 'Stable & Yard Equipment',
            'desc'  => 'Tack boxes, buckets and the everyday kit that keeps a yard running. Chosen to survive Kenyan yards, stocked in Nairobi since 1997.',
        ],
        'breeches-tights' => [
            'title' => 'Breeches, Jodhpurs & Riding Tights',
            'desc'  => 'Show breeches, schooling jodhpurs and full-seat tights for adults and children. Sized in person at Tack Rack, Ngong Road, Nairobi.',
        ],
        'saddle-pads-blankets' => [
            'title' => 'Numnahs, Saddle Pads & Blankets',
            'desc'  => 'Quilted GP numnahs, dressage squares and saddle blankets in every colour. Shaped to sit clear of the withers. Stocked in Nairobi.',
        ],
        'gloves-accessories' => [
            'title' => 'Gloves, Whips & Riding Accessories',
            'desc'  => 'Riding gloves, hat silks, schooling whips, lunge whips and number holders — the small things that finish a turnout. In Nairobi.',
        ],
        'halters-lead-ropes' => [
            'title' => 'Headcollars, Halters & Lead Ropes',
            'desc'  => 'Leather and webbing headcollars, halters and lead ropes for the yard and the lorry. From Tack Rack, Ngong Road, Nairobi.',
        ],
        'horse-health-supplements' => [
            'title' => 'Horse Health & Feed Supplements',
            'desc'  => 'Electrolytes, hoof and joint supplements and first aid for the tack room. Advice from staff who keep horses in Kenya themselves.',
        ],
    ],

    // ---------------------------------------------------------------
    // Products, keyed by name in database/catalog.php.
    // ---------------------------------------------------------------
    'products' => [

        // Saddles
        'Thorowgood Leather Saddle' => [
            'title' => 'Thorowgood Leather Saddle',
            'desc'  => 'A soft-leather English saddle with a deep, comfortable seat, fitted on your horse by our Society of Master Saddlers qualified fitter.',
        ],
        'Wintec Synthetic Saddle' => [
            'title' => 'Wintec Synthetic Saddle',
            'desc'  => 'A washable synthetic saddle with a grippy seat — low maintenance and well suited to the Kenyan climate. Fitted on the horse in Nairobi.',
        ],
        'Jeffries Elite Leather Saddle' => [
            'title' => 'Jeffries Elite Leather Saddle',
            'desc'  => 'A hand-finished havana brown English saddle with the Elite embossing. Fitted on your horse at Tack Rack, Ngong Road, Nairobi.',
        ],
        'Black Leather General Purpose Saddle' => [
            'title' => 'Black Leather GP Saddle',
            'desc'  => 'A classic black leather general purpose saddle with a padded seat — one saddle for flatwork, hacking and jumping. Fitted in Nairobi.',
        ],
        'Black Leather Jump Saddle' => [
            'title' => 'Black Leather Jump Saddle',
            'desc'  => 'A forward-cut jumping saddle in smooth black leather, cut to keep you light over a fence. Fitted on the horse by our qualified fitter.',
        ],
        'Wintec 2000 All Purpose Saddle' => [
            'title' => 'Wintec 2000 All Purpose Saddle',
            'desc'  => 'An adjustable gullet and a washable synthetic hide — change the fit as your horse changes shape. In stock at Tack Rack, Nairobi.',
        ],

        // Helmets and head protection
        'Black Skull Cap' => [
            'title' => 'Black Skull Cap',
            'desc'  => 'A plain black skull cap for cross country and racing, worn under a silk. Fitted in person at Tack Rack, Ngong Road, Nairobi.',
        ],
        'HKM Velvet Riding Hat' => [
            'title' => 'HKM Velvet Riding Hat',
            'desc'  => 'A traditional black velvet riding hat with a fixed peak, correct for the show ring. Fitted in person at Tack Rack, Nairobi.',
        ],
        'Matt Black Vented Riding Helmet' => [
            'title' => 'Matt Black Vented Riding Helmet',
            'desc'  => 'A low-profile vented helmet in matt black — cool to ride in and smart enough to compete in. Fitted in person in Nairobi.',
        ],

        // Body protection
        'USG Body Protector — BETA 2018 Level 3' => [
            'title' => 'USG Body Protector, BETA Level 3',
            'desc'  => 'Certified BETA 2018 Level 3 body protection — the standard required for cross country. Fitted in person at Tack Rack, Nairobi.',
        ],
        'Whitaker Body Protector' => [
            'title' => 'Whitaker Body Protector',
            'desc'  => 'A close-fitting body protector with a full-length front zip. Sized and fitted in person at Tack Rack, Ngong Road, Nairobi.',
        ],

        // Bridles, bits and reins
        'Black Leather Snaffle Bridle' => [
            'title' => 'Black Leather Snaffle Bridle',
            'desc'  => 'A complete black leather snaffle bridle supplied with reins — everyday tack that smartens up for the ring. From Tack Rack, Nairobi.',
        ],
        'Havana Leather Snaffle Bridle' => [
            'title' => 'Havana Leather Snaffle Bridle',
            'desc'  => 'The same leather snaffle bridle in warm havana brown, supplied with reins. Stocked in full and cob sizes at Tack Rack, Nairobi.',
        ],
        'Shires Loose Ring Snaffle with Copper Lozenge' => [
            'title' => 'Shires Copper Lozenge Snaffle',
            'desc'  => 'A double-jointed loose ring snaffle with a copper lozenge centre, which encourages a softer, wetter mouth. Stocked in Nairobi.',
        ],
        'Shires Stainless Steel Snaffle Bit' => [
            'title' => 'Shires Stainless Steel Snaffle',
            'desc'  => 'A solid stainless steel snaffle — the honest starting point for most horses. Bitting advice included at Tack Rack, Nairobi.',
        ],
        'Loose Ring Jointed Snaffle' => [
            'title' => 'Loose Ring Jointed Snaffle',
            'desc'  => 'A single-jointed loose ring snaffle in stainless steel, the standard schooling bit. Sized and advised on at Tack Rack, Ngong Road.',
        ],
        'Training Bit with Rope Cheeks' => [
            'title' => 'Training Bit with Rope Cheeks',
            'desc'  => 'A gag-action training bit with rope cheeks and a pulley, for horses that need a clearer lift. Fitting advice included, Nairobi.',
        ],
        'Rubber Bit Guards' => [
            'title' => 'Rubber Bit Guards',
            'desc'  => 'Bit guards that stop the bit rubbing at the corners of the mouth — a small part that prevents a lot of soreness. In stock in Nairobi.',
        ],

        // Girths
        'Fleece Lined Girth' => [
            'title' => 'Fleece Lined Girth',
            'desc'  => 'A girth lined with soft fleece for horses prone to girth galls, easy to wash and quick to dry. From Tack Rack, Ngong Road, Nairobi.',
        ],
        'Black Elastic Girth' => [
            'title' => 'Black Elastic Girth',
            'desc'  => 'A hard-wearing black girth with elastic at both ends, so the pressure stays even as the horse works. Stocked in Nairobi.',
        ],
        'Padded Anti-Chafe Girth' => [
            'title' => 'Padded Anti-Chafe Girth',
            'desc'  => 'A shaped, padded girth cut away behind the elbow — the answer for a horse sore in the girth groove. In stock at Tack Rack, Nairobi.',
        ],
        'Short Dressage Girth' => [
            'title' => 'Short Dressage Girth',
            'desc'  => 'A short girth for dressage saddles with long billets, keeping the buckles clear of the rider\'s leg. From Tack Rack, Nairobi.',
        ],
        'Leather Girth Buckle Guards' => [
            'title' => 'Leather Girth Buckle Guards',
            'desc'  => 'Leather guards that keep girth buckles from wearing through the saddle flap — a small part that saves an expensive saddle.',
        ],

        // Stirrups
        'Korsteel Stainless Steel Stirrup Irons' => [
            'title' => 'Korsteel Stainless Stirrup Irons',
            'desc'  => 'Solid stainless steel irons with a wide tread, sized for adults and children. Stocked at Tack Rack, Ngong Road, Nairobi.',
        ],
        'Peacock Safety Stirrup Irons' => [
            'title' => 'Peacock Safety Stirrup Irons',
            'desc'  => 'Safety irons with a rubber release band so the foot comes free in a fall — the sensible choice for children. In stock in Nairobi.',
        ],
        'Compositi Lightweight Stirrups' => [
            'title' => 'Compositi Lightweight Stirrups',
            'desc'  => 'Light composite stirrups with a wide grippy tread, in a choice of colours. Much kinder on the knees than steel. From Tack Rack.',
        ],
        'Rubber Stirrup Treads' => [
            'title' => 'Rubber Stirrup Treads',
            'desc'  => 'Replacement treads that bring worn stirrup irons back to grip — the cheapest safety upgrade in the tack room. In stock in Nairobi.',
        ],

        // Schooling aids
        'Leather Draw Reins' => [
            'title' => 'Leather Draw Reins',
            'desc'  => 'Leather draw reins with clips at both ends, for schooling a horse into a rounder outline. Advice on correct use included, Nairobi.',
        ],
        'Leather Bib Martingale' => [
            'title' => 'Leather Bib Martingale',
            'desc'  => 'A brown leather bib martingale — safer than a standing pair for a horse that throws its head. Stocked at Tack Rack, Ngong Road.',
        ],

        // Numnahs and saddle pads
        'Quilted GP Numnah — Red' => [
            'title' => 'Quilted GP Numnah, Red',
            'desc'  => 'A shaped, quilted numnah in red, cut for a general purpose saddle and easy to wash. From Tack Rack, Ngong Road, Nairobi.',
        ],
        'Quilted GP Numnah — Teal' => [
            'title' => 'Quilted GP Numnah, Teal',
            'desc'  => 'The same shaped GP numnah in teal — quilted, washable and cut to sit clear of the withers. Stocked at Tack Rack, Nairobi.',
        ],
        'Quilted GP Numnah — Black' => [
            'title' => 'Quilted GP Numnah, Black',
            'desc'  => 'A shaped GP numnah in black, the one that always looks tidy however hard the week has been. Washable. In stock in Nairobi.',
        ],
        'Quilted GP Numnah — Navy' => [
            'title' => 'Quilted GP Numnah, Navy',
            'desc'  => 'A shaped, quilted GP numnah in navy — smart enough for a lesson and cheap enough to own several. From Tack Rack, Nairobi.',
        ],
        'Dressage Square — White' => [
            'title' => 'White Dressage Square',
            'desc'  => 'A crisp white dressage square cut square to the saddle, for the competition ring. Washes clean and holds its shape. In Nairobi.',
        ],
        'Dressage Square — Black' => [
            'title' => 'Black Dressage Square',
            'desc'  => 'A square-cut dressage pad in black for schooling at home, when white is more trouble than it is worth. From Tack Rack, Nairobi.',
        ],

        // Grooming
        'Ezi-Groom Body Brush' => [
            'title' => 'Ezi-Groom Body Brush',
            'desc'  => 'A soft body brush with a chunky moulded grip — easy to hold with cold or gloved hands. From Shires, stocked in Nairobi.',
        ],
        'Ezi-Groom Dandy Brush' => [
            'title' => 'Ezi-Groom Dandy Brush',
            'desc'  => 'A stiff dandy brush for lifting dried mud and sweat, with the Ezi-Groom grip. Stocked at Tack Rack, Ngong Road, Nairobi.',
        ],
        'Body Brushes — Colour Range' => [
            'title' => 'Body Brushes, Colour Range',
            'desc'  => 'Soft body brushes with a moulded grip in a choice of colours — one each for a yard full of horses, or a set of your own.',
        ],
        'Dandy Brushes — Colour Range' => [
            'title' => 'Dandy Brushes, Colour Range',
            'desc'  => 'Stiff dandy brushes in a choice of colours, for lifting mud before the body brush does the shine. In stock in Nairobi.',
        ],
        'Rubber Curry Comb' => [
            'title' => 'Rubber Curry Comb',
            'desc'  => 'A flexible rubber curry comb with an adjustable hand strap, for lifting loose hair and scurf. From Tack Rack, Ngong Road, Nairobi.',
        ],
        'Rubber Curry Combs — Colour Range' => [
            'title' => 'Rubber Curry Combs, Colours',
            'desc'  => 'Rubber curry combs in a choice of colours, with an adjustable strap. Buy several and keep one in every grooming kit. In stock.',
        ],
        'Mane and Tail Brush' => [
            'title' => 'Mane and Tail Brush',
            'desc'  => 'A cushioned brush that works through mane and tail without breaking the hair — worth it if you plait. Stocked in Nairobi.',
        ],
        'Stainless Steel Mane Comb' => [
            'title' => 'Stainless Steel Mane Comb',
            'desc'  => 'A traditional metal mane comb for pulling and plaiting, with teeth that will not bend. From Tack Rack, Ngong Road, Nairobi.',
        ],
        'Ezi-Groom Plaiting Bands' => [
            'title' => 'Ezi-Groom Plaiting Bands',
            'desc'  => 'Plaiting bands in white and black, sold by the bag — buy more than you think you need. From Shires, stocked in Nairobi.',
        ],

        // Stable
        'HY Grooming and Tack Box' => [
            'title' => 'HY Grooming and Tack Box',
            'desc'  => 'A sturdy plastic tack box with a lift-out tray and a lockable catch, for keeping a grooming kit together. From Tack Rack, Nairobi.',
        ],

        // Breeches
        'Beige Riding Breeches' => [
            'title' => 'Beige Riding Breeches',
            'desc'  => 'Classic beige breeches with a knee patch — the correct colour for the show ring. Sized in person at Tack Rack, Ngong Road.',
        ],
        'Navy Riding Breeches' => [
            'title' => 'Navy Riding Breeches',
            'desc'  => 'Navy schooling breeches with a contrast panel, cut to move and easy to wash. Sized in person at Tack Rack, Nairobi.',
        ],
        'Black Full Seat Breeches' => [
            'title' => 'Black Full Seat Breeches',
            'desc'  => 'Black breeches with a full grip seat, for riders who want to stay put through flatwork. Sized in person at Tack Rack, Nairobi.',
        ],

        // Footwear
        'HY Leather Jodhpur Boots — Black' => [
            'title' => 'HY Leather Jodhpur Boots, Black',
            'desc'  => 'Black leather jodhpur boots with elasticated sides, worn alone or under half chaps. Fitted in person at Tack Rack, Nairobi.',
        ],
        'HY Leather Jodhpur Boots — Brown' => [
            'title' => 'HY Leather Jodhpur Boots, Brown',
            'desc'  => 'The same leather jodhpur boot in warm brown, with elasticated sides for an easy pull-on. Fitted in person in Nairobi.',
        ],
        'Black Half Chaps' => [
            'title' => 'Black Half Chaps',
            'desc'  => 'Zip-up half chaps in black, worn over jodhpur boots for the look and grip of a long boot at a fraction of the price.',
        ],
        'Brown Leather Gaiters' => [
            'title' => 'Brown Leather Gaiters',
            'desc'  => 'Studded leather gaiters in brown, for a smarter turnout over jodhpur boots. Fitted to your calf at Tack Rack, Ngong Road.',
        ],

        // Accessories
        'Competition Number Holder' => [
            'title' => 'Competition Number Holder',
            'desc'  => 'A clear armband holder that keeps a competition number flat, dry and readable all day. From Tack Rack, Ngong Road, Nairobi.',
        ],
        'LeMieux Hat Silk' => [
            'title' => 'LeMieux Hat Silk',
            'desc'  => 'A navy LeMieux hat silk with a pom-pom, cut to sit properly over a skull cap. Stocked at Tack Rack, Ngong Road, Nairobi.',
        ],
        'Hat Silk — Red' => [
            'title' => 'Red Hat Silk',
            'desc'  => 'A plain red hat silk for a skull cap — the quickest way to smarten a hat or match a team colour. In stock in Nairobi.',
        ],
        'Dressage Schooling Whip' => [
            'title' => 'Dressage Schooling Whip',
            'desc'  => 'A long schooling whip with a moulded handle, balanced to reach behind the leg without moving the hand. From Tack Rack, Nairobi.',
        ],
        'Lunge Whip' => [
            'title' => 'Lunge Whip',
            'desc'  => 'A long lunge whip for groundwork, light enough to hold out for a full session. Stocked at Tack Rack, Ngong Road, Nairobi.',
        ],
        'Riding Crop' => [
            'title' => 'Riding Crop',
            'desc'  => 'A short jumping crop with a wrist loop, sized for schooling and the ring. From Tack Rack, Ngong Road, Nairobi.',
        ],
    ],
];
