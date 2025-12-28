<?php
namespace Template\DSL;

class Compiler
{
    public static function compile(string $tpl): string
    {
        $tokens = Tokenizer::tokenize($tpl);

        $php = "<?php\n";
        $php .= "// Compiled by CajeerEngine Template DSL (v2.0)\n";
        $php .= "$__out = '';\n";

        foreach ($tokens as $t) {
            if ($t['type'] === 'TEXT') {
                $php .= "$__out .= " . var_export($t['v'], true) . ";\n";
                continue;
            }
            $s = $t['v'];

            if (preg_match('/^\{\s*([a-zA-Z0-9_.:-]+)\s*\}$/', $s, $m)) {
                $php .= "$__out .= \Template\DSL\Runtime::value(" . var_export($m[1], true) . ", $vars);\n";
                continue;
            }
            if (preg_match('/^\{\s*include\s+file\s*=\s*"([^"]+)"\s*\}$/i', $s, $m)) {
                $php .= "$__out .= \Template\DSL\Runtime::includeFile(" . var_export($m[1], true) . ", $vars);\n";
                continue;
            }
            if (preg_match('/^\{\s*module:([a-zA-Z0-9_\-]+)(.*?)\}$/is', $s, $m)) {
                $php .= "$__out .= \Template\DSL\Runtime::module(" . var_export($m[1], true) . ", " . var_export(trim($m[2] ?? ''), true) . ", $vars);\n";
                continue;
            }

            if (preg_match('/^\[if\s+(.+)\]$/i', $s, $m)) {
                $php .= "if (\Template\DSL\Runtime::cond(" . var_export($m[1], true) . ", $vars)) {\n";
                continue;
            }
            if (preg_match('/^\[else\]$/i', $s)) { $php .= "} else {\n"; continue; }
            if (preg_match('/^\[\/if\]$/i', $s)) { $php .= "}\n"; continue; }

            if (preg_match('/^\[(group|not-group)\s*=\s*([^\]]+)\]$/i', $s, $m)) {
                $php .= "if (\Template\DSL\Runtime::groupCheck(" . var_export(strtolower($m[1]), true) . ", " . var_export($m[2], true) . ", $vars)) {\n";
                continue;
            }
            if (preg_match('/^\[\/(group|not-group)\]$/i', $s)) { $php .= "}\n"; continue; }

            if (preg_match('/^\[available\s*=\s*([^\]]+)\]$/i', $s, $m)) {
                $php .= "if (\Template\DSL\Runtime::available(" . var_export($m[1], true) . ")) {\n";
                continue;
            }
            if (preg_match('/^\[\/available\]$/i', $s)) { $php .= "}\n"; continue; }

            $php .= "$__out .= " . var_export($s, true) . ";\n";
        }

        $php .= "return $__out;\n";
        return $php;
    }
}
