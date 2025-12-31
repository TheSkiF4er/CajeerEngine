# v3.7 — AI-Assisted Platform (Production baseline)

## Providers (pluggable)
- `mock` (default, safe)
- `http` (generic JSON provider): включите в `system/ai.php`

## Governance
- Per-tenant opt-in/opt-out: `ce_ai_policies`
- Endpoints:
  - `GET /api/ai/policy`
  - `POST /api/ai/optin` `{ "opt_in": true }`

## Prompt transparency
- Requests log: `ce_ai_requests`
- Endpoints:
  - `GET /api/ai/requests?purpose=content&limit=50`
  - `GET /api/ai/request?id=123`

## Privacy & boundaries
- Redaction: `system/ai.php` (regex rules)
- Context flags:
  - `flags.secrets`, `flags.logs`, `flags.pii`
Если не разрешено политикой tenant — запрос будет blocked.

## Recommendations & Automation
- Recommendations table: `ce_ai_recommendations`
- Generate: `POST /api/ai/recommend/run`
- List: `GET /api/ai/recommendations`
- Alerts run (baseline): `POST /api/ai/alerts/run`
