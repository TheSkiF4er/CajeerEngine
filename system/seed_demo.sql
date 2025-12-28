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
