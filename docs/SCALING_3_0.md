# Horizontal Scaling (foundation)

- Stateless web tier: вынос состояния во внешние сервисы (план на 3.x minors)
- DB queue (`ce_jobs`) как foundation; далее — брокеры
- API-first: контрактные версии + version locking
