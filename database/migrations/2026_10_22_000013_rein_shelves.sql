-- =====================================================================
--  Reins shelved by what they are made of
--
--  Bridles & Reins held two shelves, Bridles and a single Reins. The review
--  asked for reins split by type, so Rubber, Leather, Webbed and Running
--  take the place of that one heading and sit beside Bridles. They are
--  siblings rather than children of Reins because the navigation renders
--  three levels — department, section, shelf — and a fourth would not show.
--
--  Additive and idempotent. Matched on slug, nothing is deleted: the old
--  Reins shelf is hidden once its listing has moved, so the rows and any
--  history pointing at them survive, and /shop/reins redirects to the
--  section through RETIRED_CATEGORIES in ShopController.
-- =====================================================================

SET NAMES utf8mb4;

-- ---------------------------------------------------------------------
--  The four shelves.
-- ---------------------------------------------------------------------
INSERT INTO `categories` (`slug`, `name`, `tagline`, `description`, `meta_title`, `meta_desc`, `sort_order`, `is_active`)
VALUES
  ('rubber-reins', 'Rubber Reins', 'Grip in the Wet',
   'Rubber-covered reins, for grip that holds when the rein is wet and the hands are cold.',
   'Rubber Reins',
   'Rubber-covered riding reins that keep their grip in the wet. Stocked at Tack Rack, Ngong Road, Nairobi.',
   2, 1),

  ('leather-reins', 'Leather Reins', 'Plain & Laced',
   'Plain and laced leather reins, and the schooling draw reins that run girth to bit ring.',
   'Leather Reins & Draw Reins',
   'Plain and laced leather reins, and leather schooling draw reins. Fitted and advised on at Tack Rack, Nairobi.',
   3, 1),

  ('webbed-reins', 'Webbed Reins', 'Light & Marked',
   'Webbed reins with leather stops, light in the hand and marked so the length can be found by feel.',
   'Webbed Reins',
   'Webbed riding reins with leather stops, light in the hand and easy to measure by feel. Stocked in Nairobi.',
   4, 1),

  ('running-reins', 'Running Reins', 'Schooling Only',
   'Running and continental reins, for schooling under instruction rather than for jumping.',
   'Running & Continental Reins',
   'Running and continental schooling reins. Ask us, or your instructor, before schooling in them.',
   5, 1)

ON DUPLICATE KEY UPDATE
  `name` = VALUES(`name`), `tagline` = VALUES(`tagline`), `description` = VALUES(`description`),
  `meta_title` = VALUES(`meta_title`), `meta_desc` = VALUES(`meta_desc`),
  `sort_order` = VALUES(`sort_order`), `is_active` = 1;

UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'bridles-reins'
   SET c.`parent_id` = p.`id`
 WHERE c.`slug` IN ('rubber-reins', 'leather-reins', 'webbed-reins', 'running-reins');

-- ---------------------------------------------------------------------
--  Anything that was filed under the old Reins shelf is leather: the draw
--  reins are the only listing there. Moved by slug so this is safe to
--  repeat, and safe on a deployment that never had the shelf.
-- ---------------------------------------------------------------------
UPDATE `products` pr
  JOIN `categories` old ON old.`id` = pr.`category_id` AND old.`slug` = 'reins'
  JOIN `categories` new ON new.`slug` = 'leather-reins'
   SET pr.`category_id` = new.`id`;

-- The live site carried one bundled listing instead; it is leather too.
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'leather-reins'
   SET pr.`category_id` = c.`id`
 WHERE pr.`slug` IN ('leather-draw-reins', 'leather-running-reins-continental-and-rubber-reins');

-- ---------------------------------------------------------------------
--  Retire the old shelf once it is empty. Hidden, not deleted.
-- ---------------------------------------------------------------------
UPDATE `categories` SET `is_active` = 0
 WHERE `slug` = 'reins'
   AND NOT EXISTS (SELECT 1 FROM (SELECT * FROM `products`) p
                    WHERE p.`category_id` = `categories`.`id` AND p.`is_active` = 1);
