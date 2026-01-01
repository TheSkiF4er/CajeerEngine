<?php
namespace Frontend;

use Database\DB;

class ISRCache
{
    public static function ensureSchema(): void
    {
        $pdo = DB::pdo(); if(!$pdo) return;
        $sql = ROOT_PATH . '/system/sql/frontend_v3_4.sql';
        if (is_file($sql)) $pdo->exec(file_get_contents($sql));
    }

    public static function get(string $cacheKey, int $tenantId = 0): ?array
    {
        self::ensureSchema();
        $pdo = DB::pdo(); if(!$pdo) return null;

        $st = $pdo->prepare("SELECT * FROM ce_isr_cache WHERE tenant_id=:t AND cache_key=:k AND expires_at > NOW() LIMIT 1");
        $st->execute([':t'=>$tenantId,':k'=>$cacheKey]);
        $r = $st->fetch(\PDO::FETCH_ASSOC);
        return $r ?: null;
    }

    public static function put(string $cacheKey, string $body, string $contentType='text/html', int $status=200, int $ttlSec=60, ?string $surrogateKeys=null, int $tenantId=0): void
    {
        self::ensureSchema();
        $pdo = DB::pdo(); if(!$pdo) return;

        $expires = date('Y-m-d H:i:s', time()+max(1,$ttlSec));
        $st = $pdo->prepare("INSERT INTO ce_isr_cache(tenant_id,cache_key,surrogate_keys,body,content_type,status,created_at,expires_at)
                             VALUES(:t,:k,:sk,:b,:ct,:s,NOW(),:e)
                             ON DUPLICATE KEY UPDATE surrogate_keys=:sk2, body=:b2, content_type=:ct2, status=:s2, created_at=NOW(), expires_at=:e2");
        $st->execute([
            ':t'=>$tenantId,':k'=>$cacheKey,':sk'=>$surrogateKeys,':b'=>$body,':ct'=>$contentType,':s'=>$status,':e'=>$expires,
            ':sk2'=>$surrogateKeys,':b2'=>$body,':ct2'=>$contentType,':s2'=>$status,':e2'=>$expires,
        ]);
    }

    public static function purgeBySurrogate(string $surrogateKey, int $tenantId=0): int
    {
        self::ensureSchema();
        $pdo = DB::pdo(); if(!$pdo) return 0;

        $st = $pdo->prepare("DELETE FROM ce_isr_cache WHERE tenant_id=:t AND (surrogate_keys LIKE :p)");
        $st->execute([':t'=>$tenantId,':p'=>'%'.$surrogateKey.'%']);
        return $st->rowCount();
    }
}
