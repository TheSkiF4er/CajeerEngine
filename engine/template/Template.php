<?php
namespace Template;

class Template
{
    private string $tplPath;

    public function __construct(?string $tplPath = null)
    {
        $this->tplPath = $tplPath ?: ROOT_PATH . '/templates/default';
    }

    /**
     * Минимальный рендер (stub): заменяет {key} на значение.
     * В следующих итерациях будет TemplateParser/Compiler + cache.
     */
    public function render(string $tplFile, array $vars = []): void
    {
        $full = $this->tplPath . '/' . $tplFile;
        if (!file_exists($full)) {
            throw new \RuntimeException('Template not found: ' . $full);
        }

        $content = file_get_contents($full);
        foreach ($vars as $k => $v) {
            $content = str_replace('{' . $k . '}', (string)$v, $content);
        }

        echo $content;
    }
}
