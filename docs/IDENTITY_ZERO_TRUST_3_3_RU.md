# v3.3 — Enterprise Identity & Zero Trust

## Identity (OIDC / SAML)
- Конфиг: `system/identity.php`
- OIDC: `engine/Identity/OIDCProvider.php` (startAuth/callback)
- SAML: `engine/Identity/SAMLProvider.php` (startAuth/ACS)

> Важно: криптографическая валидация токенов/JWKS и XML-signature для SAML помечены как **foundation stubs** и харднятся в 3.3.x minors.

## MFA
- TOTP (RFC 6238) без сторонних библиотек: `engine/MFA/TOTP.php`
- WebAuthn: foundation placeholder (требует полноценную библиотеку)

API:
- `GET /api/mfa/totp/enroll` (requires `X-CE-USER-ID`)
- `POST /api/mfa/totp/verify` (`code`)

## Zero Trust
- Per-request auth context: `engine/Security/AuthContext.php`
- Continuous authorization: policy evaluated every request
- Device posture (foundation): `system/zero_trust.php`
- Immutable access logs: `ce_access_logs` (hash-chained) + `Security\AccessLog::verifyChain()`

## Compliance
- SOC2 foundation report generator: `Security\Compliance::generateSOC2()`
- Endpoint: `GET /api/compliance/soc2?from=...&to=...`
