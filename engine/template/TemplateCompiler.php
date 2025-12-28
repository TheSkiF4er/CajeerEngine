<?php
namespace Template;

class TemplateCompiler
{
    public function __construct(private bool $debug = false) {}

    public function compile(string $tpl, string $sourcePath = ''): string
    {
        $parser = new TemplateParser();
        $body = $parser->toPhp($tpl);

        return "<?php\n" .
            "/** Compiled template; Source: " . addslashes($sourcePath) . " */\n" .
            "?>\n" .
            $body;
    }
}
