<?php
namespace Content;

class Slug
{
    public static function normalize(string $s): string
    {
        $s = trim(mb_strtolower($s, 'UTF-8'));
        $s = preg_replace('/[^a-z0-9\p{Cyrillic}\s\-]/u', '', $s);
        $s = preg_replace('/\s+/u', '-', $s);
        $s = preg_replace('/\-+/u', '-', $s);
        $s = trim($s, '-');
        return $s ?: 'item';
    }
}
