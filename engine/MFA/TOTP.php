<?php
namespace MFA;

/**
 * TOTP (RFC 6238) implementation (no external libs).
 */
class TOTP
{
    public static function generateSecret(int $bytes = 20): string
    {
        return base64_encode(random_bytes($bytes));
    }

    public static function code(string $secretBase64, int $timeStep = 30, int $digits = 6, ?int $ts = null): string
    {
        $ts = $ts ?? time();
        $counter = intdiv($ts, $timeStep);

        $key = base64_decode($secretBase64, true);
        if ($key === false) $key = '';

        $binCounter = pack('N*', 0) . pack('N*', $counter);
        $hash = hash_hmac('sha1', $binCounter, $key, true);

        $offset = ord($hash[19]) & 0x0f;
        $part = substr($hash, $offset, 4);
        $value = unpack('N', $part)[1] & 0x7fffffff;

        $mod = 10 ** $digits;
        return str_pad((string)($value % $mod), $digits, '0', STR_PAD_LEFT);
    }

    public static function verify(string $secretBase64, string $code, int $window = 1, int $timeStep = 30, int $digits = 6): bool
    {
        $code = preg_replace('/\s+/', '', $code);
        $now = time();
        for ($i=-$window; $i<=$window; $i++) {
            $ts = $now + ($i * $timeStep);
            if (hash_equals(self::code($secretBase64, $timeStep, $digits, $ts), $code)) return true;
        }
        return false;
    }
}
