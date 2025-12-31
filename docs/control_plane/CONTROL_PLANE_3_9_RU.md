# v3.9 — Platform Control Plane

## Идея
Контрольная плоскость управления платформой: fleet, глобальные политики, rollout-автоматизация, self-healing, cross-tenant observability.

## Безопасность
`system/control_plane.php`:
- `CE_CP_TOKEN` + header `X-Control-Plane-Token`
Если token пустой — режим разработки.

## Fleet Management
API:
- `GET /api/cp/fleet`
- `POST /api/cp/fleet` (register site)
DB: `ce_fleet_sites`

## Global Policies & Overrides
DB: `ce_platform_policies`
Resolution order:
1) defaults из `system/control_plane.php`
2) DB scope=global
3) DB scope=tenant
4) DB scope=site

API:
- `GET /api/cp/policies/get?scope=tenant&tenant_id=1&key=policies`
- `POST /api/cp/policies/set` JSON `{ scope, tenant_id, site_id, key, value }`

## Observability++ (Cross-tenant)
- `GET /api/cp/insights/tenants` (perf + errors)
- Health scoring: `GET /api/cp/health/compute?tenant_id=1`
DB: `ce_platform_health`

## Capacity Forecasting (foundation)
- `GET /api/cp/capacity/forecast?tenant_id=1`
DB: `ce_capacity_forecast`

## Policy-driven rollouts (foundation)
- `GET /api/cp/rollouts/plan?tenant_id=1&target_version=3.9.0`
- `POST /api/cp/rollouts/create`
- `GET /api/cp/rollouts/list`
- `POST|GET /api/cp/rollouts/step?id=123&tenant_id=1`
DB: `ce_rollouts`

## Self-healing workflows (foundation)
- `POST /api/cp/heal/enqueue` JSON `{ tenant_id, kind }`
- `POST /api/cp/heal/run`
DB: `ce_self_heal_actions`
