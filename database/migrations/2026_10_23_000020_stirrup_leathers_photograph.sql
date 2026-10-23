-- =====================================================================
--  The stirrup leathers photograph
--
--  The file travels in the repository under public/uploads/products; this
--  is only the row that points at it. Idempotent: it goes in only where
--  that path is not already on the listing, and the lead is set by path
--  rather than by shuffling sort_order.
-- =====================================================================

SET NAMES utf8mb4;

INSERT INTO `product_images` (`product_id`, `path`, `alt`, `is_primary`, `sort_order`)
SELECT p.`id`,
       '/uploads/products/stirrup-leathers-01.jpg',
       'A pair of black leather stirrup leathers, coiled, with the buckles showing',
       1, 0
  FROM `products` p
 WHERE p.`slug` = 'stirrup-leathers'
   AND NOT EXISTS (SELECT 1 FROM `product_images` i
                    WHERE i.`product_id` = p.`id`
                      AND i.`path` = '/uploads/products/stirrup-leathers-01.jpg');

UPDATE `product_images` i JOIN `products` p ON p.`id` = i.`product_id`
   SET i.`is_primary` = IF(i.`path` = '/uploads/products/stirrup-leathers-01.jpg', 1, 0)
 WHERE p.`slug` = 'stirrup-leathers';
