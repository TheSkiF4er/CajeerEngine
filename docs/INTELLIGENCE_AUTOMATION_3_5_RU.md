# v3.5 — Platform Intelligence & Automation (baseline)

## Intelligence
### Usage analytics
- `engine/Intelligence/Analytics.php`
- Таблица: `ce_usage_events`
- Автоматическое трекинг-событие `request` добавлено в Router (после успешного dispatch).

### Performance insights
- `engine/Intelligence/Performance.php`
- Таблицы: `ce_perf_requests`, `ce_perf_queries`
- В Router логируются медленные запросы (threshold: `system/intelligence.php`).

### Cost visibility (multi-tenant)
- `engine/Intelligence/Cost.php`
- Таблица: `ce_cost_ledger`
- В Router добавлен базовый charge за запрос (1 credit).

## Automation
- `engine/Automation/PolicyEngine.php`
- Таблицы: `ce_auto_policies`, `ce_auto_runs`
- В baseline политики исполняются как **decision log** (metrics adapters будут добавлены в 3.5.x).
- Endpoint: `POST/GET /api/automation/run`
- CLI: `automation:run`

## Predictive alerts (foundation)
- `engine/Automation/PredictiveAlerts.php`
- Таблица: `ce_alerts`

## AI Assist (optional)
- `engine/AIAssist/AIClient.php`
- API:
  - `/api/ai/suggest/content`
  - `/api/ai/suggest/layout`
  - `/api/ai/admin/copilot`
- В baseline провайдеры не подключены (safe default).

## API summaries
- `/api/intelligence/usage`
- `/api/intelligence/perf`
- `/api/intelligence/cost`
