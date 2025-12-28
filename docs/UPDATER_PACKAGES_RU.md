# Updater & Packages (v1.7)

## CLI
```bash
php cli/cajeer updater:check
php cli/cajeer updater:backup manual
php cli/cajeer updater:apply /path/to/update.cajeerpkg
php cli/cajeer updater:restore /path/to/backup_*.zip
```

## Каналы stable / beta
Настройка в `system/updater.php`:
- `channel`: stable|beta
- `manifest`: локальный файл или URL (JSON)

## Форматы пакетов
ZIP с расширением `.cajeerpkg` или `.cajeerpatch`:

```
manifest.json
checks.json            (опционально)
files/...
scripts/pre.php        (опционально)
scripts/post.php       (опционально)
```

`checks.json` поддерживает:
- `require_php`
- `require_files[]`

Пакеты накладывают `files/` поверх корня проекта (overlay) + выполняют хуки.
Перед применением Updater делает backup и после применений чистит кэши.
