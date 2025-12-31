# v4.0 — AI-Native & Cloud-First Platform

## Vision
- AI-native workflows
- Declarative platform config (`system/platform.yaml`)
- Intent-based management (intent → reconciler)
- Control plane / data plane split

## Platform Config
- `GET /api/v4/platform/config` (YAML)

## Intent Engine
API:
- `POST /api/v4/intents` `{ tenant_id, name, kind, desired }`
- `GET /api/v4/intents?tenant_id=1&status=pending`
- `POST /api/v4/reconcile`

Kinds (foundation):
- `PolicyIntent`
- `FleetIntent`
- `SelfHealIntent`
- `EventIntent`

## Event Mesh
- `GET /api/v4/eventmesh/recent?topic=...`

## IaC Outputs
- `GET /api/v4/iac/docker-compose`
- `GET /api/v4/iac/kubernetes`

## Ecosystem v4
- `GET /api/v4/marketplace/ai`
- `GET /api/v4/marketplace/automation`
- `GET /api/v4/blueprints`

DB: `system/sql/platform_v4_0.sql`
