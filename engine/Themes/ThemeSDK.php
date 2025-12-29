<?php
namespace Themes;
use Observability\Logger;
class ThemeSDK {
  public static function manifest(string $themeName): array {
    $file=ROOT_PATH.'/themes/'.$themeName.'/theme.json';
    if(is_file($file)){ $d=json_decode((string)file_get_contents($file),true); return is_array($d)?$d:[]; }
    return [];
  }
  public static function build(string $themeName): array {
    $m=self::manifest($themeName); $hooks=(array)($m['build_hooks']??[]); $ran=[];
    foreach($hooks as $cmd){
      $cmd=(string)$cmd; if($cmd==='') continue;
      if(!preg_match('/^(npm|pnpm|yarn)\b/',$cmd)) continue;
      $ran[]=$cmd; Logger::info('theme.build_hook',['theme'=>$themeName,'cmd'=>$cmd]);
    }
    return ['ok'=>true,'ran'=>$ran,'note'=>'Build hooks are recorded but not executed by default in skeleton.'];
  }
}
