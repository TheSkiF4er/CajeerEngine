<?php
namespace UIBuilder\Blocks;
class ImageBlock implements BlockInterface {
  public function type(): string { return 'image'; }
  public function title(): string { return 'Image'; }
  public function schema(): array { return ['props'=>[
    'src'=>['type'=>'string','title'=>'Source URL','default'=>''],
    'alt'=>['type'=>'string','title'=>'Alt text','default'=>''],
    'class'=>['type'=>'string','title'=>'CSS class','default'=>'w-full rounded-2xl'],
  ]]; }
  public function render(array $props, array $context=[]): string {
    $src = htmlspecialchars((string)($props['src'] ?? ''), ENT_QUOTES);
    if($src==='') return '';
    $alt = htmlspecialchars((string)($props['alt'] ?? ''), ENT_QUOTES);
    $cls = htmlspecialchars((string)($props['class'] ?? ''), ENT_QUOTES);
    return '<img class="'.$cls.'" src="'.$src.'" alt="'.$alt.'" loading="lazy" />';
  }
}
