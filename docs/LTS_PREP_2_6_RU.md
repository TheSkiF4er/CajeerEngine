# v2.6 — Stability, Observability & LTS Prep

## Core
- Enforced tenant/site isolation: `system/platform.php` → `enforced` + `require_tenant`.
- API version locking: Router проверяет `X-API-Version` / `?api_version=` / `/api/v{n}/...`.

## Observability
- Structured logging: `storage/logs/app.jsonl` (JSONL)
- Request ID tracing: header `X-Request-Id` (propagate to response)
- DB query tracing: slow queries + debug logs (настраивается в `system/observability.php`)
- Prometheus metrics: `GET /metrics`
- Health probes:
  - `GET /api/v1/health/live`
  - `GET /api/v1/health/ready`

## Ops
- Environment profiles: `system/env.php` + `system/config.dev.php|staging.php|prod.php`
- Safe config validation (fast-fail)
- Backup/restore API:
  - `GET /api/v1/backup/export` (requires scope `admin.read`)
  - `POST /api/v1/backup/import` (requires `admin.write` + header `X-Restore-Confirm: YES`)

Дата: 2025-12-29
