<?php
namespace Security;

class XSS
{
    public static function e(string $s): string
    {
        return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    // Basic attribute sanitizer (allows only safe url schemes)
    public static function safeUrl(string $url): string
    {
        $url = trim($url);
        if ($url === '') return '';
        $lower = strtolower($url);
        if (str_starts_with($lower, 'javascript:') || str_starts_with($lower, 'data:')) return '';
        return $url;
    }
}
