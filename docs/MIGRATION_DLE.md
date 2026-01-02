# Migration Toolkit: DLE → CajeerEngine (v1.6)

## Настройка
1) Укажите доступ к базе DLE в `system/dle.php` (host/db/user/pass).
2) Если у DLE нестандартный префикс таблиц — поменяйте `prefix`.

## Проверка совместимости
```bash
php cli/cajeer migrate:dle:check /path/to/dle/templates
```
Отчёт: `storage/migrations/report_<id>.json`.

## Импорт БД
```bash
php cli/cajeer migrate:dle:db
```

Импорт best-effort:
- категории
- пользователи (без паролей: назначаются временные)
- новости/посты → `content(type=news)`
- статические страницы → `content(type=page)`

## Конвертация шаблонов
```bash
php cli/cajeer migrate:dle:templates /path/to/dle/templates templates/dle-converted
```

## Полный сценарий
```bash
php cli/cajeer migrate:dle:run /path/to/dle/templates templates/dle-converted
```

## Примечания
- Пароли DLE не совместимы: требуется сброс паролей после миграции.
- Специфические DLE-теги остаются как есть и попадают в warnings отчёта.
- Для DLE-совместимости включён `Template\DleTagAdapter` (best-effort).
