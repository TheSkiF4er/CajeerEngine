# v3.7 — Платформа с AI‑ассистированием (production baseline)

## Провайдеры (плагинные)
- `mock` (по умолчанию, безопасный)
- `http` (универсальный JSON‑провайдер): включите в `system/ai.php`

## Governance
- Per-tenant opt-in/opt-out: `ce_ai_policies`
- Эндпоинты:
  - `GET /api/ai/policy`
  - `POST /api/ai/optin` `{ "opt_in": true }`

## Прозрачность промптов
- Лог запросов: `ce_ai_requests`
- Эндпоинты:
  - `GET /api/ai/requests?purpose=content&limit=50`
  - `GET /api/ai/request?id=123`

## Приватность и границы
- Редакция данных: `system/ai.php` (правила regex)
- Флаги контекста:
  - `flags.secrets`, `flags.logs`, `flags.pii`
Если tenant‑политикой не разрешено — запрос будет заблокирован.

## Рекомендации и автоматизация
- Таблица рекомендаций: `ce_ai_recommendations`
- Генерация: `POST /api/ai/recommend/run`
- Список: `GET /api/ai/recommendations`
- Запуск алертов (baseline): `POST /api/ai/alerts/run`
