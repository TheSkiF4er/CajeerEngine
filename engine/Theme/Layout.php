<?php
namespace Theme;

use Template\Template;

class Layout
{
    public static function render(string $content, array $vars = [], string $layoutFile = 'layout.tpl'): void
    {
        $tpl = new Template(ThemeManager::templatePath());
        $tpl->render($layoutFile, array_merge($vars, ['content' => $content]));
    }
}
