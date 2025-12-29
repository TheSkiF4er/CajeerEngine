-- v2.8 Advanced UI Builder & Frontend Platform (foundation)

CREATE TABLE IF NOT EXISTS ce_ui_layouts (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL DEFAULT 0,
  site_id INT NOT NULL DEFAULT 0,
  page_key VARCHAR(190) NOT NULL,
  title VARCHAR(190) NULL,
  active_version INT NOT NULL DEFAULT 1,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_layout (tenant_id, site_id, page_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_ui_layout_versions (
  id BIGINT NOT NULL AUTO_INCREMENT,
  layout_id BIGINT NOT NULL,
  version INT NOT NULL,
  json MEDIUMTEXT NOT NULL,
  dsl_snapshot MEDIUMTEXT NULL,
  author_user_id BIGINT NULL,
  created_at DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_layout_ver (layout_id, version),
  KEY idx_layout_ver (layout_id, version)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_ui_patterns (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL DEFAULT 0,
  site_id INT NOT NULL DEFAULT 0,
  pattern_key VARCHAR(190) NOT NULL,
  title VARCHAR(190) NULL,
  json MEDIUMTEXT NOT NULL,
  version INT NOT NULL DEFAULT 1,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_pattern (tenant_id, site_id, pattern_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_ui_block_permissions (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL DEFAULT 0,
  site_id INT NOT NULL DEFAULT 0,
  block_key VARCHAR(190) NOT NULL,
  require_permission VARCHAR(190) NOT NULL,
  created_at DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_block_perm (tenant_id, site_id, block_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
