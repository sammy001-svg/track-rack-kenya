-- =====================================================================
--  TACK RACK KENYA - Seed data
--  Business facts sourced from tackrack-kenya.com (est. 1997, Nairobi).
--  Catalog tree follows the RIDER / HORSE / STABLE structure in the brief.
-- =====================================================================

-- ---------------------------------------------------------------------
--  WHAT THIS RESETS
--
--  Replaced:  the demo catalog — products, images, variants, categories,
--             brands and the written pages.
--
--  Preserved: settings, staff accounts, customers, quotes, orders,
--             bookings, repairs and messages. Those hold real data or
--             configuration, so this file never deletes them. Settings and
--             the admin account are inserted only if missing, which is what
--             makes this file safe to run after the migrations rather than
--             only before them.
-- ---------------------------------------------------------------------

SET NAMES utf8mb4;

-- Disable FK checks so dependent rows can be removed first.
-- Works on shared hosts even without SUPER privilege.
SET FOREIGN_KEY_CHECKS = 0;

-- Clear tables in safe order (children before parents).
DELETE FROM `product_variants`;
DELETE FROM `product_images`;
DELETE FROM `order_items`;     -- references products
DELETE FROM `products`;
DELETE FROM `categories`;
DELETE FROM `brands`;
DELETE FROM `pages`;

-- Reset auto-increment counters (DELETE does not do this automatically).
ALTER TABLE `product_variants` AUTO_INCREMENT = 1;
ALTER TABLE `product_images`   AUTO_INCREMENT = 1;
ALTER TABLE `products`         AUTO_INCREMENT = 1;
ALTER TABLE `categories`       AUTO_INCREMENT = 1;
ALTER TABLE `brands`           AUTO_INCREMENT = 1;
ALTER TABLE `pages`            AUTO_INCREMENT = 1;

SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------------------
--  Admin account   ->  admin@tackrack.co.ke  /  TackRack@2026
--  IGNORE so an existing account, or a changed password, survives.
-- ---------------------------------------------------------------------
INSERT IGNORE INTO `users` (`id`,`name`,`email`,`password_hash`,`role`,`is_active`) VALUES
(1,'Tack Rack Admin','admin@tackrack.co.ke','$2y$10$XAH0rk0QSalvKPYGNt1XUuCP7lXZfdBMWVDnbSNERGNCYlye97SEq','admin',1);

-- ---------------------------------------------------------------------
--  Categories - three pillars
-- ---------------------------------------------------------------------
INSERT INTO `categories` (`id`,`parent_id`,`name`,`slug`,`tagline`,`description`,`sort_order`,`is_active`) VALUES
(1,NULL,'Rider','rider','Rider Apparel & Gear','Everything worn in the saddle - engineered for the arena, the gallop and the long safari ride.',1,1),
(2,NULL,'Horse','horse','Horse Equipment (Tack)','Saddlery and tack selected, fitted and maintained by qualified specialists.',2,1),
(3,NULL,'Stable','stable','Yard, Grooming & Care','The quiet essentials that keep a yard running and leather living for decades.',3,1);

-- RIDER children
INSERT INTO `categories` (`id`,`parent_id`,`name`,`slug`,`tagline`,`description`,`sort_order`,`is_active`) VALUES
(10,1,'Footwear','footwear','Short Boots, Paddock Boots & Chaps','Paddock and short boots paired with half chaps - the working standard for daily riding.',1,1),
(11,1,'Riding Jackets & Vests','riding-jackets-vests','Show & Schooling Outerwear','Competition jackets, softshells and body protectors cut for a correct riding position.',2,1),
(12,1,'Breeches & Tights','breeches-tights','Full Seat, Knee Patch & Performance','Grip, stretch and breathability for schooling in the Kenyan sun.',3,1),
(13,1,'Gloves & Accessories','gloves-accessories','Hands, Head & Detail','Gloves, helmets, crops, spurs, belts and the small things that finish a turnout.',4,1);

-- HORSE children
INSERT INTO `categories` (`id`,`parent_id`,`name`,`slug`,`tagline`,`description`,`sort_order`,`is_active`) VALUES
(20,2,'Saddles & Accessories','saddles-accessories','Jumping, Dressage, GP & Polo','Fitted by the only Society of Master Saddlers qualified fitter in East Africa.',1,1),
(21,2,'Bridles, Bits & Reins','bridles-bits-reins','Contact & Communication','Anatomical bridles, stainless bits and reins in rubber, web and plaited leather.',2,1),
(22,2,'Saddle Pads & Blankets','saddle-pads-blankets','Numnahs, Squares & Rugs','Including numnahs and rugs manufactured in our own Nairobi workshop.',3,1),
(23,2,'Halters & Lead Ropes','halters-lead-ropes','Handling & Turnout','Leather and webbing headcollars with matched lead ropes.',4,1),
(24,2,'Horse Health & Supplements','horse-health-supplements','Feed Balancers & Veterinary Care','Supplements, wound care and electrolytes for hard-working horses.',5,1);

-- STABLE children
INSERT INTO `categories` (`id`,`parent_id`,`name`,`slug`,`tagline`,`description`,`sort_order`,`is_active`) VALUES
(30,3,'Grooming Kits & Supplies','grooming-kits-supplies','Brushes, Combs & Coat Care','Complete kits and individual brushes for a show-ready coat.',1,1),
(31,3,'Stable Equipment','stable-equipment','Yard Hardware','Buckets, mangers, forks, haynets and the fittings that outlast a season.',2,1),
(32,3,'Leather Care & Maintenance','leather-care-maintenance','Preserving the Investment','Balms, soaps and oils - essential in a dry, high-altitude climate.',3,1);

-- ---------------------------------------------------------------------
--  Brands
-- ---------------------------------------------------------------------
INSERT INTO `brands` (`id`,`name`,`slug`,`description`,`sort_order`,`is_active`) VALUES
(1,'Tack Rack Workshop','tack-rack-workshop','Made on site in Nairobi - rugs, numnahs, girths and stirrup leathers, plus full tree repair.',1,1),
(2,'Heritage Saddlery','heritage-saddlery','English-pattern saddlery in vegetable-tanned leather.',2,1),
(3,'Meridian Equestrian','meridian-equestrian','Technical rider apparel for warm-climate schooling.',3,1),
(4,'Kifaru Field','kifaru-field','Hard-wearing yard and safari equipment.',4,1),
(5,'Equilume Care','equilume-care','Leather care, grooming and coat products.',5,1);

-- ---------------------------------------------------------------------
--  Products
-- ---------------------------------------------------------------------
INSERT INTO `products`
(`category_id`,`brand_id`,`name`,`slug`,`sku`,`short_desc`,`description`,`specifications`,`sizing_guide`,`price`,`price_visible`,`stock_status`,`is_featured`,`is_new`,`is_active`,`sort_order`) VALUES

-- --- RIDER / Footwear -------------------------------------------------
(10,2,'Ashdown Paddock Boot','ashdown-paddock-boot','TR-FW-001','Full-grain leather paddock boot with a lace front and cushioned footbed.','A daily-wear paddock boot built on a full-grain leather upper with a reinforced toe box and a rolled topline that sits clear of the ankle. The lace front allows a precise fit under half chaps, and the moulded rubber outsole gives grip on stable yards without collecting mud.','Upper: full-grain cowhide\nLining: breathable textile\nSole: moulded rubber, herringbone tread\nClosure: 7-eyelet lace front\nColours: Black, Havana Brown','Measure the foot flat, heel to longest toe, late in the day. Sizes run EU 34-46. Between sizes, take the larger.',NULL,0,'in_stock',1,0,1,1),
(10,3,'Ridgeway Half Chap','ridgeway-half-chap','TR-FW-002','Suede-panel half chap with elasticated gusset and YKK rear zip.','Cut to sit cleanly against the saddle flap, the Ridgeway pairs a grippy suede inner panel with a smooth leather outer. The elasticated rear gusset accommodates a range of calf shapes, and the sprung zip guard keeps the pull tab from catching on the girth.','Outer: smooth leather\nInner panel: suede\nZip: YKK, rear entry with guard\nGusset: elasticated rear panel\nColours: Black, Brown','Measure calf at the widest point and from the floor to the back of the knee while wearing riding boots. Available S/M/L/XL in regular and tall.',NULL,0,'in_stock',0,1,1,2),
(10,2,'Meru Short Riding Boot','meru-short-riding-boot','TR-FW-003','Zip-front short boot for schooling and yard work.','A short boot with a full-length inner zip for quick changes between the yard and the arena. The shank is reinforced through the arch to hold the stirrup position, and the heel is set at a true riding pitch.','Upper: waxed full-grain leather\nZip: full-length inner YKK\nHeel: 2.5 cm riding pitch\nSole: rubber with steel shank','EU 36-45. Fits true to size with a thin riding sock.',NULL,0,'in_stock',0,0,1,3),

-- --- RIDER / Jackets --------------------------------------------------
(11,3,'Karen Competition Jacket','karen-competition-jacket','TR-AP-010','Four-button show jacket in a breathable technical weave.','Tailored for the show ring with a lengthened rear vent that sits correctly over the saddle. The technical weave holds a crisp line in heat and releases moisture, and the collar is finished with a subtle contrast piping.','Fabric: 92% polyester / 8% elastane technical weave\nLining: mesh back panel\nFront: four-button\nVent: extended centre-rear\nCare: machine wash cold, hang dry','Ladies 8-18, Mens 36-46 chest. Size up if layering a body protector.',NULL,0,'in_stock',1,0,1,1),
(11,3,'Ngong Softshell Riding Vest','ngong-softshell-riding-vest','TR-AP-011','Wind-resistant schooling vest with a high collar.','Built for early mornings on the Ngong hills. The softshell face sheds wind and light rain while the brushed backing holds warmth, and the armholes are cut high and wide so the rein contact is never restricted.','Fabric: 3-layer softshell, wind resistant\nPockets: two zipped hand, one chest\nCollar: high, fleece-backed\nFit: athletic','XS-XXL unisex.',NULL,0,'in_stock',0,0,1,2),

-- --- RIDER / Breeches -------------------------------------------------
(12,3,'Highveld Full-Seat Breech','highveld-full-seat-breech','TR-AP-020','Silicone full-seat breech with a mid-rise waist.','A dressage-oriented breech with a printed silicone full seat that grips without gluing the rider to the saddle. Four-way stretch through the thigh and a flat-locked inner seam keep long schooling sessions comfortable.','Fabric: 72% nylon / 28% elastane, four-way stretch\nSeat: printed silicone full seat\nWaist: mid-rise, wide contoured band\nLeg: sock-bottom with silicone hem','Ladies 24-36. Compression fit - take your usual size.',NULL,0,'in_stock',1,1,1,1),
(12,3,'Rift Knee-Patch Tight','rift-knee-patch-tight','TR-AP-021','Lightweight pull-on riding tight with phone pocket.','A pull-on schooling tight in a light, quick-drying knit with a silicone knee patch. The side pocket takes a phone without flapping at the canter.','Fabric: quick-dry performance knit, UPF 40+\nPatch: silicone knee\nWaist: high-rise elastic\nPockets: two side, one zipped','XS-XL. Take your usual size.',NULL,0,'in_stock',0,0,1,2),

-- --- RIDER / Gloves ---------------------------------------------------
(13,3,'Aiguille Riding Glove','aiguille-riding-glove','TR-AP-030','Perforated summer glove with a touchscreen index finger.','A close-fitting summer glove with perforated back panels for airflow and a synthetic suede palm that keeps rein feel precise when hands are damp.','Material: synthetic suede palm, perforated stretch back\nClosure: hook-and-loop cuff\nFeature: touchscreen index and thumb\nColours: Black, White, Tan','Sizes 6-10. Measure around the knuckles excluding the thumb.',NULL,0,'in_stock',0,0,1,1),
(13,2,'Brass Spur Set','brass-spur-set','TR-AP-031','Solid brass Prince of Wales spurs with leather straps.','Traditional Prince of Wales pattern in solid brass, supplied with matched vegetable-tanned leather straps. A quiet, correct aid for a schooled horse.','Material: solid brass\nNeck: 20 mm / 25 mm / 35 mm\nStraps: vegetable-tanned leather, brass buckle','One size, adjustable strap. Choose neck length by the horse, not the rider.',NULL,0,'in_stock',0,0,1,2),

-- --- HORSE / Saddles --------------------------------------------------
(20,2,'Kilimani General Purpose Saddle','kilimani-general-purpose-saddle','TR-SD-100','A versatile GP saddle in vegetable-tanned leather, professionally fitted.','A general purpose saddle for riders who school, jump and hack the same horse. The tree is set at a moderate forward cut with a medium-deep seat, giving security over fences without locking the rider out of a dressage position. Every saddle we supply is fitted on the horse by Sharon Ashley, the only Saddle Fitter in East Africa qualified with the Society of Master Saddlers.','Tree: spring tree, adjustable head\nSeat: 16.5 - 18 inch\nGullet: narrow to extra-wide (fitted)\nLeather: vegetable-tanned, Havana or Black\nPanels: wool flocked, adjustable\nBillets: three, long-billet option available','Seat size is chosen from the rider; gullet and panel from the horse. Book a fitting before ordering - we travel to the yard.',NULL,0,'in_stock',1,0,1,1),
(20,2,'Athi Jump Saddle','athi-jump-saddle','TR-SD-101','Forward-cut jumping saddle with an external knee block.','A close-contact jumping saddle with a forward flap and a shallow seat that lets the rider stay light over a course. External blocks can be repositioned by our workshop to suit leg length.','Tree: forward-cut spring tree\nSeat: 16.5 - 18 inch\nFlap: forward, long-flap option\nBlocks: external, repositionable\nLeather: calf-lined seat','Fitted to horse and rider. Contact us to arrange an assessment.',NULL,0,'in_stock',0,0,1,2),
(20,2,'Polo Cut-Back Saddle','polo-cut-back-saddle','TR-SD-102','Flat-seat polo saddle built for the Kenyan chukka.','A flat, open seat with minimal blocking so the player can move freely through a chukka. Reinforced at the stress points and supplied with a webbing overgirth.','Tree: flat polo tree\nSeat: 17 - 18.5 inch, open\nGirthing: point and balance straps, overgirth included\nLeather: heavy-grade, undyed or Havana','Sized to the player. Fittings by appointment.',NULL,0,'in_stock',0,0,1,3),
(20,1,'Stirrup Leathers - Workshop Made','stirrup-leathers-workshop-made','TR-SD-103','Hand-cut stirrup leathers made in our Nairobi workshop.','Cut and finished on site from a single hide, with a nylon core to stop the stretch that ruins a rider position. Punched to order in half-inch increments.','Material: single-hide leather, nylon-cored\nWidth: 25 mm\nLengths: 48 in / 54 in / 60 in\nBuckle: stainless steel roller\nMade in: Nairobi, Kenya','Measure an existing pair buckle to end. We can punch to a custom length on request.',NULL,0,'in_stock',1,0,1,4),

-- --- HORSE / Bridles --------------------------------------------------
(21,2,'Anatomic Snaffle Bridle','anatomic-snaffle-bridle','TR-BR-110','Shaped headpiece bridle with a padded, cut-back browband.','An anatomically shaped headpiece relieves pressure behind the ears, and the cut-back browband keeps the crown clear of the base of the ear. Supplied with plain rubber reins.','Leather: vegetable-tanned, Havana or Black\nHeadpiece: anatomically shaped, padded\nNoseband: cavesson or flash\nFittings: stainless steel\nReins: rubber-grip included\nSizes: Pony, Cob, Full, Extra Full','Measure from the corner of the mouth over the poll and back. Cob suits most Kenyan thoroughbreds.',NULL,0,'in_stock',1,0,1,1),
(21,2,'Loose-Ring French Link Snaffle','loose-ring-french-link-snaffle','TR-BR-111','Stainless steel double-jointed snaffle with a lozenge centre.','A double-jointed loose ring that removes the nutcracker action of a single-jointed bit, making it a fair first bit for a young horse.','Material: solid stainless steel\nMouthpiece: 16 mm, French link lozenge\nRing: 70 mm loose ring\nSizes: 4.5 in - 6 in in quarter-inch steps','Measure the horse mouth width with a bit gauge, or bring the current bit in for comparison.',NULL,0,'in_stock',0,0,1,2),
(21,2,'Plaited Leather Rein','plaited-leather-rein','TR-BR-112','Five-plait leather rein with stainless hook studs.','A traditional plaited rein that keeps grip in wet hands without the bulk of rubber. Hook-stud billets change over in seconds.','Material: vegetable-tanned leather, five-plait\nLength: 54 in / 60 in\nWidth: 16 mm\nFittings: stainless hook stud','Full-size horses take 54 in; taller riders or long-necked horses take 60 in.',NULL,0,'in_stock',0,0,1,3),

-- --- HORSE / Pads -----------------------------------------------------
(22,1,'Workshop Numnah - Made to Order','workshop-numnah-made-to-order','TR-PD-120','Shaped numnah cut and stitched in our own workshop.','Cut to the shape of your saddle rather than a generic pattern, with a cotton face, a wool-blend underside and a rolled binding in your choice of contrast. Made on site in Nairobi, typically within a week.','Face: cotton drill\nUnderside: wool-blend felt\nBinding: contrast rolled edge, colour to order\nAttachment: girth and billet straps\nLead time: approx. 7 working days\nMade in: Nairobi, Kenya','Bring the saddle in or send its make, model and seat size so we can cut to shape.',NULL,0,'in_stock',1,1,1,1),
(22,4,'Cotton Dressage Square','cotton-dressage-square','TR-PD-121','Quilted white dressage square with a wicking lining.','A crisp competition square with a diamond quilt and a wicking lining that pulls sweat away from the back. Washes clean without going grey.','Face: cotton, diamond quilt\nLining: wicking poly-mesh\nSize: Full, Cob\nColours: White, Navy, Black','Full fits most 16 hh and over; Cob for narrower or shorter backs.',NULL,0,'in_stock',0,0,1,2),
(22,1,'Lightweight Turnout Rug','lightweight-turnout-rug','TR-PD-122','Ripstop turnout rug with a shoulder gusset, made on site.','A no-fill turnout for the highland climate - waterproof and breathable, with a pleated shoulder gusset that lets the horse graze without rubbing. Repairs and re-proofing done in our workshop.','Outer: 600D ripstop, waterproof and breathable\nFill: none (lightweight)\nLining: polyester taffeta\nFittings: twin chest straps, crossed surcingles, leg straps\nSizes: 5 ft 3 in - 7 ft','Measure from the centre of the chest along the body to the point of the buttock.',NULL,0,'in_stock',0,0,1,3),

-- --- HORSE / Halters --------------------------------------------------
(23,2,'Leather Headcollar','leather-headcollar','TR-HL-130','Padded leather headcollar with a brass nameplate option.','A traditional leather headcollar with padded nose and headpiece, brass fittings and the option of an engraved nameplate fitted in our workshop.','Leather: vegetable-tanned\nPadding: nose and headpiece\nFittings: solid brass\nOptional: engraved brass nameplate\nSizes: Pony, Cob, Full','Cob fits the majority of horses in Kenya. Adjustable at the cheek and nose.',NULL,0,'in_stock',0,0,1,1),
(23,4,'Webbing Lead Rope with Brass Clip','webbing-lead-rope-brass-clip','TR-HL-131','Soft-hand webbing lead with a heavy brass trigger clip.','A 2 m lead in soft webbing that will not burn the hand if a horse pulls away, finished with a heavy brass trigger clip.','Material: soft polypropylene webbing\nLength: 2 m\nClip: heavy brass trigger\nColours: Navy, Green, Burgundy, Black','One size.',NULL,0,'in_stock',0,0,1,2),

-- --- HORSE / Health ---------------------------------------------------
(24,5,'Electrolyte Replacement Powder','electrolyte-replacement-powder','TR-HH-140','Daily electrolyte for horses working in heat and altitude.','Replaces the sodium, potassium and chloride lost through sweat - relevant year-round at Nairobi altitude and essential during racing and polo seasons.','Form: powder\nSize: 2 kg tub\nFeeding: 30 g daily, 60 g on hard-work days\nComposition: sodium chloride, potassium chloride, magnesium, dextrose','Feed in damp feed. Ensure constant access to fresh water.',NULL,0,'in_stock',0,0,1,1),
(24,5,'Hoof Conditioning Balm','hoof-conditioning-balm','TR-HH-141','Lanolin-rich balm for dry, cracking hooves.','A lanolin and pine tar balm for hooves that dry and crack in the long dry season. Painted on the wall and coronet band daily.','Size: 1 kg tub\nActive: lanolin, pine tar, beeswax\nApplication: brush daily to wall and coronet','Apply to a clean, dry hoof. Supplied with a brush.',NULL,0,'in_stock',0,0,1,2),

-- --- STABLE / Grooming ------------------------------------------------
(30,5,'Complete Grooming Kit','complete-grooming-kit','TR-GR-150','Nine-piece grooming kit in a fitted canvas tote.','A complete kit for one horse: body brush, dandy brush, water brush, curry comb, mane comb, hoof pick, sweat scraper, sponge and stable rubber, in a canvas tote that stands up on the yard floor.','Contents: 9 pieces\nBrushes: natural bristle body brush, stiff dandy\nTote: waxed canvas, reinforced base\nColours: Olive, Navy','One kit per horse is recommended to limit skin infection between animals.',NULL,0,'in_stock',1,0,1,1),
(30,5,'Natural Bristle Body Brush','natural-bristle-body-brush','TR-GR-151','Beechwood body brush with a leather hand strap.','A soft natural-bristle brush on a beechwood back, used with a curry comb to lift dust and lay the coat.','Back: beechwood\nBristle: natural, soft\nStrap: leather, adjustable\nSize: 200 mm','One size.',NULL,0,'in_stock',0,0,1,2),

-- --- STABLE / Equipment -----------------------------------------------
(31,4,'Heavy-Duty Stable Bucket','heavy-duty-stable-bucket','TR-SE-160','20 litre flat-back bucket with a galvanised handle.','A flat-back bucket that sits tight against the wall and will not swing. UV-stabilised so it does not go brittle in the sun.','Capacity: 20 litres\nMaterial: UV-stabilised polyethylene\nHandle: galvanised steel\nColours: Navy, Green, Red','One size.',NULL,0,'in_stock',0,0,1,1),
(31,4,'Small-Hole Haynet','small-hole-haynet','TR-SE-161','Slow-feed haynet in braided polypropylene.','Small holes slow the intake so a hard feed lasts the night and the horse is not standing empty by morning.','Material: braided polypropylene\nHole: 45 mm\nCapacity: approx. 6 kg\nLength: 100 cm','Hang at wither height, no lower, to keep legs clear.',NULL,0,'in_stock',0,1,1,2),

-- --- STABLE / Leather care --------------------------------------------
(32,5,'Leather Balm','leather-balm','TR-LC-170','Beeswax and neatsfoot balm for tack in a dry climate.','The single most important product for tack in Kenya. A beeswax and neatsfoot balm that feeds the fibre and holds off the cracking that altitude and dry air cause in a season.','Size: 500 ml tin\nActive: beeswax, neatsfoot oil, lanolin\nUse: apply monthly, more often on new leather\nFinish: soft satin, non-greasy','Clean with saddle soap first, then apply thinly with a cloth and leave overnight.',NULL,0,'in_stock',1,0,1,1),
(32,5,'Glycerine Saddle Soap','glycerine-saddle-soap','TR-LC-171','Traditional glycerine bar soap for daily tack cleaning.','A traditional glycerine bar used with a damp sponge to lift sweat and grease without stripping the leather.','Size: 250 g bar\nType: glycerine, bar\nUse: damp sponge, daily after riding','Use sparingly with a barely damp sponge - too much water is what damages leather.',NULL,0,'in_stock',0,0,1,2),
(32,1,'Tack Repair & Tree Repair Service','tack-repair-tree-repair-service','TR-LC-172','In-house repair of saddles, bridles and broken trees.','Our Nairobi workshop repairs what most suppliers replace - broken saddle trees, torn panels, restitched bridles, replacement billets and re-flocking. Bring the item in or send photographs and we will quote before any work begins.','Services: tree repair, re-flocking, panel repair, restitching, billet replacement, nameplate engraving\nTurnaround: quoted per job\nLocation: MacNaughton Centre, Ngong Road, Nairobi','Send photographs of the damage with your quote request for a faster assessment.',NULL,0,'in_stock',1,0,1,3);

-- ---------------------------------------------------------------------
--  Variants (sizes offered on the quote form)
-- ---------------------------------------------------------------------
INSERT INTO `product_variants` (`product_id`,`label`,`value`,`sort_order`)
SELECT p.id, 'Size', v.val, v.ord FROM `products` p
JOIN (SELECT '16.5 in' AS val, 1 AS ord UNION ALL SELECT '17 in',2 UNION ALL SELECT '17.5 in',3 UNION ALL SELECT '18 in',4) v
WHERE p.slug IN ('kilimani-general-purpose-saddle','athi-jump-saddle','polo-cut-back-saddle');

INSERT INTO `product_variants` (`product_id`,`label`,`value`,`sort_order`)
SELECT p.id, 'Size', v.val, v.ord FROM `products` p
JOIN (SELECT 'Pony' AS val, 1 AS ord UNION ALL SELECT 'Cob',2 UNION ALL SELECT 'Full',3 UNION ALL SELECT 'Extra Full',4) v
WHERE p.slug IN ('anatomic-snaffle-bridle','leather-headcollar');

INSERT INTO `product_variants` (`product_id`,`label`,`value`,`sort_order`)
SELECT p.id, 'Size', v.val, v.ord FROM `products` p
JOIN (SELECT 'XS' AS val, 1 AS ord UNION ALL SELECT 'S',2 UNION ALL SELECT 'M',3 UNION ALL SELECT 'L',4 UNION ALL SELECT 'XL',5) v
WHERE p.slug IN ('ridgeway-half-chap','karen-competition-jacket','ngong-softshell-riding-vest','highveld-full-seat-breech','rift-knee-patch-tight');

-- ---------------------------------------------------------------------
--  Settings
-- ---------------------------------------------------------------------
INSERT INTO `settings` (`key_name`,`value`,`group_name`,`label`,`input_type`,`sort_order`) VALUES
('site_name','Tack Rack','general','Site name','text',1),
('site_tagline','Premium Equestrian Gear. Trusted Heritage.','general','Hero tagline','text',2),
('site_intro','Kenya''s equestrian supplier since 1997. Saddlery, rider apparel and yard essentials - selected, fitted and maintained by specialists.','general','Short introduction','textarea',3),
('founded_year','1997','general','Year founded','text',4),
('contact_phone','+254 722 763 279','contact','Primary phone','tel',1),
('contact_phone_alt','+254 736 978 963','contact','Secondary phone','tel',2),
('contact_email','sales1997@tackrack.co.ke','contact','Email address','email',3),
('contact_address','MacNaughton Business Centre, Ngong Road','contact','Street address','text',4),
('contact_postal','P.O. Box 57, Karen 00502, Nairobi, Kenya','contact','Postal address','text',5),
('contact_hours','Monday - Friday 8:30am - 5:30pm | Saturday 9:00am - 1:00pm','contact','Opening hours','text',6),
('whatsapp_number','254722763279','contact','WhatsApp number (digits only)','text',7),
('map_embed','','contact','Google Maps embed URL','url',8),
('social_facebook','https://www.facebook.com/Tackrack/','social','Facebook URL','url',1),
('social_instagram','','social','Instagram URL','url',2),
('social_youtube','','social','YouTube URL','url',3),
('quote_recipient','sales1997@tackrack.co.ke','quotes','Quote notification email','email',1),
('quote_prefix','TR','quotes','Quote reference prefix','text',2),
('show_prices','0','quotes','Show prices publicly (1 = yes, 0 = quote only)','text',3);

-- ---------------------------------------------------------------------
--  CMS pages
-- ---------------------------------------------------------------------
INSERT INTO `pages` (`slug`,`title`,`subtitle`,`body`,`meta_desc`,`is_active`) VALUES
('heritage','About Our Heritage','Kenya''s equestrian supplier since 1997.',
'<p>Tack Rack Limited was founded in 1997 and has served as Kenya''s primary equestrian supplier ever since. We are based at the MacNaughton Business Centre off Ngong Road in Nairobi &mdash; a bright, accessible premises with parking at the door.</p>
<h3>Every discipline, properly equipped</h3>
<p>We stock equipment and supplements across every riding discipline practised in Kenya: racing, polo, showjumping, dressage, hacking and safari riding. Our staff ride, and they will tell you plainly what a horse actually needs rather than what is most expensive on the shelf.</p>
<h3>Saddle fitting by a qualified specialist</h3>
<p>Sharon Ashley is the only Saddle Fitter in East Africa qualified with the Society of Master Saddlers. A saddle that does not fit will damage a horse''s back long before the rider notices, which is why every saddle we supply is fitted on the horse &mdash; and why we travel to yards across the country to do it.</p>
<h3>Our own workshop</h3>
<p>Behind the shop is a working saddlery. We repair what others replace: broken trees, torn panels, worn billets and restitching. We also manufacture on site &mdash; rugs, numnahs, girths and stirrup leathers, cut to your horse rather than to a generic pattern.</p>
<h3>What we are for</h3>
<p>Our objective has not changed since 1997: to provide the equestrian community with good quality products at an affordable price.</p>',
'Tack Rack Limited, founded 1997, is Kenya''s primary equestrian supplier - saddlery, rider apparel and Society of Master Saddlers qualified saddle fitting in Nairobi.',1),

('how-to-order','How to Order','From catalog to yard, in four steps.',
'<ol>
<li><strong>Browse the catalog.</strong> Work through Rider, Horse and Stable, or filter by category and brand to narrow the field.</li>
<li><strong>Build a quote list.</strong> Add each item you are interested in, with the size or specification you need. Nothing is charged and nothing is committed at this stage.</li>
<li><strong>Send the request.</strong> Give us your contact details and any sizing notes. You will receive a reference number immediately.</li>
<li><strong>We respond with a quote.</strong> Usually within one working day, with current pricing, availability and lead times on anything we need to order in.</li>
</ol>
<h3>Saddles and fitted items</h3>
<p>Saddles are never sold blind. Send a quote request and we will arrange a fitting &mdash; at the shop or at your yard.</p>
<h3>Delivery</h3>
<p>We deliver across Nairobi and dispatch countrywide by courier. Delivery cost is confirmed in your quote before you commit.</p>',
'How to order from Tack Rack Kenya - browse the catalog, build a quote list, and receive a full quote within one working day.',1),

('quote-process','The Quote Process','Why we quote rather than publish a checkout price.',
'<p>Equestrian equipment is not a fixed-price commodity. A saddle depends on the horse, a rug depends on the measurement, and imported stock moves with freight and duty. Quoting lets us give you an accurate figure rather than a placeholder.</p>
<h3>What a quote includes</h3>
<ul>
<li>Current price per item, in Kenyan Shillings</li>
<li>Availability &mdash; in stock, or the lead time to bring it in</li>
<li>Sizing or fitting notes where relevant</li>
<li>Delivery cost to your location</li>
<li>Validity period for the pricing</li>
</ul>
<h3>How long it takes</h3>
<p>Most quotes are returned within one working day. Items requiring a fitting or a workshop assessment may take longer, and we will tell you so when we acknowledge the request.</p>
<h3>No obligation</h3>
<p>A quote request is an enquiry, not an order. Nothing is reserved and nothing is charged until you confirm.</p>',
'How the Tack Rack quote process works - accurate pricing, availability and lead times, usually returned within one working day.',1),

('privacy-policy','Privacy Policy','How we handle your information.',
'<p>This policy explains what we collect when you use this website and what we do with it.</p>
<h3>What we collect</h3>
<p>When you send a quote request or a message we collect your name, email address, telephone number, location and any notes you provide. We also record the items on your quote list and the IP address the request came from.</p>
<h3>Why we collect it</h3>
<p>Solely to prepare and deliver your quote, to answer your enquiry, and to keep a record of the transaction. We do not sell or rent your information to anyone.</p>
<h3>How long we keep it</h3>
<p>Quote records are retained for as long as needed to service the customer relationship and to meet Kenyan accounting requirements.</p>
<h3>Cookies</h3>
<p>This site uses a session cookie to remember the contents of your quote list while you browse. It expires when you close your browser or submit your request.</p>
<h3>Your rights</h3>
<p>Under the Data Protection Act, 2019, you may request a copy of the personal data we hold about you, ask us to correct it, or ask us to delete it. Write to sales1997@tackrack.co.ke.</p>',
'Tack Rack Kenya privacy policy - what we collect through quote requests, why, and your rights under the Data Protection Act 2019.',1),

('terms-of-service','Terms of Service','The terms on which we supply.',
'<h3>Quotes</h3>
<p>A quote issued by Tack Rack Limited is an invitation to treat, not a binding offer. Pricing is valid for the period stated on the quote and is subject to stock availability at the time of confirmation.</p>
<h3>Pricing and currency</h3>
<p>All prices are quoted in Kenyan Shillings and are subject to VAT where applicable. Imported items may be re-quoted if freight, duty or exchange rates move materially before an order is confirmed.</p>
<h3>Orders and payment</h3>
<p>An order is formed when you confirm a quote in writing and we accept it. Special-order and made-to-measure items require a deposit before work begins.</p>
<h3>Fitted goods</h3>
<p>Saddles supplied following a professional fitting are fitted to a specific horse. Where a horse''s condition changes, we will re-assess the fit; adjustment and re-flocking are chargeable services.</p>
<h3>Returns</h3>
<p>Unused stock items in original condition may be returned within 14 days of delivery. Made-to-order items, workshop-manufactured goods and supplements are not returnable unless faulty.</p>
<h3>Liability</h3>
<p>Equestrian activity carries inherent risk. Equipment is supplied on the basis that it is inspected, fitted and maintained by the user. Our liability is limited to the value of the goods supplied.</p>
<h3>Governing law</h3>
<p>These terms are governed by the laws of Kenya.</p>',
'Tack Rack Kenya terms of service covering quotes, pricing, orders, fitted goods, returns and liability.',1);
