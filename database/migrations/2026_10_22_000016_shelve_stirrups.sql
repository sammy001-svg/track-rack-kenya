-- =====================================================================
--  Put every Martingales, Stirrups & Leathers listing on a shelf
--
--  The section has three shelves — Martingales & Guards, Stirrup Irons and
--  Stirrup Treads — but on the live site three listings sit loose on the
--  section itself: the stainless steel irons, and both martingale listings,
--  which is why the Martingales shelf reads empty there. Their slugs differ
--  from ours (leather-martingale, rein-keepers-and-martingale-stops), so
--  moving them by slug missed them.
--
--  Match on what a listing is instead. Only listings sitting directly on
--  the section are touched, so anything already shelved stays put and a
--  second run changes nothing.
--
--  Treads are claimed before irons, because "Rubber Stirrup Treads" would
--  otherwise be caught by the rule meant for stirrups.
-- =====================================================================

SET NAMES utf8mb4;

-- ---------------------------------------------------------------------
--  1. Treads first, for the reason above.
-- ---------------------------------------------------------------------
UPDATE `products` pr
  JOIN `categories` sec ON sec.`id` = pr.`category_id` AND sec.`slug` = 'martingales-stirrups-leathers'
  JOIN `categories` dst ON dst.`slug` = 'stirrup-treads'
   SET pr.`category_id` = dst.`id`
 WHERE pr.`name` REGEXP 'tread';

-- ---------------------------------------------------------------------
--  2. Irons, including the composite and plastic patterns, and the
--     leathers that hang them.
-- ---------------------------------------------------------------------
UPDATE `products` pr
  JOIN `categories` sec ON sec.`id` = pr.`category_id` AND sec.`slug` = 'martingales-stirrups-leathers'
  JOIN `categories` dst ON dst.`slug` = 'stirrup-irons'
   SET pr.`category_id` = dst.`id`
 WHERE pr.`name` REGEXP 'stirrup|iron';

-- ---------------------------------------------------------------------
--  3. Martingales, their stops and the guards that go with them.
-- ---------------------------------------------------------------------
UPDATE `products` pr
  JOIN `categories` sec ON sec.`id` = pr.`category_id` AND sec.`slug` = 'martingales-stirrups-leathers'
  JOIN `categories` dst ON dst.`slug` = 'martingales'
   SET pr.`category_id` = dst.`id`
 WHERE pr.`name` REGEXP 'martingale|rein keeper|guard';
