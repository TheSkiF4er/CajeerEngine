<?php
namespace Template\DSL;

class Compiler
{
    /**
     * Compile DSL (.tpl-like) source into PHP.
     *
     * Compiled template is included by Template\Template::render().
     * It should echo the output (and also return it for optional capture).
     */
    public static function compile(string $tpl): string
    {
        $tokens = Tokenizer::tokenize($tpl);

        $php  = "<?php\n";
        $php .= "// Compiled by CajeerEngine Template DSL\n";
        $php .= '$__out = \'\';' . "\n";
        $php .= '$vars = $vars ?? [];' . "\n\n";

        foreach ($tokens as $t) {
            if (($t['type'] ?? '') === 'TEXT') {
                $php .= '$__out .= ' . var_export((string)($t['v'] ?? ''), true) . ';' . "\n";
                continue;
            }

            $s = (string)($t['v'] ?? '');

            // {var}, {config.key}, {user.name}
            if (preg_match('/^\{\s*([a-zA-Z0-9_.:-]+)\s*\}$/', $s, $m)) {
                $php .= '$__out .= \\Template\\DSL\\Runtime::value(' . var_export($m[1], true) . ', $vars);' . "\n";
                continue;
            }

            // {include file="header.tpl"}
            if (preg_match('/^\{\s*include\s+file\s*=\s*"([^"]+)"\s*\}$/i', $s, $m)) {
                $php .= '$__out .= \\Template\\DSL\\Runtime::includeFile(' . var_export($m[1], true) . ', $vars);' . "\n";
                continue;
            }

            // {module:news ...}
            if (preg_match('/^\{\s*module:([a-zA-Z0-9_\-]+)(.*?)\}$/is', $s, $m)) {
                $args = trim($m[2] ?? '');
                $php .= '$__out .= \\Template\\DSL\\Runtime::module('
                    . var_export($m[1], true) . ', '
                    . var_export($args, true) . ', $vars);' . "\n";
                continue;
            }

            // [if condition] / [else] / [/if]
            if (preg_match('/^\[if\s+(.+)\]$/i', $s, $m)) {
                $php .= 'if (\\Template\\DSL\\Runtime::cond(' . var_export($m[1], true) . ', $vars)) {' . "\n";
                continue;
            }
            if (preg_match('/^\[else\]$/i', $s)) { $php .= '} else {' . "\n"; continue; }
            if (preg_match('/^\[\/if\]$/i', $s)) { $php .= '}' . "\n"; continue; }

            // [group=...] / [not-group=...]
            if (preg_match('/^\[(group|not-group)\s*=\s*([^\]]+)\]$/i', $s, $m)) {
                $php .= 'if (\\Template\\DSL\\Runtime::groupCheck('
                    . var_export(strtolower($m[1]), true) . ', '
                    . var_export($m[2], true) . ', $vars)) {' . "\n";
                continue;
            }
            if (preg_match('/^\[\/(group|not-group)\]$/i', $s)) { $php .= '}' . "\n"; continue; }

            // [available=...]
            if (preg_match('/^\[available\s*=\s*([^\]]+)\]$/i', $s, $m)) {
                $php .= 'if (\\Template\\DSL\\Runtime::available(' . var_export($m[1], true) . ')) {' . "\n";
                continue;
            }
            if (preg_match('/^\[\/available\]$/i', $s)) { $php .= '}' . "\n"; continue; }

            // fallback: raw text
            $php .= '$__out .= ' . var_export($s, true) . ';' . "\n";
        }

        $php .= "\n" . 'echo $__out;' . "\n";
        $php .= 'return $__out;' . "\n";

        return $php;
    }
}
