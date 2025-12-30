-- v3.3 Enterprise Identity & Zero Trust (production baseline)

CREATE TABLE IF NOT EXISTS ce_sso_providers (
  id BIGINT NOT NULL AUTO_INCREMENT,
  type VARCHAR(16) NOT NULL, -- oidc|saml
  name VARCHAR(64) NOT NULL, -- key
  config_json MEDIUMTEXT NOT NULL,
  enabled TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_sso_provider (type, name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_identities (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL DEFAULT 0,
  user_id BIGINT NOT NULL,
  provider_type VARCHAR(16) NOT NULL, -- oidc|saml
  provider_name VARCHAR(64) NOT NULL,
  subject VARCHAR(190) NOT NULL, -- sub / NameID
  claims_json MEDIUMTEXT NULL,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_identity (tenant_id, provider_type, provider_name, subject),
  KEY idx_identity_user (tenant_id, user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_mfa_factors (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL DEFAULT 0,
  user_id BIGINT NOT NULL,
  type VARCHAR(16) NOT NULL, -- totp|webauthn
  secret_base64 TEXT NULL,   -- encrypted later (foundation)
  public_key_json MEDIUMTEXT NULL,
  label VARCHAR(190) NULL,
  enabled TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  KEY idx_mfa_user (tenant_id, user_id, type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_devices (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL DEFAULT 0,
  user_id BIGINT NOT NULL,
  device_id VARCHAR(128) NOT NULL,
  trust_score INT NOT NULL DEFAULT 0,
  metadata_json MEDIUMTEXT NULL,
  last_seen_at DATETIME NULL,
  created_at DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_device (tenant_id, user_id, device_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Immutable access logs (hash-chained)
CREATE TABLE IF NOT EXISTS ce_access_logs (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL DEFAULT 0,
  user_id BIGINT NULL,
  method VARCHAR(16) NOT NULL,
  path VARCHAR(255) NOT NULL,
  status INT NOT NULL DEFAULT 0,
  ip VARCHAR(64) NULL,
  ua VARCHAR(255) NULL,
  scopes_json MEDIUMTEXT NULL,
  decision VARCHAR(16) NOT NULL DEFAULT 'allow', -- allow/deny
  reason VARCHAR(190) NULL,
  ts DATETIME NOT NULL,
  prev_hash CHAR(64) NULL,
  hash CHAR(64) NOT NULL,
  PRIMARY KEY (id),
  KEY idx_access_tenant_ts (tenant_id, ts)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Compliance reports (SOC2/ISO prep, foundation)
CREATE TABLE IF NOT EXISTS ce_compliance_reports (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL DEFAULT 0,
  type VARCHAR(32) NOT NULL, -- soc2|iso27001|custom
  period_start DATETIME NULL,
  period_end DATETIME NULL,
  report_json MEDIUMTEXT NOT NULL,
  created_at DATETIME NOT NULL,
  PRIMARY KEY (id),
  KEY idx_report_tenant (tenant_id, type, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
