<?php
namespace Marketplace;
class Monetization {
  public static function checkEntitlement(array $package,int $tenantId): array {
    if(empty($package['is_paid'])) return ['ok'=>true];
    return ['ok'=>false,'error'=>'entitlement_required','hint'=>'Integrate external billing provider'];
  }
  public static function licenseMeta(array $package): array {
    $lic=$package['license']??null;
    if(is_string($lic)){ $x=json_decode($lic,true); if(is_array($x)) $lic=$x; }
    return is_array($lic)?$lic:[];
  }
}
