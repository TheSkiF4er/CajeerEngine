# Content API v1 (Headless) — CajeerEngine 2.1

## Авторизация
Bearer token: `Authorization: Bearer dev-token` (см. `system/api.php`)

## Scopes
- `content.read`
- `content.write`
- `admin.read`
- `admin.write`

Policy-aware: scopes + RBAC (caps) через `system/permissions.php`.

## Endpoints
- `GET /api/v1/ping`

### Content
- `GET /api/v1/content`
- `GET /api/v1/content/get?id=1`
- `POST /api/v1/content/create`
- `POST /api/v1/content/update?id=1`
- `POST /api/v1/content/delete?id=1`
- `POST /api/v1/content/publish?id=1`

Фильтры: `type`, `status`, `category_id`, `slug`, `page`, `per_page`, `sort`, `order`  
JSON fields filter: `field=<name>&value=<value>`

### Categories
- `GET /api/v1/categories`

### Custom types
- `GET /api/v1/types`

### Admin API v1
- `GET /api/v1/admin/me`
- `GET /api/v1/admin/stats`

## Версионирование
Каждый create/update создаёт запись в `ce_content_versions`. Статусы: `draft`, `published`.
