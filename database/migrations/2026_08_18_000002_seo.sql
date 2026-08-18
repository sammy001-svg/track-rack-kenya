-- =====================================================================
--  SEO — per-record meta control and the settings that feed structured data
--  Additive only. Safe to run against an existing install.
-- =====================================================================

SET NAMES utf8mb4;

-- ---------------------------------------------------------------------
--  Categories: their own title and description, so a section page can
--  target its real search terms rather than inheriting a generic title.
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
--  CMS pages: a meta title distinct from the on-page heading.
-- ---------------------------------------------------------------------
SET @has := (SELECT COUNT(*) FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'pages' AND COLUMN_NAME = 'meta_title');
SET @sql := IF(@has = 0,
  'ALTER TABLE `pages` ADD COLUMN `meta_title` VARCHAR(190) NULL AFTER `subtitle`',
  'SELECT "pages.meta_title already present"');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- ---------------------------------------------------------------------
--  Services: same treatment.
-- ---------------------------------------------------------------------
SET @has := (SELECT COUNT(*) FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'services' AND COLUMN_NAME = 'meta_title');
SET @sql := IF(@has = 0,
  'ALTER TABLE `services`
     ADD COLUMN `meta_title` VARCHAR(190) NULL AFTER `what_to_expect`,
     ADD COLUMN `meta_desc`  VARCHAR(300) NULL AFTER `meta_title`',
  'SELECT "services meta columns already present"');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

-- ---------------------------------------------------------------------
--  SEO settings
-- ---------------------------------------------------------------------
INSERT IGNORE INTO `settings` (`key_name`,`value`,`group_name`,`label`,`input_type`,`sort_order`) VALUES
('seo_title_suffix','Tack Rack Kenya','seo','Title suffix (appended after the separator)','text',1),
('seo_home_title','Equestrian Supplies & Saddlery in Nairobi','seo','Homepage title','text',2),
('seo_home_desc','Kenya''s equestrian supplier since 1997. Saddles, bridles, rider apparel and yard essentials in Nairobi, with Society of Master Saddlers qualified saddle fitting.','seo','Homepage meta description','textarea',3),
('seo_default_desc','Equestrian supplies, saddlery and rider apparel from Tack Rack, Ngong Road, Nairobi. Serving Kenyan riders since 1997.','seo','Fallback meta description','textarea',4),
('seo_share_image','','seo','Default share image (1200x630)','image',5),
('seo_twitter_handle','','seo','Twitter/X handle, without the @','text',6),
('seo_price_range','KSh','seo','Price range shown in search results (e.g. KSh–KSh)','text',7),

('geo_latitude','','contact','Map latitude (from Google Maps)','text',9),
('geo_longitude','','contact','Map longitude (from Google Maps)','text',10),
('hours_weekday_open','08:30','contact','Weekday opening time (24h)','text',11),
('hours_weekday_close','17:30','contact','Weekday closing time (24h)','text',12),
('hours_saturday_open','09:00','contact','Saturday opening time (24h, blank if closed)','text',13),
('hours_saturday_close','13:00','contact','Saturday closing time (24h, blank if closed)','text',14);

-- ---------------------------------------------------------------------
--  Meaningful defaults for the sections that carry the most search value
-- ---------------------------------------------------------------------
UPDATE `categories` SET
  `meta_title` = 'Rider Apparel & Riding Gear',
  `meta_desc`  = 'Riding boots, breeches, jackets, gloves and helmets for every discipline. Rider apparel from Tack Rack, Nairobi — fitted advice from staff who ride.'
WHERE `slug` = 'rider' AND `meta_title` IS NULL;

UPDATE `categories` SET
  `meta_title` = 'Horse Tack & Saddlery',
  `meta_desc`  = 'Saddles, bridles, bits, reins, numnahs and rugs. Professionally fitted saddlery in Nairobi from Kenya''s equestrian supplier since 1997.'
WHERE `slug` = 'horse' AND `meta_title` IS NULL;

UPDATE `categories` SET
  `meta_title` = 'Stable, Grooming & Leather Care',
  `meta_desc`  = 'Grooming kits, stable equipment and leather care for the Kenyan climate. Everything that keeps a yard running and tack lasting.'
WHERE `slug` = 'stable' AND `meta_title` IS NULL;

UPDATE `categories` SET
  `meta_title` = 'Saddles & Saddle Accessories',
  `meta_desc`  = 'Jumping, dressage, general purpose and polo saddles, fitted on the horse by the only Society of Master Saddlers qualified fitter in East Africa.'
WHERE `slug` = 'saddles-accessories' AND `meta_title` IS NULL;

UPDATE `categories` SET
  `meta_title` = 'Riding Boots, Paddock Boots & Chaps',
  `meta_desc`  = 'Short boots, paddock boots and half chaps in full-grain leather. Sized and stocked for Kenyan riders at Tack Rack, Ngong Road.'
WHERE `slug` = 'footwear' AND `meta_title` IS NULL;

UPDATE `categories` SET
  `meta_title` = 'Bridles, Bits & Reins',
  `meta_desc`  = 'Anatomical bridles, stainless steel bits and leather reins. Correct contact starts with the right bit — advice included.'
WHERE `slug` = 'bridles-bits-reins' AND `meta_title` IS NULL;

UPDATE `categories` SET
  `meta_title` = 'Leather Care & Tack Cleaning',
  `meta_desc`  = 'Balms, saddle soaps and oils that keep leather alive in a dry, high-altitude climate. The single most important thing you can buy for your tack.'
WHERE `slug` = 'leather-care-maintenance' AND `meta_title` IS NULL;

UPDATE `services` SET
  `meta_title` = 'Saddle Fitting in Nairobi & Countrywide',
  `meta_desc`  = 'Saddle fitting by Sharon Ashley, the only Society of Master Saddlers qualified fitter in East Africa. Fitted on the horse, at the shop or at your yard.'
WHERE `slug` = 'saddle-fitting' AND `meta_title` IS NULL;

UPDATE `services` SET
  `meta_title` = 'Saddle & Tack Repairs, Nairobi Workshop',
  `meta_desc`  = 'Broken saddle trees, torn panels, re-flocking and restitching, repaired in our own Nairobi workshop. Send photographs for a quote before any work begins.'
WHERE `slug` = 'workshop-repairs' AND `meta_title` IS NULL;

UPDATE `pages` SET `meta_title` = 'About Tack Rack — Kenya''s Equestrian Supplier Since 1997'
WHERE `slug` = 'heritage' AND `meta_title` IS NULL;
