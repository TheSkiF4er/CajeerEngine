<?php
namespace UIBuilder\Blocks;
class TextBlock implements BlockInterface {
  public function type(): string { return 'text'; }
  public function title(): string { return 'Text'; }
  public function schema(): array { return ['props'=>['html'=>['type'=>'string','title'=>'HTML','default'=>'<p>Text</p>']]]; }
  public function render(array $props, array $context=[]): string { return (string)($props['html'] ?? ''); }
}
