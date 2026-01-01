<?php
namespace Seo;

class Html
{
    /**
     * Adds loading="lazy" to <img> tags where missing.
     */
    public static function lazyImages(string $html): string
    {
        return preg_replace_callback('/<img\b([^>]*?)>/i', function($m) {
            $attrs = $m[1];
            if (stripos($attrs, 'loading=') !== false) return '<img'.$attrs.'>';
            return '<img'.$attrs.' loading="lazy">';
        }, $html) ?? $html;
    }
}
