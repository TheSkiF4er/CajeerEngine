# UI / Themes / Marketplace (подготовка) — v1.9

## Theme system
- Шаблоны: `templates/themes/<theme>/`
- Компоненты: `templates/themes/<theme>/components/`
- Ассеты: `public/assets/themes/<theme>/`
- Активная тема: `system/themes.php`

## Theme switching
### CLI
```bash
php cli/cajeer theme:list
php cli/cajeer theme:switch rarog-official
```

### Admin API (заготовка)
- `GET /admin/themes`
- `POST /admin/themes/switch?theme=rarog-official`

## UI components library
Простая библиотека partial-компонентов:
- `engine/UI/Components.php`
- компоненты лежат в `templates/themes/<theme>/components/`

## Official themes (Rarog-based)
- `default` — базовая тема
- `rarog-official` — официальная тема на базе Rarog (использует `/assets/rarog/*`).
