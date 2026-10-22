-- =====================================================================
--  Boots on a shelf of their own
--
--  Clothing held Footwear & Chaps, which carried jodhpur boots alongside
--  half chaps and gaiters. Boots now have their own shelf, and what is
--  left is named for what it actually holds.
--
--  Additive and idempotent. Matched on slug, nothing is deleted: the old
--  shelf is hidden once empty, and /shop/footwear-chaps redirects through
--  RETIRED_CATEGORIES in ShopController.
-- =====================================================================

SET NAMES utf8mb4;

-- ---------------------------------------------------------------------
--  The two shelves that replace Footwear & Chaps.
-- ---------------------------------------------------------------------
INSERT INTO `categories` (`slug`, `name`, `tagline`, `description`, `meta_title`, `meta_desc`, `sort_order`, `is_active`)
VALUES
  ('boots', 'Boots', 'Jodhpur & Long',
   'Jodhpur boots and long boots, fitted to the foot rather than guessed at from a size chart.',
   'Riding Boots & Jodhpur Boots',
   'Leather jodhpur boots and long riding boots, fitted in person at Tack Rack, Ngong Road, Nairobi.',
   2, 1),

  ('chaps-gaiters', 'Chaps & Gaiters', 'Half Chaps & Leather',
   'Half chaps and leather gaiters, fitted to the calf so they neither gape nor grip.',
   'Half Chaps & Riding Gaiters',
   'Suede half chaps and leather riding gaiters, fitted to the calf. Stocked at Tack Rack, Nairobi.',
   3, 1)

ON DUPLICATE KEY UPDATE
  `name` = VALUES(`name`), `tagline` = VALUES(`tagline`), `description` = VALUES(`description`),
  `meta_title` = VALUES(`meta_title`), `meta_desc` = VALUES(`meta_desc`),
  `sort_order` = VALUES(`sort_order`), `is_active` = 1;

UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'clothing'
   SET c.`parent_id` = p.`id`
 WHERE c.`slug` IN ('boots', 'chaps-gaiters');

-- Hat silks step back one place to make room.
UPDATE `categories` SET `sort_order` = 4 WHERE `slug` = 'hat-silks-numbers';
UPDATE `categories` SET `sort_order` = 5 WHERE `slug` = 'gloves';

-- ---------------------------------------------------------------------
--  Split what was on the old shelf. A boot goes to Boots; a chap or a
--  gaiter goes to Chaps & Gaiters. Matched on the listing's own slug, so
--  this is safe to repeat and safe where a listing is absent.
-- ---------------------------------------------------------------------
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'boots'
   SET pr.`category_id` = c.`id`
 WHERE pr.`slug` IN ('hy-leather-jodhpur-boots-black', 'hy-leather-jodhpur-boots-brown',
                     'jodhpur-boots', 'long-riding-boots');

UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'chaps-gaiters'
   SET pr.`category_id` = c.`id`
 WHERE pr.`slug` IN ('black-half-chaps', 'brown-leather-gaiters', 'half-chaps', 'leather-gaiters');

-- Anything still sitting on the old shelf is a chap or a gaiter by
-- elimination, since the boots have just been taken off it.
UPDATE `products` pr
  JOIN `categories` old ON old.`id` = pr.`category_id` AND old.`slug` = 'footwear-chaps'
  JOIN `categories` new ON new.`slug` = 'chaps-gaiters'
   SET pr.`category_id` = new.`id`;

-- ---------------------------------------------------------------------
--  Retire the old shelf once it is empty. Hidden, not deleted.
-- ---------------------------------------------------------------------
UPDATE `categories` SET `is_active` = 0
 WHERE `slug` = 'footwear-chaps'
   AND NOT EXISTS (SELECT 1 FROM (SELECT * FROM `products`) p
                    WHERE p.`category_id` = `categories`.`id` AND p.`is_active` = 1);
