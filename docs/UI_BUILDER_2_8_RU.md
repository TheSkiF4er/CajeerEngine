# v2.8 — Advanced UI Builder & Frontend Platform

## UI Builder
- Nested layouts: `layout/section/row/col` + `children`
- Patterns: `type=pattern`, `props.key`, хранение `ce_ui_patterns`
- Block permissions: поле `perm` (scope)
- Versioning/rollback: `ce_ui_layout_versions` + `active_version`

## Frontend platform
- Theme SDK: `themes/<name>/theme.json` + `Themes\ThemeSDK`
- Asset pipeline: build hooks фиксируются (по умолчанию не исполняются в skeleton)

## Headless preview
- `GET /api/v1/ui/preview?page_key=...` → `{html}`

## DX
- Diff: `GET /api/v1/ui/diff?page_key=...`
- Export UI→DSL: `GET /api/v1/ui/export_dsl?page_key=...`
