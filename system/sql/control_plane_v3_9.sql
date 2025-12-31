-- v3.9 Platform Control Plane

CREATE TABLE IF NOT EXISTS ce_fleet_sites (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL DEFAULT 0,
  site_id INT NOT NULL DEFAULT 0,
  region VARCHAR(32) NOT NULL DEFAULT 'local',
  role VARCHAR(16) NOT NULL DEFAULT 'origin',
  base_url VARCHAR(255) NULL,
  status VARCHAR(16) NOT NULL DEFAULT 'active',
  tags VARCHAR(255) NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_fleet (tenant_id, site_id),
  KEY idx_fleet_region (region, role, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_platform_policies (
  id BIGINT NOT NULL AUTO_INCREMENT,
  scope VARCHAR(16) NOT NULL DEFAULT 'global',
  tenant_id INT NOT NULL DEFAULT 0,
  site_id INT NOT NULL DEFAULT 0,
  key_name VARCHAR(64) NOT NULL,
  value_json MEDIUMTEXT NOT NULL,
  version INT NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_policy_scope (scope, tenant_id, site_id, key_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_platform_health (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL DEFAULT 0,
  site_id INT NOT NULL DEFAULT 0,
  score INT NOT NULL DEFAULT 100,
  details_json MEDIUMTEXT NULL,
  window_hours INT NOT NULL DEFAULT 24,
  created_at DATETIME NOT NULL,
  PRIMARY KEY (id),
  KEY idx_health (tenant_id, site_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_capacity_forecast (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL DEFAULT 0,
  site_id INT NOT NULL DEFAULT 0,
  window_hours INT NOT NULL DEFAULT 24,
  rps_est DOUBLE NULL,
  p95_ms_est DOUBLE NULL,
  error_rate_est DOUBLE NULL,
  notes VARCHAR(255) NULL,
  created_at DATETIME NOT NULL,
  PRIMARY KEY (id),
  KEY idx_forecast (tenant_id, site_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_rollouts (
  id BIGINT NOT NULL AUTO_INCREMENT,
  scope VARCHAR(16) NOT NULL DEFAULT 'tenant',
  tenant_id INT NOT NULL DEFAULT 0,
  site_id INT NOT NULL DEFAULT 0,
  target_version VARCHAR(32) NOT NULL,
  strategy VARCHAR(16) NOT NULL DEFAULT 'canary',
  step_percent INT NOT NULL DEFAULT 20,
  step_delay_sec INT NOT NULL DEFAULT 300,
  status VARCHAR(16) NOT NULL DEFAULT 'planned',
  current_percent INT NOT NULL DEFAULT 0,
  details_json MEDIUMTEXT NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  KEY idx_rollouts (status, created_at),
  KEY idx_rollouts_scope (scope, tenant_id, site_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_self_heal_actions (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL DEFAULT 0,
  site_id INT NOT NULL DEFAULT 0,
  kind VARCHAR(32) NOT NULL,
  input_json MEDIUMTEXT NULL,
  status VARCHAR(16) NOT NULL DEFAULT 'queued',
  result_json MEDIUMTEXT NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  KEY idx_heal (tenant_id, site_id, status, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
