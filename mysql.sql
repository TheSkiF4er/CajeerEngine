-- CajeerEngine: combined install SQL
-- Generated from project sources: concatenated *.sql files
-- Recommended: run against an EMPTY database.
--
-- Usage:
--   mysql -u <user> -p -h <host> <database> < cajeerengine-install.sql

SET NAMES utf8mb4;
SET time_zone = '+00:00';
SET sql_mode = 'NO_ENGINE_SUBSTITUTION';

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS;
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS;
SET @OLD_SQL_NOTES=@@SQL_NOTES;

SET FOREIGN_KEY_CHECKS=0;
SET UNIQUE_CHECKS=0;
SET SQL_NOTES=0;

START TRANSACTION;


-- =====================================================================
-- BEGIN: system/schema.sql
-- SHA256(content): d0f44d4f48c6a4685e39de6eec1f7cc1298ccd5a776a18a2301fbb90aa842f5a
-- =====================================================================

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

CREATE TABLE IF NOT EXISTS `groups` (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(60) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_groups_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `users` (
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

-- =====================================================================
-- BEGIN: system/seed_demo.sql
-- SHA256(content): bc92bd2c81038ce84c230943636989f3a95bb425e2167ec67de39d48227259ba
-- =====================================================================

-- Demo seed for v1.2.0

INSERT INTO categories (parent_id, title, slug, sort_order, is_active) VALUES
(NULL, 'Новости', 'news', 1, 1),
(NULL, 'Страницы', 'pages', 2, 1),
(1, 'Обновления', 'updates', 1, 1),
(1, 'Разработка', 'dev', 2, 1);

INSERT INTO content (type, category_id, title, slug, excerpt, content, fields_json, status, created_at, updated_at) VALUES
('news', 3, 'Запуск Template Engine v1.1', 'template-engine-v11', 'Релиз шаблонизатора DLE-style.', 'Полный релиз Template Engine v1.1: include/if/group/module-tags и кеш компиляции.', JSON_OBJECT('source','internal','rating',5), 'published', NOW(), NOW()),
('news', 4, 'CajeerEngine Content Core v1.2', 'content-core-v12', 'Базовая модель контента готова.', 'Добавлены новости, страницы, категории, ЧПУ, поля, пагинация.', JSON_OBJECT('source','internal','rating',4), 'published', NOW(), NOW()),
('page', 0, 'О проекте', 'about', 'Кратко о CajeerEngine.', 'CajeerEngine — open-source CMS нового поколения. Автор: TheSkiF4er.', JSON_OBJECT('menu',true), 'published', NOW(), NOW());

-- Admin/RBAC seed
INSERT INTO `groups` (id, title, slug) VALUES
(1, 'Super Admin', 'superadmin'),
(2, 'Editor', 'editor'),
(3, 'Viewer', 'viewer')
ON DUPLICATE KEY UPDATE title=VALUES(title), slug=VALUES(slug);

-- Default permissions (edit as needed)
INSERT INTO group_permissions (group_id, permission, allowed) VALUES
(2, 'admin.dashboard', 1),
(2, 'content.read', 1),
(2, 'content.write', 1),
(2, 'templates.read', 1),
(2, 'templates.write', 0),
(2, 'users.read', 0),
(2, 'users.write', 0),
(2, 'logs.read', 1),

(3, 'admin.dashboard', 1),
(3, 'content.read', 1),
(3, 'content.write', 0),
(3, 'templates.read', 0),
(3, 'templates.write', 0),
(3, 'users.read', 0),
(3, 'users.write', 0),
(3, 'logs.read', 0)
ON DUPLICATE KEY UPDATE allowed=VALUES(allowed);

INSERT INTO `users` (id, username, password_hash, group_id, created_at) VALUES
(1, 'admin', '$2b$10$2AOw8ixdqb.vuqB5ElbrHeIPH6Xb1.afDRIIqk2haG/g3vMQGYxJy', 1, NOW())
ON DUPLICATE KEY UPDATE username=VALUES(username), group_id=VALUES(group_id);

-- =====================================================================
-- BEGIN: system/sql/ai_v3_7.sql
-- SHA256(content): 7b276764cefee1bfe17fd44cb04ffc2ad9a9f93ec8d672a97a083a172ff45e03
-- =====================================================================

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

-- =====================================================================
-- BEGIN: system/sql/async_v3_1.sql
-- SHA256(content): 03a41bb88f5555b7859203a50599b4cb6ec0da606bc966d62dbbc4dec140acb5
-- =====================================================================

-- v3.1 Async Core & Workers

-- Conditional column adds for ce_jobs
SET @ce_col_exists := (
  SELECT COUNT(1) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'ce_jobs'
    AND COLUMN_NAME = 'idempotency_key'
);
SET @ce_sql := IF(@ce_col_exists = 0,
  'ALTER TABLE `ce_jobs` ADD COLUMN `idempotency_key` VARCHAR(64) NULL',
  'SELECT 1'
);
PREPARE ce_stmt FROM @ce_sql;
EXECUTE ce_stmt;
DEALLOCATE PREPARE ce_stmt;

SET @ce_col_exists := (
  SELECT COUNT(1) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'ce_jobs'
    AND COLUMN_NAME = 'available_at'
);
SET @ce_sql := IF(@ce_col_exists = 0,
  'ALTER TABLE `ce_jobs` ADD COLUMN `available_at` DATETIME NULL',
  'SELECT 1'
);
PREPARE ce_stmt FROM @ce_sql;
EXECUTE ce_stmt;
DEALLOCATE PREPARE ce_stmt;

SET @ce_col_exists := (
  SELECT COUNT(1) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'ce_jobs'
    AND COLUMN_NAME = 'visibility_timeout_sec'
);
SET @ce_sql := IF(@ce_col_exists = 0,
  'ALTER TABLE `ce_jobs` ADD COLUMN `visibility_timeout_sec` INT NOT NULL DEFAULT 60',
  'SELECT 1'
);
PREPARE ce_stmt FROM @ce_sql;
EXECUTE ce_stmt;
DEALLOCATE PREPARE ce_stmt;

SET @ce_col_exists := (
  SELECT COUNT(1) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'ce_jobs'
    AND COLUMN_NAME = 'priority'
);
SET @ce_sql := IF(@ce_col_exists = 0,
  'ALTER TABLE `ce_jobs` ADD COLUMN `priority` INT NOT NULL DEFAULT 0',
  'SELECT 1'
);
PREPARE ce_stmt FROM @ce_sql;
EXECUTE ce_stmt;
DEALLOCATE PREPARE ce_stmt;



-- Conditional index create for idx_jobs_availability on ce_jobs
SET @ce_idx_exists := (
  SELECT COUNT(1) FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'ce_jobs'
    AND INDEX_NAME = 'idx_jobs_availability'
);
SET @ce_sql := IF(@ce_idx_exists = 0,
  'CREATE INDEX `idx_jobs_availability` ON `ce_jobs` (status, queue, available_at, priority, id)',
  'SELECT 1'
);
PREPARE ce_stmt FROM @ce_sql;
EXECUTE ce_stmt;
DEALLOCATE PREPARE ce_stmt;


CREATE TABLE IF NOT EXISTS ce_job_failures (
  id BIGINT NOT NULL AUTO_INCREMENT,
  job_id BIGINT NULL,
  tenant_id INT NOT NULL DEFAULT 0,
  queue VARCHAR(64) NOT NULL DEFAULT 'default',
  handler VARCHAR(190) NOT NULL,
  payload_json MEDIUMTEXT NULL,
  attempts INT NOT NULL DEFAULT 0,
  last_error TEXT NULL,
  failed_at DATETIME NOT NULL,
  PRIMARY KEY (id),
  KEY idx_failures_tenant (tenant_id, queue, failed_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_events (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL DEFAULT 0,
  name VARCHAR(190) NOT NULL,
  payload_json MEDIUMTEXT NULL,
  status VARCHAR(16) NOT NULL DEFAULT 'stored',
  created_at DATETIME NOT NULL,
  processed_at DATETIME NULL,
  last_error TEXT NULL,
  PRIMARY KEY (id),
  KEY idx_events_status (status, created_at),
  KEY idx_events_tenant (tenant_id, status, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================================
-- BEGIN: system/sql/content_v2_1.sql
-- SHA256(content): a99430ca667cea12a95915440304531a15fd1877b0716f32cda4236657bf66fb
-- =====================================================================

-- CajeerEngine Content Core (v2.1)
CREATE TABLE IF NOT EXISTS ce_categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  parent_id INT NOT NULL DEFAULT 0,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  INDEX(parent_id),
  INDEX(slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_content_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  type VARCHAR(32) NOT NULL,
  status VARCHAR(16) NOT NULL DEFAULT 'draft',
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL,
  category_id INT NOT NULL DEFAULT 0,
  fields JSON NULL,
  relationships JSON NULL,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  published_at DATETIME NULL,
  INDEX(type),
  INDEX(status),
  INDEX(slug),
  INDEX(category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_content_versions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  item_id INT NOT NULL,
  status VARCHAR(16) NOT NULL,
  body MEDIUMTEXT NULL,
  fields JSON NULL,
  relationships JSON NULL,
  created_at DATETIME NULL,
  INDEX(item_id),
  INDEX(status),
  CONSTRAINT fk_ce_cv_item FOREIGN KEY (item_id) REFERENCES ce_content_items(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================================
-- BEGIN: system/sql/control_plane_v3_9.sql
-- SHA256(content): a490315ac59db2fadd371722214c1dea5b66da10542edea5edf34c065dfafdc3
-- =====================================================================

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

-- =====================================================================
-- BEGIN: system/sql/edge_v3_8.sql
-- SHA256(content): be1a1e12f66b5ee2a0573d6c3cb0df75363f90f216fc78fcc3fcdf669766a5e8
-- =====================================================================

-- v3.8 Distributed & Edge Platform (production baseline)

CREATE TABLE IF NOT EXISTS ce_regions (
  id INT NOT NULL AUTO_INCREMENT,
  name VARCHAR(32) NOT NULL,
  role VARCHAR(16) NOT NULL DEFAULT 'origin', -- origin|edge
  base_url VARCHAR(255) NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_region_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_edge_route_logs (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL DEFAULT 0,
  region VARCHAR(32) NOT NULL,
  decision VARCHAR(32) NOT NULL, -- origin|edge|redirect
  path VARCHAR(190) NOT NULL,
  status_code INT NOT NULL DEFAULT 200,
  ms INT NULL,
  created_at DATETIME NOT NULL,
  PRIMARY KEY (id),
  KEY idx_edge_route (tenant_id, region, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- replication journal (foundation)
CREATE TABLE IF NOT EXISTS ce_replication_journal (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL DEFAULT 0,
  entity VARCHAR(64) NOT NULL,
  entity_id VARCHAR(64) NOT NULL,
  action VARCHAR(16) NOT NULL, -- upsert|delete
  payload_json MEDIUMTEXT NULL,
  origin_region VARCHAR(32) NOT NULL,
  created_at DATETIME NOT NULL,
  PRIMARY KEY (id),
  KEY idx_repl (tenant_id, origin_region, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================================
-- BEGIN: system/sql/enterprise_v2_9.sql
-- SHA256(content): 58ff6838561a3840df01949ef6b8278e9d031f54da5c5c9357c1a371c4f84ba0
-- =====================================================================

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

-- =====================================================================
-- BEGIN: system/sql/frontend_v3_4.sql
-- SHA256(content): c488ca4db69cab7fff53a6e49f6047dd0112645b1da830130861b9054b09896c
-- =====================================================================

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

-- =====================================================================
-- BEGIN: system/sql/identity_v3_3.sql
-- SHA256(content): 26e3dcf097336c5104e0b6fe68294d6d170cf9e03d43e9f788e12ae97666fd15
-- =====================================================================

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

-- =====================================================================
-- BEGIN: system/sql/intelligence_v3_5.sql
-- SHA256(content): 5cc5569792291a6f032925bf8975f8234ab755ab343ade1431bf7fc260761978
-- =====================================================================

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

-- =====================================================================
-- BEGIN: system/sql/marketplace_v2_3.sql
-- SHA256(content): 0105ffee440d7086d7ddf8780fd4cb4efcd15950978d4efb01f0aba1e9d4cb1a
-- =====================================================================

-- Marketplace foundation (v2.3)
CREATE TABLE IF NOT EXISTS ce_marketplace_publishers (
  publisher_id VARCHAR(64) NOT NULL,
  title VARCHAR(190) NULL,
  pubkey_ed25519 VARCHAR(255) NOT NULL,
  trusted TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (publisher_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_marketplace_packages (
  id INT NOT NULL AUTO_INCREMENT,
  type VARCHAR(32) NOT NULL,
  name VARCHAR(100) NOT NULL,
  version VARCHAR(50) NOT NULL,
  title VARCHAR(190) NULL,
  publisher_id VARCHAR(64) NULL,
  signature_required TINYINT(1) NOT NULL DEFAULT 1,
  installed_at DATETIME NULL,
  updated_at DATETIME NULL,
  meta_json MEDIUMTEXT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_pkg (type, name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_marketplace_cache (
  cache_key VARCHAR(190) NOT NULL,
  payload_json MEDIUMTEXT NOT NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (cache_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================================
-- BEGIN: system/sql/marketplace_v2_7.sql
-- SHA256(content): 393a79eac4e68dacafacd4e7896a194c3d08742fd9bf7792e6f2e09deb944882
-- =====================================================================

-- v2.7 Marketplace Expansion & Economy (foundation)
CREATE TABLE IF NOT EXISTS ce_marketplace_registries (
  id INT NOT NULL AUTO_INCREMENT,
  code VARCHAR(64) NOT NULL,
  name VARCHAR(190) NOT NULL,
  base_url VARCHAR(255) NOT NULL,
  verification_level VARCHAR(16) NOT NULL DEFAULT 'community',
  enabled TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_registry_code (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_marketplace_publishers (
  id INT NOT NULL AUTO_INCREMENT,
  publisher_key VARCHAR(128) NOT NULL,
  display_name VARCHAR(190) NULL,
  verification_level VARCHAR(16) NOT NULL DEFAULT 'community',
  public_key_text MEDIUMTEXT NULL,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_publisher_key (publisher_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_marketplace_categories (
  id INT NOT NULL AUTO_INCREMENT,
  slug VARCHAR(64) NOT NULL,
  title VARCHAR(190) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_cat_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_marketplace_packages (
  id BIGINT NOT NULL AUTO_INCREMENT,
  registry_id INT NOT NULL,
  package_key VARCHAR(190) NOT NULL,
  name VARCHAR(190) NOT NULL,
  publisher_key VARCHAR(128) NOT NULL,
  type VARCHAR(32) NOT NULL,
  version VARCHAR(50) NOT NULL,
  description TEXT NULL,
  categories JSON NULL,
  rating_avg DECIMAL(3,2) NOT NULL DEFAULT 0.00,
  rating_count INT NOT NULL DEFAULT 0,
  dependencies JSON NULL,
  license JSON NULL,
  is_paid TINYINT(1) NOT NULL DEFAULT 0,
  price JSON NULL,
  signature TEXT NULL,
  manifest_json MEDIUMTEXT NULL,
  updated_at DATETIME NULL,
  created_at DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_pkg (registry_id, package_key, version),
  KEY idx_pkg_search (name(50), type, publisher_key),
  KEY idx_pkg_registry (registry_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_marketplace_ratings (
  id BIGINT NOT NULL AUTO_INCREMENT,
  package_id BIGINT NOT NULL,
  user_id BIGINT NULL,
  rating TINYINT NOT NULL,
  comment TEXT NULL,
  created_at DATETIME NULL,
  PRIMARY KEY (id),
  KEY idx_rating_pkg (package_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_installed_packages (
  id BIGINT NOT NULL AUTO_INCREMENT,
  package_key VARCHAR(190) NOT NULL,
  type VARCHAR(32) NOT NULL,
  version VARCHAR(50) NOT NULL,
  source_registry VARCHAR(64) NULL,
  installed_at DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_installed (package_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================================
-- BEGIN: system/sql/marketplace_v3_2.sql
-- SHA256(content): 36cc1fd464ee9032acb7a085d128f889f2f87ce6679b34273d8e4c2d58245bed
-- =====================================================================

-- v3.2 Marketplace 2.0 (Production baseline)

CREATE TABLE IF NOT EXISTS ce_marketplace_registries (
  id BIGINT NOT NULL AUTO_INCREMENT,
  name VARCHAR(64) NOT NULL,
  base_url VARCHAR(255) NOT NULL,
  token VARCHAR(255) NULL,
  enabled TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_registry_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_publishers (
  id BIGINT NOT NULL AUTO_INCREMENT,
  publisher_id VARCHAR(64) NOT NULL,
  name VARCHAR(190) NOT NULL,
  verification_level VARCHAR(32) NOT NULL DEFAULT 'community', -- community/verified/trusted
  public_key_base64 TEXT NULL,
  reputation_score INT NOT NULL DEFAULT 0,
  metadata_json MEDIUMTEXT NULL,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_publisher (publisher_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_installed_packages (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL DEFAULT 0,
  type VARCHAR(32) NOT NULL, -- plugin/theme/ui-block/content-type
  slug VARCHAR(190) NOT NULL,
  version VARCHAR(64) NOT NULL,
  enabled TINYINT(1) NOT NULL DEFAULT 1,
  source_registry VARCHAR(64) NULL,
  manifest_json MEDIUMTEXT NOT NULL,
  license_json MEDIUMTEXT NULL,
  installed_at DATETIME NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_installed_pkg (tenant_id, type, slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_package_licenses (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL DEFAULT 0,
  package_slug VARCHAR(190) NOT NULL,
  license_key VARCHAR(255) NULL,
  status VARCHAR(16) NOT NULL DEFAULT 'inactive', -- inactive/active/expired/revoked
  expires_at DATETIME NULL,
  metadata_json MEDIUMTEXT NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  KEY idx_license_pkg (tenant_id, package_slug, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_package_usage (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL DEFAULT 0,
  package_slug VARCHAR(190) NOT NULL,
  metric VARCHAR(64) NOT NULL,
  value BIGINT NOT NULL DEFAULT 0,
  period_start DATETIME NULL,
  period_end DATETIME NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  KEY idx_usage_pkg (tenant_id, package_slug, metric)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_security_scans (
  id BIGINT NOT NULL AUTO_INCREMENT,
  package_slug VARCHAR(190) NOT NULL,
  version VARCHAR(64) NOT NULL,
  status VARCHAR(16) NOT NULL DEFAULT 'pending', -- pending/passed/failed
  report_json MEDIUMTEXT NULL,
  scanned_at DATETIME NULL,
  PRIMARY KEY (id),
  KEY idx_scan_pkg (package_slug, version, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================================
-- BEGIN: system/sql/platform_v2_5.sql
-- SHA256(content): add65ae8583fd624668ef765d73ad7b6c6be67648122ea609c93db2c8dc7a74b
-- =====================================================================

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

-- =====================================================================
-- BEGIN: system/sql/platform_v3_0.sql
-- SHA256(content): 480547c759c85aabef78dfba34c63ca1db5730e612942f2795ac6a673d1ea8c1
-- =====================================================================

-- v3.0 CajeerEngine Platform (foundation)

CREATE TABLE IF NOT EXISTS ce_plugins (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL DEFAULT 0,
  name VARCHAR(190) NOT NULL,
  slug VARCHAR(190) NOT NULL,
  version VARCHAR(64) NOT NULL,
  enabled TINYINT(1) NOT NULL DEFAULT 0,
  manifest_json MEDIUMTEXT NOT NULL,
  installed_at DATETIME NULL,
  enabled_at DATETIME NULL,
  disabled_at DATETIME NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_plugin (tenant_id, slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_jobs (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL DEFAULT 0,
  queue VARCHAR(64) NOT NULL DEFAULT 'default',
  handler VARCHAR(190) NOT NULL,
  payload_json MEDIUMTEXT NULL,
  attempts INT NOT NULL DEFAULT 0,
  max_attempts INT NOT NULL DEFAULT 10,
  status VARCHAR(16) NOT NULL DEFAULT 'queued',
  run_at DATETIME NULL,
  locked_at DATETIME NULL,
  lock_token VARCHAR(64) NULL,
  last_error TEXT NULL,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  KEY idx_jobs_status (status, queue, run_at),
  KEY idx_jobs_tenant (tenant_id, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_api_contracts (
  id BIGINT NOT NULL AUTO_INCREMENT,
  tenant_id INT NOT NULL DEFAULT 0,
  api_name VARCHAR(64) NOT NULL,
  api_version VARCHAR(16) NOT NULL,
  locked TINYINT(1) NOT NULL DEFAULT 1,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_api_contract (tenant_id, api_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ce_partners (
  id BIGINT NOT NULL AUTO_INCREMENT,
  org_name VARCHAR(190) NOT NULL,
  contact_email VARCHAR(190) NULL,
  website VARCHAR(255) NULL,
  level VARCHAR(32) NOT NULL DEFAULT 'community',
  metadata_json MEDIUMTEXT NULL,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (id),
  KEY idx_partners_level (level)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================================
-- BEGIN: system/sql/platform_v4_0.sql
-- SHA256(content): a9ed769b0637e0099d6d8ec05feef4e569737a4b818a15c9b92eb76826cef8d1
-- =====================================================================

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

-- =====================================================================
-- BEGIN: system/sql/ui_builder_v2_2.sql
-- SHA256(content): 3782000909b2f9ffacc88b1603271b0ed16a1d17ce60b9158dbfad1beb6cb3b1
-- =====================================================================

-- UI Builder storage (v2.2)
CREATE TABLE IF NOT EXISTS ce_ui_layouts (
  content_id INT NOT NULL,
  layout_json MEDIUMTEXT NOT NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (content_id),
  CONSTRAINT fk_ce_ui_content FOREIGN KEY (content_id) REFERENCES ce_content_items(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================================
-- BEGIN: system/sql/ui_builder_v2_8.sql
-- SHA256(content): ee1e75eacff8444fd5428da57240e331a0382d770697e243fc617f52fcbd16d8
-- =====================================================================

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

-- =====================================================================
-- BEGIN: system/sql/workflow_v2_4.sql
-- SHA256(content): bcd6dd960bcc97cf72fcf18fb0d2ee0ae20d7258106490c1e7dfc58adecdf54c
-- =====================================================================

-- v2.4 workflow fields (run once)
ALTER TABLE ce_content_items ADD COLUMN workflow_state VARCHAR(20) NOT NULL DEFAULT 'draft';
ALTER TABLE ce_content_items ADD COLUMN published_at DATETIME NULL;
ALTER TABLE ce_content_items ADD COLUMN scheduled_at DATETIME NULL;

-- approvals
CREATE TABLE IF NOT EXISTS ce_workflow_approvals (
  id INT NOT NULL AUTO_INCREMENT,
  content_id INT NOT NULL,
  state VARCHAR(20) NOT NULL,
  requested_by INT NULL,
  approved_by INT NULL,
  scheduled_at DATETIME NULL,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  notes TEXT NULL,
  PRIMARY KEY (id),
  KEY idx_content (content_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;

SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
SET SQL_NOTES=@OLD_SQL_NOTES;

-- End of combined install SQL
