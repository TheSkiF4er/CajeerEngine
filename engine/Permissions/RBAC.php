<?php
namespace Permissions;

use Database\DB;

class RBAC
{
    public function allows(array $user, string $perm, array $ctx = []): bool
    {
        // Superuser shortcut
        if (!empty($user['is_admin']) && (int)$user['is_admin'] === 1) return true;

        $workspaceId = (int)($ctx['workspace_id'] ?? ($user['workspace_id'] ?? 0));
        if ($workspaceId && isset($user['workspace_id']) && (int)$user['workspace_id'] !== $workspaceId) {
            // workspace isolation by default
            return false;
        }

        $pdo = DB::pdo();
        if (!$pdo) return false;

        $pdo->exec("CREATE TABLE IF NOT EXISTS ce_rbac_roles (
            id INT NOT NULL AUTO_INCREMENT,
            workspace_id INT NOT NULL DEFAULT 1,
            name VARCHAR(64) NOT NULL,
            title VARCHAR(190) NULL,
            PRIMARY KEY (id),
            UNIQUE KEY uq_ws_name (workspace_id, name)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $pdo->exec("CREATE TABLE IF NOT EXISTS ce_rbac_permissions (
            id INT NOT NULL AUTO_INCREMENT,
            perm_key VARCHAR(190) NOT NULL,
            title VARCHAR(190) NULL,
            PRIMARY KEY (id),
            UNIQUE KEY uq_perm (perm_key)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $pdo->exec("CREATE TABLE IF NOT EXISTS ce_rbac_role_permissions (
            role_id INT NOT NULL,
            perm_key VARCHAR(190) NOT NULL,
            PRIMARY KEY (role_id, perm_key)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $pdo->exec("CREATE TABLE IF NOT EXISTS ce_rbac_user_roles (
            user_id INT NOT NULL,
            role_id INT NOT NULL,
            PRIMARY KEY (user_id, role_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // per-content grants
        $pdo->exec("CREATE TABLE IF NOT EXISTS ce_rbac_content_grants (
            content_id INT NOT NULL,
            user_id INT NULL,
            role_id INT NULL,
            perm_key VARCHAR(190) NOT NULL,
            PRIMARY KEY (content_id, perm_key, user_id, role_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $uid = (int)($user['id'] ?? 0);
        if ($uid <= 0) return false;

        // If content-specific requested
        $contentId = (int)($ctx['content_id'] ?? 0);
        if ($contentId > 0) {
            // direct user grant
            $st = $pdo->prepare("SELECT 1 FROM ce_rbac_content_grants WHERE content_id=:cid AND user_id=:uid AND perm_key=:p LIMIT 1");
            $st->execute([':cid'=>$contentId, ':uid'=>$uid, ':p'=>$perm]);
            if ($st->fetchColumn()) return true;

            // role grant
            $st = $pdo->prepare("SELECT 1
              FROM ce_rbac_content_grants g
              JOIN ce_rbac_user_roles ur ON ur.role_id = g.role_id
              WHERE g.content_id=:cid AND ur.user_id=:uid AND g.perm_key=:p
              LIMIT 1");
            $st->execute([':cid'=>$contentId, ':uid'=>$uid, ':p'=>$perm]);
            if ($st->fetchColumn()) return true;
        }

        // workspace-level role permission
        $st = $pdo->prepare("SELECT 1
            FROM ce_rbac_user_roles ur
            JOIN ce_rbac_roles r ON r.id = ur.role_id
            JOIN ce_rbac_role_permissions rp ON rp.role_id = r.id
            WHERE ur.user_id=:uid AND r.workspace_id=:ws AND rp.perm_key=:p
            LIMIT 1");
        $st->execute([':uid'=>$uid, ':ws'=>$workspaceId ?: (int)($user['workspace_id'] ?? 1), ':p'=>$perm]);
        return (bool)$st->fetchColumn();
    }
}
