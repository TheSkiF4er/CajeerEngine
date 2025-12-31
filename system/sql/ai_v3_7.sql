-- v3.7 AI-Assisted Platform (production baseline)

CREATE TABLE IF NOT EXISTS ce_ai_policies (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL DEFAULT 0,
  opt_in TINYINT(1) NOT NULL DEFAULT 0,
  allow_content TINYINT(1) NOT NULL DEFAULT 1,
  allow_templates TINYINT(1) NOT NULL DEFAULT 1,
  allow_logs TINYINT(1) NOT NULL DEFAULT 0,
  allow_secrets TINYINT(1) NOT NULL DEFAULT 0,
  allow_pii TINYINT(1) NOT NULL DEFAULT 0,
  store_requests TINYINT(1) NOT NULL DEFAULT 1,
  transparency TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_ai_policy (tenant_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_ai_requests (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL DEFAULT 0,
  user_id BIGINT NULL,
  provider VARCHAR(32) NOT NULL,
  model VARCHAR(64) NULL,
  purpose VARCHAR(32) NOT NULL,
  prompt_json MEDIUMTEXT NOT NULL,
  response_json MEDIUMTEXT NULL,
  tokens_in INT NULL,
  tokens_out INT NULL,
  latency_ms INT NULL,
  status VARCHAR(16) NOT NULL DEFAULT 'ok',
  reason VARCHAR(190) NULL,
  created_at DATETIME NOT NULL,
  PRIMARY KEY (id),
  KEY idx_ai_req (tenant_id, purpose, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_ai_recommendations (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL DEFAULT 0,
  kind VARCHAR(32) NOT NULL,
  title VARCHAR(190) NOT NULL,
  details_json MEDIUMTEXT NULL,
  source VARCHAR(32) NOT NULL DEFAULT 'heuristic',
  status VARCHAR(16) NOT NULL DEFAULT 'open',
  created_at DATETIME NOT NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  KEY idx_ai_rec (tenant_id, kind, status, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
