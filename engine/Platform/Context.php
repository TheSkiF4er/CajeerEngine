<?php
namespace Platform;

class Context
{
    public static function tenantId(): int { return (int)($_SERVER['CE_TENANT_ID'] ?? 0); }
    public static function siteId(): int { return (int)($_SERVER['CE_SITE_ID'] ?? 0); }

    public static function setTenant(int $id): void { $_SERVER['CE_TENANT_ID'] = $id; }
    public static function setSite(int $id): void { $_SERVER['CE_SITE_ID'] = $id; }
}
