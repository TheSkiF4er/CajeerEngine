<?php
namespace MarketplaceV4;

use Database\DB;

class Registry
{
    public static function ensureSchema(): void
    {
        $pdo = DB::pdo(); if(!$pdo) return;
        $sql = ROOT_PATH . '/system/sql/platform_v4_0.sql';
        if (is_file($sql)) $pdo->exec(file_get_contents($sql));
    }

    public static function listAI(int $limit=50): array
    {
        $pdo = DB::pdo(); if(!$pdo) return [];
        self::ensureSchema();
        $limit = max(1, min(200, $limit));
        $st = $pdo->query("SELECT * FROM ce_ai_marketplace_items ORDER BY id DESC LIMIT $limit");
        return $st->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    public static function listAutomation(int $limit=50): array
    {
        $pdo = DB::pdo(); if(!$pdo) return [];
        self::ensureSchema();
        $limit = max(1, min(200, $limit));
        $st = $pdo->query("SELECT * FROM ce_automation_marketplace_items ORDER BY id DESC LIMIT $limit");
        return $st->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    public static function listBlueprints(int $limit=50): array
    {
        $pdo = DB::pdo(); if(!$pdo) return [];
        self::ensureSchema();
        $limit = max(1, min(200, $limit));
        $st = $pdo->query("SELECT * FROM ce_solution_blueprints ORDER BY id DESC LIMIT $limit");
        return $st->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }
}
