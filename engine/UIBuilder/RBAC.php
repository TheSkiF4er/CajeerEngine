<?php
namespace UIBuilder;
use API\Auth;
class RBAC {
  public static function allowed(array $block): bool {
    $perm=(string)($block['perm']??'');
    if($perm!==''){
      try{ Auth::requireScope($perm); } catch(\Throwable $e){ return false; }
    }
    return true;
  }
}
