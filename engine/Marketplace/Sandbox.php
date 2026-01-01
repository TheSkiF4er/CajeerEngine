<?php
namespace Marketplace;
class Sandbox {
  public static function preflight(array $manifest): array {
    $errors=[]; $warnings=[];
    $files=(array)($manifest['files']??[]);
    if(!$files) $warnings[]='manifest.files empty';
    $conflicts=[];
    foreach($files as $f){
      $path=ROOT_PATH.'/'.ltrim((string)$f,'/');
      if(is_file($path)) $conflicts[]=(string)$f;
    }
    $deps=(array)($manifest['dependencies']??[]);
    $missing=[];
    foreach($deps as $k=>$c){
      $installed=self::installedVersion((string)$k);
      if($installed===null){ $missing[]=['package'=>$k,'constraint'=>$c]; continue; }
      if(!Semver::satisfies($installed,(string)$c)) $errors[]="dependency_mismatch:$k ($installed !~ $c)";
    }
    return ['ok'=>count($errors)===0,'errors'=>$errors,'warnings'=>$warnings,'conflicts'=>$conflicts,'missing_dependencies'=>$missing];
  }
  public static function installedVersion(string $packageKey): ?string {
    try{
      $pdo=\Database\DB::pdo();
      if($pdo){
        $st=$pdo->prepare("SELECT version FROM ce_installed_packages WHERE package_key=:k LIMIT 1");
        $st->execute([':k'=>$packageKey]); $row=$st->fetch(\PDO::FETCH_ASSOC);
        if($row && !empty($row['version'])) return (string)$row['version'];
      }
    }catch(\Throwable $e){}
    return null;
  }
}
