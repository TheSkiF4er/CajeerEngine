<?php
namespace Platform;

class Isolation
{
    /**
     * Returns SQL fragment and params to enforce tenant/site isolation for content queries.
     * Intended to be used by repositories/query builder.
     */
    public static function whereTenantSite(int $tenantId, int $siteId): array
    {
        $sql = "tenant_id = :tenant_id";
        $params = [':tenant_id'=>$tenantId];
        if ($siteId > 0) {
            $sql .= " AND site_id = :site_id";
            $params[':site_id'] = $siteId;
        }
        return [$sql, $params];
    }
}
