# v3.8 — Distributed & Edge Platform

## Цель
Подготовить платформу к multi‑region и CDN‑native архитектурам.

## Конфигурация
`system/edge.php`:
- role: origin|edge_readonly
- distributed_cache: redis|db(fallback)
- event_bus: redis|db(fallback)
- edge_rendering: HTML cache на edge
- canary + traffic shaping (foundation)

## Read-only edge nodes
На edge_readonly:
- POST/PUT/PATCH/DELETE блокируются `Edge\EdgeGuard`.

## Region-aware routing (foundation)
API:
- `GET /api/edge/route?path=/`
- `GET /api/edge/canary`
- `GET /api/edge/config`

## Data locality & replication (foundation)
- data locality policies: `system/edge.php` -> tenant_region_map
- replication strategies: none|async|dualwrite (foundation)
- conflict resolution: last_write_wins (foundation)

SQL:
- `system/sql/edge_v3_8.sql` (regions, routing logs, replication journal)
