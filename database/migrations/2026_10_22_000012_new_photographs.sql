-- =====================================================================
--  Photographs for the listings added alongside them
--
--  Generated 2026-10-22 11:11 by bin/export-photographs-sql.php. Do not edit by hand.
--
--  The image files travel in the repository under public/uploads/products;
--  these are only the rows that point at them. Idempotent: a row goes in
--  only where that path is not already on the product, and the lead image
--  is chosen by path, so running this twice changes nothing.
-- =====================================================================

SET NAMES utf8mb4;

-- Coligone Calm Down Paste
INSERT INTO `product_images` (`product_id`, `path`, `alt`, `is_primary`, `sort_order`)
SELECT p.`id`, '/uploads/products/coligone-calm-down-paste-01.jpg', 'The 50g syringe of Coligone Calm Down paste', 1, 0 FROM `products` p
 WHERE p.`slug` = 'coligone-calm-down-paste'
   AND NOT EXISTS (SELECT 1 FROM `product_images` i
                    WHERE i.`product_id` = p.`id` AND i.`path` = '/uploads/products/coligone-calm-down-paste-01.jpg');
UPDATE `product_images` i JOIN `products` p ON p.`id` = i.`product_id`
   SET i.`is_primary` = IF(i.`path` = '/uploads/products/coligone-calm-down-paste-01.jpg', 1, 0)
 WHERE p.`slug` = 'coligone-calm-down-paste';

-- Coligone PreTravel Paste
INSERT INTO `product_images` (`product_id`, `path`, `alt`, `is_primary`, `sort_order`)
SELECT p.`id`, '/uploads/products/coligone-pretravel-paste-01.jpg', 'The 50g syringe of Coligone PreTravel paste', 1, 0 FROM `products` p
 WHERE p.`slug` = 'coligone-pretravel-paste'
   AND NOT EXISTS (SELECT 1 FROM `product_images` i
                    WHERE i.`product_id` = p.`id` AND i.`path` = '/uploads/products/coligone-pretravel-paste-01.jpg');
UPDATE `product_images` i JOIN `products` p ON p.`id` = i.`product_id`
   SET i.`is_primary` = IF(i.`path` = '/uploads/products/coligone-pretravel-paste-01.jpg', 1, 0)
 WHERE p.`slug` = 'coligone-pretravel-paste';

-- Cotton Pimple-Grip Riding Gloves — Black
INSERT INTO `product_images` (`product_id`, `path`, `alt`, `is_primary`, `sort_order`)
SELECT p.`id`, '/uploads/products/cotton-pimple-grip-riding-gloves-01.jpg', 'A pair of black cotton riding gloves, palms up, showing the pimple grip', 1, 0 FROM `products` p
 WHERE p.`slug` = 'cotton-pimple-grip-riding-gloves'
   AND NOT EXISTS (SELECT 1 FROM `product_images` i
                    WHERE i.`product_id` = p.`id` AND i.`path` = '/uploads/products/cotton-pimple-grip-riding-gloves-01.jpg');
UPDATE `product_images` i JOIN `products` p ON p.`id` = i.`product_id`
   SET i.`is_primary` = IF(i.`path` = '/uploads/products/cotton-pimple-grip-riding-gloves-01.jpg', 1, 0)
 WHERE p.`slug` = 'cotton-pimple-grip-riding-gloves';

-- Hook Polo Grip Pro Gloves
INSERT INTO `product_images` (`product_id`, `path`, `alt`, `is_primary`, `sort_order`)
SELECT p.`id`, '/uploads/products/hook-polo-grip-pro-gloves-01.jpg', 'The Hook Polo Grip Pro glove, showing the leather palm and the printed back', 1, 0 FROM `products` p
 WHERE p.`slug` = 'hook-polo-grip-pro-gloves'
   AND NOT EXISTS (SELECT 1 FROM `product_images` i
                    WHERE i.`product_id` = p.`id` AND i.`path` = '/uploads/products/hook-polo-grip-pro-gloves-01.jpg');
UPDATE `product_images` i JOIN `products` p ON p.`id` = i.`product_id`
   SET i.`is_primary` = IF(i.`path` = '/uploads/products/hook-polo-grip-pro-gloves-01.jpg', 1, 0)
 WHERE p.`slug` = 'hook-polo-grip-pro-gloves';

-- Red Gorilla Feed Tub
INSERT INTO `product_images` (`product_id`, `path`, `alt`, `is_primary`, `sort_order`)
SELECT p.`id`, '/uploads/products/red-gorilla-feed-tub-01.jpg', 'The flexible tub in black, with both handles', 1, 0 FROM `products` p
 WHERE p.`slug` = 'red-gorilla-feed-tub'
   AND NOT EXISTS (SELECT 1 FROM `product_images` i
                    WHERE i.`product_id` = p.`id` AND i.`path` = '/uploads/products/red-gorilla-feed-tub-01.jpg');
UPDATE `product_images` i JOIN `products` p ON p.`id` = i.`product_id`
   SET i.`is_primary` = IF(i.`path` = '/uploads/products/red-gorilla-feed-tub-01.jpg', 1, 0)
 WHERE p.`slug` = 'red-gorilla-feed-tub';

-- Wintec 500 All Purpose Saddle
INSERT INTO `product_images` (`product_id`, `path`, `alt`, `is_primary`, `sort_order`)
SELECT p.`id`, '/uploads/products/wintec-500-all-purpose-saddle-01.jpg', 'The Wintec 500 flap and girth billets, with the 42cm seat marker', 1, 0 FROM `products` p
 WHERE p.`slug` = 'wintec-500-all-purpose-saddle'
   AND NOT EXISTS (SELECT 1 FROM `product_images` i
                    WHERE i.`product_id` = p.`id` AND i.`path` = '/uploads/products/wintec-500-all-purpose-saddle-01.jpg');
UPDATE `product_images` i JOIN `products` p ON p.`id` = i.`product_id`
   SET i.`is_primary` = IF(i.`path` = '/uploads/products/wintec-500-all-purpose-saddle-01.jpg', 1, 0)
 WHERE p.`slug` = 'wintec-500-all-purpose-saddle';

-- Arena Leather General Purpose Saddle
INSERT INTO `product_images` (`product_id`, `path`, `alt`, `is_primary`, `sort_order`)
SELECT p.`id`, '/uploads/products/arena-leather-general-purpose-saddle-01.jpg', 'Full side profile of the Arena leather general purpose saddle', 1, 0 FROM `products` p
 WHERE p.`slug` = 'arena-leather-general-purpose-saddle'
   AND NOT EXISTS (SELECT 1 FROM `product_images` i
                    WHERE i.`product_id` = p.`id` AND i.`path` = '/uploads/products/arena-leather-general-purpose-saddle-01.jpg');
INSERT INTO `product_images` (`product_id`, `path`, `alt`, `is_primary`, `sort_order`)
SELECT p.`id`, '/uploads/products/arena-leather-general-purpose-saddle-03.jpg', 'The Arena Saddles stamp and girth billets beneath the flap', 0, 2 FROM `products` p
 WHERE p.`slug` = 'arena-leather-general-purpose-saddle'
   AND NOT EXISTS (SELECT 1 FROM `product_images` i
                    WHERE i.`product_id` = p.`id` AND i.`path` = '/uploads/products/arena-leather-general-purpose-saddle-03.jpg');
UPDATE `product_images` i JOIN `products` p ON p.`id` = i.`product_id`
   SET i.`is_primary` = IF(i.`path` = '/uploads/products/arena-leather-general-purpose-saddle-01.jpg', 1, 0)
 WHERE p.`slug` = 'arena-leather-general-purpose-saddle';

-- Wintec 2000 All Purpose Saddle
INSERT INTO `product_images` (`product_id`, `path`, `alt`, `is_primary`, `sort_order`)
SELECT p.`id`, '/uploads/products/wintec-2000-all-purpose-saddle-01.jpg', 'Full side profile of the Wintec 2000', 1, 0 FROM `products` p
 WHERE p.`slug` = 'wintec-2000-all-purpose-saddle'
   AND NOT EXISTS (SELECT 1 FROM `product_images` i
                    WHERE i.`product_id` = p.`id` AND i.`path` = '/uploads/products/wintec-2000-all-purpose-saddle-01.jpg');
INSERT INTO `product_images` (`product_id`, `path`, `alt`, `is_primary`, `sort_order`)
SELECT p.`id`, '/uploads/products/wintec-2000-all-purpose-saddle-02.jpg', 'Seat and cantle, showing the suede-effect grip', 0, 1 FROM `products` p
 WHERE p.`slug` = 'wintec-2000-all-purpose-saddle'
   AND NOT EXISTS (SELECT 1 FROM `product_images` i
                    WHERE i.`product_id` = p.`id` AND i.`path` = '/uploads/products/wintec-2000-all-purpose-saddle-02.jpg');
INSERT INTO `product_images` (`product_id`, `path`, `alt`, `is_primary`, `sort_order`)
SELECT p.`id`, '/uploads/products/wintec-2000-all-purpose-saddle-03.jpg', 'Knee roll and flap detail', 0, 2 FROM `products` p
 WHERE p.`slug` = 'wintec-2000-all-purpose-saddle'
   AND NOT EXISTS (SELECT 1 FROM `product_images` i
                    WHERE i.`product_id` = p.`id` AND i.`path` = '/uploads/products/wintec-2000-all-purpose-saddle-03.jpg');
INSERT INTO `product_images` (`product_id`, `path`, `alt`, `is_primary`, `sort_order`)
SELECT p.`id`, '/uploads/products/wintec-2000-all-purpose-saddle-04.jpg', 'Seat and pommel from above', 0, 3 FROM `products` p
 WHERE p.`slug` = 'wintec-2000-all-purpose-saddle'
   AND NOT EXISTS (SELECT 1 FROM `product_images` i
                    WHERE i.`product_id` = p.`id` AND i.`path` = '/uploads/products/wintec-2000-all-purpose-saddle-04.jpg');
INSERT INTO `product_images` (`product_id`, `path`, `alt`, `is_primary`, `sort_order`)
SELECT p.`id`, '/uploads/products/wintec-2000-all-purpose-saddle-05.jpg', 'Wintec 2000 label and girth billet straps', 0, 4 FROM `products` p
 WHERE p.`slug` = 'wintec-2000-all-purpose-saddle'
   AND NOT EXISTS (SELECT 1 FROM `product_images` i
                    WHERE i.`product_id` = p.`id` AND i.`path` = '/uploads/products/wintec-2000-all-purpose-saddle-05.jpg');
INSERT INTO `product_images` (`product_id`, `path`, `alt`, `is_primary`, `sort_order`)
SELECT p.`id`, '/uploads/products/wintec-2000-all-purpose-saddle-06.jpg', 'The Wintec 2000 flap badge and girth billets from beneath', 0, 5 FROM `products` p
 WHERE p.`slug` = 'wintec-2000-all-purpose-saddle'
   AND NOT EXISTS (SELECT 1 FROM `product_images` i
                    WHERE i.`product_id` = p.`id` AND i.`path` = '/uploads/products/wintec-2000-all-purpose-saddle-06.jpg');
UPDATE `product_images` i JOIN `products` p ON p.`id` = i.`product_id`
   SET i.`is_primary` = IF(i.`path` = '/uploads/products/wintec-2000-all-purpose-saddle-01.jpg', 1, 0)
 WHERE p.`slug` = 'wintec-2000-all-purpose-saddle';

