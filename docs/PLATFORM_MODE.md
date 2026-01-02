# v2.5 — SaaS и Platform Mode

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
