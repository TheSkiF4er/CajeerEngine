<?php
namespace UIBuilder;
use Observability\Logger;
class Renderer {
  public static function render(array $layout, array $ctx=[]): string {
    $errors=[]; if(!Schema::validate($layout,$errors)){ Logger::warn('ui_builder.schema_invalid',['errors'=>$errors]); return "<!-- ui_builder invalid schema -->"; }
    return self::renderNode($layout,$ctx);
  }
  protected static function renderNode(array $node, array $ctx): string {
    if(!RBAC::allowed($node)) return "<!-- ui_builder block denied -->";
    $type=(string)$node['type'];
    $props=is_array($node['props']??null)?$node['props']:[];
    $children=is_array($node['children']??null)?$node['children']:[];
    if($type==='pattern'){
      $key=(string)($props['key']??'');
      if($key!=='' && class_exists('UIBuilder\\Store')){
        $pat=Store::getPattern($key,(int)($_SERVER['CE_TENANT_ID']??0),(int)($_SERVER['CE_SITE_ID']??0));
        if($pat) return self::render($pat,$ctx);
      }
      return "<!-- pattern not found -->";
    }
    if($type==='text'){
      return "<div data-ui='text'>".htmlspecialchars((string)($props['text']??''),ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8')."</div>";
    }
    if($type==='html'){
      return "<div data-ui='html'>".(string)($props['html']??'')."</div>";
    }
    if($type==='image'){
      $src=htmlspecialchars((string)($props['src']??''),ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');
      $alt=htmlspecialchars((string)($props['alt']??''),ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');
      return "<img data-ui='image' src=\"$src\" alt=\"$alt\" />";
    }
    if($type==='module'){
      $name=(string)($props['name']??''); $args=(array)($props['args']??[]);
      return "<!-- module:$name ".htmlspecialchars(json_encode($args),ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8')." -->";
    }
    $cls=htmlspecialchars((string)($props['class']??''),ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');
    $attr=$cls?(" class=\"$cls\""):"";
    $out="<div data-ui=\"$type\"$attr>";
    foreach($children as $ch){ $out.=self::renderNode($ch,$ctx); }
    $out.="</div>";
    return $out;
  }
  public static function preview(array $layout): array {
    return ['ok'=>true,'html'=>self::render($layout,[])];
  }
}
