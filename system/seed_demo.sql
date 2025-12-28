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
INSERT INTO groups (id, title, slug) VALUES
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

INSERT INTO users (id, username, password_hash, group_id, created_at) VALUES
(1, 'admin', '$2b$10$2AOw8ixdqb.vuqB5ElbrHeIPH6Xb1.afDRIIqk2haG/g3vMQGYxJy', 1, NOW())
ON DUPLICATE KEY UPDATE username=VALUES(username), group_id=VALUES(group_id);

