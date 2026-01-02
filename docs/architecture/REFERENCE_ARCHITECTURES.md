# Референсные продакшен‑архитектуры (3.x LTS)

## Single‑tenant
- Nginx + PHP-FPM, MySQL, Redis (опционально), воркеры

## Multi‑tenant SaaS
- Stateless app‑ноды, enforced tenant isolation, очереди/кеш в Redis, отдельный пул воркеров, CDN/ISR

## Enterprise
- SSO, неизменяемые audit‑логи, IP allow/deny, DR‑пайплайн

## 4) Multi‑region + Edge (CDN‑native)
- Origin по регионам
- Read-only edge‑ноды ближе к пользователям
- Distributed cache (Redis) + event bus
- Canary + traffic shaping + region‑aware routing
