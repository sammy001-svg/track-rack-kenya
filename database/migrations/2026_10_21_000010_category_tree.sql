-- =====================================================================
--  The category tree — four departments, their sections, and the
--  sub-categories beneath them.
--
--  Generated 2026-10-21 14:53 by bin/export-category-tree-sql.php. Do not edit by hand.
--
--  Additive and idempotent. Categories are matched on slug, so rows that
--  already exist keep their ids and nothing that points at them is
--  disturbed: orders, quotes and their line items are untouched, as are
--  users, settings, pages, bookings and repairs.
--
--  Nothing is deleted. A category that is not part of this tree is hidden
--  by clearing is_active, so it drops out of the navigation while its rows
--  and history remain.
-- =====================================================================

SET NAMES utf8mb4;

-- ---------------------------------------------------------------------
--  The meta columns arrive with an earlier migration rather than with
--  schema.sql. Add them if this database has never had them, in the same
--  guarded form the other migrations use.
-- ---------------------------------------------------------------------
SET @has := (SELECT COUNT(*) FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'categories' AND COLUMN_NAME = 'meta_title');
SET @sql := IF(@has = 0,
  'ALTER TABLE `categories`
     ADD COLUMN `meta_title` VARCHAR(190) NULL AFTER `description`,
     ADD COLUMN `meta_desc`  VARCHAR(300) NULL AFTER `meta_title`',
  'SELECT "categories meta columns already present"');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- ---------------------------------------------------------------------
--  1. Every category in the tree (76), matched on slug.
--     parent_id is deliberately left alone here and set below, because a
--     parent may not exist yet on the first pass.
-- ---------------------------------------------------------------------
INSERT INTO `categories`
  (`slug`, `name`, `tagline`, `description`, `meta_title`, `meta_desc`, `image`, `sort_order`, `is_active`)
VALUES
  ('rider', 'Rider', 'Rider Apparel & Gear', 'Everything worn in the saddle - engineered for the arena, the gallop and the long safari ride.', 'Rider Apparel & Riding Gear', 'Riding boots, breeches, jackets, gloves and helmets for every discipline. Rider apparel from Tack Rack, Nairobi, with expert advice on fit.', NULL, 1, 1),
  ('clothing', 'Clothing', 'Jodhpurs, Boots & Chaps', 'Breeches and jodhpurs, jodhpur boots, half chaps and gaiters, hat silks and competition numbers.', 'Riding Clothing, Jodhpurs & Chaps', 'Breeches and jodhpurs, jodhpur boots, half chaps, gaiters and hat silks. Sized in person at Tack Rack, Ngong Road, Nairobi.', NULL, 1, 1),
  ('safety-equipment', 'Safety Equipment', 'Helmets & Body Protectors', 'Riding hats, skull caps and BETA-certified body protectors — fitted in person, because kit that moves cannot do its job.', 'Riding Helmets & Body Protectors', 'Riding hats, skull caps and BETA 2018 body protectors, fitted in person at Tack Rack, Ngong Road, Nairobi.', NULL, 2, 1),
  ('whips', 'Whips', 'Schooling, Lunge & Short', 'Dressage and schooling whips, lunge whips and short whips, balanced to carry without moving the hand.', 'Riding Whips & Lunge Whips', 'Dressage and schooling whips, lunge whips and short whips, balanced to reach without moving the hand. Stocked in Nairobi.', NULL, 3, 1),
  ('saddles', 'Saddles', 'Fitted on the Horse', 'Leather and synthetic saddles for every discipline, fitted on the horse by our Society of Master Saddlers qualified fitter.', 'Saddles & Saddle Fitting, Nairobi', 'Leather and synthetic saddles fitted on the horse by the only Society of Master Saddlers qualified fitter in East Africa.', NULL, 1, 1),
  ('horse', 'Horse', 'Horse Equipment (Tack)', 'Saddlery and tack selected, fitted and maintained by qualified specialists.', 'Horse Tack & Saddlery', 'Saddles, bridles, bits, reins, numnahs and rugs. Professionally fitted saddlery in Nairobi from Kenya\'s equestrian supplier since 1997.', NULL, 2, 1),
  ('bridles-reins', 'Bridles & Reins', 'Leather & Webbed', 'Leather snaffle bridles supplied with reins, plus webbed, rubber and leather reins and schooling draw reins.', 'Bridles & Reins', 'Leather snaffle bridles supplied with reins, plus webbed, rubber and leather reins and draw reins. From Tack Rack, Nairobi.', NULL, 2, 1),
  ('bits-accessories', 'Bits & Accessories', 'Snaffles, Gags & Guards', 'Loose ring and jointed snaffles, lozenge and training bits, and the guards and keepers that go with them.', 'Horse Bits & Bit Accessories', 'Loose ring and jointed snaffles, copper lozenge and training bits, and rubber bit guards. Bitting advice included, Nairobi.', NULL, 3, 1),
  ('martingales-stirrups-leathers', 'Martingales, Stirrups & Leathers', 'Irons, Treads & Straps', 'Stirrup irons including safety and composite patterns, rubber treads, stirrup leathers and martingales.', 'Stirrups, Leathers & Martingales', 'Stirrup irons including safety and composite patterns, rubber treads, stirrup leathers and martingales. Stocked in Nairobi.', NULL, 4, 1),
  ('girths', 'Girths', 'Fleece, Elastic & Dressage', 'Fleece lined, elastic, anti-chafe and short dressage girths, with leather buckle guards to protect the saddle flap.', 'Horse Girths', 'Fleece lined, elastic, anti-chafe and short dressage girths, with leather buckle guards. From Tack Rack, Ngong Road, Nairobi.', NULL, 5, 1),
  ('numnahs-saddle-pads', 'Numnahs & Saddle Pads', 'Shaped, Square & Non-Slip', 'Shaped GP numnahs, dressage squares, non-slip and Prolite pads, and fleece blankets.', 'Numnahs, Saddlepads & Blankets', 'Shaped GP numnahs, dressage squares, non-slip and Prolite pads and fleece blankets. Stocked at Tack Rack, Ngong Road, Nairobi.', NULL, 6, 1),
  ('headcollars-lead-ropes', 'Headcollars & Lead Ropes', 'Foal, Pony, Cob & Full', 'Nylon and fleece-lined headcollars, lead ropes, lunge cavessons and lunge reins for the yard and the lorry.', 'Headcollars & Lead Ropes', 'Nylon and fleece-lined headcollars, lead ropes, lunge cavessons and lunge reins. From Tack Rack, Ngong Road, Nairobi.', NULL, 7, 1),
  ('horse-boots', 'Horse Boots', 'Brushing, Overreach & Bandages', 'Brushing and overreach boots, fetlock rings and temporary shoe boots, with bandages, leg pads and cohesive wrap.', 'Horse Boots & Bandages', 'Brushing and overreach boots, fetlock rings, temporary shoe boots, bandages, leg pads and cohesive wrap. Stocked in Nairobi.', NULL, 8, 1),
  ('fly-repellent', 'Fly Repellent', 'Sprays, Masks & Traps', 'Fly and midge repellent sprays, fine mesh fly masks, and outdoor fly traps and bait for the yard.', 'Fly Repellent, Masks & Traps', 'Fly and midge repellent sprays, fine mesh fly masks and outdoor fly traps for horses and yards. Stocked at Tack Rack, Nairobi.', NULL, 1, 1),
  ('joint-muscle-care', 'Joint & Muscle Care', 'Supplements, Gels & Clays', 'Joint supplements and the gels, clays and creams that go on afterwards — devils claw, arnica, MSM and cooling clay.', 'Joint & Muscle Care for Horses', 'Joint supplements with the gels, clays and creams that go on afterwards — devils claw, arnica, MSM and cooling clay. In Nairobi.', NULL, 2, 1),
  ('gut-health', 'Gut Health', 'Balancers & Soothers', 'Gut balancers, soothers and yeast cultures for horses that need settling from the inside.', 'Equine Digestive Supplements', 'Digestive balancers, soothers and yeast cultures for horses that need settling from the inside. From Tack Rack, Nairobi.', NULL, 3, 1),
  ('yard', 'Yard', 'Yard, Grooming & Care', 'The quiet essentials that keep a yard running and leather living for decades.', 'Yard, Grooming & Leather Care', 'Shampoos, fly repellents, supplements, hoof care, grooming kit and leather care for the Kenyan yard. From Tack Rack, Nairobi.', NULL, 4, 1),
  ('calming', 'Calming', 'Focus & Temperament', 'Powders, solutions and pastes to take the edge off a nervous or moody horse without dulling it.', 'Calming Supplements for Horses', 'Powders, solutions and pastes that take the edge off a nervous or moody horse without dulling it. Stocked in Nairobi.', NULL, 4, 1),
  ('feed-supplements', 'Feed Supplements', 'Vitamins, Minerals & Electrolytes', 'Electrolytes, vitamin and mineral premixes, biotin, garlic, limestone, salts and licks for the feed room.', 'Horse Feed Supplements', 'Electrolytes, vitamin and mineral premixes, biotin, garlic, limestone, salts and licks for the feed room. From Tack Rack, Nairobi.', NULL, 5, 1),
  ('yard-equipment', 'Yard Equipment', 'Buckets, Haynets & Tubs', 'Feed buckets and tubs, haynets and the everyday kit that keeps a yard running.', 'Yard & Stable Equipment', 'Feed buckets and tubs, haynets and the everyday kit that keeps a yard running. Chosen to survive Kenyan yards, stocked in Nairobi.', NULL, 6, 1),
  ('shoeing-equipment', 'Shoeing Equipment', 'Shoes, Nails & Tools', 'What goes under the hoof and the tools that put it there: shoes, nails and the farrier\'s own kit.', 'Shoeing Equipment', 'Steel and aluminium shoes, farrier nails and the tools that put them on. From Tack Rack, Ngong Road, Nairobi.', NULL, 7, 1),
  ('hoof-oils-dressings', 'Oils & Dressings', 'Daily Conditioning', 'Hoof oils, moisturisers and dressings brushed on to keep the wall and sole in condition.', 'Hoof Oils & Dressings', 'Hoof oils, moisturisers and dressings brushed on to keep the wall and sole in condition. From Tack Rack, Ngong Road, Nairobi.', NULL, 1, 1),
  ('frog-sole-care', 'Frog & Sole Care', 'Cleansers, Pastes & Paints', 'Cleansers, clay putties and sole paints for the frog and the underside of the hoof.', 'Frog & Sole Care', 'Cleansers, clay putties and sole paints for the frog and the underside of the hoof. Stocked at Tack Rack, Ngong Road, Nairobi.', NULL, 2, 1),
  ('hoof-repair', 'Hoof Repair', 'Cavities, Cracks & Nail Holes', 'Waxes and compounds that seal cavities, rebuild missing horn and repair damage around nail holes.', 'Hoof Repair & Nail Hole Care', 'Keratex waxes and compounds that seal cavities, rebuild missing horn and repair damage around nail holes. In stock in Nairobi.', NULL, 3, 1),
  ('poultice-treatment-boots', 'Poultices & Treatment Boots', 'Soaking & Dressing', 'Ready-made poultices, and the rigid and fabric boots that hold a dressing on the hoof.', 'Poultices & Treatment Boots', 'Ready-made hoof poultices and the rigid and fabric boots that hold a dressing on the hoof. From Tack Rack, Ngong Road, Nairobi.', NULL, 4, 1),
  ('hoof-supplements', 'Hoof Supplements', 'Biotin, Fed Daily', 'Biotin and the hoof supplements fed in the feed to improve horn quality from the inside out.', 'Hoof & Coat Supplements', 'Biotin and the supplements fed to improve hoof horn and coat condition from the inside. From Tack Rack, Ngong Road, Nairobi.', NULL, 6, 1),
  ('breeches-jodhpurs', 'Breeches & Jodhpurs', 'Show & Schooling', 'Show breeches, schooling jodhpurs and full-seat tights, sized in person at the shop.', 'Breeches & Jodhpurs', 'Show breeches, schooling jodhpurs and full-seat tights, sized in person at Tack Rack, Ngong Road, Nairobi.', NULL, 1, 1),
  ('footwear-chaps', 'Footwear & Chaps', 'Boots, Chaps & Gaiters', 'Jodhpur boots, half chaps and leather gaiters, fitted to the foot and the calf.', 'Jodhpur Boots, Chaps & Gaiters', 'Jodhpur boots, half chaps and leather gaiters, fitted to the foot and the calf at Tack Rack, Ngong Road, Nairobi.', NULL, 2, 1),
  ('hat-silks-numbers', 'Hat Silks & Numbers', 'Finishing the Turnout', 'Hat silks and competition number holders — the small things that finish a turnout.', 'Hat Silks & Competition Numbers', 'Hat silks and competition number holders — the small things that finish a turnout. From Tack Rack, Ngong Road, Nairobi.', NULL, 3, 1),
  ('riding-helmets', 'Riding Helmets', 'Velvet & Vented', 'Velvet show hats and vented schooling helmets, fitted on the head before you ride in them.', 'Riding Helmets & Show Hats', 'Velvet show hats and vented schooling helmets, fitted on the head at Tack Rack, Ngong Road, Nairobi. Never ride in a hat you have not worn.', NULL, 1, 1),
  ('skull-caps', 'Skull Caps', 'Cross Country & Racing', 'Jockey skull caps, worn with a silk for cross country and racing.', 'Jockey Skull Caps', 'Jockey skull caps for cross country and racing, worn with a silk. Fitted in person at Tack Rack, Ngong Road, Nairobi.', NULL, 2, 1),
  ('body-protectors', 'Body Protectors', 'BETA Level 3', 'BETA 2018 Level 3 body protectors for cross country and eventing, fitted to the torso.', 'Body Protectors, BETA Level 3', 'BETA 2018 Level 3 body protectors for cross country and eventing, fitted to the torso at Tack Rack, Ngong Road, Nairobi.', NULL, 3, 1),
  ('saddles-wintec', 'Wintec', 'Synthetic, Changeable Gullet', 'Wintec synthetic saddles with changeable gullets — washable, light and suited to the climate.', 'Wintec Saddles', 'Wintec synthetic saddles with changeable gullets, fitted on your horse by the only Society of Master Saddlers qualified fitter in East Africa.', NULL, 1, 1),
  ('saddles-jeffries', 'Jeffries', 'English Leather', 'Jeffries English leather saddles, hand finished and fitted on the horse.', 'Jeffries Saddles', 'Jeffries English leather saddles, hand finished and fitted on your horse by our Society of Master Saddlers qualified fitter in Nairobi.', NULL, 2, 1),
  ('saddles-thorowgood', 'Thorowgood', 'Soft Leather & Synthetic', 'Thorowgood saddles, soft in the hand and forgiving on a changing back.', 'Thorowgood Saddles', 'Thorowgood saddles in soft leather and synthetic, fitted on your horse by our Society of Master Saddlers qualified fitter in Nairobi.', NULL, 3, 1),
  ('bridles', 'Bridles', 'Black & Havana', 'Leather snaffle bridles in black and havana, supplied with a pair of reins.', 'Leather Snaffle Bridles', 'Leather snaffle bridles in black and havana, supplied with reins. Sized pony, cob and full at Tack Rack, Ngong Road, Nairobi.', NULL, 1, 1),
  ('reins', 'Reins', 'Leather, Rubber & Draw', 'Leather, rubber and webbed reins, and schooling draw reins.', 'Reins & Draw Reins', 'Leather, rubber and webbed reins, and schooling draw reins. In stock at Tack Rack, Ngong Road, Nairobi.', NULL, 2, 1),
  ('bits', 'Bits', 'Snaffles, Lozenges & Training', 'Loose ring and jointed snaffles, copper lozenge bits and training bits.', 'Snaffle & Training Bits', 'Loose ring and jointed snaffles, copper lozenge bits and training bits. In stock at Tack Rack, Ngong Road, Nairobi.', NULL, 1, 1),
  ('bit-accessories', 'Bit Guards & Keepers', 'Protecting the Mouth', 'Rubber bit guards and the keepers that stop a bit rubbing the corners of the mouth.', 'Bit Guards & Keepers', 'Rubber bit guards that stop a bit rubbing the corners of the mouth. In stock at Tack Rack, Ngong Road, Nairobi.', NULL, 2, 1),
  ('martingales', 'Martingales & Guards', 'Bib, Running & Rubber', 'Bib and running martingales, with the rubber guards that keep girth buckles off the saddle flap.', 'Martingales & Rubber Guards', 'Bib and running martingales, with the rubber guards that keep girth buckles off the saddle flap. From Tack Rack, Nairobi.', NULL, 1, 1),
  ('stirrup-irons', 'Stirrup Irons', 'Steel, Safety & Composite', 'Stainless steel, peacock safety and lightweight composite stirrup irons.', 'Stirrup Irons', 'Stainless steel, peacock safety and lightweight composite stirrup irons. In stock at Tack Rack, Ngong Road, Nairobi.', NULL, 2, 1),
  ('stirrup-treads', 'Stirrup Treads', 'Sold in Pairs', 'Rubber treads that grip the boot, sold in pairs to replace worn or missing ones.', 'Rubber Stirrup Treads', 'Rubber treads that grip the boot, sold in pairs to replace worn or missing ones. From Tack Rack, Ngong Road, Nairobi.', NULL, 3, 1),
  ('dressage-girths', 'Dressage', 'Short, 22\" to 30\"', 'Short girths for a dressage saddle, 22 to 30 inches, so the buckles sit clear of the leg.', 'Short Dressage Girths', 'Short girths for a dressage saddle, 22 to 30 inches, in brown and black. In stock at Tack Rack, Ngong Road, Nairobi.', NULL, 1, 1),
  ('girths-anti-chafe', 'Anti-Chafe', 'Padded Behind the Elbow', 'Girths shaped and padded to sit clear of the elbow on a horse that rubs.', 'Anti-Chafe Girths', 'Girths shaped and padded to sit clear of the elbow on a horse that rubs. From Tack Rack, Ngong Road, Nairobi.', NULL, 2, 1),
  ('girths-fleece-lined', 'Fleece-Lined', 'Soft Against the Skin', 'Girths lined with fleece, for thin skin and for horses in hard work.', 'Fleece-Lined Girths', 'Girths lined with fleece, for thin skin and for horses in hard work. In stock at Tack Rack, Ngong Road, Nairobi.', NULL, 3, 1),
  ('girths-borghey', 'Borghey', 'Long, 40\" to 50\"', 'Hard-wearing black girths in the Borghey pattern, cut long for a general purpose saddle.', 'Borghey Girths', 'Hard-wearing black girths in the Borghey pattern, 40 to 50 inches. In stock at Tack Rack, Ngong Road, Nairobi.', NULL, 4, 1),
  ('girths-elastic', 'Elastic', 'Give at the Buckle', 'Girths with elastic ends, so the horse can fill its chest without the girth fighting it.', 'Elastic Girths', 'Girths with elastic ends, so a horse can fill its chest without the girth fighting it. From Tack Rack, Nairobi.', NULL, 5, 1),
  ('brushing-boots', 'Brushing Boots', 'Pony, Cob & Full', 'Boots with a moulded strike guard, protecting the inside of the leg from the opposite hoof.', 'Brushing Boots', 'Boots with a moulded strike guard, protecting the inside of the leg from the opposite hoof. Pony, cob and full at Tack Rack, Nairobi.', NULL, 1, 1),
  ('overreach-boots', 'Overreach Boots', 'Neoprene & Rubber', 'Neoprene and rubber overreach boots, guarding the heel and coronet from a hind foot.', 'Overreach Boots', 'Neoprene and rubber overreach boots guarding the heel and coronet from a hind foot. Pony, cob and full. From Tack Rack, Nairobi.', NULL, 2, 1),
  ('bandages', 'Bandages', 'Fleece, Cohesive & Pads', 'Exercise and stable bandages with the quilted pads that go underneath them.', 'Bandages & Pads', 'Exercise and stable bandages with the quilted pads that go underneath them. In stock at Tack Rack, Ngong Road, Nairobi.', NULL, 3, 1),
  ('shampoo', 'Shampoo', 'Washing Down', 'Aloe vera, lemon and neem oil shampoos and conditioners for washing horses down.', 'Horse Shampoos & Conditioners', 'Aloe vera, lemon and neem oil shampoos and conditioners for washing horses down. Stocked at Tack Rack, Ngong Road, Nairobi.', NULL, 1, 1),
  ('detanglers', 'Detanglers', 'Mane & Tail', 'Detanglers, sheen sprays and mane and tail lotions, for shows and for every day.', 'Mane & Tail Care', 'Detanglers, sheen sprays and mane and tail lotions, for shows and for every day. From Tack Rack, Ngong Road, Nairobi.', NULL, 2, 1),
  ('skin-care', 'Skin Care', 'Soothing & Protecting', 'Sunscreen for pink noses, soothing gels for itching, and barrier creams for heels.', 'Horse Skin & Sun Care', 'Sunscreen for pink noses, soothing gels for itching and barrier creams for heels. Stocked at Tack Rack, Ngong Road, Nairobi.', NULL, 3, 1),
  ('first-aid', 'First Aid', 'Wound & Skin Care', 'Wound sprays, dressings and gels for the tack room first aid box.', 'Equine First Aid & Wound Care', 'Wound sprays, dressings and skin gels for the tack room first aid box. Stocked at Tack Rack, Ngong Road, Nairobi.', NULL, 4, 1),
  ('joint-supplements', 'Joint Supplements', 'Fed Daily', 'Glucosamine, devils claw and the rest of the joint range, fed in the feed.', 'Joint Supplements for Horses', 'Glucosamine, Cortaflex, devils claw and the rest of the joint range, fed in the feed. From Tack Rack, Ngong Road, Nairobi.', NULL, 1, 1),
  ('gels-clays-creams', 'Gels, Clays & Creams', 'Applied to the Leg', 'Cooling gels, clays, arnica and MSM creams that go on the leg after work.', 'Leg Gels, Clays & Creams', 'Cooling gels, natural clay, arnica and MSM creams that go on the leg after work. Stocked at Tack Rack, Ngong Road, Nairobi.', NULL, 2, 1),
  ('vitamins-minerals', 'Vitamins & Minerals', 'Premixes, Salts & Licks', 'Vitamin and mineral premixes, limestone, salts, bicarbonate and mineral licks for the feed room.', 'Vitamins, Minerals & Licks', 'Vitamin and mineral premixes, limestone, Epsom salts, bicarbonate and mineral licks for the feed room. From Tack Rack, Nairobi.', NULL, 1, 1),
  ('electrolytes', 'Electrolytes', 'Powders & Pastes', 'Electrolytes to replace the salts lost in sweat, in a tub, a bag or a ready-to-use syringe.', 'Horse Electrolytes', 'Electrolytes to replace the salts lost in sweat — in a tub, a bag or a ready-to-use oral syringe. Stocked in Nairobi.', NULL, 2, 1),
  ('herbs-tonics', 'Herbs & Tonics', 'Garlic, Iron & Respiratory', 'Garlic, iron tonics and respiratory support, fed daily or through a hard spell.', 'Herbs & Tonics for Horses', 'Garlic, iron tonics and respiratory support, fed daily or through a hard spell. Stocked at Tack Rack, Ngong Road, Nairobi.', NULL, 3, 1),
  ('body-brushes', 'Body Brushes', 'For the Shine', 'Soft-bristled brushes used with a curry comb to lift dust and bring up a coat.', 'Horse Brushes', 'Body brushes for the shine, dandy brushes for the mud, and brushes for mane and tail. From Tack Rack, Ngong Road, Nairobi.', NULL, 1, 1),
  ('dandy-brushes', 'Dandy Brushes', 'For the Mud', 'Stiff-bristled brushes for dried mud and sweat, used before the body brush.', 'Dandy Brushes', 'Stiff-bristled brushes for dried mud and sweat, used before the body brush. In stock at Tack Rack, Ngong Road, Nairobi.', NULL, 2, 1),
  ('mane-tail-brushes', 'Mane & Tail Brushes', 'Without Pulling', 'Brushes that work through a mane and tail without tearing the hair out.', 'Mane & Tail Brushes', 'Brushes that work through a mane and tail without tearing the hair out. In stock at Tack Rack, Ngong Road, Nairobi.', NULL, 3, 1),
  ('curry-combs', 'Curry Combs', 'Rubber & Plastic', 'Rubber and plastic curry combs, used in a circular motion to lift dust and loose hair.', 'Rubber & Plastic Curry Combs', 'Curry combs in rubber and plastic, used in a circular motion to lift dust and loose hair before body brushing. In Nairobi.', NULL, 4, 1),
  ('combs-plaiting', 'Combs & Plaiting', 'Manes & Tails', 'Metal mane combs and plaiting bands, for pulling, plaiting and turning out tidily.', 'Mane Combs & Plaiting Bands', 'Metal mane combs and plaiting bands, for pulling, plaiting and turning out tidily. From Tack Rack, Ngong Road, Nairobi.', NULL, 5, 1),
  ('grooming-tools', 'Grooming Tools', 'Scrapers, Scoops & Boxes', 'Sweat scrapers, feed scoops and the tack box that keeps a grooming kit together.', 'Sweat Scrapers, Scoops & Boxes', 'Sweat scrapers, feed scoops and the tack box that keeps a grooming kit together. Stocked at Tack Rack, Ngong Road, Nairobi.', NULL, 6, 1),
  ('numnahs', 'Numnahs', 'Shaped & Square Cut', 'Quilted GP numnahs shaped to follow the saddle, and square-cut dressage pads.', 'Numnahs & Dressage Squares', 'Shaped GP numnahs and square-cut dressage pads, washable and cut clear of the withers. Stocked in Nairobi since 1997.', NULL, 1, 1),
  ('saddle-pads', 'Saddle Pads', 'Riser & Fleece', 'Pads that sit between the saddle and the horse, and fleece for after work.', 'Saddle Pads & Blankets', 'Prolite and gel pads that sit under the saddle, and fleece rugs for after work. From Tack Rack, Ngong Road, Nairobi.', NULL, 2, 1),
  ('non-slip-gel-pads', 'Non-Slip Gel Pads', 'Grip Both Sides', 'Dimpled gel pads that grip on both sides, to stop a saddle sliding and take the jar out of the ride.', 'Non-Slip Gel Pads', 'Dimpled gel pads that grip on both sides, stopping a saddle sliding and taking the jar out of the ride. From Tack Rack, Nairobi.', NULL, 3, 1),
  ('horseshoes', 'Shoes', 'Steel & Aluminium', 'Steel shoes and aluminium racing plates, bundled by size.', 'Farriery — Horseshoes & Nails', 'Steel and aluminium horseshoes and Optima farrier nails, bundled by size. Stocked at Tack Rack, Ngong Road, Nairobi.', NULL, 1, 1),
  ('horseshoe-nails', 'Nails', 'By Head and Gauge', 'Farrier nails in the head patterns and gauges the shoes take, by the packet or the box.', 'Farrier Nails', 'Farrier nails in the head patterns and gauges your shoes take, by the packet of 50 or the box of 250. From Tack Rack, Nairobi.', NULL, 2, 1),
  ('farrier-tools', 'Farrier Tools', 'For the Shoeing Itself', 'The tools a farrier works with: pincers, rasps, knives, nippers and the rest of the kit.', 'Farrier Tools', 'Pincers, rasps, knives and nippers for shoeing. In stock at Tack Rack, Ngong Road, Nairobi.', NULL, 3, 1),
  ('grooming-equipment', 'Grooming Equipment', 'Brushes, Combs & Boxes', 'Body and dandy brushes, rubber and plastic curry combs, mane combs, sweat scrapers, plaiting bands and tack boxes.', 'Grooming Kits, Brushes & Combs', 'Body and dandy brushes, rubber and plastic curry combs, mane combs, sweat scrapers and tack boxes. In Nairobi since 1997.', NULL, 1, 1),
  ('shampoo-skin-care', 'Shampoo & Skin Care', 'Washing, Detangling & Sun', 'Shampoos, detanglers, mane and tail lotions, soothing gels and sunscreen for the Kenyan sun.', 'Horse Shampoo & Skin Care', 'Shampoos, detanglers, mane and tail lotions, soothing gels and sunscreen for the Kenyan sun. From Tack Rack, Nairobi.', NULL, 2, 1),
  ('hoof-care', 'Hoof Care', 'Oils, Dressings & Farriery', 'Hoof oils, dressings, moisturisers and repair compounds, treatment boots and poultices, and shoes and nails for the farrier.', 'Hoof Care, Hoof Oils & Farriery', 'Keratex, Red Horse and Radiol hoof care, hoof oil and Stockholm tar, treatment boots, horseshoes and nails. From Tack Rack, Nairobi.', NULL, 3, 1),
  ('grooming-care', 'Grooming & Care', 'Grooming, Skin & Leather', 'Brushes, shampoos, hoof care and leather care — everything used on the horse and on the tack rather than fed to it.', 'Grooming, Skin & Leather Care', 'Brushes, shampoos, detanglers, hoof care and leather care — everything used on the horse and on the tack. From Tack Rack, Nairobi.', NULL, 3, 1),
  ('leather-care', 'Leather Care', 'Soaps, Dressings & Dubbin', 'Saddle soaps, leather dressings and dubbin that keep tack alive in a dry, high-altitude climate.', 'Leather Care & Tack Cleaning', 'Saddle soaps, leather dressings and dubbin that keep tack alive in a dry, high-altitude climate. From Tack Rack, Nairobi.', NULL, 4, 1)
ON DUPLICATE KEY UPDATE
  `name`       = VALUES(`name`),
  `tagline`    = VALUES(`tagline`),
  `description`= VALUES(`description`),
  `meta_title` = VALUES(`meta_title`),
  `meta_desc`  = VALUES(`meta_desc`),
  `image`      = VALUES(`image`),
  `sort_order` = VALUES(`sort_order`),
  `is_active`  = 1;

-- ---------------------------------------------------------------------
--  2. Who sits under whom. Resolved by slug so existing ids survive.
-- ---------------------------------------------------------------------
UPDATE `categories` SET `parent_id` = NULL WHERE `slug` IN ('rider', 'horse', 'yard', 'grooming-care');

UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'rider' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'clothing';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'rider' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'safety-equipment';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'rider' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'whips';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'horse' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'saddles';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'horse' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'bridles-reins';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'horse' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'bits-accessories';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'horse' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'martingales-stirrups-leathers';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'horse' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'girths';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'horse' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'numnahs-saddle-pads';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'horse' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'headcollars-lead-ropes';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'horse' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'horse-boots';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'yard' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'fly-repellent';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'yard' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'joint-muscle-care';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'yard' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'gut-health';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'yard' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'calming';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'yard' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'feed-supplements';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'yard' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'yard-equipment';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'yard' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'shoeing-equipment';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'hoof-care' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'hoof-oils-dressings';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'hoof-care' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'frog-sole-care';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'hoof-care' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'hoof-repair';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'hoof-care' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'poultice-treatment-boots';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'hoof-care' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'hoof-supplements';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'clothing' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'breeches-jodhpurs';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'clothing' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'footwear-chaps';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'clothing' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'hat-silks-numbers';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'safety-equipment' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'riding-helmets';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'safety-equipment' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'skull-caps';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'safety-equipment' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'body-protectors';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'saddles' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'saddles-wintec';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'saddles' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'saddles-jeffries';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'saddles' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'saddles-thorowgood';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'bridles-reins' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'bridles';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'bridles-reins' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'reins';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'bits-accessories' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'bits';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'bits-accessories' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'bit-accessories';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'martingales-stirrups-leathers' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'martingales';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'martingales-stirrups-leathers' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'stirrup-irons';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'martingales-stirrups-leathers' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'stirrup-treads';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'girths' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'dressage-girths';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'girths' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'girths-anti-chafe';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'girths' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'girths-fleece-lined';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'girths' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'girths-borghey';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'girths' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'girths-elastic';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'horse-boots' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'brushing-boots';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'horse-boots' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'overreach-boots';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'horse-boots' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'bandages';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'shampoo-skin-care' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'shampoo';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'shampoo-skin-care' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'detanglers';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'shampoo-skin-care' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'skin-care';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'shampoo-skin-care' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'first-aid';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'joint-muscle-care' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'joint-supplements';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'joint-muscle-care' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'gels-clays-creams';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'feed-supplements' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'vitamins-minerals';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'feed-supplements' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'electrolytes';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'feed-supplements' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'herbs-tonics';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'grooming-equipment' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'body-brushes';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'grooming-equipment' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'dandy-brushes';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'grooming-equipment' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'mane-tail-brushes';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'grooming-equipment' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'curry-combs';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'grooming-equipment' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'combs-plaiting';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'grooming-equipment' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'grooming-tools';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'numnahs-saddle-pads' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'numnahs';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'numnahs-saddle-pads' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'saddle-pads';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'numnahs-saddle-pads' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'non-slip-gel-pads';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'shoeing-equipment' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'horseshoes';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'shoeing-equipment' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'horseshoe-nails';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'shoeing-equipment' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'farrier-tools';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'grooming-care' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'grooming-equipment';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'grooming-care' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'shampoo-skin-care';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'grooming-care' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'hoof-care';
UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'grooming-care' SET c.`parent_id` = p.`id` WHERE c.`slug` = 'leather-care';

-- ---------------------------------------------------------------------
--  3. Each product onto its shelf (168). A product the live
--     site does not have is simply not matched, and nothing happens.
-- ---------------------------------------------------------------------
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'gut-health' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'alltech-yea-sacc-conc';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'horseshoes' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'aluminium-horseshoes';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'brushing-boots' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'anatomic-brushing-boots';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'gels-clays-creams' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'arnica-witch-hazel';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'hoof-supplements' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'back-to-basics-immuno-hoof';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'breeches-jodhpurs' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'beige-riding-breeches';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'girths-elastic' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'black-elastic-girth';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'breeches-jodhpurs' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'black-full-seat-breeches';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'footwear-chaps' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'black-half-chaps';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'saddles' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'black-leather-general-purpose-saddle';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'bridles' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'black-leather-snaffle-bridle';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'skull-caps' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'black-skull-cap';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'body-brushes' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'body-brushes-colour-range';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'girths-borghey' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'borghey-girth';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'detanglers' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'botanica-mane-tail-6-in-1-spray';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'footwear-chaps' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'brown-leather-gaiters';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'brushing-boots' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'brushing-boots';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'joint-supplements' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'buteless';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'fly-repellent' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'buzz-off-fly-repellent';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'vitamins-minerals' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'calavite';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'bandages' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'cohesive-bandages';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'joint-supplements' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'coliflex-gastro-joint-care';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'gut-health' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'coligone-balancer';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'gut-health' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'coligone-liquid';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'gut-health' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'coligone-powder';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'hat-silks-numbers' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'competition-number-holder';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'stirrup-irons' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'compositi-lightweight-stirrups';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'joint-supplements' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'cortaflex-ha';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'headcollars-lead-ropes' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'cotton-lunge-line-red';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'dandy-brushes' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'dandy-brushes-colour-range';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'poultice-treatment-boots' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'davis-hoof-treatment-boot';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'gels-clays-creams' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'devils-claw-gel';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'joint-supplements' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'devils-claw-root-herb';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'numnahs' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'dressage-square-black';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'numnahs' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'dressage-square-white';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'leather-care' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'dubbin';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'fly-repellent' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'dudu-krin-fly-repellent';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'electrolytes' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'electrolyte-paste-oral-syringe';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'electrolytes' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'electrolytes';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'hoof-supplements' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'endeavon-biotin-supplement-h';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'shampoo' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'endeavon-lemon-shampoo';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'hoof-oils-dressings' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'endeavon-sole-hardener';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'vitamins-minerals' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'epsom-salts';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'first-aid' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'equimins-aloe-vera-gel';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'vitamins-minerals' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'equimins-b-plus-liquid';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'fly-repellent' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'equimins-fly-midge-repellent';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'herbs-tonics' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'equimins-garlic-extract-liquid';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'herbs-tonics' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'equimins-garlic-mint';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'gut-health' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'equimins-milk-thistle';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'herbs-tonics' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'equimins-respiratory-air-power-booster';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'detanglers' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'equimins-ultra-silky-detangler';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'vitamins-minerals' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'equimins-vitamin-e-selenium';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'vitamins-minerals' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'equimins-young-stock-formula';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'hoof-supplements' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'equine-america-biotin-xtra';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'calming' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'equine-america-focus';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'calming' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'equine-america-magnitude';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'calming' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'equine-america-no-more-moods';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'calming' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'equine-america-so-kalm-paste';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'body-brushes' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'ezi-groom-body-brush';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'dandy-brushes' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'ezi-groom-dandy-brush';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'combs-plaiting' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'ezi-groom-plaiting-bands';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'yard-equipment' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'feed-scoop-colour-range';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'girths-fleece-lined' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'fleece-lined-girth';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'joint-supplements' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'flex-o-joint';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'yard-equipment' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'flexible-tub-orange';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'fly-repellent' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'flyguard-pro-fine-mesh-fly-mask';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'joint-supplements' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'glucosamine-hcl-12-000';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'leather-care' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'glycerine-leather-saddle-soap';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'detanglers' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'gold-label-mane-coat-tail-lotion';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'hat-silks-numbers' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'hat-silk-red';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'bridles' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'havana-leather-snaffle-bridle';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'first-aid' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'heal-o-fast-wound-healing-spray';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'gels-clays-creams' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'herbal-cooling-gel';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'riding-helmets' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'hkm-velvet-riding-hat';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'hoof-oils-dressings' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'hoof-oil';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'grooming-tools' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'hy-grooming-and-tack-box';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'poultice-treatment-boots' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'hy-health-hoof-poultice';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'footwear-chaps' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'hy-leather-jodhpur-boots-black';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'footwear-chaps' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'hy-leather-jodhpur-boots-brown';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'yard-equipment' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'hydrophane-cribox';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'saddles-jeffries' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'jeffries-elite-leather-saddle';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'hoof-repair' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'keratex-3p-hoof-repair';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'frog-sole-care' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'keratex-frog-power-cleanser';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'hoof-oils-dressings' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'keratex-hoof-moisturiser';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'hoof-repair' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'keratex-hoof-putty';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'hoof-repair' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'keratex-nail-hole-damage-repair';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'gut-health' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'keratex-zeolite';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'stirrup-irons' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'korsteel-stainless-steel-stirrup-irons';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'martingales' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'leather-bib-martingale';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'reins' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'leather-draw-reins';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'leather-care' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'leather-dressing';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'leather-care' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'leather-soap';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'gels-clays-creams' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'leg-gel';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'hat-silks-numbers' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'lemieux-hat-silk';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'herbs-tonics' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'lincoln-blood-tonic';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'skin-care' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'lincoln-sun-bloc';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'bits' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'loose-ring-jointed-snaffle';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'whips' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'lunge-whip';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'headcollars-lead-ropes' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'lunging-cavesson';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'mane-tail-brushes' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'mane-and-tail-brush';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'riding-helmets' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'matt-black-vented-riding-helmet';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'poultice-treatment-boots' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'medi-boot';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'vitamins-minerals' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'mineral-licks';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'shampoo' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'moores-aloe-vera-shampoo';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'gels-clays-creams' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'msm-cream';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'joint-supplements' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'muscle-up';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'gels-clays-creams' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'natural-clay';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'breeches-jodhpurs' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'navy-riding-breeches';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'overreach-boots' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'neoprene-overreach-boots';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'saddle-pads' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'night-rug';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'non-slip-gel-pads' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'non-slip-gel-pad';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'headcollars-lead-ropes' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'nylon-headcollar-colour-range';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'horseshoe-nails' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'optima-horseshoe-nails';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'shampoo' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'organic-neem-oil-shampoo-conditioner';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'girths-anti-chafe' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'padded-anti-chafe-girth';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'stirrup-irons' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'peacock-safety-stirrup-irons';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'curry-combs' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'plastic-curry-comb';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'curry-combs' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'plastic-curry-combs-colour-range';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'saddle-pads' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'prolite-saddle-pad';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'bandages' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'quilted-bandage-pads';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'numnahs' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'quilted-gp-numnah-black';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'numnahs' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'quilted-gp-numnah-navy';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'numnahs' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'quilted-gp-numnah-red';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'numnahs' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'quilted-gp-numnah-teal';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'hoof-oils-dressings' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'radiol-pedicine-hoof-ointment';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'yard-equipment' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'red-gorilla-feed-tub';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'frog-sole-care' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'red-horse-artimud';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'frog-sole-care' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'red-horse-field-paste';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'skin-care' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'red-horse-honeyheel';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'hoof-oils-dressings' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'red-horse-hydrohoof';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'frog-sole-care' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'red-horse-sole-cleanse';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'frog-sole-care' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'red-horse-sole-paint';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'frog-sole-care' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'red-horse-stronghorn';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'fly-repellent' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'redtop-bait-bag-refill';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'fly-repellent' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'redtop-outdoor-flytrap';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'martingales' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'rein-keepers-martingale-stops';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'whips' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'riding-crop';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'first-aid' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'robinson-veterinary-gamgee';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'yard-equipment' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'rope-haynet-yellow';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'headcollars-lead-ropes' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'rope-lead-ropes';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'bit-accessories' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'rubber-bit-guards';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'martingales' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'rubber-guards';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'overreach-boots' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'rubber-overreach-boots';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'horse-boots' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'rubber-sausage-boot';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'stirrup-treads' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'rubber-stirrup-treads';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'whips' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'schooling-whip';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'yard-equipment' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'shallow-feed-tub-green';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'bits' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'shires-loose-ring-snaffle-with-copper-lozenge';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'curry-combs' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'shires-rubber-curry-comb';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'bits' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'shires-stainless-steel-snaffle-bit';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'dressage-girths' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'short-dressage-girth';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'vitamins-minerals' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'sodium-bicarbonate';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'skin-care' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'sooth-itch-super-strength-gel';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'combs-plaiting' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'stainless-steel-mane-comb';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'horseshoes' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'steel-horseshoes';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'hoof-oils-dressings' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'stockholm-tar';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'grooming-tools' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'sweat-scraper-colour-range';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'vitamins-minerals' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'tamfeeds-horse-pony-vitamin-mineral-premix';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'saddle-pads' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'tartan-fleece-rug';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'saddles-thorowgood' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'thorowgood-saddle';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'bits' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'training-bit-with-rope-cheeks';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'body-protectors' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'usg-body-protector-beta-2018-level-3';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'first-aid' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'veterinus-derma-gel';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'fly-repellent' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'vetsbrands-summer-spray';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'body-protectors' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'whitaker-body-protector';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'saddles-wintec' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'wintec-2000-all-purpose-saddle';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'saddles-wintec' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'wintec-synthetic-saddle';
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'first-aid' SET pr.`category_id` = c.`id` WHERE pr.`slug` = 'wound-magic';

-- ---------------------------------------------------------------------
--  4. A category from an older arrangement that is not part of this tree
--     is hidden rather than dropped, so nothing that points at it breaks.
-- ---------------------------------------------------------------------
UPDATE `categories` SET `is_active` = 0 WHERE `slug` NOT IN (
  'rider', 'clothing', 'safety-equipment', 'whips', 'saddles', 'horse', 'bridles-reins', 'bits-accessories', 'martingales-stirrups-leathers', 'girths', 'numnahs-saddle-pads', 'headcollars-lead-ropes', 'horse-boots', 'fly-repellent', 'joint-muscle-care', 'gut-health', 'yard', 'calming', 'feed-supplements', 'yard-equipment', 'shoeing-equipment', 'hoof-oils-dressings', 'frog-sole-care', 'hoof-repair', 'poultice-treatment-boots', 'hoof-supplements', 'breeches-jodhpurs', 'footwear-chaps', 'hat-silks-numbers', 'riding-helmets', 'skull-caps', 'body-protectors', 'saddles-wintec', 'saddles-jeffries', 'saddles-thorowgood', 'bridles', 'reins', 'bits', 'bit-accessories', 'martingales', 'stirrup-irons', 'stirrup-treads', 'dressage-girths', 'girths-anti-chafe', 'girths-fleece-lined', 'girths-borghey', 'girths-elastic', 'brushing-boots', 'overreach-boots', 'bandages', 'shampoo', 'detanglers', 'skin-care', 'first-aid', 'joint-supplements', 'gels-clays-creams', 'vitamins-minerals', 'electrolytes', 'herbs-tonics', 'body-brushes', 'dandy-brushes', 'mane-tail-brushes', 'curry-combs', 'combs-plaiting', 'grooming-tools', 'numnahs', 'saddle-pads', 'non-slip-gel-pads', 'horseshoes', 'horseshoe-nails', 'farrier-tools', 'grooming-equipment', 'shampoo-skin-care', 'hoof-care', 'grooming-care', 'leather-care'
);
