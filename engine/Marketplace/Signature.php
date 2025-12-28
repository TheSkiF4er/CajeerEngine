<?php
namespace Marketplace;

class Signature
{
    public static function verifyEd25519(string $pubKeyB64, string $signatureB64, string $payload): bool
    {
        $pub = base64_decode($pubKeyB64, true);
        $sig = base64_decode($signatureB64, true);
        if ($pub === false || $sig === false) return false;

        if (function_exists('sodium_crypto_sign_verify_detached')) {
            return sodium_crypto_sign_verify_detached($sig, $payload, $pub);
        }
        return false; // security-first
    }
}
