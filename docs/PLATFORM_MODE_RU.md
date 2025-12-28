# v2.5 — SaaS & Platform Mode

## Platform mode (multi-tenant)
- Конфиг: `system/platform.php`
- Резолв tenant:
  - `host` (subdomain/custom domain via `ce_tenant_domains`)
  - `header` (`X-Tenant-Id`)
  - `query` (`tenant_id`)
- Резолв site:
  - `host` (domain mapping to site)
  - `header` (`X-Site-Id`)
- Контекст доступен через `Platform\Context::tenantId()` / `siteId()`.

## Site isolation
- Для репозиториев/запросов используйте `Platform\Isolation::whereTenantSite(...)`.
- По умолчанию Kernel проставляет tenant/site в preflight.

## Usage metrics / billing hooks
- Счетчик запросов: `Platform\Usage::inc('api_requests')` (включен в Kernel).
- Таблица: `ce_usage_metrics`
- Limits: `Platform\Limits` (soft check, можно включить в конфиге)
- Billing hooks: `Platform\Billing` (placeholder)

## Auto-updates
- Cron worker: `php system/cron/autoupdate_worker.php`
- CLI: `php cli/cajeer autoupdate:worker`
- Rollouts: `ce_rollouts`, `ce_rollout_targets` (staged/canary foundation)
- Health check endpoint: `GET /api/v1/platform/health`

## Admin
- `public/admin/platform.php`

## Установка схемы
```bash
php cli/cajeer platform:install-schema
```
