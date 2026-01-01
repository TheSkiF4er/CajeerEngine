<?php
namespace UI;

use Theme\ThemeManager;

class Components
{
    public static function partial(string $file, array $vars = []): string
    {
        $path = ThemeManager::templatePath() . '/components/' . ltrim($file, '/');
        if (!str_ends_with(strtolower($path), '.tpl')) $path .= '.tpl';
        if (!is_file($path)) throw new \RuntimeException('Component not found: '.$path);

        $tpl = file_get_contents($path);
        foreach ($vars as $k=>$v) $tpl = str_replace('{'.$k.'}', (string)$v, $tpl);
        return $tpl;
    }
}
