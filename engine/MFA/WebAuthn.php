<?php
namespace MFA;

/**
 * WebAuthn foundation placeholder.
 * Real attestation/assertion verification requires a full WebAuthn library and proper origin/rpId validation.
 */
class WebAuthn
{
    public static function startRegistration(string $userHandle): array
    {
        return ['ok'=>false,'error'=>'not_implemented'];
    }

    public static function finishRegistration(array $payload): array
    {
        return ['ok'=>false,'error'=>'not_implemented'];
    }

    public static function verifyAssertion(array $payload): array
    {
        return ['ok'=>false,'error'=>'not_implemented'];
    }
}
