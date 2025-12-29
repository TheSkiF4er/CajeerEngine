<?php
namespace UIBuilder;
use Database\DB;
class Store {
  public static function ensureSchema(): void {
    $pdo=DB::pdo(); if(!$pdo) return;
    $pdo->exec(file_get_contents(ROOT_PATH.'/system/sql/ui_builder_v2_8.sql'));
  }
  public static function saveLayout(string $pageKey, array $json, int $tenantId, int $siteId, ?int $authorUserId=null): array {
    self::ensureSchema(); $pdo=DB::pdo(); if(!$pdo) return ['ok'=>false,'error'=>'db_required'];
    $st=$pdo->prepare("SELECT id, active_version FROM ce_ui_layouts WHERE tenant_id=:t AND site_id=:s AND page_key=:p LIMIT 1");
    $st->execute([':t'=>$tenantId,':s'=>$siteId,':p'=>$pageKey]); $row=$st->fetch(\PDO::FETCH_ASSOC);
    if(!$row){
      $pdo->prepare("INSERT INTO ce_ui_layouts(tenant_id,site_id,page_key,title,active_version,created_at,updated_at) VALUES(:t,:s,:p,:title,1,NOW(),NOW())")
        ->execute([':t'=>$tenantId,':s'=>$siteId,':p'=>$pageKey,':title'=>$pageKey]);
      $layoutId=(int)$pdo->lastInsertId(); $ver=1;
    } else {
      $layoutId=(int)$row['id']; $ver=((int)$row['active_version'])+1;
      $pdo->prepare("UPDATE ce_ui_layouts SET active_version=:v, updated_at=NOW() WHERE id=:id")->execute([':v'=>$ver,':id'=>$layoutId]);
    }
    $dsl=Export::toDSL($json);
    $pdo->prepare("INSERT INTO ce_ui_layout_versions(layout_id,version,json,dsl_snapshot,author_user_id,created_at) VALUES(:id,:v,:j,:dsl,:u,NOW())")
      ->execute([':id'=>$layoutId,':v'=>$ver,':j'=>json_encode($json,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),':dsl'=>$dsl,':u'=>$authorUserId]);
    return ['ok'=>true,'layout_id'=>$layoutId,'version'=>$ver];
  }
  public static function getActiveLayout(string $pageKey, int $tenantId, int $siteId): ?array {
    $pdo=DB::pdo(); if(!$pdo) return null;
    $st=$pdo->prepare("SELECT id, active_version FROM ce_ui_layouts WHERE tenant_id=:t AND site_id=:s AND page_key=:p LIMIT 1");
    $st->execute([':t'=>$tenantId,':s'=>$siteId,':p'=>$pageKey]); $l=$st->fetch(\PDO::FETCH_ASSOC);
    if(!$l) return null;
    $st2=$pdo->prepare("SELECT json FROM ce_ui_layout_versions WHERE layout_id=:id AND version=:v LIMIT 1");
    $st2->execute([':id'=>(int)$l['id'],':v'=>(int)$l['active_version']); $r=$st2->fetch(\PDO::FETCH_ASSOC);
    if(!$r) return null;
    $j=json_decode((string)$r['json'],true);
    return is_array($j)?$j:null;
  }
  public static function rollback(string $pageKey, int $toVersion, int $tenantId, int $siteId): array {
    self::ensureSchema(); $pdo=DB::pdo(); if(!$pdo) return ['ok'=>false,'error'=>'db_required'];
    $st=$pdo->prepare("SELECT id FROM ce_ui_layouts WHERE tenant_id=:t AND site_id=:s AND page_key=:p LIMIT 1");
    $st->execute([':t'=>$tenantId,':s'=>$siteId,':p'=>$pageKey]); $l=$st->fetch(\PDO::FETCH_ASSOC);
    if(!$l) return ['ok'=>false,'error'=>'layout_not_found'];
    $layoutId=(int)$l['id'];
    $st2=$pdo->prepare("SELECT id FROM ce_ui_layout_versions WHERE layout_id=:id AND version=:v LIMIT 1");
    $st2->execute([':id'=>$layoutId,':v'=>$toVersion]); $vrow=$st2->fetch(\PDO::FETCH_ASSOC);
    if(!$vrow) return ['ok'=>false,'error'=>'version_not_found'];
    $pdo->prepare("UPDATE ce_ui_layouts SET active_version=:v, updated_at=NOW() WHERE id=:id")->execute([':v'=>$toVersion,':id'=>$layoutId]);
    return ['ok'=>true,'layout_id'=>$layoutId,'active_version'=>$toVersion];
  }
  public static function savePattern(string $patternKey, array $json, int $tenantId, int $siteId): array {
    self::ensureSchema(); $pdo=DB::pdo(); if(!$pdo) return ['ok'=>false,'error'=>'db_required'];
    $st=$pdo->prepare("SELECT id, version FROM ce_ui_patterns WHERE tenant_id=:t AND site_id=:s AND pattern_key=:k LIMIT 1");
    $st->execute([':t'=>$tenantId,':s'=>$siteId,':k'=>$patternKey]); $row=$st->fetch(\PDO::FETCH_ASSOC);
    $ver=1;
    if(!$row){
      $pdo->prepare("INSERT INTO ce_ui_patterns(tenant_id,site_id,pattern_key,title,json,version,created_at,updated_at) VALUES(:t,:s,:k,:title,:j,1,NOW(),NOW())")
        ->execute([':t'=>$tenantId,':s'=>$siteId,':k'=>$patternKey,':title'=>$patternKey,':j'=>json_encode($json,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)]);
    } else {
      $ver=((int)$row['version'])+1;
      $pdo->prepare("UPDATE ce_ui_patterns SET json=:j, version=:v, updated_at=NOW() WHERE id=:id")
        ->execute([':j'=>json_encode($json,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),':v'=>$ver,':id'=>(int)$row['id']]);
    }
    return ['ok'=>true,'pattern_key'=>$patternKey,'version'=>$ver];
  }
  public static function getPattern(string $patternKey, int $tenantId, int $siteId): ?array {
    $pdo=DB::pdo(); if(!$pdo) return null;
    $st=$pdo->prepare("SELECT json FROM ce_ui_patterns WHERE tenant_id=:t AND site_id=:s AND pattern_key=:k LIMIT 1");
    $st->execute([':t'=>$tenantId,':s'=>$siteId,':k'=>$patternKey]); $row=$st->fetch(\PDO::FETCH_ASSOC);
    if(!$row) return null;
    $j=json_decode((string)$row['json'],true);
    return is_array($j)?$j:null;
  }
  public static function diffUIvsDSL(string $pageKey, int $tenantId, int $siteId): array {
    $pdo=DB::pdo(); if(!$pdo) return ['ok'=>false,'error'=>'db_required'];
    $st=$pdo->prepare("SELECT id, active_version FROM ce_ui_layouts WHERE tenant_id=:t AND site_id=:s AND page_key=:p LIMIT 1");
    $st->execute([':t'=>$tenantId,':s'=>$siteId,':p'=>$pageKey]); $l=$st->fetch(\PDO::FETCH_ASSOC);
    if(!$l) return ['ok'=>false,'error'=>'layout_not_found'];
    $st2=$pdo->prepare("SELECT json,dsl_snapshot FROM ce_ui_layout_versions WHERE layout_id=:id AND version=:v LIMIT 1");
    $st2->execute([':id'=>(int)$l['id'],':v'=>(int)$l['active_version']); $r=$st2->fetch(\PDO::FETCH_ASSOC);
    if(!$r) return ['ok'=>false,'error'=>'version_not_found'];
    return ['ok'=>true,'diff'=>Diff::unified((string)$r['json'], (string)($r['dsl_snapshot']??''))];
  }
}
