<?php
namespace Template;

class TemplateExpr
{
    /**
     * Мини-язык выражений для [if ...]
     * Поддержка:
     * - logged / !logged
     * - group=1,2
     * - not-group=1,2
     * - available=main,news
     */
    public static function toPhp(string $expr): string
    {
        $expr = trim($expr);

        if (preg_match('/^!\s*logged$/i', $expr)) {
            return '!(bool)($__ctx[\'user\'][\'logged\'] ?? false)';
        }
        if (preg_match('/^logged$/i', $expr)) {
            return '(bool)($__ctx[\'user\'][\'logged\'] ?? false)';
        }
        if (preg_match('/^group\s*=\s*(.+)$/i', $expr, $m)) {
            return self::groupExpr($m[1], false);
        }
        if (preg_match('/^not-group\s*=\s*(.+)$/i', $expr, $m)) {
            return self::groupExpr($m[1], true);
        }
        if (preg_match('/^available\s*=\s*(.+)$/i', $expr, $m)) {
            return self::availableExpr($m[1]);
        }
        return 'false';
    }

    public static function groupExpr(string $list, bool $negate): string
    {
        $items = array_filter(array_map('trim', explode(',', $list)), fn($v)=>$v!=='');
        $items = array_map('intval', $items);
        $arr = var_export($items, true);
        $base = "in_array((int)(\$__ctx['user']['group'] ?? 0), {$arr}, true)";
        return $negate ? "!({$base})" : $base;
    }

    public static function availableExpr(string $list): string
    {
        $items = array_filter(array_map('trim', explode(',', $list)), fn($v)=>$v!=='');
        $arr = var_export($items, true);
        return "in_array((string)(\$__ctx['page'] ?? ''), {$arr}, true)";
    }
}
