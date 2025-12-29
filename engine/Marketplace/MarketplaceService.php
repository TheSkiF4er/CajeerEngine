<?php
namespace Marketplace;
use Database\DB;
class MarketplaceService {
  public static function ensureSchema(): void {
    $pdo=DB::pdo(); if(!$pdo) return;
    $pdo->exec(file_get_contents(ROOT_PATH.'/system/sql/marketplace_v2_7.sql'));
  }
  public static function syncRegistries(array $cfg): array {
    self::ensureSchema();
    $pdo=DB::pdo(); if(!$pdo) return ['ok'=>false,'error'=>'db_required'];
    $regs=(array)($cfg['registries']??[]);
    $synced=[];
    foreach($regs as $code=>$r){
      if(empty($r['enabled'])) continue;
      $code=(string)$code; $name=(string)($r['name']??$code); $base=(string)($r['base_url']??'');
      if(!$base) continue;
      $pdo->prepare("INSERT INTO ce_marketplace_registries(code,name,base_url,verification_level,enabled,created_at,updated_at)
        VALUES(:c,:n,:b,:v,1,NOW(),NOW())
        ON DUPLICATE KEY UPDATE name=:n2, base_url=:b2, verification_level=:v2, enabled=1, updated_at=NOW()")
        ->execute([':c'=>$code,':n'=>$name,':b'=>$base,':v'=>(string)($r['verification_level']??'community'),
                   ':n2'=>$name,':b2'=>$base,':v2'=>(string)($r['verification_level']??'community')]);
      $rid=(int)$pdo->query("SELECT id FROM ce_marketplace_registries WHERE code=".$pdo->quote($code)." LIMIT 1")->fetchColumn();
      $idx=RegistryClient::index($base);
      if(empty($idx['packages'])||!is_array($idx['packages'])) continue;
      foreach($idx['packages'] as $p){ if(is_array($p)) self::upsertPackage($rid,$p); }
      $synced[]=$code;
    }
    return ['ok'=>true,'synced'=>$synced];
  }
  public static function upsertPackage(int $registryId,array $p): void {
    $pdo=DB::pdo(); if(!$pdo) return;
    $key=(string)($p['key']??$p['package_key']??'');
    $name=(string)($p['name']??$key);
    $publisher=(string)($p['publisher']??$p['publisher_key']??'community');
    $type=(string)($p['type']??'plugin');
    $version=(string)($p['version']??'0.0.0');
    $desc=(string)($p['description']??'');
    $cats=json_encode($p['categories']??[], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    $deps=json_encode($p['dependencies']??[], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    $lic=json_encode($p['license']??[], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    $paid=!empty($p['is_paid'])?1:0;
    $price=json_encode($p['price']??null, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    $sig=(string)($p['signature']??'');
    $manifest=json_encode($p, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    $pdo->prepare("INSERT INTO ce_marketplace_packages
      (registry_id,package_key,name,publisher_key,type,version,description,categories,dependencies,license,is_paid,price,signature,manifest_json,created_at,updated_at)
      VALUES(:r,:k,:n,:p,:t,:v,:d,:c,:deps,:lic,:paid,:price,:sig,:mj,NOW(),NOW())
      ON DUPLICATE KEY UPDATE name=:n2, description=:d2, categories=:c2, dependencies=:deps2, license=:lic2, is_paid=:paid2, price=:price2, signature=:sig2, manifest_json=:mj2, updated_at=NOW()")
      ->execute([':r'=>$registryId,':k'=>$key,':n'=>$name,':p'=>$publisher,':t'=>$type,':v'=>$version,':d'=>$desc,':c'=>$cats,
                ':deps'=>$deps,':lic'=>$lic,':paid'=>$paid,':price'=>$price,':sig'=>$sig,':mj'=>$manifest,
                ':n2'=>$name,':d2'=>$desc,':c2'=>$cats,':deps2'=>$deps,':lic2'=>$lic,':paid2'=>$paid,':price2'=>$price,':sig2'=>$sig,':mj2'=>$manifest]);
  }
  public static function searchLocal(string $q, ?string $type=null): array {
    $pdo=DB::pdo(); if(!$pdo) return [];
    $q=trim($q);
    $sql="SELECT id,registry_id,package_key,name,publisher_key,type,version,description,rating_avg,rating_count,is_paid,license,price
          FROM ce_marketplace_packages WHERE (name LIKE :q OR package_key LIKE :q)";
    $params=[':q'=>'%'.$q.'%'];
    if($type){ $sql.=" AND type=:t"; $params[':t']=$type; }
    $sql.=" ORDER BY rating_avg DESC, rating_count DESC, name ASC LIMIT 50";
    $st=$pdo->prepare($sql); $st->execute($params);
    return $st->fetchAll(\PDO::FETCH_ASSOC);
  }
}
