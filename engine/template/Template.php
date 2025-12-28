<?php
namespace Template;

use Core\Config;

/**
 * Template Engine v1.1 (DLE-style)
 * - compile .tpl to cached PHP
 * - supports include/if/group/available/module tags
 */
class Template
{
    private string $tplRoot;
    private string $theme;
    private string $cacheDir;
    private bool $debug;

    public function __construct(?string $tplRoot = null, string $theme = 'default')
    {
        $this->tplRoot = $tplRoot ?: (string)Config::get('template.templates_dir', ROOT_PATH . '/templates');
        $this->theme = $theme;
        $this->cacheDir = (string)Config::get('template.cache_dir', ROOT_PATH . '/storage/compiled_tpl');
        $this->debug = (bool)Config::get('template.debug', false);
    }

    public function render(string $tplFile, array $vars = [], array $ctxOverride = []): void
    {
        $rt = new TemplateRuntime($this->tplRoot, $this->theme, $this->cacheDir, $this->debug);
        $rt->render($tplFile, $vars, $ctxOverride);
    }
}
