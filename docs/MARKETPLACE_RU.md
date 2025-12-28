# Marketplace (v2.3) — Production foundation

## Цель
Запуск реальной экосистемы: установка из админки, проверка версий и зависимостей, подписи и trusted publishers.

## Типы пакетов
- Plugins
- Themes
- UI Blocks
- Content Types

## Формат пакета (.cajeerpkg)
ZIP-архив с:
- `marketplace.json` (manifest)
- `payload/...` (файлы пакета)

### Структура payload
- plugin: `payload/plugins/<name>/...` → `/plugins/<name>/...`
- theme: `payload/themes/<name>/...` → `/themes/<name>/...`
- ui_block: `payload/ui_blocks/<name>/...` → `/ui_blocks/<name>/...`
- content_type: `payload/content_types/<name>/...` → `/content_types/<name>/...`

### Manifest пример
```json
{
  "type": "plugin",
  "name": "hello",
  "version": "1.0.0",
  "title": "Hello Plugin",
  "publisher": { "id": "cajeer-official", "title": "Cajeer Official" },
  "requires": { "engine": ">=2.3.0" },
  "dependencies": { "plugin/some-lib": "^1.2.0" },
  "signature": { "ed25519": "BASE64_SIGNATURE" }
}
```

Подпись считается по canonical JSON (manifest без поля `signature`).

## Подписи
- `system/marketplace.php` → `require_signature`
- Проверка ed25519 требует `ext-sodium` (рекомендуется)

## Установка схемы БД
```bash
php cli/cajeer marketplace:install-schema
```

## Админка
- `public/admin/marketplace.php`

## API (admin)
- `GET /api/v1/marketplace/index`
- `GET /api/v1/marketplace/installed`
- `POST /api/v1/marketplace/upload-install` (multipart file `package`)
- `POST /api/v1/marketplace/trust-publisher` (publisher_id, pubkey_ed25519, title)

## CLI
- `php cli/cajeer marketplace:install <file.cajeerpkg>`
- `php cli/cajeer marketplace:trust <publisher_id> <pubkey_base64>`
