-- v3.1 Async Core & Workers

ALTER TABLE ce_jobs
  ADD COLUMN IF NOT EXISTS idempotency_key VARCHAR(64) NULL,
  ADD COLUMN IF NOT EXISTS available_at DATETIME NULL,
  ADD COLUMN IF NOT EXISTS visibility_timeout_sec INT NOT NULL DEFAULT 60,
  ADD COLUMN IF NOT EXISTS priority INT NOT NULL DEFAULT 0;

CREATE INDEX IF NOT EXISTS idx_jobs_availability ON ce_jobs(status, queue, available_at, priority, id);

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
