-- =====================================================================
--  Stirrup irons and leathers on one shelf
--
--  The section is called Martingales, Stirrups & Leathers and its own
--  description promises stirrup leathers, but there was no listing for
--  them and nowhere obvious to put one: the shelf was named for irons
--  alone. It is renamed to hold both, and the leathers are added.
--
--  Additive and idempotent. Matched on slug, nothing is deleted: the
--  irons keep their ids and simply hang off the renamed shelf, and the
--  old shelf is hidden once empty, with /shop/stirrup-irons redirecting
--  through RETIRED_CATEGORIES in ShopController.
-- =====================================================================

SET NAMES utf8mb4;

-- ---------------------------------------------------------------------
--  The shelf, under its new name.
-- ---------------------------------------------------------------------
INSERT INTO `categories` (`slug`, `name`, `tagline`, `description`, `meta_title`, `meta_desc`, `sort_order`, `is_active`)
VALUES (
  'stirrup-irons-leathers', 'Stirrup Irons & Leathers', 'Steel, Safety & Composite',
  'Stainless steel, peacock safety and lightweight composite stirrup irons, and the leathers that hang them.',
  'Stirrup Irons & Stirrup Leathers',
  'Stainless steel, peacock safety and composite stirrup irons, and leather stirrup leathers. Stocked at Tack Rack, Ngong Road, Nairobi.',
  2, 1
)
ON DUPLICATE KEY UPDATE
  `name` = VALUES(`name`), `tagline` = VALUES(`tagline`), `description` = VALUES(`description`),
  `meta_title` = VALUES(`meta_title`), `meta_desc` = VALUES(`meta_desc`),
  `sort_order` = VALUES(`sort_order`), `is_active` = 1;

UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'martingales-stirrups-leathers'
   SET c.`parent_id` = p.`id` WHERE c.`slug` = 'stirrup-irons-leathers';

-- ---------------------------------------------------------------------
--  Everything that was on the irons shelf moves across, ids intact.
-- ---------------------------------------------------------------------
UPDATE `products` pr
  JOIN `categories` old ON old.`id` = pr.`category_id` AND old.`slug` = 'stirrup-irons'
  JOIN `categories` new ON new.`slug` = 'stirrup-irons-leathers'
   SET pr.`category_id` = new.`id`;

UPDATE `categories` SET `is_active` = 0
 WHERE `slug` = 'stirrup-irons'
   AND NOT EXISTS (SELECT 1 FROM (SELECT * FROM `products`) p
                    WHERE p.`category_id` = `categories`.`id` AND p.`is_active` = 1);

-- ---------------------------------------------------------------------
--  The leathers themselves.
-- ---------------------------------------------------------------------
INSERT INTO `products`
  (`category_id`, `brand_id`, `name`, `slug`, `sku`, `short_desc`, `description`, `specifications`,
   `sizing_guide`, `price_visible`, `buyable`, `stock_status`, `is_featured`, `is_new`, `is_active`,
   `sort_order`, `created_at`, `updated_at`)
VALUES (
  (SELECT id FROM `categories` WHERE `slug` = 'stirrup-irons-leathers'),
  NULL,
  'Stirrup Leathers', 'stirrup-leathers', 'TR-MST-008',
  'Leather stirrup leathers, sold in pairs, to hang irons from the saddle.',
  'Stirrup leathers in black leather, sold as a pair. They carry the rider''s weight every stride, so they are the one strap on a saddle worth replacing before it argues: stretched holes, a thinning fold at the buckle or a buckle that no longer sits flat are all reasons to change them.

Bring your saddle in and we will fit the length to you rather than to the chart — a leather that is right for a long-legged rider on a 17" saddle is wrong for a child on a 15".',
  'Material: leather
Sold: in pairs
Fitting: measured against the rider and the saddle, in person',
  'Measured in inches from the buckle. Most adults ride on 48" to 54"; a short-legged rider or a small pony may want 42" to 48". We measure you in the shop rather than guess.',
  0, 0, 'in_stock', 0, 1, 1, 4, NOW(), NOW()
)
ON DUPLICATE KEY UPDATE
  `category_id`    = VALUES(`category_id`),
  `name`           = VALUES(`name`),
  `sku`            = VALUES(`sku`),
  `short_desc`     = VALUES(`short_desc`),
  `description`    = VALUES(`description`),
  `specifications` = VALUES(`specifications`),
  `sizing_guide`   = VALUES(`sizing_guide`),
  `is_active`      = 1;
