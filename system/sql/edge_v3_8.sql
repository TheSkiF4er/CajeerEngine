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
