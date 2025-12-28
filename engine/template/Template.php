<?php
namespace Template;

use Core\KernelSingleton;

class Template
{
    protected string $tplPath;
    protected string $cachePath;
    protected bool $debug;

    public function __construct(?string $tplPath = null, ?string $cachePath = null, bool $debug = false, ?string $theme = null)
    {
        $theme = $theme ?: 'default';
        $this->tplPath = $tplPath ?: ROOT_PATH . '/templates/' . $theme;
        $this->cachePath = $cachePath ?: ROOT_PATH . '/storage/compiled_tpl';
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

            file_put_contents($compiled, $php);
        }

        // execute compiled
        extract($vars, EXTR_SKIP);
        include $compiled;
    }
}
