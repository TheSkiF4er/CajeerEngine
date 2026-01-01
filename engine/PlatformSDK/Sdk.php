<?php
namespace PlatformSDK;

class Sdk
{
    public static function platformConfigPath(): string
    {
        return ROOT_PATH . '/system/platform.yaml';
    }

    public static function readPlatformYaml(): string
    {
        $p = self::platformConfigPath();
        return is_file($p) ? file_get_contents($p) : '';
    }
}
