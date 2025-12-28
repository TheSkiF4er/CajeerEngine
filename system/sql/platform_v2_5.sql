-- v2.5 SaaS / Platform Mode

CREATE TABLE IF NOT EXISTS ce_tenants (
  id INT NOT NULL AUTO_INCREMENT,
  slug VARCHAR(64) NOT NULL,
  title VARCHAR(190) NULL,
  plan VARCHAR(32) NOT NULL DEFAULT 'free',
  status VARCHAR(16) NOT NULL DEFAULT 'active',
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_tenant_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_sites (
  id INT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL,
  title VARCHAR(190) NULL,
  host VARCHAR(190) NULL,
  status VARCHAR(16) NOT NULL DEFAULT 'active',
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  KEY idx_tenant (tenant_id),
  UNIQUE KEY uq_host (host)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_tenant_domains (
  id INT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL,
  site_id INT NULL,
  host VARCHAR(190) NOT NULL,
  created_at DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_domain_host (host),
  KEY idx_domain_tenant (tenant_id),
  KEY idx_domain_site (site_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Usage metrics
CREATE TABLE IF NOT EXISTS ce_usage_metrics (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL,
  site_id INT NULL,
  metric_key VARCHAR(64) NOT NULL,
  metric_value BIGINT NOT NULL DEFAULT 0,
  bucket_date DATE NOT NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_metric (tenant_id, site_id, metric_key, bucket_date),
  KEY idx_tenant_date (tenant_id, bucket_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Auto-update rollout state
CREATE TABLE IF NOT EXISTS ce_rollouts (
  id INT NOT NULL AUTO_INCREMENT,
  version VARCHAR(50) NOT NULL,
  channel VARCHAR(16) NOT NULL,
  strategy VARCHAR(16) NOT NULL,
  status VARCHAR(16) NOT NULL DEFAULT 'running',
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_rollout_targets (
  rollout_id INT NOT NULL,
  tenant_id INT NOT NULL,
  status VARCHAR(16) NOT NULL DEFAULT 'pending', -- pending|in_progress|success|failed|skipped
  last_error VARCHAR(255) NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (rollout_id, tenant_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
