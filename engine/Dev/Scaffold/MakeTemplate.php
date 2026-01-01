<?php
namespace Dev\Scaffold;

class MakeTemplate
{
    public function run(string $file): void
    {
        $file = str_replace('..', '', $file);
        $file = ltrim($file, '/');
        if (!str_ends_with(strtolower($file), '.tpl')) $file .= '.tpl';

        $path = ROOT_PATH . '/templates/default/' . $file;
        if (is_file($path)) throw new \RuntimeException('Template already exists: ' . $file);

        @mkdir(dirname($path), 0775, true);
        file_put_contents($path, "<!-- Generated template: {$file} -->\n");
    }
}
