<?php
namespace Template;

class TemplateParser
{
    public function toPhp(string $tpl): string
    {
        $tpl = str_replace(['<?', '?>'], ['&lt;?', '?&gt;'], $tpl);

        // {include file="header.tpl"}
        $tpl = preg_replace_callback('/\{\s*include\s+file\s*=\s*"([^"]+)"\s*\}/i', function($m) {
            $file = addslashes($m[1]);
            return "<?php \$__tpl->include(\"{$file}\", \$__ctx); ?>";
        }, $tpl);

        // {module:news limit="5"}
        $tpl = preg_replace_callback('/\{\s*module:([a-zA-Z0-9_\-]+)\s*([^}]*)\}/', function($m) {
            $name = addslashes($m[1]);
            $params = $this->parseAttrs(trim($m[2] ?? ''));
            $paramsPhp = var_export($params, true);
            return "<?php echo \$__tpl->module('{$name}', {$paramsPhp}, \$__ctx); ?>";
        }, $tpl);

        // [if expr] ... [else] ... [/if]
        $tpl = preg_replace_callback('/\[if\s+([^\]]+)\]/i', function($m) {
            $expr = TemplateExpr::toPhp(trim($m[1]));
            return "<?php if ({$expr}) { ?>";
        }, $tpl);
        $tpl = preg_replace('/\[else\]/i', "<?php } else { ?>", $tpl);
        $tpl = preg_replace('/\[\/if\]/i', "<?php } ?>", $tpl);

        // [group=1,2]
        $tpl = preg_replace_callback('/\[group=([^\]]+)\]/i', function($m) {
            $expr = TemplateExpr::groupExpr($m[1], false);
            return "<?php if ({$expr}) { ?>";
        }, $tpl);
        $tpl = preg_replace('/\[\/group\]/i', "<?php } ?>", $tpl);

        // [not-group=...]
        $tpl = preg_replace_callback('/\[not-group=([^\]]+)\]/i', function($m) {
            $expr = TemplateExpr::groupExpr($m[1], true);
            return "<?php if ({$expr}) { ?>";
        }, $tpl);
        $tpl = preg_replace('/\[\/not-group\]/i', "<?php } ?>", $tpl);

        // [available=main,news]
        $tpl = preg_replace_callback('/\[available=([^\]]+)\]/i', function($m) {
            $expr = TemplateExpr::availableExpr($m[1]);
            return "<?php if ({$expr}) { ?>";
        }, $tpl);
        $tpl = preg_replace('/\[\/available\]/i', "<?php } ?>", $tpl);

        // {config path.to.key}
        $tpl = preg_replace_callback('/\{\s*config\s+([a-zA-Z0-9_\.]+)\s*\}/', function($m) {
            $path = addslashes($m[1]);
            return "<?php echo TemplateValue::getConfig('{$path}', \$__ctx); ?>";
        }, $tpl);

        // {user.name}
        $tpl = preg_replace_callback('/\{\s*user\.([a-zA-Z0-9_\.]+)\s*\}/', function($m) {
            $path = addslashes($m[1]);
            return "<?php echo TemplateValue::getUser('{$path}', \$__ctx); ?>";
        }, $tpl);

        // {var}
        $tpl = preg_replace_callback('/\{\s*([a-zA-Z0-9_\.]+)\s*\}/', function($m) {
            $key = $m[1];
            $lk = strtolower($key);
            if ($lk === 'include' || $lk === 'config' || str_starts_with($lk, 'module:') || str_starts_with($lk, 'user.')) {
                return $m[0];
            }
            return "<?php echo TemplateValue::getVar('" . addslashes($key) . "', \$__ctx); ?>";
        }, $tpl);

        return $tpl;
    }

    private function parseAttrs(string $raw): array
    {
        $out = [];
        if (!$raw) return $out;
        if (preg_match_all('/([a-zA-Z0-9_\-]+)\s*=\s*"([^"]*)"/', $raw, $mm, PREG_SET_ORDER)) {
            foreach ($mm as $m) {
                $k = $m[1];
                $v = $m[2];
                if (preg_match('/^\d+$/', $v)) $v = (int)$v;
                $out[$k] = $v;
            }
        }
        return $out;
    }
}
