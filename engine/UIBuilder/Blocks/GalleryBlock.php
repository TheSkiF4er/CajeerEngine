<?php
namespace UIBuilder\Blocks;
class GalleryBlock implements BlockInterface {
  public function type(): string { return 'gallery'; }
  public function title(): string { return 'Gallery'; }
  public function schema(): array { return ['props'=>[
    'images'=>['type'=>'array','title'=>'Images','default'=>[]],
    'cols'=>['type'=>'number','title'=>'Columns','default'=>3],
    'gap'=>['type'=>'number','title'=>'Gap (rem)','default'=>1],
  ]]; }
  public function render(array $props, array $context=[]): string {
    $images = (array)($props['images'] ?? []);
    if(!$images) return '';
    $cols = max(1,(int)($props['cols'] ?? 3));
    $gap = max(0,(int)($props['gap'] ?? 1));
    $html = '<div class="ce-uib-gallery" style="display:grid;grid-template-columns:repeat('.$cols.',minmax(0,1fr));gap:'.$gap.'rem;">';
    foreach($images as $img){
      $src = htmlspecialchars((string)($img['src'] ?? $img ?? ''), ENT_QUOTES);
      if(!$src) continue;
      $alt = htmlspecialchars((string)($img['alt'] ?? ''), ENT_QUOTES);
      $html .= '<img class="w-full rounded-xl" src="'.$src.'" alt="'.$alt.'" loading="lazy" />';
    }
    return $html.'</div>';
  }
}
