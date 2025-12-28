<?php
namespace Core;
class Logger {
  protected static string $file;
  public static function init(string $file): void { self::$file=$file; $dir=dirname($file); if(!is_dir($dir)) @mkdir($dir,0775,true); }
  public static function info(string $m,array $c=[]): void { self::w('INFO',$m,$c); }
  public static function warn(string $m,array $c=[]): void { self::w('WARN',$m,$c); }
  public static function error(string $m,array $c=[]): void { self::w('ERROR',$m,$c); }
  protected static function w(string $l,string $m,array $c): void {
    $f=self::$file ?? (ROOT_PATH.'/storage/logs/app.log');
    $line=date('c')." [$l] ".$m.($c?(" ".json_encode($c,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)):"")."\n";
    @file_put_contents($f,$line,FILE_APPEND);
  }
}
