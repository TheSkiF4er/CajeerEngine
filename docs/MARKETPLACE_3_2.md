# v3.2 — Marketplace 2.0 (production)

## Концепция
Marketplace состоит из:
- Registries (official/custom): `system/marketplace.php`
- Package Manager: `engine/Marketplace/PackageManager.php`
- Registry client (HTTP + file://): `engine/Marketplace/V2/RegistryClient.php`
- Обязательные подписи (Ed25519 через ext-sodium): `engine/Marketplace/Security/Signature.php`

## Remote registry API (v2, contract)
- GET `/v2/search?q=...&type=plugin|theme|ui-block|content-type`
- GET `/v2/package/<id>/manifest`
- GET `/v2/package/<id>/download`  (returns `.cajeerpkg` bytes)

## Формат пакета (.cajeerpkg)
ZIP‑архив:
- `manifest.json`
- `payload/`  (файлы для установки)
- optional `LICENSE.txt`
- optional `scan.json` (security scan report, foundation)

## Жизненный цикл
- install/update: скачивание + верификация подписи + backup + установка + запись в `ce_installed_packages`
- uninstall: backup snapshot + удаление
- rollback: восстановление из snapshot

## Hooks монетизации (foundation)
- webhook events (optional): `billing.enabled=true` + `billing.webhook_url`
  - event: `package.installed`
