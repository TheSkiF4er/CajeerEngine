<?php
namespace UIBuilder\Blocks;
class FormBlock implements BlockInterface {
  public function type(): string { return 'form'; }
  public function title(): string { return 'Form'; }
  public function schema(): array { return ['props'=>[
    'action'=>['type'=>'string','title'=>'Action','default'=>'/'],
    'method'=>['type'=>'string','title'=>'Method','default'=>'post'],
    'fields'=>['type'=>'array','title'=>'Fields','default'=>[['type'=>'text','name'=>'email','label'=>'Email']]],
    'submit_label'=>['type'=>'string','title'=>'Submit label','default'=>'Send'],
  ]]; }
  public function render(array $props, array $context=[]): string {
    $action=htmlspecialchars((string)($props['action'] ?? '/'),ENT_QUOTES);
    $method=htmlspecialchars((string)($props['method'] ?? 'post'),ENT_QUOTES);
    $fields=(array)($props['fields'] ?? []);
    $submit=htmlspecialchars((string)($props['submit_label'] ?? 'Send'),ENT_QUOTES);
    $html='<form class="ce-uib-form" action="'.$action.'" method="'.$method.'">';
    foreach($fields as $f){
      $type=htmlspecialchars((string)($f['type'] ?? 'text'),ENT_QUOTES);
      $name=htmlspecialchars((string)($f['name'] ?? 'field'),ENT_QUOTES);
      $label=htmlspecialchars((string)($f['label'] ?? $name),ENT_QUOTES);
      $html.='<label style="display:block;margin:.5rem 0 .25rem 0;">'.$label.'</label>';
      $html.='<input class="w-full" type="'.$type.'" name="'.$name.'" />';
    }
    return $html.'<button type="submit">'.$submit.'</button></form>';
  }
}
