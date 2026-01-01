<?php
namespace UIBuilder\Blocks;
class HtmlBlock implements BlockInterface {
  public function type(): string { return 'html'; }
  public function title(): string { return 'Custom HTML'; }
  public function schema(): array { return ['props'=>['html'=>['type'=>'string','title'=>'Raw HTML','default'=>'<div>Custom</div>']]]; }
  public function render(array $props, array $context=[]): string { return (string)($props['html'] ?? ''); }
}
