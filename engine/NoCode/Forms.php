<?php
namespace NoCode;

use Database\DB;

class Forms
{
    public static function ensureSchema(): void
    {
        $pdo = DB::pdo(); if(!$pdo) return;
        $sql = ROOT_PATH . '/system/sql/frontend_v3_4.sql';
        if (is_file($sql)) $pdo->exec(file_get_contents($sql));
    }

    public static function submit(string $slug, array $payload, int $tenantId = 0): array
    {
        self::ensureSchema();
        $pdo = DB::pdo(); if(!$pdo) return ['ok'=>false,'error'=>'db_required'];

        $form = $pdo->query("SELECT * FROM ce_forms WHERE tenant_id=".(int)$tenantId." AND slug=".$pdo->quote($slug)." LIMIT 1")
                    ->fetch(\PDO::FETCH_ASSOC);
        if (!$form) return ['ok'=>false,'error'=>'form_not_found'];

        $pdo->prepare("INSERT INTO ce_form_submissions(tenant_id,form_slug,payload_json,ip,ua,created_at)
                       VALUES(:t,:s,:p,:ip,:ua,NOW())")
            ->execute([
                ':t'=>$tenantId,':s'=>$slug,':p'=>json_encode($payload, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
                ':ip'=>$_SERVER['REMOTE_ADDR'] ?? null, ':ua'=>$_SERVER['HTTP_USER_AGENT'] ?? null,
            ]);

        return ['ok'=>true];
    }
}
