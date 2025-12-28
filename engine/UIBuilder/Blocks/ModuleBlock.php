<?php
namespace UIBuilder\Blocks;
class ModuleBlock implements BlockInterface {
  public function type(): string { return 'module'; }
  public function title(): string { return 'Module block'; }
  public function schema(): array { return ['props'=>[
    'name'=>['type'=>'string','title'=>'Module name','default'=>'news'],
    'action'=>['type'=>'string','title'=>'Action','default'=>'index'],
    'params'=>['type'=>'object','title'=>'Params','default'=>new \stdClass()],
  ]]; }
  public function render(array $props, array $context=[]): string {
    $name=preg_replace('/[^a-zA-Z0-9_\-]/','',(string)($props['name'] ?? ''));
    $action=preg_replace('/[^a-zA-Z0-9_\-]/','',(string)($props['action'] ?? 'index'));
    $params=(array)($props['params'] ?? []);
    $controllerClass='\\Modules\\'.$name.'\\Controller';
    if(!class_exists($controllerClass)) return '<!-- module not found: '.htmlspecialchars($name,ENT_QUOTES).' -->';
    $ctrl=new $controllerClass();
    if(!method_exists($ctrl,$action)) return '<!-- module action not found: '.htmlspecialchars($action,ENT_QUOTES).' -->';
    ob_start(); $ctrl->{$action}($params); return (string)ob_get_clean();
  }
}
