# Установка CajeerEngine v1.2

## 1) Подготовка
- PHP 8.2+
- MySQL 8+ / MariaDB 10.4+
- Права на запись: `storage/`, `uploads/`

## 2) Настройка DB
Отредактируйте `system/db.php`.

## 3) Установка схемы
Через CLI (рекомендуется):
```bash
php cli/cajeer db:install
php cli/cajeer seed:demo
```

Или вручную:
- выполните SQL из `system/schema.sql`
- затем (опционально) `system/seed_demo.sql`

## 4) Проверка
- Откройте `/news`
- Откройте `/news/view?slug=content-core-v12`
- Откройте `/page?slug=about`
- Откройте `/category?slug=updates&type=news`
