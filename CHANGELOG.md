# Changelog

## [1.7.0] - 2025-12-28
### Добавлено — Updater & Packages
- Полноценный Updater: backup/rollback (restore), каналы stable/beta, поддержка локального/удалённого манифеста.
- Форматы пакетов:
  - `.cajeerpkg` — пакет (overlay файлов + хуки pre/post).
  - `.cajeerpatch` — патч (overlay + опциональные проверки).
- CLI-команды: updater:check/apply/backup/restore.
- Поддержка обновления компонентов (в т.ч. public/assets/rarog) через пакеты.


## [1.6.0] - 2025-12-28
### Добавлено — Migration Toolkit (DLE → CajeerEngine)
- CLI-набор миграции: проверка совместимости, импорт БД, конвертация шаблонов, отчёт ошибок.
- Импорт основных сущностей DLE (best-effort): новости/посты, категории, статические страницы, пользователи.
- Конвертер шаблонов DLE → Cajeer `.tpl` + генерация предупреждений/логов.
- DLE Tag Adapter: режим совместимости для наиболее частых DLE-тегов в шаблонах.


## [1.5.0] - 2025-12-28
### Добавлено — SEO
- Meta Manager: управление title/description/keywords/canonical, OpenGraph/Twitter Cards.
- JSON-LD микроразметка (Article/Website) через SEO API.
- `/sitemap.xml` (динамический) и `public/robots.txt` (production-friendly шаблон).

### Добавлено — Performance
- Page cache (full HTML) для GET запросов (skip for logged-in/admin/POST).
- Fragment cache API (`Cache::remember`) для компонентов/виджетов/модулей.
- Template cache hardening: авто-инвалидация compiled tpl при изменении исходника.
- Cache invalidation по тегам (`tags`) и быстрый `cache:clear` через CLI.
- Lazy-loading helpers: `Html::lazyImages()` для безопасного добавления `loading="lazy"`.

### Изменено
- Kernel выполняет output buffering для перехвата HTML и записи в page cache.


## [1.4.0] - 2025-12-28
### Добавлено
- Plugins & Events v1.4: системный EventBus (events/hooks) и менеджер плагинов.
- Lifecycle модулей: install / enable / disable / uninstall (через PluginManager + CLI).
- Dependency graph + topological load order + version constraints (семантическая проверка).
- Override без правки ядра: плагины могут:
  - подписываться на события (kernel.*, content.*, admin.*),
  - регистрировать template-теги `{module:*}` через Template\Extensions,
  - добавлять маршруты (frontend/admin),
  - подменять сервисы в контейнере (Container bindings).

### Изменено
- Ядро и AdminKernel инициализируют контейнер/события и загружают плагины до роутинга.


## [1.3.0] - 2025-12-28
### Добавлено
- Admin Panel v1.3: полноценный AdminKernel, авторизация и сессии.
- RBAC: группы, права доступа, защита административных маршрутов.
- CRUD контента (news/pages) + массовые операции (publish/unpublish/delete).
- Управление шаблонами: просмотр/редактирование файлов `.tpl` из админки.
- Логи действий (action log) для ключевых операций администрирования.
- Rarog v3.5.0: подключены реальные ассеты из репозитория.


## [1.2.0] - 2025-12-28
### Добавлено
- Content Core v1.2: универсальная контентная модель (news/pages/categories) на PDO.
- Категории (вложенные), ЧПУ (slug), пагинация, сортировка и фильтрация.
- Дополнительные поля: определения + значения (с хранением в JSON на уровне записи).
- CLI: `db:install`, `seed:demo` для быстрого старта.

### Изменено
- Добавлены роуты `/news`, `/news/view`, `/page`, `/category`.
- Шаблоны `.tpl` расширены под списки/карточки контента.


## [1.1.0] - 2025-12-28
### Добавлено
- Template Engine v1.1: компиляция `.tpl → PHP` + кеш.
- Поддержка: `{var}`, `{config ...}`, `{user.*}`, `{include file="...tpl"}`.
- Блоки: `[if]/[else]/[/if]`, `[group]`, `[not-group]`, `[available]` (вложенные).
- Модульные теги: `{module:news ...}` (расширяемый registry).
- Debug-режим шаблонов: `storage/logs/template.debug.log`.

### Изменено
- Примеры шаблонов и контроллеров адаптированы под новый шаблонизатор.


## [1.0.0] - 2025-12-12
### Добавлено
- Публичный релиз скелета CajeerEngine: ядро, роутер, шаблонизатор .tpl (минимальный), DB слой (stub), система обновлений (stub), языки (stub).
- Встроенная интеграция UI-фреймворка Rarog (placeholder assets + docs).
- CLI утилиты (каркас).

### Примечания
- Это релиз скелета: функциональность базовая, предназначена для дальнейшей разработки.
