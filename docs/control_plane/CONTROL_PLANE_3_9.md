# v3.9 — Platform Control Plane

## Идея
Контрольная плоскость управления платформой: fleet, глобальные политики/оверрайды, rollout‑автоматизация, self‑healing и cross‑tenant observability.

## Безопасность
`system/control_plane.php`:
- `CE_CP_TOKEN` + header `X-Control-Plane-Token`
Если token пустой — режим разработки.

## Fleet Management
API:
- `GET /api/cp/fleet`
- `POST /api/cp/fleet` (register site)
DB: `ce_fleet_sites`

## Capacity forecasting (foundation)
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
