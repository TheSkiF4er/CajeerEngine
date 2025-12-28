<?php
namespace Template;

use Core\Config;

class TemplateRuntime
{
    public function __construct(
        private string $tplRoot,
        private string $theme,
        private string $cacheDir,
        private bool $debug = false
    ) {}

    public function render(string $tplFile, array $vars = [], array $ctxOverride = []): void
    {
        $tplPath = $this->resolveTpl($tplFile);

        if (!is_dir($this->cacheDir)) {
            @mkdir($this->cacheDir, 0775, true);
        }

        $compiled = $this->compiledPath($tplPath);

        if (!is_file($compiled) || filemtime($compiled) < filemtime($tplPath)) {
            $compiler = new TemplateCompiler($this->debug);
            $php = $compiler->compile((string)file_get_contents($tplPath), $tplPath);
            file_put_contents($compiled, $php);
            if ($this->debug) {
                $this->log('compile', ['tpl'=>$tplPath, 'compiled'=>$compiled]);
            }
        }

        $ctx = [
            'vars' => $vars,
            'config' => Config::all(),
            'user' => [
                'logged' => false,
                'group' => 5,
                'name' => 'Guest',
            ],
            'page' => (string)($_SERVER['CAJEER_PAGE'] ?? 'main'),
        ];
        foreach ($ctxOverride as $k => $v) {
            $ctx[$k] = $v;
        }

        $__tpl = $this;
        $__ctx = $ctx;
        require $compiled;
    }

    public function include(string $file, array $__ctx): void
    {
        $this->render($file, $__ctx['vars'] ?? [], $__ctx);
    }

    public function module(string $name, array $params, array $__ctx): string
    {
        return Extensions::callModule($name, $params, $__ctx);
    }

    private function resolveTpl(string $tplFile): string
    {
        $path = rtrim($this->tplRoot, '/').'/'.$this->theme.'/'.ltrim($tplFile, '/');
        if (!is_file($path)) {
            throw new \RuntimeException('Template not found: ' . $path);
        }
        return $path;
    }

    private function compiledPath(string $tplPath): string
    {
        return rtrim($this->cacheDir, '/').'/'.basename($tplPath).'.'.sha1($tplPath).'.php';
    }

    private function log(string $type, array $data): void
    {
        $dir = ROOT_PATH . '/storage/logs';
        if (!is_dir($dir)) @mkdir($dir, 0775, true);
        @file_put_contents($dir.'/template.debug.log',
            json_encode(['ts'=>date('c'),'type'=>$type,'data'=>$data], JSON_UNESCAPED_UNICODE)."\n",
            FILE_APPEND
        );
    }
}
