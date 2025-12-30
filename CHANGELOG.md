# Changelog

## [3.2.0] - 2025-12-30
### Добавлено
- Marketplace 2.0 (production baseline): remote registry client, authenticated calls.
- Package lifecycle: install/update/rollback/uninstall + backups.
- Mandatory signatures (Ed25519, fail-closed if sodium missing).
- Publisher reputation + security scans tables (foundation).
- Monetization hooks: license metadata + usage hooks (foundation).
- CLI commands: marketplace:search/install/update/uninstall/rollback.

## [3.1.0] - 2025-12-30
### Добавлено
- Async Jobs (production baseline): visibility timeout, retries (backoff+jitter), DLQ.
- Pluggable queue backends: DB default, Redis/SQS/RabbitMQ adapters (stubs).
- Exactly-once foundation via idempotency_key.
- Async events: persistence (optional), async delivery, replay.
- Ops: graceful shutdown, supervisor.

## [3.0.0] - 2025-12-29
### Добавлено
- Platform Core: stable Public Kernel API + plugin-first architecture (foundation).
- Event-driven core: EventBus.
- Async jobs/queues: DB-backed queue + worker (CLI v2).
- Ecosystem foundation: PHP/JS SDK stubs, plugins registry table.
- LTS strategy docs for 3.x.

## [2.9.0] - 2025-12-29
### Добавлено
- Enterprise security foundation: SSO (OIDC/SAML configs + DB), MFA factors storage.
- Immutable audit trails (hash-chained) + verification API/CLI.
- SaaS: tenant lifecycle controls + quotas enforcement hook.
- Compliance: GDPR export/erase tooling (queue/run) + access reports listing.
- Ops: SLA/incident hooks foundation + incident create endpoint.

## [2.8.0] - 2025-12-29
### Добавлено
- Advanced UI Builder: nested layouts, patterns, block permissions, layout versioning + rollback.
- Frontend platform: Theme SDK + asset build hooks (recorded), headless preview endpoint.
- DX: UI vs DSL diff, export UI -> DSL.

## [2.7.0] - 2025-12-29
### Добавлено
- Remote registries (official + custom) + sync to local DB index.
- Marketplace search + ratings + categories (foundation).
- Dependency resolution v2 with SemVer constraints.
- Security: signature enforcement + sandbox preflight.
- Monetization hooks: paid packages foundation + license metadata.

## [2.6.0] - 2025-12-29
### Добавлено
- Enforced tenant/site isolation (platform.enforced + require_tenant).
- API version locking (header/query/prefix) with HTTP 426 on mismatch.
- Observability: JSONL logging, request-id tracing, DB query tracing, Prometheus metrics.
- Health probes: live/ready endpoints.
- Ops: env profiles, safe config validation, backup & restore API.

## [2.5.0] - 2025-12-28
### Добавлено
- Platform mode: multi-tenant + site isolation (tenant/site resolver, context).
- SaaS foundation: tenants, sites, domain mapping tables.
- Usage metrics + optional limits + billing hook placeholders.
- Auto-updates foundation: rollout tables, worker/cron, health checks.

## [2.4.0] - 2025-12-28
### Добавлено
- Security: CSRF (cookie+header/form), basic XSS helpers, rate limiting, IP allow/deny.
- Audit logs: ce_audit_logs + API listing.
- Enterprise RBAC: fine-grained perms, per-content grants, workspace/team isolation helpers.
- Workflow: draft→review→publish, approval foundation, scheduled publishing + cron script.

## [2.3.0] - 2025-12-28
### Добавлено
- Marketplace Core: установка из админки (upload), реестр установленных пакетов, получение remote index.
- Типы пакетов: Plugins, Themes, UI Blocks, Content Types.
- Проверка версий и зависимостей (минимальный semver).
- Подписи пакетов (ed25519) и Trusted publishers (config + DB).
- CLI: marketplace:install-schema, marketplace:install, marketplace:trust.

## [2.2.0] - 2025-12-28
### Добавлено
- UI Builder (Visual Editor): drag&drop editor, sections/grid/blocks, preview.
- JSON → render pipeline + blocks registry.
- Blocks: text, image, gallery, form, custom HTML, module blocks.
- Admin API: /api/v1/ui/*.
- `.tpl` sync: `{ui_builder}` (auto-inject при наличии content_id).
- CLI: `ui:install` + SQL `system/sql/ui_builder_v2_2.sql`.

## [2.1.0] - 2025-12-28
### Добавлено
- Headless Content API v1: CRUD, фильтрация/сортировка/пагинация.
- JSON fields и relationships.
- Версионирование контента (draft/published).
- API Permissions: scopes `content.*` и `admin.*`, policy-aware (RBAC + caps).
- Headless Admin API v1 (JSON).
- CLI `content:install` и SQL схема `system/sql/content_v2_1.sql`.

## [2.0.0] - 2025-12-28
### Добавлено — CajeerEngine Next
- Template DSL v2: DLE-совместимый синтаксис + расширения, компиляция `.tpl → PHP` (storage/compiled_tpl_v2).
- UI Builder (skeleton): схемы (storage/ui_builder) + admin endpoints.
- Headless API-first: `/api/v1/*` endpoints + token auth (system/api.php).
- Multi-site: host-based sites registry (system/sites.php) + site-level theme.
- Marketplace: stubs (status/themes/plugins) + API endpoint.
- Advanced permissions: Policy engine + system/permissions.php.
- LTS: политика поддержки.

## [1.9.0] - 2025-12-28
### Добавлено — UI / Themes / Marketplace (подготовка)
- Theme system: `templates/themes/*` + `public/assets/themes/*` + `Theme\ThemeManager`.
- UI components library: partial-компоненты (`components/*.tpl`) + `UI\Components`.
- Theme switching: CLI и admin endpoints (JSON).
- Marketplace API (подготовка): конфиг + client stub + admin статус.
- Official themes: `default` и `rarog-official` (Rarog-based).

## [1.8.0] - 2025-12-28
### Добавлено — Developer Experience
- CLI генераторы: make:module, make:content, make:template.
- Devtools: Debug панель (/?__debug=1), сбор метрик запросов/шаблонов/SQL.
- Логи: запросы, шаблоны, SQL (в storage/dev).
- Документация для разработчиков: docs/DEVTOOLS_RU.md.

## [1.6.0] - 2025-12-28
### Добавлено — Migration Toolkit (DLE → CajeerEngine)
- CLI-набор миграции: проверка совместимости, импорт БД, конвертация шаблонов, отчёт ошибок.
- Импорт основных сущностей DLE (best-effort): новости/посты, категории, статические страницы, пользователи.
- Конвертер шаблонов DLE → Cajeer `.tpl` + генерация предупреждений/логов.
- DLE Tag Adapter: режим совместимости для наиболее частых DLE-тегов в шаблонах.

## [1.7.0] - 2025-12-28
### Добавлено — Updater & Packages
- Полноценный Updater: backup/rollback, каналы stable/beta, поддержка локального/удалённого манифеста.
- Форматы пакетов:
  - `.cajeerpkg` — пакет (overlay файлов + хуки pre/post).
  - `.cajeerpatch` — патч (overlay + опциональные проверки/инвалидации кэша).
- CLI-команды: updater:check/apply/backup/restore.
- Поддержка обновления компонентов (в т.ч. public/assets/rarog) через пакеты.

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