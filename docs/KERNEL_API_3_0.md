# Публичный Kernel API 3.0 (stable)

Файл: `engine/Core/PublicKernelAPI.php`

## Plugin‑first
- `/plugins/<slug>/plugin.json`
- provider: класс `ServiceProviderContract`
- autoload: php‑файл, подключаемый при enable

## Event‑driven
- `events()->on()` / `events()->emit()`
