-- UI Builder storage (v2.2)
CREATE TABLE IF NOT EXISTS ce_ui_layouts (
  content_id INT NOT NULL,
  layout_json MEDIUMTEXT NOT NULL,
  updated_at DATETIME NULL,
  PRIMARY KEY (content_id),
  CONSTRAINT fk_ce_ui_content FOREIGN KEY (content_id) REFERENCES ce_content_items(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
