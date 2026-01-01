<?php
namespace UIBuilder;

class Builder
{
    private array $cfg;

    public function __construct()
    {
        $this->cfg = is_file(ROOT_PATH . '/system/ui_builder.php') ? (array)require ROOT_PATH . '/system/ui_builder.php' : ['enabled'=>false];
    }

    public function enabled(): bool { return (bool)($this->cfg['enabled'] ?? false); }

    public function save(string $name, array $schema): string
    {
        $dir = (string)($this->cfg['storage'] ?? (ROOT_PATH . '/storage/ui_builder'));
        if (!is_dir($dir)) @mkdir($dir, 0775, true);
        $file = $dir . '/' . preg_replace('/[^a-z0-9_-]+/i','-', $name) . '.json';
        file_put_contents($file, json_encode($schema, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
        return $file;
    }

    public function list(): array
    {
        $dir = (string)($this->cfg['storage'] ?? (ROOT_PATH . '/storage/ui_builder'));
        if (!is_dir($dir)) return [];
        return array_map('basename', glob($dir . '/*.json') ?: []);
    }
}
