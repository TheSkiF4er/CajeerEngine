-- Marketplace foundation (v2.3)
CREATE TABLE IF NOT EXISTS ce_marketplace_publishers (
  publisher_id VARCHAR(64) NOT NULL,
  title VARCHAR(190) NULL,
  pubkey_ed25519 VARCHAR(255) NOT NULL,
  trusted TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (publisher_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_marketplace_packages (
  id INT NOT NULL AUTO_INCREMENT,
  type VARCHAR(32) NOT NULL,
  name VARCHAR(100) NOT NULL,
  version VARCHAR(50) NOT NULL,
  title VARCHAR(190) NULL,
  publisher_id VARCHAR(64) NULL,
  signature_required TINYINT(1) NOT NULL DEFAULT 1,
  installed_at DATETIME NULL,
  updated_at DATETIME NULL,
  meta_json MEDIUMTEXT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_pkg (type, name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_marketplace_cache (
  cache_key VARCHAR(190) NOT NULL,
  payload_json MEDIUMTEXT NOT NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (cache_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
