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
