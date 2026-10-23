-- =====================================================================
--  The Bates saddle's photographs
--
--  Two studio frames that had never been assigned to a listing, and the
--  underside stamp from the supplier's own picture, which the studio
--  shoot does not show. The files travel in the repository under
--  public/uploads/products; these are the rows that point at them.
--
--  Idempotent: a row goes in only where that path is not already on the
--  listing, and the lead is set by path rather than by shuffling
--  sort_order.
-- =====================================================================

SET NAMES utf8mb4;

INSERT INTO `product_images` (`product_id`, `path`, `alt`, `is_primary`, `sort_order`)
SELECT p.`id`, v.`path`, v.`alt`, v.`lead`, v.`pos` FROM `products` p
  JOIN (
        SELECT '/uploads/products/bates-general-purpose-saddle-01.jpg' AS `path`,
               'Full side profile of the Bates saddle' AS `alt`, 1 AS `lead`, 0 AS `pos`
  UNION ALL SELECT '/uploads/products/bates-general-purpose-saddle-03.jpg',
               'The Bates badge on the skirt', 0, 1
  UNION ALL SELECT '/uploads/products/bates-general-purpose-saddle-06.jpg',
               'The Bates Saddles stamp and girth billets beneath the flap', 0, 2
  ) v
 WHERE p.`slug` = 'bates-general-purpose-saddle'
   AND NOT EXISTS (SELECT 1 FROM `product_images` i
                    WHERE i.`product_id` = p.`id` AND i.`path` = v.`path`);

UPDATE `product_images` i JOIN `products` p ON p.`id` = i.`product_id`
   SET i.`is_primary` = IF(i.`path` = '/uploads/products/bates-general-purpose-saddle-01.jpg', 1, 0)
 WHERE p.`slug` = 'bates-general-purpose-saddle';
