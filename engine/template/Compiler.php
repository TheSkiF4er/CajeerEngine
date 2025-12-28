<?php
namespace Template;

class Compiler
{
    public static function compile(string $tpl, string $tplRoot, string $cachePath): string
    {
        // includes: {include file="header.tpl"}
        $tpl = preg_replace_callback('/\{include\s+file=\"([^\"]+)\"\}/i', function($m) use ($tplRoot, $cachePath) {
            $file = $tplRoot . '/' . $m[1];
            $compiled = $cachePath . '/' . md5($file) . '.php';
            if (is_file($file)) {
                $src = file_get_contents($file);
                $php = self::compile((string)$src, $tplRoot, $cachePath);
                file_put_contents($compiled, $php);
                return "<?php include '" . addslashes($compiled) . "'; ?>";
            }
            return '';
        }, $tpl);

        // variables: {var}
        $tpl = preg_replace('/\{([a-zA-Z0-9_\.]+)\}/', '<?php echo htmlspecialchars($$1 ?? \'\', ENT_QUOTES, \'UTF-8\'); ?>', $tpl);

        return "<?php /* compiled tpl */ ?>\n" . $tpl;
    }
}
