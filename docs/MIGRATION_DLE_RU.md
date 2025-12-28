# Migration Toolkit: DLE → CajeerEngine (v1.6)

Цель — максимально безболезненный переход с DLE на CajeerEngine.

## 1) Настройка доступа к базе DLE
Отредактируйте `system/dle.php`:
- `db.host / db.database / db.username / db.password`
- `prefix` (если у DLE нестандартный)

## 2) Проверка совместимости
```bash
php cli/cajeer migrate:dle:check /path/to/dle/templates
```
Отчёт сохранится в `storage/migrations/report_<id>.json`.

## 3) Импорт БД
```bash
php cli/cajeer migrate:dle:db
```

Импортируется best-effort:
- категории
- пользователи (без паролей, ставятся временные)
- новости/посты → `content(type=news)`
- статические страницы → `content(type=page)`

## 4) Конвертация шаблонов
```bash
php cli/cajeer migrate:dle:templates /path/to/dle/templates templates/dle-converted
```

## 5) Полный сценарий
```bash
php cli/cajeer migrate:dle:run /path/to/dle/templates templates/dle-converted
```

## Важно
- DLE пароли не совместимы: после миграции требуется сброс.
- Специфические DLE теги остаются как есть и подсвечиваются в отчёте.
- Для базовой совместимости используется `Template\DleTagAdapter` (best-effort).
