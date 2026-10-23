-- =====================================================================
--  Cortaflex under its full name, and Equimins Biotin 15 added
--
--  Cortaflex HA was already shelved under Joint & Muscle Care but listed
--  under a shortened name, with a description that said little beyond the
--  pack size. It takes the name on the pouch and a description of what is
--  actually in it.
--
--  Equimins Biotin 15 was not carried at all. It is a hoof supplement, so
--  it goes under Hoof Care on the Hoof Supplements shelf — not beside
--  Cortaflex, which is a joint supplement.
--
--  Additive and idempotent. Matched on slug, so the Cortaflex listing
--  keeps its id and the orders and quotes pointing at it are undisturbed.
-- =====================================================================

SET NAMES utf8mb4;

-- ---------------------------------------------------------------------
--  Cortaflex, correctly named and described.
-- ---------------------------------------------------------------------
UPDATE `products`
   SET `name`        = 'Cortaflex HA Regular Strength Powder',
       `short_desc`  = 'Regular strength joint powder, with collagen, hyaluronic acid and MSM.',
       `description` = 'Cortaflex HA Regular Strength Powder from Equine America, fed to support joint, tendon and ligament health rather than to treat a problem already there.

It carries collagen, hyaluronic acid and MSM, with amino acids and trace minerals alongside them. Fed at 7g a day, the 900g pouch runs four months for a 500kg horse.

There is a double strength in the range as well; ask us which suits the horse and the work.',
       `specifications` = 'Maker: Equine America
Size: 900g
Feeding rate: 7g a day for a 500kg horse
Supply: 4 months
Key ingredients: collagen, hyaluronic acid, MSM, amino acids, trace minerals'
 WHERE `slug` IN ('cortaflex-ha', 'equine-america-cortaflex-ha');

-- Wherever it sits, make sure it is on the joint supplements shelf.
UPDATE `products` pr JOIN `categories` c ON c.`slug` = 'joint-supplements'
   SET pr.`category_id` = c.`id`
 WHERE pr.`slug` IN ('cortaflex-ha', 'equine-america-cortaflex-ha');

-- ---------------------------------------------------------------------
--  Equimins Biotin 15 — a hoof supplement, on the hoof shelf.
-- ---------------------------------------------------------------------
INSERT INTO `products`
  (`category_id`, `brand_id`, `name`, `slug`, `sku`, `short_desc`, `description`, `specifications`,
   `sizing_guide`, `price_visible`, `buyable`, `stock_status`, `is_featured`, `is_new`, `is_active`,
   `sort_order`, `created_at`, `updated_at`)
VALUES (
  (SELECT id FROM `categories` WHERE `slug` = 'hoof-supplements'),
  (SELECT id FROM `brands` WHERE `slug` = 'equimins'),
  'Equimins Biotin 15', 'equimins-biotin-15', 'TR-HOO-022',
  'A hoof supplement giving 15mg of biotin a day, with zinc, methionine and copper.',
  'Equimins Biotin 15, a complementary feedstuff giving 15mg of biotin a day with generous levels of zinc, methionine and copper.

Fed to support healthy horn growth, and it tells on the skin and coat as well. Hoof supplements are slow work: horn grows about a centimetre a month, so give it a season before judging whether it has helped.',
  'Maker: Equimins
Size: 3kg
Biotin: 15mg a day
Also carries: zinc, methionine, copper',
  'Fed daily. A 3kg tub lasts a 500kg horse several months at the standard rate.',
  0, 0, 'in_stock', 0, 1, 1, 4, NOW(), NOW()
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
