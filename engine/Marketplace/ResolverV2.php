<?php
namespace Marketplace;
class ResolverV2 {
  public static function resolve(array $indexRows,array $deps): array {
    $byKey=[];
    foreach($indexRows as $r){
      $k=(string)($r['package_key']??''); if(!$k) continue;
      $byKey[$k][]=$r;
    }
    foreach($byKey as $k=>$rows){
      usort($rows, fn($a,$b)=>Semver::cmp((string)$b['version'],(string)$a['version']));
      $byKey[$k]=$rows;
    }
    $plan=[]; $errors=[];
    foreach($deps as $depKey=>$constraint){
      $rows=$byKey[(string)$depKey]??[]; $picked=null;
      foreach($rows as $r){ if(Semver::satisfies((string)$r['version'],(string)$constraint)){ $picked=$r; break; } }
      if(!$picked){ $errors[]="cannot_resolve:$depKey ($constraint)"; continue; }
      $plan[]=['package_key'=>(string)$depKey,'version'=>$picked['version'],'type'=>$picked['type']??null];
    }
    return ['ok'=>count($errors)===0,'plan'=>$plan,'errors'=>$errors];
  }
}
