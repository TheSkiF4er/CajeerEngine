<?php
namespace Security;

use Database\DB;

class AccessLog
{
    public static function hashRow(array $row, ?string $prevHash): string
    {
        $base = json_encode([
            'tenant_id'=>$row['tenant_id'] ?? 0,
            'user_id'=>$row['user_id'] ?? null,
            'method'=>$row['method'] ?? '',
            'path'=>$row['path'] ?? '',
            'status'=>$row['status'] ?? 0,
            'ip'=>$row['ip'] ?? null,
            'ua'=>$row['ua'] ?? null,
            'scopes'=>$row['scopes_json'] ?? null,
            'decision'=>$row['decision'] ?? 'allow',
            'reason'=>$row['reason'] ?? null,
            'ts'=>$row['ts'] ?? '',
            'prev_hash'=>$prevHash ?? null,
        ], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        return hash('sha256', $base);
    }

    public static function append(array $row): void
    {
        $pdo = DB::pdo(); if(!$pdo) return;

        $prev = $pdo->query("SELECT hash FROM ce_access_logs ORDER BY id DESC LIMIT 1")->fetch(\PDO::FETCH_ASSOC);
        $prevHash = $prev['hash'] ?? null;

        $row['prev_hash'] = $prevHash;
        $row['hash'] = self::hashRow($row, $prevHash);

        $st = $pdo->prepare("INSERT INTO ce_access_logs(tenant_id,user_id,method,path,status,ip,ua,scopes_json,decision,reason,ts,prev_hash,hash)
                             VALUES(:t,:u,:m,:p,:s,:ip,:ua,:sc,:d,:r,:ts,:ph,:h)");
        $st->execute([
            ':t'=>(int)($row['tenant_id'] ?? 0),
            ':u'=>$row['user_id'] ?? null,
            ':m'=>(string)($row['method'] ?? ''),
            ':p'=>(string)($row['path'] ?? ''),
            ':s'=>(int)($row['status'] ?? 0),
            ':ip'=>$row['ip'] ?? null,
            ':ua'=>$row['ua'] ?? null,
            ':sc'=>$row['scopes_json'] ?? null,
            ':d'=>(string)($row['decision'] ?? 'allow'),
            ':r'=>$row['reason'] ?? null,
            ':ts'=>(string)($row['ts'] ?? date('Y-m-d H:i:s')),
            ':ph'=>$prevHash,
            ':h'=>$row['hash'],
        ]);
    }

    public static function verifyChain(int $limit = 10000): array
    {
        $pdo = DB::pdo(); if(!$pdo) return ['ok'=>false,'error'=>'db_required'];

        $rows = $pdo->query("SELECT * FROM ce_access_logs ORDER BY id ASC LIMIT ".(int)$limit)->fetchAll(\PDO::FETCH_ASSOC);
        $prev = null;
        foreach ($rows as $r) {
            $calc = self::hashRow($r, $prev);
            if (($r['prev_hash'] ?? null) !== $prev) {
                return ['ok'=>false,'error'=>'prev_hash_mismatch','id'=>(int)$r['id']];
            }
            if (($r['hash'] ?? '') !== $calc) {
                return ['ok'=>false,'error'=>'hash_mismatch','id'=>(int)$r['id']];
            }
            $prev = $r['hash'];
        }
        return ['ok'=>true,'count'=>count($rows)];
    }
}
