<?php
namespace Permissions;

class Policy
{
    /**
     * Determine if a user may perform an action.
     * $ctx can include: workspace_id, content_id, type, owner_id, etc.
     */
    public static function allows(array $user, string $perm, array $ctx = []): bool
    {
        $rbac = new RBAC();
        return $rbac->allows($user, $perm, $ctx);
    }
}
