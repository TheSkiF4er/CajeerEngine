# v3.4 — Frontend Platform & No-Code (baseline)

## UI Builder Pro
- Collaborative editing (foundation):
  - Locks: `ce_builder_locks`
  - Patches log: `ce_builder_changes`
  - API: `/api/builder/lock`, `/api/builder/patch`
- Component marketplace (runtime registry): `ce_components`
- A/B testing (foundation): experiments + assignments, API `/api/ab/assign`

## Frontend Runtime
- ISR cache in DB: `ce_isr_cache` (`Frontend\ISRCache`)
- Runtime adapter: `Frontend\FrontendRuntime`
- Edge rendering (foundation stub): `Frontend\EdgeRenderer`
- CDN-native headers (optional): `system/frontend.php`

> В этом релизе origin renderer — hook-заглушка. В 3.4.x будет подключение к Template/DSL и UI Builder JSON pipeline.

## No-code / Low-code
- Workflows: `ce_workflows` + `NoCode\Workflows::run()` (foundation execution)
- Logic blocks registry: `ce_logic_blocks`
- Forms:
  - `ce_forms`
  - submissions: `ce_form_submissions`
  - API: `/api/nocode/form/submit`

## DB migration
- `system/sql/frontend_v3_4.sql`
