# Content API v1 (Headless) — CajeerEngine 2.1

## Авторизация
Bearer‑токен: `Authorization: Bearer dev-token` (см. `system/api.php`)

## Области доступа (scopes)
- `content.read`
- `content.write`
- `admin.read`
- `admin.write`

Policy-aware: scopes + RBAC (capabilities) через `system/permissions.php`.

## Эндпоинты
- `GET /api/v1/ping`

...
JSON fields filter: `field=<name>&value=<value>`

### Категории
- `GET /api/v1/categories`

### Пользовательские типы
- `GET /api/v1/types`

### Admin API v1
- `GET /api/v1/admin/me`
- `GET /api/v1/admin/stats`

## Версионирование
Каждый create/update создаёт запись в `ce_content_versions`. Статусы: `draft`, `published`.
