-- v2.9 Enterprise SaaS & Compliance (foundation)

CREATE TABLE IF NOT EXISTS ce_tenants (
  id INT NOT NULL AUTO_INCREMENT,
  slug VARCHAR(64) NOT NULL,
  name VARCHAR(190) NOT NULL,
  status VARCHAR(16) NOT NULL DEFAULT 'active', -- active/suspended/archived/deleted
  region VARCHAR(16) NOT NULL DEFAULT 'eu',
  residency_region VARCHAR(16) NULL,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  suspended_at DATETIME NULL,
  archived_at DATETIME NULL,
  deleted_at DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_tenant_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_tenant_quotas (
  id INT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL,
  quotas JSON NOT NULL, -- {"sites":10,"storage_mb":5000,"requests_per_min":300}
  enforced TINYINT(1) NOT NULL DEFAULT 1,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_quota_tenant (tenant_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_sso_providers (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL,
  type VARCHAR(16) NOT NULL, -- oidc/saml
  name VARCHAR(190) NOT NULL,
  enabled TINYINT(1) NOT NULL DEFAULT 1,
  config_json MEDIUMTEXT NOT NULL,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  KEY idx_sso_tenant (tenant_id, type, enabled)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_mfa_factors (
  id BIGINT NOT NULL AUTO_INCREMENT,
  user_id BIGINT NOT NULL,
  type VARCHAR(16) NOT NULL, -- totp/webauthn
  label VARCHAR(190) NULL,
  secret_enc TEXT NULL, -- encrypted secret for totp
  webauthn_json MEDIUMTEXT NULL, -- public key / credential id
  enabled TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  KEY idx_mfa_user (user_id, type, enabled)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Immutable audit trail (hash-chain)
CREATE TABLE IF NOT EXISTS ce_audit_log_immutable (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL DEFAULT 0,
  site_id INT NOT NULL DEFAULT 0,
  actor_user_id BIGINT NULL,
  actor_ip VARCHAR(64) NULL,
  action VARCHAR(190) NOT NULL,
  target VARCHAR(255) NULL,
  payload_json MEDIUMTEXT NULL,
  created_at DATETIME NOT NULL,
  prev_hash CHAR(64) NULL,
  entry_hash CHAR(64) NOT NULL,
  PRIMARY KEY (id),
  KEY idx_audit_tenant (tenant_id, site_id, created_at),
  KEY idx_audit_action (action(50))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Access reports + GDPR requests
CREATE TABLE IF NOT EXISTS ce_access_reports (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL DEFAULT 0,
  user_id BIGINT NULL,
  report_type VARCHAR(32) NOT NULL, -- access/export/erase
  status VARCHAR(16) NOT NULL DEFAULT 'queued', -- queued/running/done/failed
  result_json MEDIUMTEXT NULL,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  KEY idx_access_reports (tenant_id, report_type, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_incidents (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL DEFAULT 0,
  severity VARCHAR(16) NOT NULL DEFAULT 'info', -- info/warn/high/critical
  title VARCHAR(190) NOT NULL,
  details TEXT NULL,
  status VARCHAR(16) NOT NULL DEFAULT 'open', -- open/closed
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  KEY idx_incident (tenant_id, severity, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
