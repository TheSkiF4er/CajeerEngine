<?php
namespace Core;
class Policy {
  public static function allow(array $actor,string $ability,array $resource=[]): bool {
    $gid=(string)($actor['group_id']??'');
    $caps=(array)($actor['caps']??[]);
    if($gid==='1' || in_array('*',$caps,true)) return true;
    return in_array($ability,$caps,true);
  }
}
