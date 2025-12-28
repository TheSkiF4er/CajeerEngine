# UI Builder (v2.2) — Visual Editor

## Возможности
- Drag & Drop editor
- Grid / sections / blocks
- JSON → render pipeline
- Preview mode

## Blocks
- Text
- Image
- Gallery
- Form
- Custom HTML
- Module blocks

## Установка
```bash
php cli/cajeer ui:install
```

## Admin UI
Открыть: `public/admin/ui_builder.php`  
Токен: `Bearer dev-token` (см. `system/api.php`)

## API
- `GET /api/v1/ui/blocks`
- `GET /api/v1/ui/get?content_id=ID`
- `POST /api/v1/ui/save?content_id=ID`
- `POST /api/v1/ui/preview`

## Sync с .tpl (без поломки)
В шаблоне используйте: `{ui_builder}`

Чтобы `{ui_builder}` автоматически подставлялся — передайте `content_id` в `Template->render()`:
`Template->render('page.tpl', ['content_id'=>10, ...])`

Если layout отсутствует — `{ui_builder}` пустой (fallback в код/DSL/шаблон).
