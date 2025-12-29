-- v2.7 Marketplace Expansion & Economy (foundation)
CREATE TABLE IF NOT EXISTS ce_marketplace_registries (
  id INT NOT NULL AUTO_INCREMENT,
  code VARCHAR(64) NOT NULL,
  name VARCHAR(190) NOT NULL,
  base_url VARCHAR(255) NOT NULL,
  verification_level VARCHAR(16) NOT NULL DEFAULT 'community',
  enabled TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_registry_code (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_marketplace_publishers (
  id INT NOT NULL AUTO_INCREMENT,
  publisher_key VARCHAR(128) NOT NULL,
  display_name VARCHAR(190) NULL,
  verification_level VARCHAR(16) NOT NULL DEFAULT 'community',
  public_key_text MEDIUMTEXT NULL,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_publisher_key (publisher_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_marketplace_categories (
  id INT NOT NULL AUTO_INCREMENT,
  slug VARCHAR(64) NOT NULL,
  title VARCHAR(190) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_cat_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_marketplace_packages (
  id BIGINT NOT NULL AUTO_INCREMENT,
  registry_id INT NOT NULL,
  package_key VARCHAR(190) NOT NULL,
  name VARCHAR(190) NOT NULL,
  publisher_key VARCHAR(128) NOT NULL,
  type VARCHAR(32) NOT NULL,
  version VARCHAR(50) NOT NULL,
  description TEXT NULL,
  categories JSON NULL,
  rating_avg DECIMAL(3,2) NOT NULL DEFAULT 0.00,
  rating_count INT NOT NULL DEFAULT 0,
  dependencies JSON NULL,
  license JSON NULL,
  is_paid TINYINT(1) NOT NULL DEFAULT 0,
  price JSON NULL,
  signature TEXT NULL,
  manifest_json MEDIUMTEXT NULL,
  updated_at DATETIME NULL,
  created_at DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_pkg (registry_id, package_key, version),
  KEY idx_pkg_search (name(50), type, publisher_key),
  KEY idx_pkg_registry (registry_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_marketplace_ratings (
  id BIGINT NOT NULL AUTO_INCREMENT,
  package_id BIGINT NOT NULL,
  user_id BIGINT NULL,
  rating TINYINT NOT NULL,
  comment TEXT NULL,
  created_at DATETIME NULL,
  PRIMARY KEY (id),
  KEY idx_rating_pkg (package_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_installed_packages (
  id BIGINT NOT NULL AUTO_INCREMENT,
  package_key VARCHAR(190) NOT NULL,
  type VARCHAR(32) NOT NULL,
  version VARCHAR(50) NOT NULL,
  source_registry VARCHAR(64) NULL,
  installed_at DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_installed (package_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
