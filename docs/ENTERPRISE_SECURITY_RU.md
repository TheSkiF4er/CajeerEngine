# v2.4 — Enterprise & Security

## Security
### CSRF
- Конфиг: `system/security.php`
- Токен хранится в cookie `ce_csrf` и доступен через API `GET /api/v1/security/csrf`
- Для state-changing запросов без Authorization требуется CSRF (header `X-CSRF-Token` или поле формы `_csrf`)

### Rate limiting
- Включено по умолчанию
- Ключ: `ip|path`
- Таблица: `ce_rate_limit`

### IP allow/deny
- `system/security.php` → `ip_filter`
- deny имеет приоритет, allow ограничивает доступ

### Audit logs
- Таблица: `ce_audit_logs`
- API: `GET /api/v1/audit/list?limit=...`

## Enterprise RBAC
- Workspace isolation (header `X-Workspace-Id` или `workspace_id` query)
- Fine-grained permissions по ключам: `content.workflow`, `content.schedule`, ...
- Per-content grants через `ce_rbac_content_grants`

## Workflow
- Состояния: draft → review → published
- API:
  - `GET /api/v1/workflow/transition?content_id=ID&to=draft|review|published`
  - `GET /api/v1/workflow/schedule?content_id=ID&at=YYYY-mm-dd HH:ii:ss`
- Cron:
  - `php system/cron/publish_scheduled.php`
