# 3.x LTS стратегия (foundation)

## Принципы
- Stable public kernel API: `engine/Core/PublicKernelAPI.php` (стабильно в пределах 3.x)
- SemVer: 3.x.y (minor без ломания stable API, patch — фиксы)

## Правила обратной совместимости
- Запрещены breaking changes для KernelContract/ServiceProviderContract/PluginContract внутри 3.x
- Публичные API могут эволюционировать только через versioning (v1/v2) и contract locking
