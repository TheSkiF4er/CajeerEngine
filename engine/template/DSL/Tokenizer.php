<?php
namespace Template\DSL;

class Tokenizer
{
    public static function tokenize(string $tpl): array
    {
        $pattern = '/(\{[^\}]+\}|\[(?:\/)?(?:if|else|group|not-group|available)[^\]]*\])/i';
        $parts = preg_split($pattern, $tpl, -1, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);
        $out = [];
        foreach ($parts as $p) {
            if (preg_match('/^\{.+\}$/s', $p) || preg_match('/^\[.+\]$/s', $p)) $out[] = ['type'=>'SYNTAX','v'=>$p];
            else $out[] = ['type'=>'TEXT','v'=>$p];
        }
        return $out;
    }
}
