<?php
namespace Audit;
use Database\DB;

class AuditLogger
{
    public static function log(string $action, array $context = []): void
    {
        $cfgFile = ROOT_PATH . '/system/security.php';
        $cfg = is_file($cfgFile) ? require $cfgFile : ['audit'=>['enabled'=>false]];
        if (empty($cfg['audit']['enabled'])) return;

        $pdo = DB::pdo();
        if (!$pdo) return;

        $pdo->exec("CREATE TABLE IF NOT EXISTS ce_audit_logs (
            id BIGINT NOT NULL AUTO_INCREMENT,
            created_at DATETIME NULL,
            user_id INT NULL,
            workspace_id INT NULL,
            action VARCHAR(190) NOT NULL,
            ip VARCHAR(64) NULL,
            user_agent VARCHAR(255) NULL,
            context_json MEDIUMTEXT NULL,
            PRIMARY KEY (id),
            KEY idx_action (action),
            KEY idx_user (user_id),
            KEY idx_ws (workspace_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $userId = $context['user_id'] ?? null;
        $wsId = $context['workspace_id'] ?? null;

        $ip = null; $ua = null;
        if (!empty($cfg['audit']['store_ip'])) $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        if (!empty($cfg['audit']['store_user_agent'])) $ua = $_SERVER['HTTP_USER_AGENT'] ?? null;

        $payload = json_encode($context, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

        $st = $pdo->prepare("INSERT INTO ce_audit_logs(created_at,user_id,workspace_id,action,ip,user_agent,context_json)
            VALUES(NOW(),:uid,:ws,:a,:ip,:ua,:ctx)");
        $st->execute([
            ':uid'=>$userId, ':ws'=>$wsId, ':a'=>$action,
            ':ip'=>$ip, ':ua'=>$ua, ':ctx'=>$payload,
        ]);
    }
}
