<?php
namespace Security;

class IpFilter
{
    public static function allowed(string $ip, array $cfg): bool
    {
        if (empty($cfg['enabled'])) return true;

        $deny = (array)($cfg['deny'] ?? []);
        foreach ($deny as $rule) {
            if (self::match($ip, (string)$rule)) return false;
        }

        $allow = (array)($cfg['allow'] ?? []);
        if (!$allow) return true; // if allow list empty -> allow all (except deny)
        foreach ($allow as $rule) {
            if (self::match($ip, (string)$rule)) return true;
        }
        return false;
    }

    protected static function match(string $ip, string $rule): bool
    {
        $rule = trim($rule);
        if ($rule === '') return false;
        if (!str_contains($rule, '/')) return $ip === $rule;

        [$sub, $bits] = explode('/', $rule, 2);
        $bits = (int)$bits;
        $ipL = ip2long($ip);
        $subL = ip2long($sub);
        if ($ipL === false || $subL === false) return false;
        $mask = -1 << (32 - $bits);
        return (($ipL & $mask) === ($subL & $mask));
    }
}
