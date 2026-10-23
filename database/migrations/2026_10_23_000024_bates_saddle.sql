-- =====================================================================
--  Bates General Purpose Saddle
--
--  A maker we had not carried. It sits on the Saddles section beside the
--  Arena and the black leather general purpose saddle, rather than on a
--  shelf of its own: Wintec, Jeffries and Thorowgood have shelves because
--  there is more than one of each, and there is one Bates.
--
--  Additive and idempotent, matched on slug.
-- =====================================================================

SET NAMES utf8mb4;

INSERT INTO `brands` (`name`, `slug`, `sort_order`, `is_active`)
VALUES ('Bates Saddles', 'bates-saddles', 0, 1)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `is_active` = 1;

INSERT INTO `products`
  (`category_id`, `brand_id`, `name`, `slug`, `sku`, `short_desc`, `description`, `specifications`,
   `sizing_guide`, `price_visible`, `buyable`, `stock_status`, `is_featured`, `is_new`, `is_active`,
   `sort_order`, `created_at`, `updated_at`)
VALUES (
  (SELECT id FROM `categories` WHERE `slug` = 'saddles'),
  (SELECT id FROM `brands` WHERE `slug` = 'bates-saddles'),
  'Bates General Purpose Saddle', 'bates-general-purpose-saddle', 'TR-SAD-008',
  'A black leather general purpose saddle from Bates, with the changeable gullet system.',
  'A Bates general purpose saddle in black leather, cut to school, jump and hack the same horse without asking the rider to sit differently for each.

Bates build around a gullet that can be changed rather than a tree that cannot, so the saddle can be refitted as a horse muscles up or lets down instead of being sold on. Ours is fitted on the horse before it leaves us, and we will come back to it as the horse changes.',
  'Maker: Bates Saddles
Leather: black
Cut: general purpose
Gullet: changeable',
  'Fitted on the horse. Tell us the horse and the work and we will bring the right seat sizes out to you.',
  0, 0, 'in_stock', 0, 1, 1, 3, NOW(), NOW()
)
ON DUPLICATE KEY UPDATE
  `category_id`    = VALUES(`category_id`),
  `brand_id`       = VALUES(`brand_id`),
  `name`           = VALUES(`name`),
  `sku`            = VALUES(`sku`),
  `short_desc`     = VALUES(`short_desc`),
  `description`    = VALUES(`description`),
  `specifications` = VALUES(`specifications`),
  `sizing_guide`   = VALUES(`sizing_guide`),
  `is_active`      = 1;
