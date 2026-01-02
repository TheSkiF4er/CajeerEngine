# Plugins & Events (v1.4)

## Структура плагина
```
plugins/<id>/
  plugin.json
  plugin.php
  install.sql        (опционально)
  uninstall.sql      (опционально)
```

## Lifecycle
- install: `php cli/cajeer plugin:install <id>` (исполнит install.sql и `Plugin::install()`)
- enable:  `php cli/cajeer plugin:enable <id>` (добавит в `system/plugins.php`)
- disable: `php cli/cajeer plugin:disable <id>`
- uninstall:`php cli/cajeer plugin:uninstall <id>` (`Plugin::uninstall()` + uninstall.sql)

## События (Events) и хуки (Filters)
EventBus:
- `emit(name, payload)` — событие
- `filter(name, value, payload)` — цепочка фильтров (хуки)

Примеры событий:
- `kernel.booting`, `kernel.routing`, `kernel.done`
- `routes.filter`, `routes.dispatching`
- `admin.kernel.booting`, `admin.kernel.done`
- `admin.dashboard.widgets`
- `content.admin.created/updated/deleted/bulk`

## Override без правки ядра
1) Подписки на события (расширение поведения).
2) Регистрация `{{module:*}}` через `Template\Extensions::registerModule(...)`.
3) Модификация маршрутов:
   - frontend: `routes.filter` (value = array routes)
   - admin: `routes.admin.path` (filter path) или `routes.dispatching` (set handled)
4) Подмена сервисов в контейнере: `Container::bind/singleton/instance`.

## Dependency graph + constraints
- В `requires` можно указать зависимости:
  - `cajeer: ">=1.4.0"`
  - `plugin:foo: "^1.2.0"`
- При enable/boot выполняется проверка и topo-order загрузки.
