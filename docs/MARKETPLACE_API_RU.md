# Marketplace API (подготовка) — v1.9

В v1.9 реализованы заготовки интеграции marketplace для тем и плагинов.

## Конфигурация
`system/marketplace.php`:
- `enabled`
- `base_url`
- `token` (future)
- `timeout`

## CLI
```bash
php cli/cajeer marketplace:status
```

## Admin API (заготовка)
- `GET /admin/marketplace/status`
