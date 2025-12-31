# Deprecation Policy (3.x LTS)

## Правила
1. Любое изменение поведения публичного API требует периода депрекации.
2. Депрекация оформляется через `Core\Deprecation::warn($id, $message, $meta)`.
3. Минимальное окно депрецирования: **3 minor-релиза** (см. `system/api_lock.php`).
4. В LTS допустимы только bugfix/security/perf и совместимые расширения.

## CI
- `CE_DEPRECATIONS_AS_ERRORS=1` включает строгий режим (рекомендуется для CI).
