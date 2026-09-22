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
            'desc'  => 'Electrolytes, hoof and joint supplements and first aid for the tack room, with expert product advice from Tack Rack, Ngong Road, Nairobi.',
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
        'Plastic Curry Comb' => [
            'title' => 'Plastic Curry Comb',
            'desc'  => 'A plastic curry comb with an adjustable hand strap, for lifting loose hair and scurf before body brushing. From Tack Rack, Ngong Road, Nairobi.',
        ],
        'Plastic Curry Combs — Colour Range' => [
            'title' => 'Plastic Curry Combs, Colours',
            'desc'  => 'Plastic curry combs with an adjustable strap in blue, black and purple. Buy several and keep one in every grooming kit. In stock.',
        ],
        'Shires Rubber Curry Comb' => [
            'title' => 'Shires Rubber Curry Comb',
            'desc'  => 'An oval Shires rubber curry comb with a moulded grip and three rings of teeth, in blue, black, red and purple. From Tack Rack, Nairobi.',
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
        // ---- Second shoot (database/catalog-2.php) ----

        // Pads and rugs
        'Prolite Saddle Pad' => [
            'title' => 'Prolite Saddle Pad',
            'desc'  => 'A shaped black Prolite pad with a split spine channel, worn under the saddle. Bring your saddle and we will check the fit. Tack Rack, Nairobi.',
        ],
        'Non-Slip Gel Pad' => [
            'title' => 'Non-Slip Gel Pad',
            'desc'  => 'A dimpled gel pad that grips on both sides to stop the saddle slipping and take some of the jar out of the ride. Wipes clean. In stock in Nairobi.',
        ],
        'Tartan Fleece Rug' => [
            'title' => 'Tartan Fleece Rug',
            'desc'  => 'A warm red, yellow and black tartan fleece rug with chest straps — a cooler after work or a light layer on cold mornings. From Tack Rack, Nairobi.',
        ],

        // Headcollars and lunging
        'Nylon Headcollar — Colour Range' => [
            'title' => 'Nylon Headcollars, Colour Range',
            'desc'  => 'Adjustable nylon headcollars with brass fittings in red, black and royal blue. Sized pony, cob and full at Tack Rack, Ngong Road, Nairobi.',
        ],
        'Fleece-Lined Headcollar' => [
            'title' => 'Fleece-Lined Headcollar',
            'desc'  => 'A black headcollar padded with grey fleece at the noseband and headpiece, so it will not rub on a horse that wears one all day. In stock.',
        ],
        'Rope Lead Ropes' => [
            'title' => 'Rope Lead Ropes',
            'desc'  => 'Soft, thick twisted lead ropes with brass trigger clips, in purple, sky blue and black. Comfortable to hold. From Tack Rack, Nairobi.',
        ],
        'Cotton Lunge Line — Red' => [
            'title' => 'Red Cotton Lunge Line',
            'desc'  => 'A long flat cotton lunge line in red, soft on the hands, with a swivel clip and a hand loop. Stocked at Tack Rack, Ngong Road, Nairobi.',
        ],

        // Boots and bandages
        'ARMA Brushing Boots — Navy' => [
            'title' => 'ARMA Brushing Boots, Navy',
            'desc'  => 'Navy ARMA brushing boots with a moulded strike guard and three touch-close straps, protecting the inside of the leg. In stock in Nairobi.',
        ],
        'ARMA Anatomic Brushing Boots' => [
            'title' => 'ARMA Anatomic Brushing Boots',
            'desc'  => 'Black ARMA Anatomic brushing boots with a moulded shell and wide red straps, close to the leg without rubbing. From Tack Rack, Nairobi.',
        ],
        'Shires Rubber Overreach Boots' => [
            'title' => 'Shires Rubber Overreach Boots',
            'desc'  => 'Ribbed rubber overreach boots from Shires with a touch-close fastening, protecting heels and coronets from the hind feet. Stocked in Nairobi.',
        ],
        'ARMA Overreach Boots — Red' => [
            'title' => 'ARMA Overreach Boots, Red',
            'desc'  => 'Soft black and red ARMA overreach boots — lighter and quieter than rubber, protecting the heel and coronet. In stock at Tack Rack, Nairobi.',
        ],
        'Weatherbeeta Fleece Bandages' => [
            'title' => 'Weatherbeeta Fleece Bandages',
            'desc'  => 'Soft Weatherbeeta fleece leg bandages with a suede-effect tab, in navy with grey and in lime green. For exercise or the stable. In Nairobi.',
        ],
        'Cohesive Bandages' => [
            'title' => 'Cohesive Bandages',
            'desc'  => 'Self-adhesive cohesive bandage in blue and purple — sticks to itself, not to hair, and tears by hand. For the first aid box. From Tack Rack.',
        ],
        'Quilted Bandage Pads' => [
            'title' => 'Quilted Bandage Pads',
            'desc'  => 'White quilted pads for under stable and travel bandages, spreading the pressure evenly down the leg. Sold as a set at Tack Rack, Nairobi.',
        ],
        'Rubber Sausage Boot' => [
            'title' => 'Rubber Sausage Boot',
            'desc'  => 'A padded rubber shoe boil ring buckled round the pastern, keeping the shoe off the elbow of a horse that lies tucked up. In stock in Nairobi.',
        ],
        'Robinson Veterinary Gamgee' => [
            'title' => 'Robinson Veterinary Gamgee',
            'desc'  => 'A roll of Robinson veterinary gamgee — absorbent cotton wool in gauze, for padding bandages and covering dressings. From Tack Rack, Nairobi.',
        ],

        // Hoof care
        'Davis Hoof Treatment Boot' => [
            'title' => 'Davis Hoof Treatment Boot',
            'desc'  => 'A rigid Davis treatment boot, made in the USA, that holds a soak or poultice on the hoof and keeps it clean. Stocked at Tack Rack, Nairobi.',
        ],
        'ARMA Medi-Boot' => [
            'title' => 'ARMA Medi-Boot Poultice Boot',
            'desc'  => 'A tough fabric ARMA Medi-Boot that draws closed at the pastern, holding a poultice or dressing on the hoof. In stock at Tack Rack, Nairobi.',
        ],
        'Trail Hoof Boot' => [
            'title' => 'Trail Hoof Boot',
            'desc'  => 'A strapped hoof boot with a deep treaded sole, for a barefoot horse on stony tracks or one that has lost a shoe. Fitted by measurement in Nairobi.',
        ],
        'Hoof Oil' => [
            'title' => 'Hoof Oil',
            'desc'  => 'Hoof oil brushed on to the wall and sole to keep hooves in good condition and looking smart. Distributed by Tack Rack, in two bottle sizes.',
        ],
        'Stockholm Tar' => [
            'title' => 'Stockholm Tar for Hooves',
            'desc'  => 'Traditional Stockholm tar for the hoof, distributed by Tack Rack in two bottle sizes. From our shop off Ngong Road, Nairobi.',
        ],
        'Keratex 3P Hoof Repair' => [
            'title' => 'Keratex 3P Hoof Repair',
            'desc'  => 'Keratex 3P Hoof Repair rebuilds hooves after damage or surgery and fills missing horn. 50ml, full instructions included. In stock in Nairobi.',
        ],
        'Keratex Hoof Moisturiser' => [
            'title' => 'Keratex Hoof Moisturiser',
            'desc'  => 'Keratex Hoof Moisturiser for dry, cracked hooves, keeping moisture levels right through dry spells and seasonal changes. From Tack Rack.',
        ],
        'Keratex Hoof Putty' => [
            'title' => 'Keratex Hoof Putty',
            'desc'  => 'A flexible, semi-permanent Keratex wax that seals and stabilises horn separation cavities and punctured soles. Stocked at Tack Rack, Nairobi.',
        ],
        'Keratex Frog Power Cleanser' => [
            'title' => 'Keratex Frog Power Cleanser',
            'desc'  => 'A powerful Keratex cleanser for problems in the frog, with soothing tea tree oil and a nozzle that reaches into the clefts. In stock in Nairobi.',
        ],
        'Keratex Nail Hole Damage Repair' => [
            'title' => 'Keratex Nail Hole Damage Repair',
            'desc'  => 'A penetrating Keratex liquid that helps repair damage around nail holes and stops old holes cracking the hoof. 200ml. From Tack Rack, Nairobi.',
        ],
        'Red Horse Hydrohoof' => [
            'title' => 'Red Horse Hydrohoof',
            'desc'  => 'Hydrohoof from Red Horse Products, a hoof moisturiser and natural barrier, in 500ml and 200ml pots. Stocked at Tack Rack, Ngong Road, Nairobi.',
        ],
        'Red Horse Artimud' => [
            'title' => 'Red Horse Artimud Hoof Putty',
            'desc'  => 'Artimud from Red Horse Products — a clay and eucalyptus hoof putty in a 750g pot. In stock at Tack Rack, Ngong Road, Nairobi.',
        ],
        'Red Horse Field Paste' => [
            'title' => 'Red Horse Field Paste',
            'desc'  => 'Field Paste from Red Horse Products, a conditioning frog and sole dressing in a 750g pot. Stocked at Tack Rack, Ngong Road, Nairobi.',
        ],
        'Red Horse Sole Paint' => [
            'title' => 'Red Horse Sole Paint',
            'desc'  => 'Sole Paint from Red Horse Products, applied to the frog and sole, in a 500ml bottle. In stock at Tack Rack, Ngong Road, Nairobi.',
        ],
        'Red Horse Stronghorn' => [
            'title' => 'Red Horse Stronghorn',
            'desc'  => 'Stronghorn from Red Horse Products, a hoof hardening and cleansing spray in a 500ml bottle. Stocked at Tack Rack, Ngong Road, Nairobi.',
        ],
        'Red Horse Sole Cleanse' => [
            'title' => 'Red Horse Sole Cleanse',
            'desc'  => 'Sole Cleanse from Red Horse Products, an active sole and frog cleansing spray in a 500ml trigger bottle. In stock at Tack Rack, Nairobi.',
        ],
        'Endeavon Sole Hardener' => [
            'title' => 'Endeavon Sole Hardener',
            'desc'  => 'Endeavon Sole Hardener, sprayed on to toughen soft or thin soles. Read the label warnings before use. From Tack Rack, Ngong Road, Nairobi.',
        ],
        'Radiol Pedicine Hoof Ointment' => [
            'title' => 'Radiol Pedicine Hoof Ointment',
            'desc'  => 'Pedicine hoof ointment from Radiol Health Care Products, in 450g and 150g tubs. Stocked at Tack Rack, Ngong Road, Nairobi.',
        ],
        'HY Health Hoof Poultice' => [
            'title' => 'HY Health Hoof Poultice, 3 Pack',
            'desc'  => 'Ready-made, all-purpose hoof poultices from HY Health — 100% natural, veterinary approved, three to a pack. In stock at Tack Rack, Nairobi.',
        ],
        'Optima Horseshoe Nails' => [
            'title' => 'Optima Horseshoe Nails',
            'desc'  => 'Optima horseshoe nails by the box for farriers, in E-3, E-4, Crown Head and ASV patterns. Stocked at Tack Rack, Ngong Road, Nairobi.',
        ],
        'Aluminium Horseshoes' => [
            'title' => 'Aluminium Horseshoes',
            'desc'  => 'Lightweight aluminium horseshoes with a toe clip, bundled by size for farriers. Available from Tack Rack, Ngong Road, Nairobi.',
        ],
        'Steel Horseshoes' => [
            'title' => 'Steel Horseshoes',
            'desc'  => 'Standard steel horseshoes with a toe clip, bundled by size for farriers. Available from Tack Rack, Ngong Road, Nairobi.',
        ],

        // Fly control
        'VetsBrands Summer Spray' => [
            'title' => 'VetsBrands Summer Spray',
            'desc'  => 'A spray-on repellent against midges, mosquitoes and stable flies, with DEET and no citronella. 1 litre and a larger can. From Tack Rack, Nairobi.',
        ],
        'Equimins Fly & Midge Repellent' => [
            'title' => 'Equimins Fly & Midge Repellent',
            'desc'  => 'A 750ml Equimins trigger spray formulated to help repel biting flies, midges and mosquitoes. In stock at Tack Rack, Ngong Road, Nairobi.',
        ],
        'Dudu-Krin Fly Repellent' => [
            'title' => 'Dudu-Krin Fly Repellent',
            'desc'  => 'A 250ml fly repellent concentrate for horses (5% EC) with DEET, piperonyl butoxide, sumithrin and E. citriodora. From Tack Rack, Nairobi.',
        ],
        'Buzz Off Fly Repellent' => [
            'title' => 'Buzz Off Fly Repellent',
            'desc'  => 'Buzz Off fly repellent spray for dogs and horses, in a ready-to-use trigger bottle or a large refill can. Stocked at Tack Rack, Nairobi.',
        ],
        'FlyGuard Pro Fine Mesh Fly Mask' => [
            'title' => 'FlyGuard Pro Fine Mesh Fly Mask',
            'desc'  => 'A FlyGuard Pro fine mesh fly mask with ear holes and a contoured nose, blocking around 70% of UV. Sized pony, cob and full. In Nairobi.',
        ],
        'Redtop Outdoor Flytrap' => [
            'title' => 'Redtop Outdoor Flytrap',
            'desc'  => 'A re-usable Redtop outdoor fly trap, hung around the yard to catch flies without sprays. Refill with Bait & Bag. From Tack Rack, Nairobi.',
        ],
        'Redtop Bait & Bag Refill' => [
            'title' => 'Redtop Bait & Bag Refill',
            'desc'  => 'The service pack for the re-usable Redtop Outdoor Flytrap — a fresh bag and a sachet of bait. In stock at Tack Rack, Ngong Road, Nairobi.',
        ],

        // First aid and skin care
        'Lincoln Sun Bloc' => [
            'title' => 'Lincoln Sun Bloc Horse Sunscreen',
            'desc'  => 'A water-resistant UVA and UVB sunscreen from Lincoln with aloe vera — worth having for pink noses under the Kenyan sun. In stock in Nairobi.',
        ],
        'Sooth-Itch Super Strength Gel' => [
            'title' => 'Sooth-Itch Super Strength Gel',
            'desc'  => 'A 500ml gel that soothes itching and supports natural hair regrowth. Stocked at Tack Rack, Ngong Road, Nairobi.',
        ],
        'Equimins Leg Gel' => [
            'title' => 'Equimins Leg Gel',
            'desc'  => 'A cool, refreshing Equimins leg gel with camphor and menthol, ideal after training or exercise. 500g tub. From Tack Rack, Nairobi.',
        ],
        'Natural Clay' => [
            'title' => 'Natural Clay Cooling Dressing',
            'desc'  => 'A non-irritating natural clay dressing, extra cooling and soothing for minor soreness and stiffness. Distributed by Tack Rack, tub or bucket.',
        ],
        'Equimins Arnica & Witch Hazel' => [
            'title' => 'Equimins Arnica & Witch Hazel',
            'desc'  => 'Arnica and witch hazel with MSM and allantoin from Equimins — great for bruises and sprains. In stock at Tack Rack, Ngong Road, Nairobi.',
        ],
        'Moore’s Herbal Cooling Gel' => [
            'title' => 'Moore’s Herbal Cooling Gel',
            'desc'  => 'Moore’s Quality Herbal Cooling Gel for fast relief from inflammation and sore muscles, in a 500ml tub. Stocked at Tack Rack, Nairobi.',
        ],
        'Equimins Devils Claw Gel' => [
            'title' => 'Equimins Devils Claw Gel',
            'desc'  => 'A cooling Equimins joint gel with devils claw that is quickly absorbed through the skin. 500g tub. From Tack Rack, Ngong Road, Nairobi.',
        ],
        'Equimins Aloe Vera Gel' => [
            'title' => 'Equimins Aloe Vera Gel',
            'desc'  => 'A soothing Equimins first aid and stable gel with aloe vera, for skin disorders. 500g tub. In stock at Tack Rack, Ngong Road, Nairobi.',
        ],
        'Equimins MSM Cream' => [
            'title' => 'Equimins MSM Cream',
            'desc'  => 'An MSM cream from the Equimins first aid and stable range, in a 500g tub. Stocked at Tack Rack, Ngong Road, Nairobi.',
        ],
        'Heal-O-Fast Wound Healing Spray' => [
            'title' => 'Heal-O-Fast Wound Healing Spray',
            'desc'  => 'Heal-O-Fast Herbal wound healing spray, sprayed straight on to the wound — one for the tack room first aid box. From Tack Rack, Nairobi.',
        ],
        'Wound Magic' => [
            'title' => 'Wound Magic',
            'desc'  => 'Wound Magic for animal emergencies — worth keeping in the tack room first aid box. In stock at Tack Rack, Ngong Road, Nairobi.',
        ],
        'Veterinus Derma Gel' => [
            'title' => 'Veterinus Derma Gel 4-in-1',
            'desc'  => 'Veterinus Derma Gel, a 4-in-1 animal skin care gel: moist environment, skin care, bacterial control and a protective film. 100ml. In Nairobi.',
        ],
        'Red Horse Honeyheel' => [
            'title' => 'Red Horse Honeyheel',
            'desc'  => 'Honeyheel from Red Horse Products, a honey-based barrier cream in a 500ml pot. Stocked at Tack Rack, Ngong Road, Nairobi.',
        ],

        // Health and supplements
        'Equine America Glucosamine HCl 12,000' => [
            'title' => 'Equine America Glucosamine 12,000',
            'desc'  => 'Extra strength glucosamine powder with MSM and hyaluronic acid for equine joint support — a 66-day supply. From Tack Rack, Nairobi.',
        ],
        'Equine America Cortaflex HA' => [
            'title' => 'Equine America Cortaflex HA',
            'desc'  => 'Cortaflex HA Regular Strength Powder from Equine America to support joint mobility — 900g, a four-month supply. In stock in Nairobi.',
        ],
        'Equine America Buteless' => [
            'title' => 'Equine America Buteless Solution',
            'desc'  => 'Buteless High Strength Solution from Equine America, for joint comfort and mobility support. 1 litre. From Tack Rack, Ngong Road, Nairobi.',
        ],
        'Equine America Magnitude' => [
            'title' => 'Equine America Magnitude Powder',
            'desc'  => 'Magnitude Powder from Equine America for a more relaxed horse, helping reduce nervous tension — a six-month supply. In stock in Nairobi.',
        ],
        'Equine America No More Moods' => [
            'title' => 'Equine America No More Moods',
            'desc'  => 'No More Moods Solution from Equine America, nutritional support for moody mares and temperamental stallions. 1 litre. From Tack Rack.',
        ],
        'Equine America Focus' => [
            'title' => 'Equine America Focus Powder',
            'desc'  => 'Focus Powder from Equine America to support concentration, relaxation and hormonal balance — 1.5kg, a 30-day supply. In stock in Nairobi.',
        ],
        'Equine America So-Kalm Paste' => [
            'title' => 'Equine America So-Kalm Paste',
            'desc'  => 'So-Kalm paste from Equine America in a 30ml syringe, to support concentration and focus. Stocked at Tack Rack, Ngong Road, Nairobi.',
        ],
        'Equine America Biotin Xtra' => [
            'title' => 'Equine America Biotin Xtra',
            'desc'  => 'Biotin Xtra Powder from Equine America with zinc and methionine to support hoof health — 2.5kg, a 50-day supply. From Tack Rack, Nairobi.',
        ],
        'Coligone ColiFLEX Gastro-Joint Care' => [
            'title' => 'Coligone ColiFLEX Gastro-Joint Care',
            'desc'  => 'ColiFLEX from Coligone combines a digestive feed supplement with joint care. Competition safe per the label. In stock at Tack Rack, Nairobi.',
        ],
        'Coligone Liquid' => [
            'title' => 'Coligone Digestive Liquid',
            'desc'  => 'Coligone equine digestive soothing supplement in liquid form. Stocked at Tack Rack, Ngong Road, Nairobi.',
        ],
        'Coligone Balancer' => [
            'title' => 'Coligone Balancer',
            'desc'  => 'Coligone Balancer — every horse, every day — a daily digestive feed supplement in a large tub. From Tack Rack, Ngong Road, Nairobi.',
        ],
        'Coligone Powder' => [
            'title' => 'Coligone Digestive Powder',
            'desc'  => 'Coligone equine digestive soothing supplement in powder form. Stocked at Tack Rack, Ngong Road, Nairobi.',
        ],
        'Endeavon Flex-O-Joint' => [
            'title' => 'Endeavon Flex-O-Joint',
            'desc'  => 'A joint supplement for horses and dogs with chondroitin, glucosamine, MSM and vitamin C. Distributed by Tack Rack, Ngong Road, Nairobi.',
        ],
        'Endeavon Biotin Supplement H' => [
            'title' => 'Endeavon Biotin Supplement H',
            'desc'  => 'Endeavon Biotin Supplement H for horses, distributed by Tack Rack. Store below 30°C in a cool, dry place. From our shop on Ngong Road.',
        ],
        'Keratex Zeolite' => [
            'title' => 'Keratex Zeolite',
            'desc'  => 'Keratex Zeolite feed supplement — one tub gives up to three months’ supply, according to the label. In stock at Tack Rack, Nairobi.',
        ],
        'Equimins Devils Claw Root Herb' => [
            'title' => 'Equimins Devils Claw Root Herb',
            'desc'  => 'Devils claw root herb from Equimins in a resealable 1kg eco pack, for joint support. Stocked at Tack Rack, Ngong Road, Nairobi.',
        ],
        'Equimins Milk Thistle' => [
            'title' => 'Equimins Milk Thistle',
            'desc'  => 'Milk thistle from the Equimins nutrition range, in a resealable eco pack. In stock at Tack Rack, Ngong Road, Nairobi.',
        ],
        'Equimins Vitamin E & Selenium' => [
            'title' => 'Equimins Vitamin E & Selenium',
            'desc'  => 'A vitamin E and selenium supplement from Equimins for increased stamina in horses in hard training. 3kg tub. From Tack Rack, Nairobi.',
        ],
        'Equimins Young Stock Formula' => [
            'title' => 'Equimins Young Stock Formula',
            'desc'  => 'Equimins Young Stock Formula — advanced nutrition for young foals and growing horses. 4kg tub. Stocked at Tack Rack, Ngong Road, Nairobi.',
        ],
        'Equimins B-Plus Liquid' => [
            'title' => 'Equimins B-Plus Liquid',
            'desc'  => 'A B vitamin supplement from Equimins to aid recovery after illness and stimulate appetite. 1 litre. In stock at Tack Rack, Nairobi.',
        ],
        'Equimins Respiratory Air Power Booster' => [
            'title' => 'Equimins Air Power Booster',
            'desc'  => 'A soothing blend of natural ingredients from Equimins to help with coughs and blocked airways. 1 litre. From Tack Rack, Ngong Road, Nairobi.',
        ],
        'Equimins Garlic Extract Liquid' => [
            'title' => 'Equimins Garlic Extract Liquid',
            'desc'  => 'Concentrated liquid garlic extract from the Equimins nutrition range, in a 1 litre bottle. Stocked at Tack Rack, Ngong Road, Nairobi.',
        ],
        'Equimins Garlic & Mint' => [
            'title' => 'Equimins Garlic & Mint',
            'desc'  => 'A garlic and mint feed supplement from Equimins, supplied by the bag. In stock at Tack Rack, Ngong Road, Nairobi.',
        ],
        'Lincoln Blood Tonic' => [
            'title' => 'Lincoln Blood Tonic',
            'desc'  => 'An iron-rich Lincoln tonic with yucca and kelp to support red blood cells, energy and vitality. Large can or 1 litre. From Tack Rack, Nairobi.',
        ],
        'Back to Basics Immuno Hoof' => [
            'title' => 'Back to Basics Immuno Hoof',
            'desc'  => 'Immuno Hoof from the Back to Basics supplement range, in a 1kg bag. Stocked at Tack Rack, Ngong Road, Nairobi.',
        ],
        'Back to Basics Muscle Up' => [
            'title' => 'Back to Basics Muscle Up',
            'desc'  => 'Muscle Up from the Back to Basics supplement range, in a 1.5kg bag. Stocked at Tack Rack, Ngong Road, Nairobi.',
        ],
        'Electrolytes' => [
            'title' => 'Horse Electrolytes',
            'desc'  => 'Electrolytes with vitamins and minerals to replace salts lost in sweat, added to feed or water. Distributed by Tack Rack, tub or bag.',
        ],
        'Electrolyte Paste Oral Syringe' => [
            'title' => 'Electrolyte Paste Oral Syringe',
            'desc'  => 'Electrolyte paste in a ready-to-use oral syringe, for when a horse will not drink. In stock at Tack Rack, Ngong Road, Nairobi.',
        ],
        'Tamfeeds Horse & Pony Vitamin & Mineral Premix' => [
            'title' => 'Tamfeeds Horse & Pony Premix',
            'desc'  => 'A Tamfeeds vitamin and mineral premix for horses and ponies, included at 2.5kg per ton of feed. In two bag sizes at Tack Rack, Nairobi.',
        ],
        'Epsom Salts' => [
            'title' => 'Epsom Salts',
            'desc'  => 'Epsom salts (magnesium sulphate heptahydrate) — use as required and keep dry. Distributed by Tack Rack, in a tub or a bag. Nairobi.',
        ],
        'Calavite' => [
            'title' => 'Calavite High Calcium Limestone',
            'desc'  => 'Calavite processed high calcium limestone for horses and other animals, with feeding instructions on the label. Distributed by Tack Rack.',
        ],
        'Sodium Bicarbonate' => [
            'title' => 'Sodium Bicarbonate (Bi-Carb)',
            'desc'  => 'Sodium bicarbonate — use as required and keep dry. Distributed by Tack Rack in a tub or a bag, from our shop on Ngong Road, Nairobi.',
        ],
        'Mineral Licks' => [
            'title' => 'Mineral Licks & Stock Lick Mix',
            'desc'  => 'Mineral blocks and stock lick mix for the stable or paddock, including Morendat, Maclik Plus and Ideal Block. From Tack Rack, Nairobi.',
        ],
        'Alltech Yea-Sacc Conc' => [
            'title' => 'Alltech Yea-Sacc Conc',
            'desc'  => 'Yea-Sacc Conc from Alltech, a viable yeast culture for livestock feeds including horses. 1kg bag. In stock at Tack Rack, Nairobi.',
        ],

        // Grooming
        'Sweat Scraper — Colour Range' => [
            'title' => 'Sweat Scrapers, Colour Range',
            'desc'  => 'Curved sweat scrapers with a flexible rubber blade, taking water off after a wash so the horse dries faster. Black, red and purple.',
        ],
        'Moore’s Aloe Vera Shampoo' => [
            'title' => 'Moore’s Aloe Vera Horse Shampoo',
            'desc'  => 'Moore’s Quality aloe vera shampoo for washing horses, in a 1 litre bottle. Stocked at Tack Rack, Ngong Road, Nairobi.',
        ],
        'Endeavon Lemon Shampoo' => [
            'title' => 'Endeavon Lemon Horse Shampoo',
            'desc'  => 'Endeavon lemon shampoo for horses, distributed by Tack Rack, in a large can and two bottle sizes. From our shop on Ngong Road, Nairobi.',
        ],
        'Calro Organic Neem Oil Shampoo & Conditioner' => [
            'title' => 'Calro Neem Oil Shampoo & Conditioner',
            'desc'  => 'Calro organic neem oil shampoo and conditioner with apple fragrance, in 5 litre and 1 litre sizes. Tack Rack label, Ngong Road, Nairobi.',
        ],
        'Equimins Ultra Silky Detangler' => [
            'title' => 'Equimins Ultra Silky Detangler',
            'desc'  => 'A silky Equimins detangling spray that loosens knots in manes and tails before brushing. In stock at Tack Rack, Ngong Road, Nairobi.',
        ],
        'Botanica Mane & Tail 6-in-1 Spray' => [
            'title' => 'Botanica Mane & Tail 6-in-1 Spray',
            'desc'  => 'Botanica’s 750ml 6-in-1 multi-purpose spray: body sheen, dandruff and flaky skin, detangler, insects and wounds. From Tack Rack, Nairobi.',
        ],
        'Gold Label Mane, Coat & Tail Lotion' => [
            'title' => 'Gold Label Mane, Coat & Tail Lotion',
            'desc'  => 'Gold Label show lotion that imparts a quick, healthy shine and helps remove stains, knots and tangles. 500ml spray or 2.5L refill.',
        ],

        // Stable equipment
        'Feed Scoop — Colour Range' => [
            'title' => 'Feed Scoops, Colour Range',
            'desc'  => 'Deep round feed scoops with a long easy-grip handle in black, green, purple, orange and red — one colour per horse. From Tack Rack, Nairobi.',
        ],
        'Rope Haynet — Yellow' => [
            'title' => 'Yellow Rope Haynet',
            'desc'  => 'A knotted rope haynet in yellow with a drawstring top for tying up. In stock at Tack Rack, Ngong Road, Nairobi.',
        ],
        'Flexible Tub — Orange' => [
            'title' => 'Orange Flexible Tub',
            'desc'  => 'A deep, flexible plastic tub with two handles for water, feed or carrying kit — it will not crack if trodden on. From Tack Rack, Nairobi.',
        ],
        'Shallow Feed Tub — Green' => [
            'title' => 'Green Shallow Feed Tub',
            'desc'  => 'A shallow, flexible feed tub with two handles, low enough for a horse to eat from on the ground. In stock at Tack Rack, Nairobi.',
        ],
        'Hydrophane Cribox' => [
            'title' => 'Hydrophane Cribox',
            'desc'  => 'Cribox from Hydrophane prevents crib-biting. Available in 450g and 225g tubs at Tack Rack, Ngong Road, Nairobi.',
        ],

        // Leather care
        'Moore’s Leather Dressing' => [
            'title' => 'Moore’s Leather Dressing',
            'desc'  => 'Moore’s Quality leather dressing that softens and preserves leather, in a 1 litre can. Stocked at Tack Rack, Ngong Road, Nairobi.',
        ],
        'Moore’s Glycerine Leather & Saddle Soap' => [
            'title' => 'Moore’s Glycerine Saddle Soap',
            'desc'  => 'A bar of Moore’s Quality glycerine leather and saddle soap for everyday cleaning of saddles and bridles. From Tack Rack, Nairobi.',
        ],
        'Moore’s Dubbin' => [
            'title' => 'Moore’s Dubbin',
            'desc'  => 'Moore’s Quality dubbin for leather, in a 500ml tub. In stock at Tack Rack, Ngong Road, Nairobi.',
        ],
        'Moore’s Leather Soap' => [
            'title' => 'Moore’s Leather Soap',
            'desc'  => 'Moore’s Quality leather soap in a 500ml tub, for cleaning saddles, bridles and boots. Stocked at Tack Rack, Ngong Road, Nairobi.',
        ],

        // Rider accessories
        'Braided Schooling Whip' => [
            'title' => 'Braided Schooling Whip',
            'desc'  => 'A braided schooling whip with a flexible tip, a rubber grip and a wrist strap. In stock at Tack Rack, Ngong Road, Nairobi.',
        ],
    ],
];
