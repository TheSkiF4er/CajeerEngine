<?php
namespace UIBuilder;
class Diff {
  public static function unified(string $a, string $b): string {
    $aLines=preg_split('/\R/',$a); $bLines=preg_split('/\R/',$b);
    $out=["--- ui.json","+++ dsl.tpl"];
    $max=max(count($aLines),count($bLines));
    for($i=0;$i<$max;$i++){
      $al=$aLines[$i]??''; $bl=$bLines[$i]??'';
      if($al===$bl){ $out[]=" ".$al; continue; }
      if($al!=='') $out[]="-".$al;
      if($bl!=='') $out[]="+".$bl;
    }
    return implode("\n",$out);
  }
}
