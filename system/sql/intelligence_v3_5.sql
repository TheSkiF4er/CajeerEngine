-- v3.5 Platform Intelligence & Automation (baseline)

-- Usage analytics (per request / per feature)
CREATE TABLE IF NOT EXISTS ce_usage_events (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL DEFAULT 0,
  user_id BIGINT NULL,
  event_type VARCHAR(64) NOT NULL,
  path VARCHAR(255) NULL,
  feature VARCHAR(64) NULL,
  value BIGINT NOT NULL DEFAULT 1,
  meta_json MEDIUMTEXT NULL,
  ts DATETIME NOT NULL,
  PRIMARY KEY (id),
  KEY idx_usage_tenant_ts (tenant_id, ts),
  KEY idx_usage_type (event_type, ts)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Performance insights (slow requests / slow queries)
CREATE TABLE IF NOT EXISTS ce_perf_requests (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL DEFAULT 0,
  method VARCHAR(16) NOT NULL,
  path VARCHAR(255) NOT NULL,
  status INT NOT NULL DEFAULT 200,
  duration_ms INT NOT NULL,
  meta_json MEDIUMTEXT NULL,
  ts DATETIME NOT NULL,
  PRIMARY KEY (id),
  KEY idx_perf_req (tenant_id, ts),
  KEY idx_perf_req_path (path, ts)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_perf_queries (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL DEFAULT 0,
  query_hash CHAR(64) NOT NULL,
  duration_ms INT NOT NULL,
  meta_json MEDIUMTEXT NULL,
  ts DATETIME NOT NULL,
  PRIMARY KEY (id),
  KEY idx_perf_q (tenant_id, ts),
  KEY idx_perf_q_hash (query_hash, ts)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Cost visibility (abstract credits)
CREATE TABLE IF NOT EXISTS ce_cost_ledger (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL DEFAULT 0,
  category VARCHAR(64) NOT NULL, -- storage|requests|workers|marketplace|custom
  amount BIGINT NOT NULL,
  unit VARCHAR(32) NOT NULL DEFAULT 'credits',
  meta_json MEDIUMTEXT NULL,
  ts DATETIME NOT NULL,
  PRIMARY KEY (id),
  KEY idx_cost_tenant_ts (tenant_id, ts)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Automation policies and executions
CREATE TABLE IF NOT EXISTS ce_auto_policies (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL DEFAULT 0,
  policy_id VARCHAR(128) NOT NULL,
  type VARCHAR(32) NOT NULL, -- autoscale|optimize|alert
  spec_json MEDIUMTEXT NOT NULL,
  enabled TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_policy (tenant_id, policy_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_auto_runs (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL DEFAULT 0,
  policy_id VARCHAR(128) NOT NULL,
  status VARCHAR(16) NOT NULL DEFAULT 'ok', -- ok|skipped|failed
  decision_json MEDIUMTEXT NULL,
  created_at DATETIME NOT NULL,
  PRIMARY KEY (id),
  KEY idx_auto_runs (tenant_id, policy_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Predictive alerts (foundation)
CREATE TABLE IF NOT EXISTS ce_alerts (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL DEFAULT 0,
  severity VARCHAR(16) NOT NULL DEFAULT 'info', -- info|warn|critical
  title VARCHAR(190) NOT NULL,
  details_json MEDIUMTEXT NULL,
  status VARCHAR(16) NOT NULL DEFAULT 'open', -- open|ack|closed
  created_at DATETIME NOT NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  KEY idx_alerts (tenant_id, status, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
