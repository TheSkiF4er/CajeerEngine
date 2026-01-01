<?php
namespace AIAssist;

class Redactor
{
    public function __construct(protected array $rules = []) {}
    public function apply(string $text): string
    {
        $out = $text;
        foreach ($this->rules as $r) {
            if (($r['type'] ?? '') === 'regex') {
                $pattern = (string)($r['pattern'] ?? '');
                $replace = (string)($r['replace'] ?? '');
                if ($pattern !== '') $out = preg_replace($pattern, $replace, $out) ?? $out;
            }
        }
        return $out;
    }
}
