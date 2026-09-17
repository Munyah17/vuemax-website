-- ============================================================
-- VUEMAX INDUSTRIES — DATABASE SCHEMA
-- File: /sql/schema.sql
-- Engine: InnoDB · Charset: utf8mb4 · Collation: utf8mb4_unicode_ci
--
-- Safe to re-run during development (drops + recreates).
-- ⚠️  Do NOT re-run this on production once real data exists.
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
-- 1. CATEGORIES  (top-level: Fencing / Steel / Hardware)
-- ============================================================
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug`         VARCHAR(80)  NOT NULL,
  `name`         VARCHAR(120) NOT NULL,
  `tagline`      VARCHAR(255) DEFAULT NULL,
  `description`  TEXT         DEFAULT NULL,
  `image`        VARCHAR(255) DEFAULT NULL,
  `hero_image`   VARCHAR(255) DEFAULT NULL,
  `icon`         VARCHAR(80)  DEFAULT NULL,
  `sort_order`   INT          NOT NULL DEFAULT 0,
  `is_featured`  TINYINT(1)   NOT NULL DEFAULT 0,
  `is_active`    TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_categories_slug` (`slug`),
  KEY `idx_categories_active` (`is_active`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 2. SUBCATEGORIES
-- ============================================================
DROP TABLE IF EXISTS `subcategories`;
CREATE TABLE `subcategories` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id`  INT UNSIGNED NOT NULL,
  `slug`         VARCHAR(80)  NOT NULL,
  `name`         VARCHAR(120) NOT NULL,
  `description`  VARCHAR(255) DEFAULT NULL,
  `sort_order`   INT          NOT NULL DEFAULT 0,
  `is_active`    TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_subcategories_slug` (`slug`),
  KEY `idx_subcategories_cat` (`category_id`, `is_active`, `sort_order`),
  CONSTRAINT `fk_subcategories_category`
    FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 3. PRODUCTS
-- ============================================================
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `subcategory_id`  INT UNSIGNED NOT NULL,
  `slug`            VARCHAR(120) NOT NULL,
  `name`            VARCHAR(160) NOT NULL,
  `short_desc`      VARCHAR(255) DEFAULT NULL,
  `long_desc`       TEXT         DEFAULT NULL,
  `unit`            VARCHAR(40)  DEFAULT 'roll',   -- roll | piece | metre | set | lot
  `price_usd`       DECIMAL(10,2) DEFAULT NULL,
  `price_zwl`       DECIMAL(12,2) DEFAULT NULL,
  `roll_metres`     DECIMAL(8,2)  DEFAULT NULL,    -- coverage per roll (for calculator)
  `post_price`      DECIMAL(10,2) DEFAULT NULL,    -- per post (for calculator)
  `top_wire_rate`   DECIMAL(10,4) DEFAULT NULL,    -- per metre
  `gate_price`      DECIMAL(10,2) DEFAULT NULL,    -- per gate
  `install_rate`    DECIMAL(10,4) DEFAULT NULL,    -- per metre
  `image`           VARCHAR(255) DEFAULT NULL,
  `gallery`         TEXT         DEFAULT NULL,     -- JSON array of image URLs
  `badge`           VARCHAR(40)  DEFAULT NULL,     -- e.g. 'Best Seller'
  `rating`          DECIMAL(2,1) DEFAULT NULL,     -- e.g. 4.8
  `reviews_count`   INT UNSIGNED DEFAULT 0,
  `is_featured`     TINYINT(1)   NOT NULL DEFAULT 0,
  `is_active`       TINYINT(1)   NOT NULL DEFAULT 1,
  `sort_order`      INT          NOT NULL DEFAULT 0,
  `created_at`      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_products_slug` (`slug`),
  KEY `idx_products_subcat` (`subcategory_id`, `is_active`, `sort_order`),
  KEY `idx_products_featured` (`is_featured`, `is_active`),
  CONSTRAINT `fk_products_subcategory`
    FOREIGN KEY (`subcategory_id`) REFERENCES `subcategories` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 4. PRODUCT SPECS  (label/value rows per product)
-- ============================================================
DROP TABLE IF EXISTS `product_specs`;
CREATE TABLE `product_specs` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id`  INT UNSIGNED NOT NULL,
  `label`       VARCHAR(120) NOT NULL,
  `value`       VARCHAR(255) NOT NULL,
  `sort_order`  INT          NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_specs_product` (`product_id`, `sort_order`),
  CONSTRAINT `fk_specs_product`
    FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 5. PRODUCT FEATURES  (bullet list per product)
-- ============================================================
DROP TABLE IF EXISTS `product_features`;
CREATE TABLE `product_features` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id`  INT UNSIGNED NOT NULL,
  `text`        VARCHAR(255) NOT NULL,
  `sort_order`  INT          NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_features_product` (`product_id`, `sort_order`),
  CONSTRAINT `fk_features_product`
    FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 6. PROJECTS  (installations gallery)
-- ============================================================
DROP TABLE IF EXISTS `projects`;
CREATE TABLE `projects` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug`         VARCHAR(120) NOT NULL,
  `title`        VARCHAR(200) NOT NULL,
  `category`     VARCHAR(40)  NOT NULL,   -- farms | residential | commercial | security
  `location`     VARCHAR(120) DEFAULT NULL,
  `year`         SMALLINT     DEFAULT NULL,
  `perimeter`    VARCHAR(60)  DEFAULT NULL,   -- e.g. '5km perimeter'
  `fence_type`   VARCHAR(80)  DEFAULT NULL,
  `spec_chips`   TEXT         DEFAULT NULL,   -- JSON array of small chips
  `image`        VARCHAR(255) DEFAULT NULL,
  `gallery`      TEXT         DEFAULT NULL,   -- JSON array
  `description`  TEXT         DEFAULT NULL,
  `is_featured`  TINYINT(1)   NOT NULL DEFAULT 0,
  `is_active`    TINYINT(1)   NOT NULL DEFAULT 1,
  `sort_order`   INT          NOT NULL DEFAULT 0,
  `created_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_projects_slug` (`slug`),
  KEY `idx_projects_cat` (`category`, `is_active`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 7. QUOTES  (saved from calculator / estimator)
-- ============================================================
DROP TABLE IF EXISTS `quotes`;
CREATE TABLE `quotes` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ref`           VARCHAR(40)  NOT NULL,     -- e.g. VX-2024-00001
  `source`        VARCHAR(20)  NOT NULL DEFAULT 'calculator',  -- calculator | estimator | admin
  `customer_name`  VARCHAR(160) DEFAULT NULL,
  `customer_phone` VARCHAR(60)  DEFAULT NULL,
  `customer_email` VARCHAR(160) DEFAULT NULL,
  `customer_notes` TEXT         DEFAULT NULL,

  -- Project parameters
  `perimeter`     DECIMAL(10,2) DEFAULT NULL,
  `corners`       INT           DEFAULT NULL,
  `fence_type`    VARCHAR(80)   DEFAULT NULL,
  `height`        DECIMAL(6,2)  DEFAULT NULL,
  `post_spacing`  DECIMAL(6,2)  DEFAULT NULL,

  -- Options (JSON booleans)
  `options`       TEXT          DEFAULT NULL,

  -- Totals
  `total_usd`     DECIMAL(12,2) DEFAULT NULL,
  `total_zwl`     DECIMAL(14,2) DEFAULT NULL,

  `status`        VARCHAR(20)   NOT NULL DEFAULT 'new',  -- new | sent | accepted | archived
  `ip_address`    VARCHAR(45)   DEFAULT NULL,
  `user_agent`    VARCHAR(255)  DEFAULT NULL,
  `created_at`    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_quotes_ref` (`ref`),
  KEY `idx_quotes_status` (`status`, `created_at`),
  KEY `idx_quotes_source` (`source`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 8. QUOTE ITEMS
-- ============================================================
DROP TABLE IF EXISTS `quote_items`;
CREATE TABLE `quote_items` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `quote_id`    INT UNSIGNED NOT NULL,
  `name`        VARCHAR(200) NOT NULL,
  `spec`        VARCHAR(255) DEFAULT NULL,
  `qty`         VARCHAR(60)  DEFAULT NULL,   -- '27 rolls', '324 pcs' — freeform to match UI
  `unit_price`  DECIMAL(12,2) DEFAULT NULL,
  `total`       DECIMAL(12,2) DEFAULT NULL,
  `sort_order`  INT          NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_quote_items_quote` (`quote_id`, `sort_order`),
  CONSTRAINT `fk_quote_items_quote`
    FOREIGN KEY (`quote_id`) REFERENCES `quotes` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 9. CONTACTS  (contact form submissions)
-- ============================================================
DROP TABLE IF EXISTS `contacts`;
CREATE TABLE `contacts` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(160) NOT NULL,
  `phone`       VARCHAR(60)  NOT NULL,
  `email`       VARCHAR(160) DEFAULT NULL,
  `subject`     VARCHAR(80)  DEFAULT NULL,   -- quote | product | installation | bulk | support | other
  `message`     TEXT         NOT NULL,
  `status`      VARCHAR(20)  NOT NULL DEFAULT 'new',   -- new | read | replied | archived
  `ip_address`  VARCHAR(45)  DEFAULT NULL,
  `user_agent`  VARCHAR(255) DEFAULT NULL,
  `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_contacts_status` (`status`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 10. FAQS  (optional — allows admin to manage FAQ later)
-- ============================================================
DROP TABLE IF EXISTS `faqs`;
CREATE TABLE `faqs` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `category`    VARCHAR(40)  NOT NULL DEFAULT 'general',
  `question`    VARCHAR(300) NOT NULL,
  `answer`      TEXT         NOT NULL,
  `sort_order`  INT          NOT NULL DEFAULT 0,
  `is_active`   TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_faqs_cat` (`category`, `is_active`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 10b. CUSTOMERS  (customer portal accounts — login via /account)
-- ============================================================
DROP TABLE IF EXISTS `order_events`;
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `customers`;

CREATE TABLE `customers` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`          VARCHAR(160) NOT NULL,
  `email`         VARCHAR(160) NOT NULL,
  `phone`         VARCHAR(60)  DEFAULT NULL,
  `company`       VARCHAR(160) DEFAULT NULL,
  `address`       VARCHAR(255) DEFAULT NULL,
  `password_hash` VARCHAR(255) NOT NULL,   -- password_hash()
  `status`        VARCHAR(20)  NOT NULL DEFAULT 'active',  -- active | suspended
  `last_login`    TIMESTAMP    NULL DEFAULT NULL,
  `created_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_customers_email` (`email`),
  KEY `idx_customers_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Customer permissions model (enforced in account/inc.php):
--   CAN: view own orders + tracking timeline, download own quote,
--        edit own profile, change own password
--   CANNOT: see other customers' data, modify orders, access /admin

-- ============================================================
-- 10c. ORDERS  (purchases + delivery pipeline, admin-managed)
-- ============================================================
CREATE TABLE `orders` (
  `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_no`        VARCHAR(40)  NOT NULL,          -- e.g. VO-2026-0001
  `customer_id`     INT UNSIGNED NOT NULL,
  `quote_id`        INT UNSIGNED DEFAULT NULL,       -- optional link to saved quote
  `status`          VARCHAR(30)  NOT NULL DEFAULT 'pending',
  -- pending | confirmed | processing | packed | shipped |
  -- out_for_delivery | delivered | cancelled
  `payment_status`  VARCHAR(20)  NOT NULL DEFAULT 'unpaid',  -- unpaid | deposit | paid
  `subtotal_usd`    DECIMAL(12,2) DEFAULT NULL,
  `delivery_usd`    DECIMAL(10,2) DEFAULT 0.00,
  `total_usd`       DECIMAL(12,2) DEFAULT NULL,
  `delivery_address` VARCHAR(255) DEFAULT NULL,
  `tracking_ref`    VARCHAR(80)   DEFAULT NULL,
  `driver_phone`    VARCHAR(60)   DEFAULT NULL,
  `eta`             DATE          DEFAULT NULL,
  `admin_notes`     TEXT          DEFAULT NULL,       -- internal only
  `created_at`      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_orders_no` (`order_no`),
  KEY `idx_orders_customer` (`customer_id`, `status`),
  KEY `idx_orders_status` (`status`, `created_at`),
  CONSTRAINT `fk_orders_customer` FOREIGN KEY (`customer_id`)
    REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_orders_quote` FOREIGN KEY (`quote_id`)
    REFERENCES `quotes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `order_items` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id`    INT UNSIGNED NOT NULL,
  `product_id`  INT UNSIGNED DEFAULT NULL,
  `name`        VARCHAR(200) NOT NULL,
  `spec`        VARCHAR(255) DEFAULT NULL,   -- '1.8m × 30m roll', '50 kg roll'
  `qty`         DECIMAL(10,2) NOT NULL DEFAULT 1,
  `unit_price`  DECIMAL(12,2) DEFAULT NULL,
  `total`       DECIMAL(12,2) DEFAULT NULL,
  `sort_order`  INT          NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_order_items_order` (`order_id`, `sort_order`),
  CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`)
    REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tracking timeline — one row per status change / delivery update,
-- shown to the customer on their order tracking page.
CREATE TABLE `order_events` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id`   INT UNSIGNED NOT NULL,
  `status`     VARCHAR(30)  NOT NULL,
  `note`       VARCHAR(255) DEFAULT NULL,   -- e.g. 'Left depot, ETA Thursday'
  `is_public`  TINYINT(1)   NOT NULL DEFAULT 1,  -- 0 = internal note, hidden from customer
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_order_events` (`order_id`, `created_at`),
  CONSTRAINT `fk_order_events` FOREIGN KEY (`order_id`)
    REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 10b. POST SETS — client pricing model
-- Each fence height uses a specific post length. Three post types:
--   corner posts (one per corner), standard posts (every 5 m),
--   supporter/stay posts (2 per corner post).
-- ============================================================
DROP TABLE IF EXISTS `post_sets`;
CREATE TABLE `post_sets` (
  `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `fence_height`    DECIMAL(3,1) NOT NULL,   -- fence height in m
  `post_length`     DECIMAL(3,1) NOT NULL,   -- post length in m
  `corner_price`    DECIMAL(10,2) NOT NULL,  -- USD per corner post
  `standard_price`  DECIMAL(10,2) NOT NULL,  -- USD per standard post (every 5 m)
  `supporter_price` DECIMAL(10,2) NOT NULL,  -- USD per supporter post (2 per corner)
  `sort_order`      INT          NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_post_height` (`fence_height`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `post_sets` (`fence_height`, `post_length`, `corner_price`, `standard_price`, `supporter_price`, `sort_order`) VALUES
(1.2, 1.8, 16.00,  8.00, 12.00, 1),
(1.5, 2.0, 13.00,  9.00, 13.00, 2),
(2.1, 2.6, 26.00, 16.00, 13.00, 3),
(2.4, 3.0, 33.00, 18.00, 15.00, 4),
(2.5, 3.0, 33.00, 18.00, 15.00, 5),
(3.0, 3.6, 40.00, 20.00, 16.00, 6);

-- ============================================================
-- 11. ADMIN USERS  (for Phase 3 admin panel)
-- ============================================================
DROP TABLE IF EXISTS `admin_users`;
CREATE TABLE `admin_users` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username`       VARCHAR(60)  NOT NULL,
  `email`          VARCHAR(160) NOT NULL,
  `password_hash`  VARCHAR(255) NOT NULL,   -- password_hash() with PASSWORD_DEFAULT
  `role`           VARCHAR(20)  NOT NULL DEFAULT 'admin', -- admin | editor | viewer
  `last_login`     TIMESTAMP    NULL DEFAULT NULL,
  `is_active`      TINYINT(1)   NOT NULL DEFAULT 1,
  `is_protected`   TINYINT(1)   NOT NULL DEFAULT 0,  -- primary admin: can never be deleted/deactivated
  `created_at`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_admin_username` (`username`),
  UNIQUE KEY `uk_admin_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Primary admin login (works straight after import):
--   Username: munyah   (or email: munyah777@gmail.com)
--   Password: griezmann17
-- is_protected=1 → this account cannot be deleted or deactivated.
INSERT INTO `admin_users` (`username`, `email`, `password_hash`, `role`, `is_protected`) VALUES
('munyah', 'munyah777@gmail.com', '$2y$10$TsLUFaPH6/8zhfFxASx0CugjSkNAZ.tWnSE2KPwAjSVn5/Vw.ZWzS', 'admin', 1);

-- ============================================================
-- SEED DATA
-- ============================================================

-- ---------- CATEGORIES ----------
INSERT INTO `categories` (`slug`, `name`, `tagline`, `description`, `image`, `sort_order`, `is_featured`, `is_active`) VALUES
('fencing',  'Fencing Solutions', 'Fencing & wire for every need',  'Diamond mesh, game fence, barbed wire, chicken mesh, posts and accessories for homes, farms and commercial sites.', 'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?auto=format&fit=crop&w=800&q=80', 1, 1, 1),
('steel',    'Steel Products',    'Steel for construction & industry', 'Steel sections, tubing, sheets, rebar and structural steel for construction, fabrication and industrial projects.', 'https://images.unsplash.com/photo-1565793298595-6a879b1d9492?auto=format&fit=crop&w=800&q=80', 2, 1, 1),
('hardware', 'General Hardware',  'Tools & everyday hardware',      'Tools, fixings, gates, hinges, locks and everyday hardware essentials for tradesmen, farms and DIY.', 'https://images.unsplash.com/photo-1581147036324-c17ac41dfa6c?auto=format&fit=crop&w=800&q=80', 3, 1, 1);

-- ---------- SUBCATEGORIES ----------
-- Fencing
INSERT INTO `subcategories` (`category_id`, `slug`, `name`, `description`, `sort_order`) VALUES
(1, 'diamond-mesh',  'Diamond Mesh',   'Versatile & durable',          1),
(1, 'game-fence',    'Game Fence',     'Heavy-duty wildlife',          2),
(1, 'barbed-wire',   'Barbed Wire',    'High-security wire',           3),
(1, 'chicken-mesh',  'Chicken Mesh',   'Lightweight poultry',          4),
(1, 'field-fence',   'Field Fence',    'General agricultural',         5),
(1, 'razor-wire',    'Razor Wire',     'Enhanced perimeter security',  6),
(1, 'fence-posts',   'Fence Posts',    'Wooden, steel & concrete',     7),
(1, 'accessories',   'Accessories',    'Gates, binding, clamps',       8);

-- Steel
INSERT INTO `subcategories` (`category_id`, `slug`, `name`, `description`, `sort_order`) VALUES
(2, 'steel-tubing',  'Steel Tubing',   'Square & rectangular',         1),
(2, 'steel-sheets',  'Steel Sheets',   'Flat & corrugated',            2),
(2, 'rebar',         'Rebar',          'Reinforcement bar',            3),
(2, 'structural',    'Structural Steel','I-beams, channels, angles',   4);

-- Hardware
INSERT INTO `subcategories` (`category_id`, `slug`, `name`, `description`, `sort_order`) VALUES
(3, 'gate-hardware', 'Gate Hardware',  'Locks, hinges, latches',       1),
(3, 'fixings',       'Fixings',        'Bolts, nuts, screws',          2),
(3, 'tools',         'Tools',          'Hand & power tools',           3);

-- Extra fencing subcategory (appended so existing subcategory IDs don't shift)
INSERT INTO `subcategories` (`category_id`, `slug`, `name`, `description`, `sort_order`) VALUES
(1, 'welded-mesh',   'Welded Mesh',    'Galvanised welded mesh rolls', 9);

-- ---------- PRODUCTS ----------
-- Fencing products (category_id = 1)
INSERT INTO `products`
(`subcategory_id`, `slug`, `name`, `short_desc`, `long_desc`, `unit`,
 `price_usd`, `roll_metres`, `post_price`, `top_wire_rate`, `gate_price`, `install_rate`,
 `image`, `badge`, `rating`, `reviews_count`, `is_featured`, `sort_order`) VALUES

(1, 'diamond-mesh', 'Diamond Mesh 50x50 (2mm)',
 'Versatile, durable and cost-effective fencing. 50x50mm aperture, 2mm wire, 30m rolls, heights 1.0m to 3.0m.',
 'Our diamond mesh fencing is designed for maximum strength and durability. 50x50mm aperture, 2mm wire gauge, hot-dip galvanised to resist rust and corrosion. Each roll is 30m long, available in heights from 1.0m to 3.0m. Also stocked in 2.5mm and 3.15mm gauges and 30x30mm aperture.',
 'roll', 65.00, 30, 8.00, 0.80, 180.00, 3.50,
 'assets/img/products/diamond-mesh-2.jpg',
 'Best Seller', 4.8, 120, 1, 1),

(2, 'game-fence', 'Game Fence',
 'Heavy-duty fencing for wildlife, farms and large properties.',
 'Manufactured from high-tensile galvanised wire, our game fence is built to withstand the demands of wildlife and livestock enclosures. Ideal for game reserves, large farms and perimeter security.',
 'roll', 280.00, 50, 12.00, 1.10, 220.00, 4.00,
 'assets/img/products/game-fence.jpg',
 NULL, 4.7, 86, 1, 2),

(3, 'barbed-wire', 'Barbed Wire 25 kg Roll',
 'High-tensile, high-security barbed wire for perimeter and farm protection. 25 kg roll.',
 'Hot-dip galvanised barbed wire with 3-strand twist, supplied in a 25 kg roll. Also available in a 50 kg roll. Perfect for farm perimeter security, commercial sites and any application requiring a low-cost but effective deterrent.',
 'roll', 38.00, 100, 8.00, 0.60, 160.00, 2.50,
 'assets/img/products/barbed-wire.jpg',
 NULL, 4.6, 74, 1, 3),

(4, 'chicken-mesh', 'Chicken Mesh',
 'Lightweight, galvanised mesh for poultry runs and small animal enclosures.',
 'Fine-gauge galvanised chicken mesh ideal for poultry runs, garden enclosures and small animal protection. Lightweight yet durable, resistant to rust and easy to install.',
 'roll', 32.00, 30, 6.00, 0.50, 140.00, 2.00,
 'https://images.unsplash.com/photo-1767416171650-4bff1da861fe?auto=format&fit=crop&w=600&q=80',
 NULL, 4.6, 62, 1, 4),

(5, 'field-fence', 'Field Fence',
 'General agricultural fencing for livestock and crop protection.',
 'A versatile field fence designed for livestock, crop protection and rural boundaries. Galvanised steel wire with reinforced knots for long life even under constant animal pressure.',
 'roll', 180.00, 50, 10.00, 0.90, 200.00, 3.00,
 'https://images.unsplash.com/photo-1566780856910-f0cc7a8fb0c1?auto=format&fit=crop&w=600&q=80',
 NULL, 4.7, 51, 1, 5),

(6, 'razor-wire', 'Razor Wire',
 'Enhanced perimeter security for high-risk installations.',
 'Concertina razor wire for maximum perimeter security. Razor-sharp blades mounted on a galvanised core, ideal for prisons, banks, warehouses and high-risk commercial properties.',
 'roll', 95.00, 50, 14.00, 1.40, 260.00, 4.50,
 'https://images.unsplash.com/photo-1759614539716-01f836befe88?auto=format&fit=crop&w=600&q=80',
 NULL, 4.9, 40, 1, 6),

(7, 'fence-posts', 'Fence Posts',
 'Galvanised steel posts — corner, standard and supporter posts matched to fence height.',
 'Galvanised fencing posts in three types: corner posts (one per corner), standard posts (placed every 5 m along the fence line) and supporter/stay posts (two per corner post). Post length is matched to fence height — e.g. a 2.1 m fence uses 2.6 m posts. Available heights: 1.8 m, 2.0 m, 2.6 m, 3.0 m and 3.6 m.',
 'piece', 8.00, NULL, NULL, NULL, NULL, NULL,
 'assets/img/products/round-pole-75mm.jpg',
 NULL, 4.5, 38, 0, 7),

(8, 'binding-wire', 'Binding Wire & Clamps',
 'Binding wire, tensioning wire, clamps and tensioners for installation.',
 'Everything you need to secure and tension your fence line — galvanised binding wire, tension wire, wire clamps and turnbuckles for a professional finish.',
 'lot', 25.00, NULL, NULL, NULL, NULL, NULL,
 'https://images.unsplash.com/photo-1533154683836-84ea7a0bc310?auto=format&fit=crop&w=600&q=80',
 NULL, 4.6, 29, 0, 8);

-- Steel products (category_id = 2)
INSERT INTO `products`
(`subcategory_id`, `slug`, `name`, `short_desc`, `long_desc`, `unit`, `price_usd`, `image`, `is_featured`, `sort_order`) VALUES

(9, 'steel-tubing', 'Steel Tubing',
 'Square and rectangular steel tubing for fabrication, gates and frames.',
 'Cold-formed square and rectangular steel tubing in multiple wall thicknesses — used for gates, frames, railings and general fabrication.',
 'metre', 12.00,
 'https://images.unsplash.com/photo-1565793298595-6a879b1d9492?auto=format&fit=crop&w=600&q=80',
 1, 10),

(10, 'steel-sheets', 'Steel Sheets',
 'Flat and corrugated steel sheets for roofing and cladding.',
 'Galvanised and colour-coated steel sheets — flat or corrugated — in standard gauges. Ideal for roofing, cladding, fencing panels and general sheet metal work.',
 'sheet', 22.00,
 'https://images.unsplash.com/photo-1565793298595-6a879b1d9492?auto=format&fit=crop&w=600&q=80',
 1, 11),

(11, 'deformed-bars', 'Deformed Bars',
 'High-strength ribbed reinforcement bars for reinforced concrete.',
 'High-strength steel reinforcement bars with a ribbed surface designed to provide excellent bonding with concrete. Ideal for reinforced concrete structures, foundations, columns, beams, slabs and general construction projects. Not a stock item — supplied on request.',
 'length', NULL,
 'assets/img/products/deformed-bars.jpg',
 1, 12),

(12, 'structural-steel', 'Structural Steel',
 'I-beams, channels and angles for structural fabrication.',
 'Structural steel sections including I-beams, U-channels and equal angles. Available in standard lengths and made to order for large projects.',
 'length', 45.00,
 'https://images.unsplash.com/photo-1565793298595-6a879b1d9492?auto=format&fit=crop&w=600&q=80',
 0, 13),

(10, 'checkered-plate-3mm', 'Checkered Plate Galvanised 3mm x 2.4m x 1.2m',
 'Durable galvanised steel plate with a raised checkered pattern for enhanced grip and slip resistance.',
 'Durable galvanised steel plate featuring a raised checkered pattern for enhanced grip and slip resistance. Ideal for flooring, walkways, platforms, steps, ramps, trailers and general fabrication applications. The galvanised coating provides excellent resistance to corrosion and weathering, making it suitable for both indoor and outdoor use.',
 'sheet', 122.00,
 'assets/img/products/checkered-plate-3mm.jpg',
 1, 14),

(9, 'galvanised-round-pole-75mm', 'Galvanised Round Pole 75mm x 2mm x 6000mm',
 'Heavy-duty galvanised steel round pole, 75mm diameter, 2mm thickness, 6m length.',
 'Heavy-duty galvanised steel round pole, 75mm diameter, 2mm thickness and 6m length. Corrosion-resistant and suitable for fencing, structural supports, agricultural applications and general fabrication.',
 'length', 36.00,
 'assets/img/products/round-pole-75mm.jpg',
 1, 15),

(9, 'galvanised-round-pole-32mm', 'Galvanised Round Pole 32mm x 2mm x 6000mm',
 'Galvanised steel round pole, 32mm diameter, 2mm thickness, 6m length.',
 'Heavy-duty galvanised steel round pole, 32mm diameter, 2mm thickness and 6m length. Corrosion-resistant and suitable for fencing, structural supports, agricultural applications and general fabrication.',
 'length', 25.00,
 'assets/img/products/round-pole-32mm.jpg',
 1, 16),

(9, 'galvanised-round-pole-38mm', 'Galvanised Round Pole 38mm x 2mm x 6000mm',
 'Galvanised steel round pole, 38mm diameter, 2mm thickness, 6m length.',
 'Heavy-duty galvanised steel round pole, 38mm diameter, 2mm thickness and 6m length. Corrosion-resistant and suitable for fencing, structural supports, agricultural applications and general fabrication.',
 'length', 28.00,
 'assets/img/products/round-pole-38mm.jpg',
 1, 17),

(9, 'square-tubes', 'Square Tubes',
 'High-quality steel square tubes for structural work, fabrication, fencing, gates and frames.',
 'High-quality steel square tubes designed for strength, durability and versatility. Ideal for structural work, fabrication, fencing, gates, frames, roofing supports and general construction applications. Available in various sizes and thicknesses. Not a stock item — supplied on request.',
 'length', NULL,
 'assets/img/products/square-tubes.jpg',
 1, 18),

(12, 'angle-irons', 'Angle Irons',
 'Strong, versatile steel angle sections for structural support and general fabrication.',
 'Strong and versatile steel angle sections designed for structural support and general fabrication. Ideal for frames, brackets, supports, fencing, construction and engineering applications. Available in various sizes and thicknesses. Not a stock item — supplied on request.',
 'length', NULL,
 'assets/img/products/angle-irons.jpg',
 1, 19);

-- Hardware products (category_id = 3)
INSERT INTO `products`
(`subcategory_id`, `slug`, `name`, `short_desc`, `long_desc`, `unit`, `price_usd`, `image`, `is_featured`, `sort_order`) VALUES

(13, 'gate-locks', 'Gate Locks & Hinges',
 'Heavy-duty locks, hinges and latches for gates and security doors.',
 'A range of heavy-duty gate locks, hinges and latches suited for swing gates, sliding gates and security doors. Galvanised and powder-coated finishes available.',
 'set', 18.00,
 'https://images.unsplash.com/photo-1554863885-e3a33dd1bc82?auto=format&fit=crop&w=600&q=80',
 1, 20),

(13, 'gate-wheels', 'Gate Wheels & Tracks',
 'Wheels and tracks for sliding gates.',
 'Heavy-duty gate wheels and tracks for smooth operation of sliding gates. Suitable for residential and commercial installations.',
 'set', 32.00,
 'https://images.unsplash.com/photo-1581147036324-c17ac41dfa6c?auto=format&fit=crop&w=600&q=80',
 0, 21),

(14, 'bolts-nuts', 'Bolts & Nuts',
 'Assorted galvanised bolts, nuts and washers.',
 'Assorted high-tensile bolts, nuts, washers and anchors in common sizes. Sold per pack or by weight for larger quantities.',
 'pack', 6.00,
 'https://images.unsplash.com/photo-1581147036324-c17ac41dfa6c?auto=format&fit=crop&w=600&q=80',
 1, 22),

(15, 'hand-tools', 'Hand Tools',
 'Everyday hand tools for fencing, building and DIY.',
 'A curated range of hand tools for fencing, building, farming and general DIY — pliers, cutters, hammers, spanners and fencing-specific tools.',
 'piece', 14.00,
 'https://images.unsplash.com/photo-1581147036324-c17ac41dfa6c?auto=format&fit=crop&w=600&q=80',
 0, 23);

-- Barbed wire 50 kg variant (fencing catalog column set)
INSERT INTO `products`
(`subcategory_id`, `slug`, `name`, `short_desc`, `long_desc`, `unit`,
 `price_usd`, `roll_metres`, `post_price`, `top_wire_rate`, `gate_price`, `install_rate`,
 `image`, `badge`, `rating`, `reviews_count`, `is_featured`, `sort_order`) VALUES
(3, 'barbed-wire-50kg', 'Barbed Wire 50 kg Roll',
 'High-tensile, high-security barbed wire for perimeter and farm protection. 50 kg roll.',
 'Hot-dip galvanised barbed wire with 3-strand twist, supplied in a 50 kg roll. Also available in a 25 kg roll. Perfect for farm perimeter security, commercial sites and any application requiring a low-cost but effective deterrent.',
 'roll', 75.00, 100, 8.00, 0.60, 160.00, 2.50,
 'assets/img/products/barbed-wire-50kg.jpg',
 NULL, 4.6, 74, 1, 24);

-- Diamond mesh variants (fencing catalog column set) — ids 24, 25, 26
INSERT INTO `products`
(`subcategory_id`, `slug`, `name`, `short_desc`, `long_desc`, `unit`,
 `price_usd`, `roll_metres`, `post_price`, `top_wire_rate`, `gate_price`, `install_rate`,
 `image`, `badge`, `rating`, `reviews_count`, `is_featured`, `sort_order`) VALUES
(1, 'diamond-mesh-50x50-2-5mm', 'Diamond Mesh 50x50 (2.5mm)',
 'Versatile, durable fencing. 50x50mm aperture, 2.5mm wire, 30m rolls, heights 1.0m to 3.0m.',
 'Diamond mesh fencing, 50x50mm aperture, 2.5mm wire gauge, hot-dip galvanised. Each roll is 30m long, available in heights from 1.0m to 3.0m. Also stocked in 2mm and 3.15mm gauges and 30x30mm aperture.',
 'roll', 85.00, 30, 8.00, 0.80, 180.00, 3.50,
 'assets/img/products/diamond-mesh-2.jpg',
 'Best Seller', 4.8, 120, 1, 25),
(1, 'diamond-mesh-50x50-3-15mm', 'Diamond Mesh 50x50 (3.15mm)',
 'Heavy-duty diamond mesh. 50x50mm aperture, 3.15mm wire, 30m rolls, heights 1.0m to 3.0m.',
 'Heavy-duty diamond mesh fencing, 50x50mm aperture, 3.15mm wire gauge, hot-dip galvanised. Each roll is 30m long, available in heights from 1.0m to 3.0m. Also stocked in 2mm and 2.5mm gauges and 30x30mm aperture.',
 'roll', 150.00, 30, 8.00, 0.80, 180.00, 3.50,
 'assets/img/products/diamond-mesh-2.jpg',
 NULL, 4.8, 110, 1, 26),
(1, 'diamond-mesh-30x30-2-5mm', 'Diamond Mesh 30x30 (2.5mm)',
 'Tighter-mesh diamond fence. 30x30mm aperture, 2.5mm wire, 30m rolls, heights 1.0m to 3.0m.',
 'Diamond mesh fencing with a tighter 30x30mm aperture, 2.5mm wire gauge, hot-dip galvanised. Each roll is 30m long, available in heights from 1.0m to 3.0m. Also stocked in 50x50mm aperture in 2mm, 2.5mm and 3.15mm gauges.',
 'roll', 110.00, 30, 8.00, 0.80, 180.00, 3.50,
 'assets/img/products/diamond-mesh.jpg',
 NULL, 4.8, 95, 1, 27);

-- Welded Mesh (subcategory_id = 16) — product id 27
INSERT INTO `products`
(`subcategory_id`, `slug`, `name`, `short_desc`, `long_desc`, `unit`,
 `price_usd`, `roll_metres`, `post_price`, `top_wire_rate`, `gate_price`, `install_rate`,
 `image`, `badge`, `rating`, `reviews_count`, `is_featured`, `sort_order`) VALUES
(16, 'welded-mesh', 'Welded Mesh',
 'High-quality galvanised welded mesh with evenly welded intersections for strength and long life.',
 'High-quality galvanised welded mesh manufactured from durable steel wire, with evenly welded intersections for strength, stability, and long-lasting performance. Ideal for fencing, security applications, construction, animal enclosures, and general fabrication projects.',
 'roll', NULL, 30, 8.00, 0.80, 180.00, 3.50,
 'assets/img/products/welded-mesh.jpg',
 NULL, 4.7, 58, 1, 8);

-- Generic 'Steel Tubing' placeholder replaced by real products — keep the row
-- (stable IDs) but hide it from the catalog.
UPDATE `products` SET `is_active` = 0 WHERE `slug` = 'steel-tubing';

-- ---------- PRODUCT SPECS ----------
INSERT INTO `product_specs` (`product_id`, `label`, `value`, `sort_order`) VALUES
(1, 'Material',           'Hot-dip galvanised steel wire', 1),
(1, 'Mesh Aperture',      '50 mm × 50 mm',                 2),
(1, 'Wire Gauge',         '2.0 mm',                        3),
(1, 'Roll Length',        '30 m',                          4),
(1, 'Height 1.0 m',       '$65',                           10),
(1, 'Height 1.2 m',       '$75',                           11),
(1, 'Height 1.5 m',       '$90',                           12),
(1, 'Height 1.8 m',       '$110',                          13),
(1, 'Height 2.0 m',       '$130',                          14),
(1, 'Height 2.1 m',       '$200',                          15),
(1, 'Height 2.4 m',       '$220',                          16),
(1, 'Height 2.5 m',       '$235',                          17),
(1, 'Height 3.0 m',       '$270',                          18),
(1, 'Finish',             'Hot-dip galvanised',            20),
(1, 'Standard',           'SABS 1587',                     21),
(1, 'Warranty',           '10 years against manufacturing defects', 22),

-- Diamond mesh 50x50 2.5mm (product_id = 24)
(24, 'Material',          'Hot-dip galvanised steel wire', 1),
(24, 'Mesh Aperture',     '50 mm × 50 mm',                 2),
(24, 'Wire Gauge',        '2.5 mm',                        3),
(24, 'Roll Length',       '30 m',                          4),
(24, 'Height 1.0 m',      '$85',                           10),
(24, 'Height 1.2 m',      '$105',                          11),
(24, 'Height 1.5 m',      '$130',                          12),
(24, 'Height 1.8 m',      '$150',                          13),
(24, 'Height 2.0 m',      '$168',                          14),
(24, 'Height 2.1 m',      '$225',                          15),
(24, 'Height 2.4 m',      '$250',                          16),
(24, 'Height 2.5 m',      '$265',                          17),
(24, 'Height 3.0 m',      '$300',                          18),
(24, 'Finish',            'Hot-dip galvanised',            20),

-- Diamond mesh 50x50 3.15mm (product_id = 25)
(25, 'Material',          'Hot-dip galvanised steel wire', 1),
(25, 'Mesh Aperture',     '50 mm × 50 mm',                 2),
(25, 'Wire Gauge',        '3.15 mm',                       3),
(25, 'Roll Length',       '30 m',                          4),
(25, 'Height 1.0 m',      '$150',                          10),
(25, 'Height 1.2 m',      '$180',                          11),
(25, 'Height 1.5 m',      '$230',                          12),
(25, 'Height 1.8 m',      '$270',                          13),
(25, 'Height 2.0 m',      '$300',                          14),
(25, 'Height 2.1 m',      '$375',                          15),
(25, 'Height 2.4 m',      '$420',                          16),
(25, 'Height 2.5 m',      '$440',                          17),
(25, 'Height 3.0 m',      '$505',                          18),
(25, 'Finish',            'Hot-dip galvanised',            20),

-- Diamond mesh 30x30 2.5mm (product_id = 26)
(26, 'Material',          'Hot-dip galvanised steel wire', 1),
(26, 'Mesh Aperture',     '30 mm × 30 mm',                 2),
(26, 'Wire Gauge',        '2.5 mm',                        3),
(26, 'Roll Length',       '30 m',                          4),
(26, 'Height 1.0 m',      '$110',                          10),
(26, 'Height 1.2 m',      '$133',                          11),
(26, 'Height 1.5 m',      '$165',                          12),
(26, 'Height 1.8 m',      '$185',                          13),
(26, 'Height 2.0 m',      '$205',                          14),
(26, 'Height 2.1 m',      '$223',                          15),
(26, 'Height 2.4 m',      '$250',                          16),
(26, 'Height 2.5 m',      '$270',                          17),
(26, 'Height 3.0 m',      '$350',                          18),
(26, 'Finish',            'Hot-dip galvanised',            20),

-- Barbed wire specs (product_id = 3 → 25 kg, product_id = 23 → 50 kg)
(3, 'Material',           'Hot-dip galvanised steel wire', 1),
(3, 'Roll Weight',        '25 kg',                         2),
(3, 'Also Available',     '50 kg roll',                    3),
(3, 'Strand Twist',       '3-strand',                      4),
(3, 'Finish',             'Hot-dip galvanised',            5),
(23, 'Material',          'Hot-dip galvanised steel wire', 1),
(23, 'Roll Weight',       '50 kg',                         2),
(23, 'Also Available',    '25 kg roll',                    3),
(23, 'Strand Twist',      '3-strand',                      4),
(23, 'Finish',            'Hot-dip galvanised',            5),

-- Fence posts (product_id = 7) — corner / standard / supporter tiers per post set
(7, 'For 1.2 m fence — 1.8 m posts', 'Corner $16 · Standard $8 · Supporter $12', 1),
(7, 'For 1.5 m fence — 2.0 m posts', 'Corner $13 · Standard $9 · Supporter $13', 2),
(7, 'For 2.1 m fence — 2.6 m posts', 'Corner $26 · Standard $16 · Supporter $13', 3),
(7, 'For 2.4 m fence — 3.0 m posts', 'Corner $33 · Standard $18 · Supporter $15', 4),
(7, 'For 2.5 m fence — 3.0 m posts', 'Corner $33 · Standard $18 · Supporter $15', 5),
(7, 'For 3.0 m fence — 3.6 m posts', 'Corner $40 · Standard $20 · Supporter $16', 6),
(7, 'Placement',                   'Standards every 5 m · 1 corner post per corner · 2 supporters per corner', 10),
(7, 'Finish',                      'Galvanised',                            20);

-- Welded Mesh (product_id = 27)
INSERT INTO `product_specs` (`product_id`, `label`, `value`, `sort_order`) VALUES
(27, 'Material',   'Hot-dip galvanised steel wire', 1),
(27, 'Pattern',    'Evenly welded square intersections', 2),
(27, 'Supply',     'Rolls — various heights and apertures', 3),
(27, 'Uses',       'Fencing, security, enclosures, construction, fabrication', 10),
(27, 'Pricing',    'Supplied on request — contact us with your spec', 20);

-- ---------- PRODUCT FEATURES (for diamond mesh) ----------
INSERT INTO `product_features` (`product_id`, `text`, `sort_order`) VALUES
(1, 'High tensile galvanised wire',        1),
(1, 'Available in multiple heights',       2),
(1, 'SABS/SAZ quality standards',          3),
(1, 'Ideal for game reserves, farms, estates', 4),
(1, 'Long lifespan',                       5);

-- ---------- PROJECTS (sample gallery data) ----------
INSERT INTO `projects`
(`slug`, `title`, `category`, `location`, `year`, `perimeter`, `fence_type`, `spec_chips`, `image`, `is_featured`, `sort_order`) VALUES

('game-reserve-masvingo', 'Game Reserve Perimeter — Masvingo', 'farms',       'Masvingo',     2024, '5km perimeter',   'Game Fence',   '["5km perimeter","1.8m game fence","Steel posts"]', 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?auto=format&fit=crop&w=800&q=80', 1, 1),
('borrowdale-residential','Residential Boundary — Borrowdale, Harare', 'residential', 'Harare', 2024, '180m perimeter','Diamond Mesh', '["180m perimeter","1.8m diamond mesh","Double gate"]', 'https://images.unsplash.com/photo-1449844908441-8829872d2607?auto=format&fit=crop&w=800&q=80', 1, 2),
('graniteside-warehouse', 'Warehouse Site — Graniteside, Harare', 'commercial','Harare',      2023, '450m perimeter',  'Razor Wire',   '["450m perimeter","2.1m mesh","Razor top"]', 'https://images.unsplash.com/photo-1587293852726-70cdb56c2866?auto=format&fit=crop&w=800&q=80', 1, 3),
('marondera-farm',        'Farm Perimeter — Marondera',       'farms',       'Marondera',    2024, '1.2km perimeter', 'Field Fence',  '["1.2km perimeter","1.5m field fence","Timber posts"]', 'https://images.unsplash.com/photo-1500595046743-cd271d694d30?auto=format&fit=crop&w=800&q=80', 1, 4),
('harare-prison-upgrade', 'Prison Facility Upgrade — Harare', 'security',    'Harare',       2023, '1.5km perimeter', 'Razor Wire',   '["1.5km perimeter","Razor wire","High-security"]', 'https://images.unsplash.com/photo-1595278069441-2cf29f8005a4?auto=format&fit=crop&w=800&q=80', 1, 5),
('mt-pleasant-townhouses','Townhouse Complex — Mt Pleasant, Harare', 'residential', 'Harare', 2024, '320m perimeter', 'Diamond Mesh', '["320m perimeter","1.8m mesh","Concrete posts"]', 'https://images.unsplash.com/photo-1568605114967-8130f3a36994?auto=format&fit=crop&w=800&q=80', 1, 6),
('chitungwiza-school',    'School Perimeter — Chitungwiza',  'commercial',  'Chitungwiza',  2023, '800m perimeter',  'Diamond Mesh', '["800m perimeter","1.8m diamond mesh","Top wire"]', 'https://images.unsplash.com/photo-1553413077-190dd305871c?auto=format&fit=crop&w=800&q=80', 1, 7),
('chinhoyi-cattle',       'Cattle Ranch — Chinhoyi',          'farms',       'Chinhoyi',     2024, '3.5km perimeter', 'Field Fence',  '["3.5km perimeter","Field fence","Steel posts"]', 'https://images.unsplash.com/photo-1560493676-04071c5f467b?auto=format&fit=crop&w=800&q=80', 0, 8),
('bulawayo-bank',         'Bank Branch Perimeter — Bulawayo', 'security',    'Bulawayo',     2024, '120m perimeter',  'Razor Wire',   '["120m perimeter","2.1m razor","Reinforced posts"]', 'https://images.unsplash.com/photo-1580129954963-a6d2dd1d6bdd?auto=format&fit=crop&w=800&q=80', 1, 9),
('victoria-falls-estate', 'Estate Boundary — Victoria Falls', 'residential', 'Victoria Falls', 2024, '650m perimeter','Diamond Mesh', '["650m perimeter","1.8m diamond mesh","2 gates"]', 'https://images.unsplash.com/photo-1518780664697-55e3ad937233?auto=format&fit=crop&w=800&q=80', 1, 10),
('norton-poultry',        'Poultry Operation — Norton',       'farms',       'Norton',       2024, '400m perimeter',  'Chicken Mesh', '["400m perimeter","1.2m chicken mesh","Light posts"]', 'https://images.unsplash.com/photo-1500076656116-558758c991c1?auto=format&fit=crop&w=800&q=80', 0, 11),
('zvishavane-mining',     'Mining Camp — Zvishavane',         'commercial',  'Zvishavane',   2023, '2.5km perimeter', 'Game Fence',   '["2.5km perimeter","Game fence","Razor top"]', 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=800&q=80', 1, 12);

-- ---------- FAQS ----------
INSERT INTO `faqs` (`category`, `question`, `answer`, `sort_order`) VALUES
('general', 'Who is Vuemax and where are you located?', "Vuemax Industries is a Zimbabwean manufacturing and supply company specialising in steel, wire and fencing products. Our main showroom and warehouse is at 103 Willowvale Road, Harare. We also deliver nationwide.", 1),
('general', 'What areas of Zimbabwe do you serve?', "We serve customers in all ten provinces of Zimbabwe:
- Harare and Bulawayo metropolitan
- Manicaland, Mashonaland Central, East and West
- Masvingo, Midlands
- Matabeleland North and South
Delivery fees vary with distance and order size — contact us for a quote.", 2),
('general', 'Are your products SABS compliant?', "Yes. Our fencing products including diamond mesh, game fence and barbed wire are manufactured to SABS standards, ensuring consistent quality, correct wire gauge and proper galvanisation for Zimbabwe's climate. For commercial tenders requiring SABS certification, we provide documentation.", 3),
('products', 'What is the best fence for a farm?', "It depends on what you're protecting against and your livestock:
- Game fence — wildlife and large properties, heavy-duty and long-lasting
- Field fence — cattle, goats and general livestock
- Diamond mesh — crop protection and mixed-use boundaries
- Barbed wire — affordable perimeter addition for livestock deterrence
Tell us your farm size and livestock type and we'll recommend the right solution.", 1),
('products', 'How long will my fence last?', "Our galvanised fencing typically lasts 10–20 years depending on the environment, install method and maintenance. Coastal or high-rainfall areas may reduce lifespan slightly — we recommend hot-dip galvanised products for those locations. Concrete footings and proper tensioning significantly extend lifespan.", 2),
('products', 'Do you sell fencing accessories and posts?', "Yes — we stock the complete fencing kit:
- Fence posts (steel, timber and concrete) in multiple heights
- Top wire (barbed or razor)
- Binding wire and tensioning wire
- Gates, hinges, locks and latches
- Concrete footings and accessories", 3),
('products', 'Can you supply steel and general hardware too?', "Absolutely. While fencing is our flagship division, we also supply:
- Steel products — tubing, sheets, deformed bars, structural sections
- General hardware — tools, fixings, gate hardware, fasteners
If you need both fencing and building materials, we can consolidate your order and deliver together.", 4),
('delivery', 'Do you deliver nationwide?', "Yes. We deliver to all ten provinces of Zimbabwe. Delivery cost is calculated based on distance from Harare and total order weight/volume. For larger orders we offer free or discounted delivery — ask us when requesting a quote.", 1),
('delivery', 'How long does delivery take?', "Typical delivery timelines:
- Harare metro — 1–2 business days
- Bulawayo, Mutare, Gweru, Masvingo — 2–4 business days
- Remote and rural areas — 3–7 business days
We'll confirm an exact delivery date when you order.", 2),
('payment', 'What payment methods do you accept?', "We accept:
- Cash (USD and ZWL)
- EcoCash
- Bank transfer (USD and ZWL accounts)
- Point-of-sale card payments at our Harare showroom
For large orders we can arrange staged or deposit-based payment terms.", 1),
('payment', 'Do you offer bulk discounts?', "Yes. We offer tiered discounts on bulk orders — typically from 5% for medium orders up to 15%+ for very large projects. Contact us with your quantities and we'll send a tailored quotation.", 2),
('payment', 'How accurate is the online quote calculator?', "Our calculator provides a strong working estimate based on real product pricing and standard material ratios — typically accurate to within ±10% for standard rectangular sites. Final pricing is confirmed by our team after reviewing terrain, access and requirements. For unusual shapes or difficult access, we recommend a site visit.", 3),
('installation', 'Do you install the fence, or just supply?', "Both. You can:
- Buy materials only and install yourself (or with your own team)
- Have us supply and install using our experienced installation crews
Our install teams work nationwide. Pricing depends on perimeter length, terrain and access.", 1),
('installation', 'How long does installation take?', "Installation timelines vary with site size and complexity:
- Residential (100–200m) — 1–2 days
- Commercial (200–500m) — 2–4 days
- Farm perimeter (500m+) — 4–10 days
We'll give you a firm timeline as part of your quotation.", 2);

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- END OF SCHEMA
-- ============================================================

-- ============================================================
-- SITE IMAGES  (managed via /admin image manager)
-- ============================================================
DROP TABLE IF EXISTS `site_images`;
CREATE TABLE `site_images` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `img_key`    VARCHAR(80)  NOT NULL,
  `label`      VARCHAR(160) NOT NULL,
  `page`       VARCHAR(160) NOT NULL DEFAULT '',
  `path`       VARCHAR(255) NOT NULL DEFAULT '',
  `updated_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_site_images_key` (`img_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `site_images` (`img_key`, `label`, `page`, `path`) VALUES
('logo','Site logo (header, footer, drawer)','Global','assets/img/logo.png'),
('home-hero','Homepage hero background','index.php','https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=1200&q=80'),
('cat-fencing','Category card: Fencing','index.php','https://images.unsplash.com/photo-1640538336739-ce6ee5ee853e?auto=format&fit=crop&w=800&q=80'),
('cat-steel','Category card: Steel','index.php','assets/img/products/square-tubes.jpg'),
('cat-hardware','Category card: General Hardware','index.php','https://images.unsplash.com/photo-1581147036324-c17ac41dfa6c?auto=format&fit=crop&w=800&q=80'),
('prod-diamond-mesh','Product: Diamond Mesh','index.php, products.php, product-detail.php, calculator.php','assets/img/products/diamond-mesh-2.jpg'),
('prod-game-fence','Product: Game Fence','index.php, products.php, product-detail.php, calculator.php','assets/img/products/game-fence.jpg'),
('prod-barbed-wire','Product: Barbed Wire (25 kg roll)','products.php, product-detail.php, calculator.php','assets/img/products/barbed-wire.jpg'),
('prod-barbed-wire-50','Product: Barbed Wire (50 kg roll)','products.php, product-detail.php','assets/img/products/barbed-wire-50kg.jpg'),
('prod-diamond-25','Product: Diamond Mesh 50x50 (2.5mm)','products.php','assets/img/products/diamond-mesh-2.jpg'),
('prod-diamond-315','Product: Diamond Mesh 50x50 (3.15mm)','products.php','assets/img/products/diamond-mesh-2.jpg'),
('prod-diamond-30x30','Product: Diamond Mesh 30x30 (2.5mm)','products.php','assets/img/products/diamond-mesh.jpg'),
('prod-chicken-mesh','Product: Chicken Mesh','products.php, calculator.php','https://images.unsplash.com/photo-1767416171650-4bff1da861fe?auto=format&fit=crop&w=600&q=80'),
('prod-field-fence','Product: Field Fence','products.php, calculator.php','https://images.unsplash.com/photo-1566780856910-f0cc7a8fb0c1?auto=format&fit=crop&w=600&q=80'),
('prod-razor-wire','Product: Razor Wire','products.php, product-detail.php, calculator.php','https://images.unsplash.com/photo-1759614539716-01f836befe88?auto=format&fit=crop&w=800&q=80'),
('prod-fence-posts','Product: Fence Posts','products.php, product-detail.php','assets/img/products/round-pole-75mm.jpg'),
('prod-gate-locks','Product: Gate Locks & Hinges','index.php, products.php','https://images.unsplash.com/photo-1554863885-e3a33dd1bc82?auto=format&fit=crop&w=600&q=80'),
('prod-checkered-plate','Product: Checkered Plate 3mm','products.php','assets/img/products/checkered-plate-3mm.jpg'),
('prod-round-pole-75','Product: Galvanised Round Pole 75mm','products.php','assets/img/products/round-pole-75mm.jpg'),
('prod-round-pole-32','Product: Galvanised Round Pole 32mm','products.php','assets/img/products/round-pole-32mm.jpg'),
('prod-round-pole-38','Product: Galvanised Round Pole 38mm','products.php','assets/img/products/round-pole-38mm.jpg'),
('prod-square-tubes','Product: Square Tubes','products.php','assets/img/products/square-tubes.jpg'),
('prod-angle-irons','Product: Angle Irons','products.php','assets/img/products/angle-irons.jpg'),
('prod-deformed-bars','Product: Deformed Bars','products.php','assets/img/products/deformed-bars.jpg'),
('prod-welded-mesh','Product: Welded Mesh','products.php, product-detail.php','assets/img/products/welded-mesh.jpg'),
('pd-gallery-2','Product gallery: image 2','product-detail.php','https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=400&q=80'),
('pd-gallery-3','Product gallery: image 3','product-detail.php','https://images.unsplash.com/photo-1518709268805-4e9042af9f23?auto=format&fit=crop&w=400&q=80'),
('pd-gallery-4','Product gallery: image 4','product-detail.php','https://images.unsplash.com/photo-1533154683836-84ea7a0bc310?auto=format&fit=crop&w=400&q=80'),
('about-hero','About page hero background','about.php','https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=1920&q=80'),
('about-story','About: story image','about.php','https://images.unsplash.com/photo-1621905251189-08b45d6a269e?auto=format&fit=crop&w=1000&q=80'),
('about-why','About: why-choose image','about.php','https://images.unsplash.com/photo-1565793298595-6a879b1d9492?auto=format&fit=crop&w=1000&q=80'),
('products-hero','Products page hero background','products.php','https://images.unsplash.com/photo-1621905251189-08b45d6a269e?auto=format&fit=crop&w=1920&q=80'),
('calc-hero','Calculator page hero background','calculator.php','https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=1920&q=80'),
('est-hero','Estimator page hero background','estimator.php','https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=1920&q=80'),
('est-why','Estimator: why-choose image','estimator.php','https://images.unsplash.com/photo-1621905251189-08b45d6a269e?auto=format&fit=crop&w=1000&q=80'),
('inst-hero','Installations page hero background','installations.php','https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=1920&q=80'),
('faq-hero','FAQ page hero background','faq.php','https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=1920&q=80'),
('contact-hero','Contact page hero background','contact.php','https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=1920&q=80'),
('proj-1','Gallery project image 1','installations.php','https://images.unsplash.com/photo-1518709268805-4e9042af9f23?auto=format&fit=crop&w=800&q=80'),
('proj-2','Gallery project image 2','installations.php','https://images.unsplash.com/photo-1449844908441-8829872d2607?auto=format&fit=crop&w=800&q=80'),
('proj-3','Gallery project image 3','installations.php','https://images.unsplash.com/photo-1587293852726-70cdb56c2866?auto=format&fit=crop&w=800&q=80'),
('proj-4','Gallery project image 4','installations.php','https://images.unsplash.com/photo-1500595046743-cd271d694d30?auto=format&fit=crop&w=800&q=80'),
('proj-5','Gallery project image 5','installations.php','https://images.unsplash.com/photo-1595278069441-2cf29f8005a4?auto=format&fit=crop&w=800&q=80'),
('proj-6','Gallery project image 6','installations.php','https://images.unsplash.com/photo-1568605114967-8130f3a36994?auto=format&fit=crop&w=800&q=80'),
('proj-7','Gallery project image 7','installations.php','https://images.unsplash.com/photo-1553413077-190dd305871c?auto=format&fit=crop&w=800&q=80'),
('proj-8','Gallery project image 8','installations.php','https://images.unsplash.com/photo-1560493676-04071c5f467b?auto=format&fit=crop&w=800&q=80'),
('proj-9','Gallery project image 9','installations.php','https://images.unsplash.com/photo-1580129954963-a6d2dd1d6bdd?auto=format&fit=crop&w=800&q=80'),
('proj-10','Gallery project image 10','installations.php','https://images.unsplash.com/photo-1518780664697-55e3ad937233?auto=format&fit=crop&w=800&q=80'),
('proj-11','Gallery project image 11','installations.php','https://images.unsplash.com/photo-1500076656116-558758c991c1?auto=format&fit=crop&w=800&q=80'),
('proj-12','Gallery project image 12','installations.php','https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=800&q=80');

-- ============================================================
-- SEED: demo customer + order (remove or keep for testing)
-- ============================================================
-- Demo customer login:  demo@customer.com / customer123
INSERT INTO `customers` (`name`, `email`, `phone`, `company`, `address`, `password_hash`, `status`) VALUES
('Demo Customer', 'demo@customer.com', '+263 77 000 0000', 'Demo Farms (Pvt) Ltd', 'Graniteside, Harare', '$2y$10$Zu0Kd2/gR4uEkqmeeBYbfOAiP7E1PNh2vpIxulcSkgmY0ADsGEH.2', 'active');

INSERT INTO `orders` (`order_no`, `customer_id`, `status`, `payment_status`, `subtotal_usd`, `delivery_usd`, `total_usd`, `delivery_address`, `tracking_ref`, `driver_phone`, `eta`) VALUES
('VO-2026-0001', 1, 'processing', 'deposit', 284.00, 25.00, 309.00, 'Graniteside, Harare', 'VMX-TRK-0001', '+263 71 111 1111', '2026-09-20');

INSERT INTO `order_items` (`order_id`, `product_id`, `name`, `spec`, `qty`, `unit_price`, `total`, `sort_order`) VALUES
(1, 1, 'Diamond Mesh 50x50 (2mm)', '1.8m height x 30m roll', 2, 110.00, 220.00, 1),
(1, 7, 'Fence Posts', '2.4m steel posts', 8, 8.00, 64.00, 2);

INSERT INTO `order_events` (`order_id`, `status`, `note`, `is_public`) VALUES
(1, 'pending',    'Order placed', 1),
(1, 'confirmed',  'Payment deposit confirmed', 1),
(1, 'processing', 'Items being picked at warehouse', 1);

SET FOREIGN_KEY_CHECKS = 1;

