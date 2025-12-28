<?php
namespace Marketplace;

class Semver
{
    public static function normalize(string $v): array
    {
        $v = trim($v);
        $v = ltrim($v, 'v');
        $parts = explode('.', $v);
        return [(int)($parts[0] ?? 0), (int)($parts[1] ?? 0), (int)($parts[2] ?? 0)];
    }

    public static function cmp(string $a, string $b): int
    {
        $A = self::normalize($a); $B = self::normalize($b);
        for ($i=0;$i<3;$i++){
            if ($A[$i] < $B[$i]) return -1;
            if ($A[$i] > $B[$i]) return 1;
        }
        return 0;
    }

    public static function satisfies(string $version, string $constraint): bool
    {
        $constraint = trim($constraint);
        if ($constraint === '' || $constraint === '*') return true;

        if (str_starts_with($constraint, '^')) {
            $base = substr($constraint, 1);
            $b = self::normalize($base); $v = self::normalize($version);
            if ($b[0] == 0) return $v[0]==0 && $v[1]==$b[1] && self::cmp($version, $base) >= 0;
            return $v[0]==$b[0] && self::cmp($version, $base) >= 0;
        }

        if (str_starts_with($constraint, '~')) {
            $base = substr($constraint, 1);
            $b = self::normalize($base); $v = self::normalize($version);
            return $v[0]==$b[0] && $v[1]==$b[1] && self::cmp($version, $base) >= 0;
        }

        if (preg_match('/^(>=|<=|>|<|=)\s*(.+)$/', $constraint, $m)) {
            $op = $m[1]; $base = trim($m[2]);
            $c = self::cmp($version, $base);
            return match($op){
                '>=' => $c >= 0,
                '<=' => $c <= 0,
                '>'  => $c > 0,
                '<'  => $c < 0,
                '='  => $c == 0,
                default => false,
            };
        }

        return self::cmp($version, $constraint) === 0;
    }
}
