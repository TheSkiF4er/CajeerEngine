# v2.7 — Marketplace Expansion & Economy

## Remote registries
- Конфиг: `system/marketplace.php` → `registries`
- Ожидаемый API реестра:
  - `GET {base}/api/v1/registry/index`
  - `GET {base}/api/v1/registry/search?q=...`
  - `GET {base}/api/v1/registry/package?key=...&version=...`

## Локальный индекс
- Таблицы: `system/sql/marketplace_v2_7.sql`
- Пакеты: `ce_marketplace_packages`
- Ratings: `ce_marketplace_ratings`

## SemVer constraints
- Поддержка: `^`, `~`, `>= <`, wildcard `*`

## Security
- Signature enforcement: `marketplace.security.require_signature`
- Sandbox preflight: конфликты файлов + зависимости

## Monetization hooks
- Поля: `is_paid`, `license`, `price`
- Entitlement: external provider (foundation)

## API
- `/api/v1/marketplace/sync`
- `/api/v1/marketplace/search?q=...`
- `/api/v1/marketplace/rate`
- `/api/v1/marketplace/preflight?package_id=...`

## CLI
- `php cli/cajeer marketplace:sync`
- `php cli/cajeer marketplace:search "rarog" theme`
- `php cli/cajeer marketplace:preflight 123`
