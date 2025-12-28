-- CajeerEngine v1.2.0 schema
-- MySQL / MariaDB

CREATE TABLE IF NOT EXISTS categories (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  parent_id INT UNSIGNED NULL,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(190) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  UNIQUE KEY uq_categories_slug (slug),
  KEY idx_categories_parent (parent_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS content (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  type VARCHAR(20) NOT NULL, -- news|page
  category_id INT UNSIGNED NOT NULL DEFAULT 0,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(190) NOT NULL,
  excerpt TEXT NULL,
  content LONGTEXT NULL,
  fields_json JSON NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'published',
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_content_type_slug (type, slug),
  KEY idx_content_type_created (type, created_at),
  KEY idx_content_category (category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS custom_fields (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  scope VARCHAR(20) NOT NULL, -- news|page
  name VARCHAR(60) NOT NULL,
  title VARCHAR(255) NOT NULL,
  field_type VARCHAR(20) NOT NULL DEFAULT 'text', -- text|number|select|bool
  options_json JSON NULL,
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  UNIQUE KEY uq_custom_fields_scope_name (scope, name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Admin/RBAC

CREATE TABLE IF NOT EXISTS groups (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(60) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_groups_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  username VARCHAR(60) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  group_id INT UNSIGNED NOT NULL DEFAULT 2,
  created_at DATETIME NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_users_username (username),
  KEY idx_users_group (group_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS group_permissions (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  group_id INT UNSIGNED NOT NULL,
  permission VARCHAR(120) NOT NULL,
  allowed TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  UNIQUE KEY uq_group_perm (group_id, permission)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS action_logs (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id INT UNSIGNED NOT NULL,
  username VARCHAR(60) NOT NULL,
  action VARCHAR(60) NOT NULL,
  entity VARCHAR(60) NOT NULL,
  entity_id INT NULL,
  meta_json JSON NULL,
  ip VARCHAR(64) NULL,
  created_at DATETIME NOT NULL,
  PRIMARY KEY (id),
  KEY idx_logs_user (user_id),
  KEY idx_logs_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

