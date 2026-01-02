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

## Official themes (Rarog-based)
- `default` — базовая
- `rarog-official` — официальная тема на базе Rarog (использует `/assets/rarog/*`).
