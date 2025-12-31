# Reference Production Architectures (3.x LTS)

## Single-tenant
- Nginx + PHP-FPM, MySQL, Redis (optional), Workers

## Multi-tenant SaaS
- Stateless app nodes, tenant isolation enforced, Redis queues/cache, separate worker pool, CDN/ISR

## Enterprise
- SSO, immutable audit logs, IP allow/deny, DR pipeline


## 4) Multi-region + Edge (CDN-native)
- Origin per region
- Edge read-only nodes near users
- Distributed cache (Redis) + event bus
- Canary + traffic shaping + region-aware routing
