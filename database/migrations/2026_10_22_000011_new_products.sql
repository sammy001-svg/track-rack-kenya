-- =====================================================================
--  Products photographed after the main shoot
--
--  Six listings the catalogue did not carry, from the photographs sent on
--  16 October: two Coligone pastes, two pairs of gloves, the Wintec 500 and
--  the Arena leather general purpose saddle. Gloves had nowhere to live, so
--  a shelf is added for them under Clothing.
--
--  Additive and idempotent. Everything is matched on slug, so running it
--  twice changes nothing and existing rows keep their ids.
-- =====================================================================

SET NAMES utf8mb4;

-- ---------------------------------------------------------------------
--  Makers we had not stocked before.
-- ---------------------------------------------------------------------
INSERT INTO `brands` (`name`, `slug`, `sort_order`, `is_active`) VALUES
  ('Arena Saddles', 'arena-saddles', 0, 1),
  ('Hook Polo',     'hook-polo',     0, 1)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `is_active` = 1;

-- ---------------------------------------------------------------------
--  Gloves, a shelf of their own under Clothing.
-- ---------------------------------------------------------------------
INSERT INTO `categories` (`slug`, `name`, `tagline`, `description`, `meta_title`, `meta_desc`, `sort_order`, `is_active`)
VALUES (
  'gloves', 'Gloves', 'Riding & Polo',
  'Riding and polo gloves, cut for grip on the rein without deadening the feel of the horse''s mouth.',
  'Riding & Polo Gloves',
  'Cotton riding gloves and leather polo gloves, for grip on the rein in all weathers. Stocked at Tack Rack, Ngong Road, Nairobi.',
  4, 1
)
ON DUPLICATE KEY UPDATE
  `name` = VALUES(`name`), `tagline` = VALUES(`tagline`), `description` = VALUES(`description`),
  `meta_title` = VALUES(`meta_title`), `meta_desc` = VALUES(`meta_desc`),
  `sort_order` = VALUES(`sort_order`), `is_active` = 1;

UPDATE `categories` c JOIN `categories` p ON p.`slug` = 'clothing'
   SET c.`parent_id` = p.`id` WHERE c.`slug` = 'gloves';

-- ---------------------------------------------------------------------
--  The listings themselves. Category and brand are resolved by slug, so
--  this does not care what ids a given deployment happens to use.
-- ---------------------------------------------------------------------
INSERT INTO `products`
  (`category_id`, `brand_id`, `name`, `slug`, `sku`, `short_desc`, `description`, `specifications`,
   `price_visible`, `buyable`, `stock_status`, `is_featured`, `is_new`, `is_active`, `sort_order`,
   `created_at`, `updated_at`)
VALUES
  ((SELECT id FROM `categories` WHERE `slug` = 'gut-health'),
   (SELECT id FROM `brands` WHERE `slug` = 'coligone'),
   'Coligone Calm Down Paste', 'coligone-calm-down-paste', 'TR-GUT-007',
   'Coligone, nucleotides, prebiotics and plant extracts in a 50g syringe, to settle a horse from the inside.',
   'H Bradshaw''s Coligone Calm Down paste combines Coligone with nucleotides, prebiotics and plant extracts. A 50g graduated syringe, given before the moments a horse finds difficult — the lorry ramp, the warm-up, the farrier.',
   'Maker: H Bradshaw''s
Pack: 50g syringe',
   0, 0, 'in_stock', 0, 1, 1, 0, NOW(), NOW()),

  ((SELECT id FROM `categories` WHERE `slug` = 'gut-health'),
   (SELECT id FROM `brands` WHERE `slug` = 'coligone'),
   'Coligone PreTravel Paste', 'coligone-pretravel-paste', 'TR-GUT-008',
   'The same Coligone combination with electrolytes built in, for the start of a long or stressful day.',
   'H Bradshaw''s Coligone PreTravel paste: Coligone, nucleotides, prebiotics and plant extracts with electrolytes built in, for the perfect start to any stressful day. A 50g graduated syringe.',
   'Maker: H Bradshaw''s
Pack: 50g syringe',
   0, 0, 'in_stock', 0, 1, 1, 1, NOW(), NOW()),

  ((SELECT id FROM `categories` WHERE `slug` = 'gloves'),
   NULL,
   'Cotton Pimple-Grip Riding Gloves — Black', 'cotton-pimple-grip-riding-gloves', 'TR-CLO-011',
   'Cotton riding gloves with a pimpled palm, for grip on a wet rein without losing the feel of it.',
   'Black cotton riding gloves with a pimpled palm and fingers. Light enough to feel the rein through, close enough at the wrist to stay put, and cheap enough to keep a spare pair in the lorry.',
   'Material: cotton with PVC pimple grip
Closure: elasticated wrist',
   0, 0, 'in_stock', 0, 1, 1, 2, NOW(), NOW()),

  ((SELECT id FROM `categories` WHERE `slug` = 'gloves'),
   (SELECT id FROM `brands` WHERE `slug` = 'hook-polo'),
   'Hook Polo Grip Pro Gloves', 'hook-polo-grip-pro-gloves', 'TR-CLO-012',
   'Leather-palmed polo glove with a ventilated printed back and a wide wrist strap.',
   'The Hook Polo Grip Pro: a soft leather palm for the rein and the stick, a printed and perforated back to let the hand breathe, and a wide hook-and-loop wrist strap that takes the strain off the fingers through a chukka.',
   'Maker: Hook Polo
Palm: leather
Closure: wide wrist strap',
   0, 0, 'in_stock', 0, 1, 1, 3, NOW(), NOW()),

  ((SELECT id FROM `categories` WHERE `slug` = 'saddles-wintec'),
   (SELECT id FROM `brands` WHERE `slug` = 'wintec'),
   'Wintec 500 All Purpose Saddle', 'wintec-500-all-purpose-saddle', 'TR-SAD-006',
   'The synthetic all purpose Wintec, with the changeable gullet system, in a 42cm seat.',
   'The Wintec 500 in its all purpose cut: light, synthetic, and washable, with the EASY-CHANGE gullet system so the same saddle can be refitted as a horse changes shape. Fitted on the horse before it leaves us.',
   'Maker: Wintec
Seat: 42cm / 16½"
Gullet: EASY-CHANGE system',
   0, 0, 'in_stock', 0, 1, 1, 3, NOW(), NOW()),

  ((SELECT id FROM `categories` WHERE `slug` = 'saddles'),
   (SELECT id FROM `brands` WHERE `slug` = 'arena-saddles'),
   'Arena Leather General Purpose Saddle', 'arena-leather-general-purpose-saddle', 'TR-SAD-007',
   'A black leather general purpose saddle from Arena, cut to school, jump and hack the same horse.',
   'An Arena Saddles general purpose saddle in black leather, with a forward-cut flap and a knee roll shaped to hold the leg without locking it. Like every saddle we supply, it is fitted on the horse before it leaves us.',
   'Maker: Arena Saddles
Leather: black',
   0, 0, 'in_stock', 0, 1, 1, 2, NOW(), NOW())

ON DUPLICATE KEY UPDATE
  `category_id`    = VALUES(`category_id`),
  `brand_id`       = VALUES(`brand_id`),
  `name`           = VALUES(`name`),
  `sku`            = VALUES(`sku`),
  `short_desc`     = VALUES(`short_desc`),
  `description`    = VALUES(`description`),
  `specifications` = VALUES(`specifications`),
  `is_active`      = 1;
