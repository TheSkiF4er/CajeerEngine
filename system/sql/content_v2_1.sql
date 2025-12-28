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
