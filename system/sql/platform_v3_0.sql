-- v3.0 CajeerEngine Platform (foundation)

CREATE TABLE IF NOT EXISTS ce_plugins (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL DEFAULT 0,
  name VARCHAR(190) NOT NULL,
  slug VARCHAR(190) NOT NULL,
  version VARCHAR(64) NOT NULL,
  enabled TINYINT(1) NOT NULL DEFAULT 0,
  manifest_json MEDIUMTEXT NOT NULL,
  installed_at DATETIME NULL,
  enabled_at DATETIME NULL,
  disabled_at DATETIME NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_plugin (tenant_id, slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_jobs (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL DEFAULT 0,
  queue VARCHAR(64) NOT NULL DEFAULT 'default',
  handler VARCHAR(190) NOT NULL,
  payload_json MEDIUMTEXT NULL,
  attempts INT NOT NULL DEFAULT 0,
  max_attempts INT NOT NULL DEFAULT 10,
  status VARCHAR(16) NOT NULL DEFAULT 'queued',
  run_at DATETIME NULL,
  locked_at DATETIME NULL,
  lock_token VARCHAR(64) NULL,
  last_error TEXT NULL,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  KEY idx_jobs_status (status, queue, run_at),
  KEY idx_jobs_tenant (tenant_id, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_api_contracts (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL DEFAULT 0,
  api_name VARCHAR(64) NOT NULL,
  api_version VARCHAR(16) NOT NULL,
  locked TINYINT(1) NOT NULL DEFAULT 1,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_api_contract (tenant_id, api_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_partners (
  id BIGINT NOT NULL AUTO_INCREMENT,
  org_name VARCHAR(190) NOT NULL,
  contact_email VARCHAR(190) NULL,
  website VARCHAR(255) NULL,
  level VARCHAR(32) NOT NULL DEFAULT 'community',
  metadata_json MEDIUMTEXT NULL,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  KEY idx_partners_level (level)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
