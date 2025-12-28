# Template Engine v1.1 (RU)

## Поддерживаемый синтаксис
- `{var}` — переменная из `$vars`
- `{config path.to.key}` — значение из `system/config.php`
- `{user.name}` — значение из контекста пользователя
- `{include file="header.tpl"}`
- `[if logged] ... [else] ... [/if]`
- `[group=1,2] ... [/group]`
- `[not-group=1] ... [/not-group]`
- `[available=main,news] ... [/available]`
- `{module:news limit="5"}` — модульный тег (через registry)

## Компиляция и кеш
`.tpl` компилируется в PHP и сохраняется в `storage/compiled_tpl/`.
Перекомпиляция выполняется при изменении исходного `.tpl`.
