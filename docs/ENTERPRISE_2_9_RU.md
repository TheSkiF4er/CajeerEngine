# v2.9 — Enterprise SaaS & Compliance (foundation)

## Security & Compliance
### SSO (OIDC/SAML)
- Флаги: `system/sso.php`
- Хранилище провайдеров: `ce_sso_providers`
- Endpoint: `POST /api/v1/sso/provider/create`

> Протокольные флоу (OIDC redirect, SAML ACS/SLO) планируются на v3.x.

### MFA (foundation)
- Таблица: `ce_mfa_factors`
- Endpoint: `GET /api/v1/mfa/list?user_id=...`

### Immutable audit trails
- Таблица: `ce_audit_log_immutable`
- Hash-chain verify:
  - API: `GET /api/v1/audit/verify`
  - CLI: `php cli/cajeer audit:verify`

### Data residency
- Конфиг: `system/residency.php`
- Поля tenant: `region`, `residency_region`

## SaaS
### Tenant lifecycle
- Таблица: `ce_tenants` (status: active/suspended/archived/deleted)
- API: `POST /api/v1/tenant/status`
- CLI: `php cli/cajeer tenant:status <id> suspended`

### Quotas enforcement
- Таблица: `ce_tenant_quotas`
- API: `POST /api/v1/tenant/quotas/set`
- Hook: `SaaS\TenantManager::checkUsage($tenantId, $usageMap)`

### SLA / Incident hooks
- Конфиг: `system/ops.php`
- Hook logger: `Ops\Hooks::sla()` / `Ops\Hooks::incident()`

## Legal / Ops
### GDPR tooling
- Очередь: `POST /api/v1/gdpr/queue`
- Выполнение: `POST /api/v1/gdpr/run`
- CLI: `php cli/cajeer gdpr:queue ...` / `gdpr:run ...`

### Access reports
- `GET /api/v1/access-reports/list?tenant_id=...`

### Incidents
- `POST /api/v1/incident/create`
