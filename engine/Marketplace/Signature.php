<?php
namespace Marketplace;
class Signature {
  public static function verify(string $publicKeyPem,string $signedPayload,string $signatureB64): bool {
    if(!$publicKeyPem||!$signedPayload||!$signatureB64) return false;
    $sig=base64_decode($signatureB64,true); if($sig===false) return false;
    if(!function_exists('openssl_verify')) return false;
    $pub=openssl_pkey_get_public($publicKeyPem); if(!$pub) return false;
    return openssl_verify($signedPayload,$sig,$pub,OPENSSL_ALGO_SHA256)===1;
  }
}
