<?php
namespace Template;

use Template\DSL\Compiler;

use Theme\ThemeManager;

use Core\KernelSingleton;

class Template
{

    private function ensureDirWritable(string $dir): void
    {
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        if (!is_dir($dir) || !is_writable($dir)) {
            throw new \Exception('Template cache directory not writable: ' . $dir);
        }
    }

    protected string $tplPath;
    protected string $cachePath;
    protected bool $debug;

    public function __construct(?string $tplPath = null, ?string $cachePath = null, bool $debug = false, ?string $theme = null)
    {
        $theme = $theme ?: 'default';
        $this->tplPath = $tplPath ?: ROOT_PATH . '/templates/' . $theme;
        $this->cachePath = $cachePath ?: ROOT_PATH . '/storage/compiled_tpl_v2';
        $this->debug = $debug;
        if (!is_dir($this->cachePath)) @mkdir($this->cachePath, 0775, true);
    }

    private function globals(): array
    {
        $g = [];
        try {
            $seo = KernelSingleton::container()->get('seo');
            if ($seo instanceof \Seo\MetaManager) {
                $g['meta_tags'] = $seo->renderHead();
                $g['jsonld'] = $seo->renderJsonLd();
            }
        } catch (\Throwable $e) {
            $g['meta_tags'] = '';
            $g['jsonld'] = '';
        }
        return $g;
    }

    public function render(string $tplFile, array $vars = []): void
    {
        $vars = array_merge($this->globals(), $vars);

        $t0 = microtime(true);
        $full = $this->tplPath . '/' . $tplFile;
        if (!file_exists($full)) {
            throw new \Exception("Template not found: $full");
        }

        // compile/invalidate cache
        $compiled = $this->cachePath . '/' . md5($full) . '.php';
        $srcMtime = filemtime($full) ?: 0;
        $cmpMtime = file_exists($compiled) ? (filemtime($compiled) ?: 0) : 0;

        if (!file_exists($compiled) || $cmpMtime < $srcMtime) {
            $content = file_get_contents($full);

            // basic DLE-like replacements for {var}
            // {include file="x.tpl"} supported in compiler stage
            $php = Compiler::compile((string)$content, $this->tplPath, $this->cachePath);

            if (!is_dir(dirname($compiled))) { @mkdir(dirname($compiled), 0775, true); }


            file_put_contents($compiled, $php);
        }

        // execute compiled
        extract($vars, EXTR_SKIP);
        include $compiled;
    }

    protected function compiledPath(string $tplFile): string
    {
        $key = md5($this->tplPath . '|' . $tplFile);
        return rtrim($this->cachePath,'/') . '/' . $key . '.php';
    }

    protected function ensureCompiled(string $tplFile, string $full): string
    {
        if (!is_dir($this->cachePath)) @mkdir($this->cachePath, 0775, true);
        $compiled = $this->compiledPath($tplFile);
        $need = !is_file($compiled) || (filemtime($compiled) < filemtime($full));
        if ($need) {
            $src = file_get_contents($full);
            $php = \Template\DSL\Compiler::compile($src);
            file_put_contents($compiled, $php);
        }
        return $compiled;
    }

}
