<?php
namespace Marketplace;
class Semver {
  public static function parse(string $v): array {
    $v = trim(ltrim($v,'v')); $parts = preg_split('/[+\-]/',$v); $core=$parts[0]??'0.0.0';
    $a=explode('.',$core); return [(int)($a[0]??0),(int)($a[1]??0),(int)($a[2]??0)];
  }
  public static function cmp(string $a,string $b): int {
    $pa=self::parse($a); $pb=self::parse($b);
    for($i=0;$i<3;$i++){ if($pa[$i]<$pb[$i]) return -1; if($pa[$i]>$pb[$i]) return 1; }
    return 0;
  }
  public static function satisfies(string $version,string $constraint): bool {
    $c=trim($constraint); if($c===''||$c==='*') return true;
    if(str_contains($c,'*')){ $v=self::parse($version); $c=ltrim($c,'v'); $p=explode('.',$c);
      if(($p[0]??'*')!=='*' && (int)$p[0]!==$v[0]) return false;
      if(($p[1]??'*')!=='*' && (int)$p[1]!==$v[1]) return false;
      return true;
    }
    if(str_starts_with($c,'^')){ $base=substr($c,1); $b=self::parse($base); $v=self::parse($version);
      if($v[0]!=$b[0]) return false; return self::cmp($version,$base)>=0;
    }
    if(str_starts_with($c,'~')){ $base=substr($c,1); $b=self::parse($base); $v=self::parse($version);
      if($v[0]!=$b[0]||$v[1]!=$b[1]) return false; return self::cmp($version,$base)>=0;
    }
    if(preg_match_all('/(>=|<=|>|<|=)\s*([0-9A-Za-z\.\-\+v]+)/',$c,$m,PREG_SET_ORDER)){
      foreach($m as $t){ $op=$t[1]; $ver=$t[2]; $cmp=self::cmp($version,$ver);
        if($op==='>' && !($cmp>0)) return false;
        if($op==='>=' && !($cmp>=0)) return false;
        if($op==='<' && !($cmp<0)) return false;
        if($op==='<=' && !($cmp<=0)) return false;
        if($op==='=' && !($cmp===0)) return false;
      }
      return true;
    }
    return self::cmp($version,$c)===0;
  }
}
