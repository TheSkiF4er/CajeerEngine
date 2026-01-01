<?php
namespace UIBuilder;
class Export {
  public static function toDSL(array $layout): string {
    $buf=["{!-- UI Builder Export (v2.8) --}", self::nodeToTpl($layout,0), "{!-- /UI Builder Export --}"];
    return implode("\n",$buf);
  }
  protected static function nodeToTpl(array $n, int $lvl): string {
    $pad=str_repeat("  ",$lvl);
    $type=(string)($n['type']??'node');
    $props=is_array($n['props']??null)?$n['props']:[];
    $children=is_array($n['children']??null)?$n['children']:[];
    if($type==='text'){
      $t=(string)($props['text']??'');
      return $pad."<div class=\"ui-text\">".htmlspecialchars($t,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8')."</div>";
    }
    if($type==='html'){
      return $pad."<div class=\"ui-html\">".(string)($props['html']??'')."</div>";
    }
    $cls=htmlspecialchars((string)($props['class']??''),ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');
    $open=$pad."<div class=\"ui-$type".($cls?(" ".$cls):"")."\">";
    $mid=[];
    foreach($children as $c){ $mid[]=self::nodeToTpl($c,$lvl+1); }
    $close=$pad."</div>";
    return implode("\n", array_merge([$open],$mid,[$close]));
  }
}
