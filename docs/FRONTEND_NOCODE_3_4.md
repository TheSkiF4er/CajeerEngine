# v3.4 — Frontend‑платформа и No‑Code (baseline)

## UI Builder Pro
- Совместное редактирование (foundation):
  - Locks: `ce_builder_locks`
  - Лог патчей: `ce_builder_changes`
  - API: `/api/builder/lock`, `/api/builder/patch`
- Marketplace компонентов (runtime registry): `ce_components`
- A/B‑тестирование (foundation): experiments + assignments, API `/api/ab/assign`

## Frontend Runtime
- ISR cache в БД: `ce_isr_cache` (`Frontend\ISRCache`)
- Runtime adapter: `Frontend\FrontendRuntime`
- Edge rendering (foundation stub): `Frontend\EdgeRenderer`
- CDN‑native headers (optional): `system/frontend.php`

> В этом релизе origin renderer — hook‑заглушка. В 3.4.x будет подключение к Template/DSL и UI Builder JSON pipeline.

## No‑code / Low‑code
- Workflows: `ce_workflows` + `NoCode\Workflows::run()` (foundation execution)
- Реестр logic blocks: `ce_logic_blocks`
- Forms:
  - `ce_forms`
  - submissions: `ce_form_submissions`
  - API: `/api/nocode/form/submit`

## DB migration
- `system/sql/frontend_v3_4.sql`
