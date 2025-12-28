<?php
namespace Plugins;

class Semver
{
    public static function parse(string $v): array
    {
        $v = trim($v);
        $v = ltrim($v, 'vV');
        $parts = explode('.', $v);
        return [(int)($parts[0] ?? 0), (int)($parts[1] ?? 0), (int)($parts[2] ?? 0)];
    }

    public static function cmp(string $a, string $b): int
    {
        return self::parse($a) <=> self::parse($b);
    }

    public static function satisfies(string $version, string $constraint): bool
    {
        $constraint = trim($constraint);
        if ($constraint === '' || $constraint === '*') return true;

        if (preg_match('/^(\d+)\.\*$/', $constraint, $m)) {
            return self::parse($version)[0] === (int)$m[1];
        }
        if (preg_match('/^(\d+)\.(\d+)\.\*$/', $constraint, $m)) {
            $p = self::parse($version);
            return $p[0]==(int)$m[1] && $p[1]==(int)$m[2];
        }

        if (str_starts_with($constraint, '^')) {
            $base = substr($constraint, 1);
            [$M,$m,$p] = self::parse($base);
            $v = self::parse($version);
            if ($M > 0) return $v[0] === $M && self::cmp($version, $base) >= 0;
            if ($m > 0) return $v[0] === 0 && $v[1] === $m && self::cmp($version, $base) >= 0;
            return self::cmp($version, $base) === 0;
        }

        if (str_starts_with($constraint, '~')) {
            $base = substr($constraint, 1);
            [$M,$m,$p] = self::parse($base);
            $v = self::parse($version);
            return $v[0]===$M && $v[1]===$m && self::cmp($version, $base) >= 0;
        }

        if (preg_match('/^(>=|<=|>|<)\s*(.+)$/', $constraint, $m)) {
            $op = $m[1]; $b = trim($m[2]);
            $c = self::cmp($version, $b);
            return match($op) {
                '>' => $c > 0,
                '>=' => $c >= 0,
                '<' => $c < 0,
                '<=' => $c <= 0,
            };
        }

        return self::cmp($version, $constraint) === 0;
    }
}
