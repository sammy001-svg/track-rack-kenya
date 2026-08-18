-- =====================================================================
--  Phase 2 — Services & booking, customer accounts, orders & payments
--  Additive only. Safe to run against an existing Phase 1 install.
-- =====================================================================

SET NAMES utf8mb4;

-- ---------------------------------------------------------------------
--  Customers  (storefront accounts — entirely separate from staff users)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `customers` (
  `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`              VARCHAR(150)  NOT NULL,
  `email`             VARCHAR(190)  NOT NULL,
  `phone`             VARCHAR(60)   NULL,
  `password_hash`     VARCHAR(255)  NOT NULL,
  `location`          VARCHAR(150)  NULL,
  `discipline`        VARCHAR(80)   NULL,
  `is_active`         TINYINT(1)    NOT NULL DEFAULT 1,
  `reset_token`       CHAR(64)      NULL,
  `reset_expires_at`  DATETIME      NULL,
  `last_login_at`     DATETIME      NULL,
  `created_at`        DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`        DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_customers_email` (`email`),
  KEY `ix_customers_reset` (`reset_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  Saved horse profiles — the sizes we ask for on every quote
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `customer_horses` (
  `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `customer_id`      INT UNSIGNED NOT NULL,
  `name`             VARCHAR(120)  NOT NULL,
  `height_hh`        DECIMAL(4,1)  NULL,
  `breed`            VARCHAR(120)  NULL,
  `discipline`       VARCHAR(80)   NULL,
  `saddle_seat_size` VARCHAR(40)   NULL,
  `gullet_width`     VARCHAR(40)   NULL,
  `rug_size`         VARCHAR(40)   NULL,
  `girth_size`       VARCHAR(40)   NULL,
  `bit_size`         VARCHAR(40)   NULL,
  `notes`            TEXT          NULL,
  `created_at`       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `ix_horses_customer` (`customer_id`),
  CONSTRAINT `fk_horses_customer` FOREIGN KEY (`customer_id`)
    REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  Services offered (saddle fitting, workshop repair, ...)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `services` (
  `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug`             VARCHAR(120)  NOT NULL,
  `name`             VARCHAR(150)  NOT NULL,
  `tagline`          VARCHAR(255)  NULL,
  `description`      TEXT          NULL,
  `what_to_expect`   TEXT          NULL,
  `duration_minutes` INT UNSIGNED  NULL,
  `price_from`       DECIMAL(12,2) NULL,
  `travel_available` TINYINT(1)    NOT NULL DEFAULT 1,
  `image`            VARCHAR(255)  NULL,
  `is_active`        TINYINT(1)    NOT NULL DEFAULT 1,
  `sort_order`       INT           NOT NULL DEFAULT 0,
  `created_at`       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_services_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  Bookings (saddle fitting appointments)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `bookings` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `reference`      VARCHAR(30)   NOT NULL,
  `service_id`     INT UNSIGNED  NULL,
  `customer_id`    INT UNSIGNED  NULL,
  `name`           VARCHAR(150)  NOT NULL,
  `email`          VARCHAR(190)  NOT NULL,
  `phone`          VARCHAR(60)   NOT NULL,
  `location`       VARCHAR(200)  NULL,
  `at_yard`        TINYINT(1)    NOT NULL DEFAULT 0,
  `horse_name`     VARCHAR(120)  NULL,
  `horse_details`  TEXT          NULL,
  `discipline`     VARCHAR(80)   NULL,
  `saddle_details` TEXT          NULL,
  `preferred_date` DATE          NULL,
  `preferred_slot` ENUM('morning','afternoon','flexible') NOT NULL DEFAULT 'flexible',
  `alternate_date` DATE          NULL,
  `notes`          TEXT          NULL,
  `status`         ENUM('new','confirmed','scheduled','completed','cancelled') NOT NULL DEFAULT 'new',
  `scheduled_at`   DATETIME      NULL,
  `fee`            DECIMAL(12,2) NULL,
  `admin_notes`    TEXT          NULL,
  `ip_address`     VARCHAR(45)   NULL,
  `created_at`     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_bookings_reference` (`reference`),
  KEY `ix_bookings_status` (`status`,`preferred_date`),
  KEY `ix_bookings_customer` (`customer_id`),
  CONSTRAINT `fk_bookings_service` FOREIGN KEY (`service_id`)
    REFERENCES `services` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_bookings_customer` FOREIGN KEY (`customer_id`)
    REFERENCES `customers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  Workshop repair requests
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `repair_requests` (
  `id`                INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `reference`         VARCHAR(30)   NOT NULL,
  `customer_id`       INT UNSIGNED  NULL,
  `name`              VARCHAR(150)  NOT NULL,
  `email`             VARCHAR(190)  NOT NULL,
  `phone`             VARCHAR(60)   NOT NULL,
  `location`          VARCHAR(200)  NULL,
  `item_type`         VARCHAR(120)  NOT NULL,
  `item_make`         VARCHAR(150)  NULL,
  `damage`            TEXT          NOT NULL,
  `urgency`           ENUM('standard','urgent','competition') NOT NULL DEFAULT 'standard',
  `status`            ENUM('new','assessing','quoted','approved','in_progress','ready','collected','cancelled') NOT NULL DEFAULT 'new',
  `quoted_amount`     DECIMAL(12,2) NULL,
  `estimated_ready`   DATE          NULL,
  `admin_notes`       TEXT          NULL,
  `ip_address`        VARCHAR(45)   NULL,
  `created_at`        DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`        DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_repairs_reference` (`reference`),
  KEY `ix_repairs_status` (`status`,`created_at`),
  KEY `ix_repairs_customer` (`customer_id`),
  CONSTRAINT `fk_repairs_customer` FOREIGN KEY (`customer_id`)
    REFERENCES `customers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `repair_photos` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `repair_id`  INT UNSIGNED NOT NULL,
  `path`       VARCHAR(255) NOT NULL,
  `caption`    VARCHAR(200) NULL,
  `uploaded_by` ENUM('customer','staff') NOT NULL DEFAULT 'customer',
  `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `ix_rphotos_repair` (`repair_id`),
  CONSTRAINT `fk_rphotos_repair` FOREIGN KEY (`repair_id`)
    REFERENCES `repair_requests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  Orders — only for items carrying a visible price.
--  Quote-only items continue to flow through `quotes`.
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `orders` (
  `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `reference`        VARCHAR(30)   NOT NULL,
  `customer_id`      INT UNSIGNED  NULL,
  `quote_id`         INT UNSIGNED  NULL,
  `customer_name`    VARCHAR(150)  NOT NULL,
  `email`            VARCHAR(190)  NOT NULL,
  `phone`            VARCHAR(60)   NOT NULL,
  `delivery_method`  ENUM('collect','nairobi','courier') NOT NULL DEFAULT 'collect',
  `delivery_address` TEXT          NULL,
  `delivery_town`    VARCHAR(120)  NULL,
  `subtotal`         DECIMAL(12,2) NOT NULL DEFAULT 0,
  `delivery_cost`    DECIMAL(12,2) NOT NULL DEFAULT 0,
  `total`            DECIMAL(12,2) NOT NULL DEFAULT 0,
  `amount_paid`      DECIMAL(12,2) NOT NULL DEFAULT 0,
  `status`           ENUM('pending','confirmed','processing','dispatched','completed','cancelled') NOT NULL DEFAULT 'pending',
  `payment_status`   ENUM('unpaid','partial','paid','refunded') NOT NULL DEFAULT 'unpaid',
  `notes`            TEXT          NULL,
  `admin_notes`      TEXT          NULL,
  `ip_address`       VARCHAR(45)   NULL,
  `created_at`       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_orders_reference` (`reference`),
  KEY `ix_orders_status` (`status`,`created_at`),
  KEY `ix_orders_payment` (`payment_status`),
  KEY `ix_orders_customer` (`customer_id`),
  CONSTRAINT `fk_orders_customer` FOREIGN KEY (`customer_id`)
    REFERENCES `customers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_orders_quote` FOREIGN KEY (`quote_id`)
    REFERENCES `quotes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `order_items` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id`     INT UNSIGNED NOT NULL,
  `product_id`   INT UNSIGNED NULL,
  `product_name` VARCHAR(200) NOT NULL,
  `product_sku`  VARCHAR(80)  NULL,
  `variant`      VARCHAR(160) NULL,
  `quantity`     INT UNSIGNED NOT NULL DEFAULT 1,
  `unit_price`   DECIMAL(12,2) NOT NULL DEFAULT 0,
  `line_total`   DECIMAL(12,2) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `ix_oitems_order` (`order_id`),
  CONSTRAINT `fk_oitems_order` FOREIGN KEY (`order_id`)
    REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_oitems_product` FOREIGN KEY (`product_id`)
    REFERENCES `products` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  Payments (M-Pesa STK push, bank transfer, cash on collection)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `payments` (
  `id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id`           INT UNSIGNED  NOT NULL,
  `method`             ENUM('mpesa','bank','cash','card') NOT NULL DEFAULT 'mpesa',
  `amount`             DECIMAL(12,2) NOT NULL,
  `status`             ENUM('pending','success','failed','cancelled') NOT NULL DEFAULT 'pending',
  `phone`              VARCHAR(20)   NULL,
  `merchant_request_id` VARCHAR(80)  NULL,
  `checkout_request_id` VARCHAR(80)  NULL,
  `mpesa_receipt`      VARCHAR(40)   NULL,
  `result_code`        VARCHAR(10)   NULL,
  `result_desc`        VARCHAR(255)  NULL,
  `raw_callback`       TEXT          NULL,
  `created_at`         DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`         DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `ix_payments_order` (`order_id`),
  KEY `ix_payments_checkout` (`checkout_request_id`),
  KEY `ix_payments_receipt` (`mpesa_receipt`),
  CONSTRAINT `fk_payments_order` FOREIGN KEY (`order_id`)
    REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  Link existing quotes to accounts where one exists
-- ---------------------------------------------------------------------
SET @has_col := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'quotes' AND COLUMN_NAME = 'customer_id'
);
SET @sql := IF(@has_col = 0,
  'ALTER TABLE `quotes` ADD COLUMN `customer_id` INT UNSIGNED NULL AFTER `reference`, ADD KEY `ix_quotes_customer` (`customer_id`)',
  'SELECT "quotes.customer_id already present"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Stock quantity tracking, so paid orders can decrement inventory
SET @has_col := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND COLUMN_NAME = 'stock_qty'
);
SET @sql := IF(@has_col = 0,
  'ALTER TABLE `products` ADD COLUMN `stock_qty` INT NULL AFTER `stock_status`, ADD COLUMN `buyable` TINYINT(1) NOT NULL DEFAULT 0 AFTER `price_visible`',
  'SELECT "products.stock_qty already present"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ---------------------------------------------------------------------
--  Seed the two services Tack Rack actually offers
-- ---------------------------------------------------------------------
INSERT INTO `services` (`slug`,`name`,`tagline`,`description`,`what_to_expect`,`duration_minutes`,`travel_available`,`is_active`,`sort_order`)
SELECT * FROM (SELECT
  'saddle-fitting' AS slug,
  'Saddle Fitting' AS name,
  'Fitted on the horse by a Society of Master Saddlers qualified fitter' AS tagline,
  'A saddle that does not fit will damage a horse''s back long before the rider notices it. Sharon Ashley is the only Saddle Fitter in East Africa qualified with the Society of Master Saddlers, and every saddle we supply is fitted on the horse — at the shop or at your yard.' AS description,
  'The horse is assessed standing and in work. We check wither clearance, panel contact, balance and billet alignment, then flock or adjust as needed. Bring the current saddle if you have one; if you are buying, we bring a selection to try.' AS what_to_expect,
  90 AS duration_minutes,
  1 AS travel_available,
  1 AS is_active,
  1 AS sort_order
) AS s
WHERE NOT EXISTS (SELECT 1 FROM `services` WHERE `slug` = 'saddle-fitting');

INSERT INTO `services` (`slug`,`name`,`tagline`,`description`,`what_to_expect`,`duration_minutes`,`travel_available`,`is_active`,`sort_order`)
SELECT * FROM (SELECT
  'workshop-repairs' AS slug,
  'Workshop Repairs' AS name,
  'We repair what most suppliers replace' AS tagline,
  'Our Nairobi workshop handles broken saddle trees, torn panels, re-flocking, restitching, replacement billets and nameplate engraving. Send photographs of the damage and we will assess and quote before any work begins.' AS description,
  'Send clear photographs of the damage from two or three angles. We assess, quote, and only start once you approve. Most repairs are turned around within a week; tree repairs take longer and we will tell you so up front.' AS what_to_expect,
  NULL AS duration_minutes,
  0 AS travel_available,
  1 AS is_active,
  2 AS sort_order
) AS s
WHERE NOT EXISTS (SELECT 1 FROM `services` WHERE `slug` = 'workshop-repairs');

-- ---------------------------------------------------------------------
--  New settings
-- ---------------------------------------------------------------------
INSERT IGNORE INTO `settings` (`key_name`,`value`,`group_name`,`label`,`input_type`,`sort_order`) VALUES
('enable_shop','1','commerce','Allow direct purchase of priced items (1/0)','text',1),
('delivery_nairobi','500','commerce','Nairobi delivery cost (KSh)','text',2),
('delivery_courier','1200','commerce','Countrywide courier cost (KSh)','text',3),
('free_delivery_over','25000','commerce','Free delivery above this order value (0 = never)','text',4),
('bank_details','','commerce','Bank transfer details shown at checkout','textarea',5),

('mpesa_enabled','0','mpesa','Enable M-Pesa STK push (1/0)','text',1),
('mpesa_env','sandbox','mpesa','Environment: sandbox or production','text',2),
('mpesa_shortcode','','mpesa','Business short code / Till number','text',3),
('mpesa_consumer_key','','mpesa','Daraja consumer key','text',4),
('mpesa_consumer_secret','','mpesa','Daraja consumer secret','text',5),
('mpesa_passkey','','mpesa','Daraja passkey','text',6),
('mpesa_account_ref','TACKRACK','mpesa','Account reference shown on the prompt','text',7),

('smtp_enabled','0','mail','Send email over SMTP (1/0)','text',1),
('smtp_host','','mail','SMTP host','text',2),
('smtp_port','587','mail','SMTP port (587 STARTTLS, 465 SSL)','text',3),
('smtp_user','','mail','SMTP username','text',4),
('smtp_pass','','mail','SMTP password','text',5),
('smtp_secure','tls','mail','Encryption: tls, ssl or none','text',6),
('smtp_from','no-reply@tackrack.co.ke','mail','From address','email',7),

('booking_recipient','','services','Saddle fitting notification email','email',1),
('repair_recipient','','services','Repair request notification email','email',2),
('fitting_fee_note','Fitting at the shop is free with a saddle purchase. Yard visits are charged by distance.','services','Fitting fee note','textarea',3);
