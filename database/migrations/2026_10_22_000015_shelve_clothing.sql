-- =====================================================================
--  Put every Clothing listing on a shelf
--
--  The previous migration moved listings onto the new Boots and
--  Chaps & Gaiters shelves by slug, which worked here and missed on the
--  live site, where the same products carry different slugs — jodhpur
--  boots are listed there as 'jodhpur-boots-black-brown' and
--  'hy-leather-jodhpur-boots-brown-black'. The boots shelf came out empty
--  and the boots stayed loose on the section.
--
--  So match on what a listing is rather than on what it happens to be
--  called. Only listings sitting directly on Clothing are touched, so
--  anything already shelved stays where it was put, and running this
--  twice does nothing the second time.
--
--  Boots are claimed before breeches, because "Jodhpur Boots" would
--  otherwise be caught by the rule meant for jodhpurs.
-- =====================================================================

SET NAMES utf8mb4;

-- ---------------------------------------------------------------------
--  1. Boots — including long boots and jodhpur boots however they are
--     written. Claimed first, for the reason above.
-- ---------------------------------------------------------------------
UPDATE `products` pr
  JOIN `categories` sec ON sec.`id` = pr.`category_id` AND sec.`slug` = 'clothing'
  JOIN `categories` dst ON dst.`slug` = 'boots'
   SET pr.`category_id` = dst.`id`
 WHERE pr.`name` REGEXP 'boot';

-- ---------------------------------------------------------------------
--  2. Chaps and gaiters.
-- ---------------------------------------------------------------------
UPDATE `products` pr
  JOIN `categories` sec ON sec.`id` = pr.`category_id` AND sec.`slug` = 'clothing'
  JOIN `categories` dst ON dst.`slug` = 'chaps-gaiters'
   SET pr.`category_id` = dst.`id`
 WHERE pr.`name` REGEXP 'chap|gaiter';

-- ---------------------------------------------------------------------
--  3. Breeches, jodhpurs and riding tights.
-- ---------------------------------------------------------------------
UPDATE `products` pr
  JOIN `categories` sec ON sec.`id` = pr.`category_id` AND sec.`slug` = 'clothing'
  JOIN `categories` dst ON dst.`slug` = 'breeches-jodhpurs'
   SET pr.`category_id` = dst.`id`
 WHERE pr.`name` REGEXP 'breech|jodhpur|tight';

-- ---------------------------------------------------------------------
--  4. Hat silks and competition numbers.
-- ---------------------------------------------------------------------
UPDATE `products` pr
  JOIN `categories` sec ON sec.`id` = pr.`category_id` AND sec.`slug` = 'clothing'
  JOIN `categories` dst ON dst.`slug` = 'hat-silks-numbers'
   SET pr.`category_id` = dst.`id`
 WHERE pr.`name` REGEXP 'hat silk|silk|number';

-- ---------------------------------------------------------------------
--  5. Gloves.
-- ---------------------------------------------------------------------
UPDATE `products` pr
  JOIN `categories` sec ON sec.`id` = pr.`category_id` AND sec.`slug` = 'clothing'
  JOIN `categories` dst ON dst.`slug` = 'gloves'
   SET pr.`category_id` = dst.`id`
 WHERE pr.`name` REGEXP 'glove';

-- ---------------------------------------------------------------------
--  6. The same for the old Footwear & Chaps shelf, in case a deployment
--     still has listings on it that the earlier migration did not move.
-- ---------------------------------------------------------------------
UPDATE `products` pr
  JOIN `categories` sec ON sec.`id` = pr.`category_id` AND sec.`slug` = 'footwear-chaps'
  JOIN `categories` dst ON dst.`slug` = 'boots'
   SET pr.`category_id` = dst.`id`
 WHERE pr.`name` REGEXP 'boot';

UPDATE `products` pr
  JOIN `categories` sec ON sec.`id` = pr.`category_id` AND sec.`slug` = 'footwear-chaps'
  JOIN `categories` dst ON dst.`slug` = 'chaps-gaiters'
   SET pr.`category_id` = dst.`id`;

UPDATE `categories` SET `is_active` = 0
 WHERE `slug` = 'footwear-chaps'
   AND NOT EXISTS (SELECT 1 FROM (SELECT * FROM `products`) p
                    WHERE p.`category_id` = `categories`.`id` AND p.`is_active` = 1);
