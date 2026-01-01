<?php
namespace UIBuilder;
class Schema {
  public static function validate(array $node, array &$errors = [], string $path = '$'): bool {
    $type=(string)($node['type']??'');
    if($type===''){ $errors[]="$path.type required"; return false; }
    $allowed=['layout','section','row','col','pattern','text','image','gallery','form','html','module'];
    if(!in_array($type,$allowed,true)) $errors[]="$path.type invalid:$type";
    if(isset($node['children']) && !is_array($node['children'])) $errors[]="$path.children must be array";
    if(is_array($node['children']??null)){
      foreach($node['children'] as $i=>$ch){
        if(!is_array($ch)){ $errors[]="$path.children[$i] must be object"; continue; }
        self::validate($ch,$errors,$path.".children[$i]");
      }
    }
    return count($errors)===0;
  }
}
