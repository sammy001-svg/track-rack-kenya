-- =====================================================================
--  Borghey becomes Burghley
--
--  The shelf and its listing were spelled Borghey here while the live
--  site spelled the same girth Burghley. Burghley is the correct one, so
--  the shelf, the listing and its copy are corrected to match.
--
--  Additive and idempotent. The shelf is matched on slug and the listing
--  keeps its id, so orders and quotes that point at it are undisturbed.
--  Nothing is deleted: the old shelf is hidden once empty, and both old
--  addresses redirect through the maps in ShopController and
--  ProductController.
-- =====================================================================

SET NAMES utf8mb4;

-- ---------------------------------------------------------------------
--  The shelf, correctly spelled.
-- ---------------------------------------------------------------------
INSERT INTO `categories` (`slug`, `name`, `tagline`, `description`, `meta_title`, `meta_desc`, `sort_order`, `is_active`)
VALUES (
  'girths-burghley', 'Burghley', 'Long, 40" to 50"',
  'Hard-wearing black girths in the Burghley pattern, cut long for a general purpose saddle.',
  'Burghley Girths',
  'Hard-wearing black girths in the Burghley pattern, 40 to 50 inches. In stock at Tack Rack, Ngong Road, Nairobi.',
  4, 1
)
ON DUPLICATE KEY UPDATE
  `name` = VALUES(`name`), `tagline` = VALUES(`tagline`), `description` = VALUES(`description`),
  `meta_title` = VALUES(`meta_title`), `meta_desc` = VALUES(`meta_desc`),
  `sort_order` = VALUES(`sort_order`), `is_active` = 1;

UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'girths'
   SET c.`parent_id` = p.`id` WHERE c.`slug` = 'girths-burghley';

-- ---------------------------------------------------------------------
--  Whatever was on the old shelf moves across, ids intact.
-- ---------------------------------------------------------------------
UPDATE `products` pr
  JOIN `categories` old ON old.`id` = pr.`category_id` AND old.`slug` = 'girths-borghey'
  JOIN `categories` new ON new.`slug` = 'girths-burghley'
   SET pr.`category_id` = new.`id`;

-- And any girth of that pattern still loose on the section, whichever
-- way it happens to be spelled there.
UPDATE `products` pr
  JOIN `categories` sec ON sec.`id` = pr.`category_id` AND sec.`slug` = 'girths'
  JOIN `categories` dst ON dst.`slug` = 'girths-burghley'
   SET pr.`category_id` = dst.`id`
 WHERE pr.`name` REGEXP 'borghey|burghley';

UPDATE `categories` SET `is_active` = 0
 WHERE `slug` = 'girths-borghey'
   AND NOT EXISTS (SELECT 1 FROM (SELECT * FROM `products`) p
                    WHERE p.`category_id` = `categories`.`id` AND p.`is_active` = 1);

-- ---------------------------------------------------------------------
--  The listing itself. Guarded so it cannot collide with a row that
--  already carries the corrected slug.
-- ---------------------------------------------------------------------
UPDATE `products`
   SET `slug`       = 'burghley-girth',
       `name`       = 'Burghley Girth',
       `short_desc` = 'A hard-wearing black girth in the Burghley pattern.',
       `meta_title` = 'Burghley Girth, 40" to 50"',
       `meta_desc`  = 'A hard-wearing black girth in the Burghley pattern, cut long for a general purpose saddle. 40 to 50 inches, from Tack Rack, Nairobi.'
 WHERE `slug` = 'borghey-girth'
   AND NOT EXISTS (SELECT 1 FROM (SELECT `slug` FROM `products`) x WHERE x.`slug` = 'burghley-girth');

-- Anything else still carrying the old spelling in its words.
UPDATE `products`
   SET `name`       = REPLACE(`name`, 'Borghey', 'Burghley'),
       `short_desc` = REPLACE(`short_desc`, 'Borghey', 'Burghley'),
       `description`= REPLACE(`description`, 'Borghey', 'Burghley')
 WHERE `name` LIKE '%Borghey%' OR `short_desc` LIKE '%Borghey%' OR `description` LIKE '%Borghey%';
