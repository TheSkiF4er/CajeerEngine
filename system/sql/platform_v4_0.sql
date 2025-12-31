-- v4.0 AI-Native & Cloud-First Platform

CREATE TABLE IF NOT EXISTS ce_platform_intents (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL DEFAULT 0,
  name VARCHAR(64) NOT NULL,
  kind VARCHAR(64) NOT NULL,
  desired_json MEDIUMTEXT NOT NULL,
  status VARCHAR(16) NOT NULL DEFAULT 'pending',
  last_error VARCHAR(255) NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  KEY idx_intents (tenant_id, status, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_platform_reconcile_runs (
  id BIGINT NOT NULL AUTO_INCREMENT,
  run_kind VARCHAR(32) NOT NULL DEFAULT 'manual',
  stats_json MEDIUMTEXT NULL,
  created_at DATETIME NOT NULL,
  PRIMARY KEY (id),
  KEY idx_runs (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_event_mesh (
  id BIGINT NOT NULL AUTO_INCREMENT,
  topic VARCHAR(190) NOT NULL,
  payload MEDIUMTEXT NOT NULL,
  created_at DATETIME NOT NULL,
  PRIMARY KEY (id),
  KEY idx_mesh (topic, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_declarative_plugins (
  id BIGINT NOT NULL AUTO_INCREMENT,
  name VARCHAR(64) NOT NULL,
  version VARCHAR(32) NOT NULL,
  manifest_json MEDIUMTEXT NOT NULL,
  status VARCHAR(16) NOT NULL DEFAULT 'enabled',
  created_at DATETIME NOT NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_plugin (name, version)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_solution_blueprints (
  id BIGINT NOT NULL AUTO_INCREMENT,
  name VARCHAR(128) NOT NULL,
  kind VARCHAR(32) NOT NULL DEFAULT 'certified',
  manifest_json MEDIUMTEXT NOT NULL,
  created_at DATETIME NOT NULL,
  PRIMARY KEY (id),
  KEY idx_blueprints (kind, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_ai_marketplace_items (
  id BIGINT NOT NULL AUTO_INCREMENT,
  name VARCHAR(128) NOT NULL,
  kind VARCHAR(32) NOT NULL DEFAULT 'model',
  publisher VARCHAR(64) NOT NULL,
  version VARCHAR(32) NOT NULL,
  signature VARCHAR(255) NULL,
  metadata_json MEDIUMTEXT NULL,
  created_at DATETIME NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_ai_item (name, version)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_automation_marketplace_items (
  id BIGINT NOT NULL AUTO_INCREMENT,
  name VARCHAR(128) NOT NULL,
  kind VARCHAR(32) NOT NULL DEFAULT 'workflow',
  publisher VARCHAR(64) NOT NULL,
  version VARCHAR(32) NOT NULL,
  signature VARCHAR(255) NULL,
  metadata_json MEDIUMTEXT NULL,
  created_at DATETIME NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_auto_item (name, version)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
