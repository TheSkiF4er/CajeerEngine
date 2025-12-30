-- v3.2 Marketplace 2.0 (Production baseline)

CREATE TABLE IF NOT EXISTS ce_marketplace_registries (
  id BIGINT NOT NULL AUTO_INCREMENT,
  name VARCHAR(64) NOT NULL,
  base_url VARCHAR(255) NOT NULL,
  token VARCHAR(255) NULL,
  enabled TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_registry_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_publishers (
  id BIGINT NOT NULL AUTO_INCREMENT,
  publisher_id VARCHAR(64) NOT NULL,
  name VARCHAR(190) NOT NULL,
  verification_level VARCHAR(32) NOT NULL DEFAULT 'community', -- community/verified/trusted
  public_key_base64 TEXT NULL,
  reputation_score INT NOT NULL DEFAULT 0,
  metadata_json MEDIUMTEXT NULL,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_publisher (publisher_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_installed_packages (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL DEFAULT 0,
  type VARCHAR(32) NOT NULL, -- plugin/theme/ui-block/content-type
  slug VARCHAR(190) NOT NULL,
  version VARCHAR(64) NOT NULL,
  enabled TINYINT(1) NOT NULL DEFAULT 1,
  source_registry VARCHAR(64) NULL,
  manifest_json MEDIUMTEXT NOT NULL,
  license_json MEDIUMTEXT NULL,
  installed_at DATETIME NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_installed_pkg (tenant_id, type, slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_package_licenses (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL DEFAULT 0,
  package_slug VARCHAR(190) NOT NULL,
  license_key VARCHAR(255) NULL,
  status VARCHAR(16) NOT NULL DEFAULT 'inactive', -- inactive/active/expired/revoked
  expires_at DATETIME NULL,
  metadata_json MEDIUMTEXT NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  KEY idx_license_pkg (tenant_id, package_slug, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_package_usage (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL DEFAULT 0,
  package_slug VARCHAR(190) NOT NULL,
  metric VARCHAR(64) NOT NULL,
  value BIGINT NOT NULL DEFAULT 0,
  period_start DATETIME NULL,
  period_end DATETIME NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  KEY idx_usage_pkg (tenant_id, package_slug, metric)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_security_scans (
  id BIGINT NOT NULL AUTO_INCREMENT,
  package_slug VARCHAR(190) NOT NULL,
  version VARCHAR(64) NOT NULL,
  status VARCHAR(16) NOT NULL DEFAULT 'pending', -- pending/passed/failed
  report_json MEDIUMTEXT NULL,
  scanned_at DATETIME NULL,
  PRIMARY KEY (id),
  KEY idx_scan_pkg (package_slug, version, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
