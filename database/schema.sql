-- =====================================================================
--  TACK RACK KENYA - Database Schema
--  MySQL / MariaDB 10.4+  |  utf8mb4
-- =====================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
--  Admin users
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`           VARCHAR(120)  NOT NULL,
  `email`          VARCHAR(190)  NOT NULL,
  `password_hash`  VARCHAR(255)  NOT NULL,
  `role`           ENUM('admin','manager') NOT NULL DEFAULT 'manager',
  `is_active`      TINYINT(1)    NOT NULL DEFAULT 1,
  `last_login_at`  DATETIME      NULL,
  `created_at`     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  Categories  (self-referencing: RIDER / HORSE / STABLE -> children)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_id`     INT UNSIGNED NULL,
  `name`          VARCHAR(150)  NOT NULL,
  `slug`          VARCHAR(170)  NOT NULL,
  `tagline`       VARCHAR(255)  NULL,
  `description`   TEXT          NULL,
  `image`         VARCHAR(255)  NULL,
  `sort_order`    INT           NOT NULL DEFAULT 0,
  `is_active`     TINYINT(1)    NOT NULL DEFAULT 1,
  `created_at`    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_categories_slug` (`slug`),
  KEY `ix_categories_parent` (`parent_id`),
  CONSTRAINT `fk_categories_parent` FOREIGN KEY (`parent_id`)
    REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  Brands  (supplier / marque logos)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `brands`;
CREATE TABLE `brands` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(150) NOT NULL,
  `slug`        VARCHAR(170) NOT NULL,
  `logo`        VARCHAR(255) NULL,
  `description` TEXT         NULL,
  `sort_order`  INT          NOT NULL DEFAULT 0,
  `is_active`   TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_brands_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  Products
--  price_visible = 0  ->  item behaves as a pure quote-request entry
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id`    INT UNSIGNED NULL,
  `brand_id`       INT UNSIGNED NULL,
  `name`           VARCHAR(200)  NOT NULL,
  `slug`           VARCHAR(220)  NOT NULL,
  `sku`            VARCHAR(80)   NULL,
  `short_desc`     VARCHAR(500)  NULL,
  `description`    TEXT          NULL,
  `specifications` TEXT          NULL,
  `sizing_guide`   TEXT          NULL,
  `price`          DECIMAL(12,2) NULL,
  `price_visible`  TINYINT(1)    NOT NULL DEFAULT 0,
  `stock_status`   ENUM('in_stock','low_stock','on_order','out_of_stock') NOT NULL DEFAULT 'in_stock',
  `is_featured`    TINYINT(1)    NOT NULL DEFAULT 0,
  `is_new`         TINYINT(1)    NOT NULL DEFAULT 0,
  `is_active`      TINYINT(1)    NOT NULL DEFAULT 1,
  `views`          INT UNSIGNED  NOT NULL DEFAULT 0,
  `sort_order`     INT           NOT NULL DEFAULT 0,
  `meta_title`     VARCHAR(190)  NULL,
  `meta_desc`      VARCHAR(300)  NULL,
  `created_at`     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_products_slug` (`slug`),
  KEY `ix_products_category` (`category_id`),
  KEY `ix_products_brand` (`brand_id`),
  KEY `ix_products_flags` (`is_active`,`is_featured`),
  FULLTEXT KEY `ft_products_search` (`name`,`short_desc`,`description`),
  CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`)
    REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_products_brand` FOREIGN KEY (`brand_id`)
    REFERENCES `brands` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  Product images  (high-resolution gallery)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `product_images`;
CREATE TABLE `product_images` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` INT UNSIGNED NOT NULL,
  `path`       VARCHAR(255) NOT NULL,
  `alt`        VARCHAR(200) NULL,
  `is_primary` TINYINT(1)   NOT NULL DEFAULT 0,
  `sort_order` INT          NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `ix_pimages_product` (`product_id`),
  CONSTRAINT `fk_pimages_product` FOREIGN KEY (`product_id`)
    REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  Product variants (sizes / colours offered for selection on the quote)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `product_variants`;
CREATE TABLE `product_variants` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` INT UNSIGNED NOT NULL,
  `label`      VARCHAR(80)  NOT NULL,
  `value`      VARCHAR(120) NOT NULL,
  `sort_order` INT          NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `ix_variants_product` (`product_id`),
  CONSTRAINT `fk_variants_product` FOREIGN KEY (`product_id`)
    REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  Quote requests  (the primary conversion of the site)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `quotes`;
CREATE TABLE `quotes` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `reference`     VARCHAR(30)   NOT NULL,
  `customer_name` VARCHAR(150)  NOT NULL,
  `email`         VARCHAR(190)  NOT NULL,
  `phone`         VARCHAR(60)   NOT NULL,
  `location`      VARCHAR(150)  NULL,
  `discipline`    VARCHAR(80)   NULL,
  `notes`         TEXT          NULL,
  `status`        ENUM('new','in_review','quoted','won','closed') NOT NULL DEFAULT 'new',
  `admin_notes`   TEXT          NULL,
  `quoted_total`  DECIMAL(12,2) NULL,
  `ip_address`    VARCHAR(45)   NULL,
  `created_at`    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_quotes_reference` (`reference`),
  KEY `ix_quotes_status` (`status`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `quote_items`;
CREATE TABLE `quote_items` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `quote_id`     INT UNSIGNED NOT NULL,
  `product_id`   INT UNSIGNED NULL,
  `product_name` VARCHAR(200) NOT NULL,
  `product_sku`  VARCHAR(80)  NULL,
  `variant`      VARCHAR(160) NULL,
  `quantity`     INT UNSIGNED NOT NULL DEFAULT 1,
  `unit_price`   DECIMAL(12,2) NULL,
  PRIMARY KEY (`id`),
  KEY `ix_qitems_quote` (`quote_id`),
  CONSTRAINT `fk_qitems_quote` FOREIGN KEY (`quote_id`)
    REFERENCES `quotes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_qitems_product` FOREIGN KEY (`product_id`)
    REFERENCES `products` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  General contact / enquiry messages
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(150) NOT NULL,
  `email`      VARCHAR(190) NOT NULL,
  `phone`      VARCHAR(60)  NULL,
  `subject`    VARCHAR(200) NULL,
  `body`       TEXT         NOT NULL,
  `is_read`    TINYINT(1)   NOT NULL DEFAULT 0,
  `ip_address` VARCHAR(45)  NULL,
  `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `ix_messages_read` (`is_read`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  Editable site settings (key/value)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `key_name`   VARCHAR(100) NOT NULL,
  `value`      TEXT         NULL,
  `group_name` VARCHAR(60)  NOT NULL DEFAULT 'general',
  `label`      VARCHAR(150) NULL,
  `input_type` ENUM('text','textarea','email','tel','url','image') NOT NULL DEFAULT 'text',
  `sort_order` INT          NOT NULL DEFAULT 0,
  PRIMARY KEY (`key_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
--  CMS pages (Heritage, How to Order, Quote Process, Privacy, Terms)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `pages`;
CREATE TABLE `pages` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug`       VARCHAR(170) NOT NULL,
  `title`      VARCHAR(200) NOT NULL,
  `subtitle`   VARCHAR(300) NULL,
  `body`       LONGTEXT     NULL,
  `meta_desc`  VARCHAR(300) NULL,
  `is_active`  TINYINT(1)   NOT NULL DEFAULT 1,
  `updated_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pages_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
