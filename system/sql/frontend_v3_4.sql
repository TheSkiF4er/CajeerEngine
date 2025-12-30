-- v3.4 Frontend Platform & No-Code (baseline)

-- Collaborative editing sessions / locks
CREATE TABLE IF NOT EXISTS ce_builder_locks (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL DEFAULT 0,
  doc_type VARCHAR(32) NOT NULL, -- page|layout|form|workflow
  doc_id VARCHAR(128) NOT NULL,
  user_id BIGINT NULL,
  lock_token VARCHAR(64) NOT NULL,
  acquired_at DATETIME NOT NULL,
  expires_at DATETIME NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_lock (tenant_id, doc_type, doc_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_builder_changes (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL DEFAULT 0,
  doc_type VARCHAR(32) NOT NULL,
  doc_id VARCHAR(128) NOT NULL,
  user_id BIGINT NULL,
  patch_json MEDIUMTEXT NOT NULL,
  created_at DATETIME NOT NULL,
  PRIMARY KEY (id),
  KEY idx_changes_doc (tenant_id, doc_type, doc_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Component marketplace (runtime registry for UI blocks/components)
CREATE TABLE IF NOT EXISTS ce_components (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL DEFAULT 0,
  slug VARCHAR(190) NOT NULL,
  version VARCHAR(64) NOT NULL,
  manifest_json MEDIUMTEXT NOT NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_component (tenant_id, slug, version)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- A/B testing foundation
CREATE TABLE IF NOT EXISTS ce_ab_experiments (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL DEFAULT 0,
  key_name VARCHAR(128) NOT NULL,
  status VARCHAR(16) NOT NULL DEFAULT 'draft', -- draft|running|paused|ended
  variants_json MEDIUMTEXT NOT NULL,
  traffic_json MEDIUMTEXT NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_ab_key (tenant_id, key_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_ab_assignments (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL DEFAULT 0,
  experiment_key VARCHAR(128) NOT NULL,
  user_hash VARCHAR(64) NOT NULL,
  variant VARCHAR(64) NOT NULL,
  assigned_at DATETIME NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_ab_assign (tenant_id, experiment_key, user_hash)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- No-code: logic blocks + workflows + forms
CREATE TABLE IF NOT EXISTS ce_logic_blocks (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL DEFAULT 0,
  slug VARCHAR(190) NOT NULL,
  version VARCHAR(64) NOT NULL,
  definition_json MEDIUMTEXT NOT NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_logic_block (tenant_id, slug, version)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_workflows (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL DEFAULT 0,
  slug VARCHAR(190) NOT NULL,
  status VARCHAR(16) NOT NULL DEFAULT 'draft', -- draft|active|paused
  graph_json MEDIUMTEXT NOT NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_workflow (tenant_id, slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_forms (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL DEFAULT 0,
  slug VARCHAR(190) NOT NULL,
  status VARCHAR(16) NOT NULL DEFAULT 'draft',
  schema_json MEDIUMTEXT NOT NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_form (tenant_id, slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_form_submissions (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL DEFAULT 0,
  form_slug VARCHAR(190) NOT NULL,
  payload_json MEDIUMTEXT NOT NULL,
  ip VARCHAR(64) NULL,
  ua VARCHAR(255) NULL,
  created_at DATETIME NOT NULL,
  PRIMARY KEY (id),
  KEY idx_form_sub (tenant_id, form_slug, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Frontend ISR cache registry
CREATE TABLE IF NOT EXISTS ce_isr_cache (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL DEFAULT 0,
  cache_key VARCHAR(190) NOT NULL,
  surrogate_keys VARCHAR(255) NULL,
  body MEDIUMBLOB NOT NULL,
  content_type VARCHAR(64) NOT NULL DEFAULT 'text/html',
  status INT NOT NULL DEFAULT 200,
  created_at DATETIME NOT NULL,
  expires_at DATETIME NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_isr_key (tenant_id, cache_key),
  KEY idx_isr_exp (tenant_id, expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
