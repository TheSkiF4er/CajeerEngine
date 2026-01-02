# v2.6 — Стабильность, наблюдаемость и подготовка к LTS

## Core
- Enforced tenant/site isolation: `system/platform.php` → `enforced` + `require_tenant`.
- Фиксация версии API: Router проверяет `X-API-Version` / `?api_version=` / `/api/v{n}/...`.

## Observability
- Структурное логирование: `storage/logs/app.jsonl` (JSONL)
- Трассировка Request ID: header `X-Request-Id` (прокидывается в response)
- Трассировка запросов БД: slow queries + debug logs (настраивается в `system/observability.php`)
- Метрики Prometheus: `GET /metrics`
- Health probes:
  - `GET /api/v1/health/live`
  - `GET /api/v1/health/ready`

## Ops
- Профили окружений: `system/env.php` + `system/config.dev.php|staging.php|prod.php`
- Безопасная валидация конфигов (fast-fail)
- Backup/restore API:
  - `GET /api/v1/backup/export` (requires scope `admin.read`)
  - `POST /api/v1/backup/import` (requires `admin.write` + header `X-Restore-Confirm: YES`)

Дата: 2025-12-29
