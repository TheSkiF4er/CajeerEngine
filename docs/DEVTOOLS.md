# Developer Experience (v1.8)

## CLI генераторы
- `make:module <Name>` — создаёт модуль с контроллером и шаблоном.
- `make:content <type>` — создаёт заготовку типа контента (schema.json).
- `make:template <file.tpl>` — создаёт .tpl в `templates/default`.

Примеры:
```bash
php cli/cajeer make:module Blog
php cli/cajeer make:content product
php cli/cajeer make:template blog/list.tpl
```

## Devtools / Debug панель
Включается через `system/dev.php`.

Открыть debug панель:
- Добавьте `?__debug=1` к любому URL, например: `/news?__debug=1`

Панель покажет:
- время выполнения
- данные запроса
- список рендеров шаблонов (имя, ms, vars)
- список SQL (если используете DB::query)

## Логи
Пишутся в `storage/dev` при включённом dev режиме:
- `requests.log`
- `templates.log`
- `sql.log`

## Инструментирование SQL
Если вы используете `Database\DB::query($sql, $params)` — будет тайминг и логирование SQL.
