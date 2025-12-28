<?php
namespace Template;

/**
 * Best-effort adapter for common DLE tags and blocks.
 * Conservative: unsupported tags remain as-is and should be handled manually.
 */
class DleTagAdapter
{
    public static function preprocess(string $tpl): string
    {
        $tpl = str_ireplace(['[logged]','[/logged]'], ['[if user.logged]','[/if]'], $tpl);
        $tpl = str_ireplace(['[not-logged]','[/not-logged]'], ['[if not user.logged]','[/if]'], $tpl);

        $tpl = str_ireplace('{THEME}', '{theme_url}', $tpl);
        $tpl = str_ireplace('{home-url}', '{base_url}', $tpl);

        return $tpl;
    }
}
