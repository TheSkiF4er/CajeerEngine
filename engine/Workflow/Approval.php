<?php
namespace Workflow;

use Database\DB;

class Approval
{
    public static function ensureSchema(): void
    {
        $pdo = DB::pdo();
        if (!$pdo) return;

        $pdo->exec("CREATE TABLE IF NOT EXISTS ce_workflow_approvals (
            id INT NOT NULL AUTO_INCREMENT,
            content_id INT NOT NULL,
            state VARCHAR(20) NOT NULL,
            requested_by INT NULL,
            approved_by INT NULL,
            scheduled_at DATETIME NULL,
            created_at DATETIME NULL,
            updated_at DATETIME NULL,
            notes TEXT NULL,
            PRIMARY KEY (id),
            KEY idx_content (content_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }

    public static function requestReview(int $contentId, int $userId, string $notes=''): void
    {
        self::ensureSchema();
        $pdo = DB::pdo(); if(!$pdo) return;
        $pdo->prepare("INSERT INTO ce_workflow_approvals(content_id,state,requested_by,created_at,updated_at,notes)
            VALUES(:cid,'review',:u,NOW(),NOW(),:n)")
            ->execute([':cid'=>$contentId, ':u'=>$userId, ':n'=>$notes]);
    }

    public static function approve(int $contentId, int $approverId): void
    {
        self::ensureSchema();
        $pdo = DB::pdo(); if(!$pdo) return;
        $pdo->prepare("UPDATE ce_workflow_approvals SET state='approved', approved_by=:a, updated_at=NOW()
            WHERE content_id=:cid ORDER BY id DESC LIMIT 1")
            ->execute([':cid'=>$contentId, ':a'=>$approverId]);
    }
}
