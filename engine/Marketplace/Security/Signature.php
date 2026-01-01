<?php
namespace Marketplace\Security;

use Observability\Logger;

class Signature
{
    /**
     * Verify detached signature for content using publisher public key (base64).
     * Requires ext-sodium for Ed25519 (recommended). If sodium missing -> fail closed.
     */
    public static function verifyEd25519(string $content, string $signatureBase64, string $publicKeyBase64): bool
    {
        if (!function_exists('sodium_crypto_sign_verify_detached')) {
            Logger::warn('marketplace.signature.no_sodium', []);
            return false; // mandatory signatures: fail closed
        }
        $sig = base64_decode($signatureBase64, true);
        $pk = base64_decode($publicKeyBase64, true);
        if ($sig === false || $pk === false) return false;
        return sodium_crypto_sign_verify_detached($sig, $content, $pk);
    }
}
