# CajeerEngine 3.x LTS Policy

**Статус:** Active (с 2025-12-31)  
**Ветка:** 3.x (начиная с 3.6.0)

## Окно поддержки
- **Security fixes:** 24 месяца
- **Bugfixes:** 12 месяцев

## API freeze
- Публичные API закреплены в `system/api_lock.php`.
- Breaking changes запрещены в 3.x.
- Новая функциональность — через плагины/расширение контрактов без ломки.

## Deprecation policy
- Окно депрекации: минимум **3 minor-релиза**.
- Логирование через `Core\Deprecation`.

## CVE и патчи
- Патчи публикуются как 3.x.y с release notes и рекомендациями.
