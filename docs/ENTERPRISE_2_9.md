# v2.9 — Enterprise SaaS и Compliance (foundation)

## Безопасность и соответствие требованиям
### SSO (OIDC/SAML)
- Флаги: `system/sso.php`
- Хранилище провайдеров: `ce_sso_providers`
- Endpoint: `POST /api/v1/sso/provider/create`

> Протокольные флоу (OIDC redirect, SAML ACS/SLO) планируются на v3.x.

### MFA (foundation)
- Таблица: `ce_mfa_factors`
- Endpoint: `GET /api/v1/mfa/list?user_id=...`

### Неизменяемые аудит‑следы
- Таблица: `ce_audit_log_immutable`
- Конфиг: `system/ops.php`
- Hook logger: `Ops\Hooks::sla()` / `Ops\Hooks::incident()`

## Legal / Ops
### Инструменты GDPR
- Очередь: `POST /api/v1/gdpr/queue`
- Выполнение: `POST /api/v1/gdpr/run`
- CLI: `php cli/cajeer gdpr:queue ...` / `gdpr:run ...`

### Отчёты доступа
- `GET /api/v1/access-reports/list?tenant_id=...`

### Инциденты
- `POST /api/v1/incident/create`
