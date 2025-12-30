<?php
namespace Security;

use Database\DB;

class Compliance
{
    public static function generateSOC2(int $tenantId, ?string $from = null, ?string $to = null): array
    {
        $pdo = DB::pdo(); if(!$pdo) return ['ok'=>false,'error'=>'db_required'];

        $from = $from ?: date('Y-m-01 00:00:00');
        $to = $to ?: date('Y-m-d 23:59:59');

        $count = (int)$pdo->query("SELECT COUNT(*) c FROM ce_access_logs WHERE tenant_id=".(int)$tenantId." AND ts BETWEEN ".$pdo->quote($from)." AND ".$pdo->quote($to))->fetch(\PDO::FETCH_ASSOC)['c'];

        $report = [
            'type' => 'soc2',
            'tenant_id' => $tenantId,
            'period' => ['from'=>$from,'to'=>$to],
            'controls' => [
                'access_logging' => ['enabled'=>true,'records'=>$count],
                'immutable_chain' => AccessLog::verifyChain(10000),
            ],
            'notes' => 'Foundation report for SOC2/ISO preparation (not an audit).',
        ];

        $pdo->prepare("INSERT INTO ce_compliance_reports(tenant_id,type,period_start,period_end,report_json,created_at)
                       VALUES(:t,'soc2',:f,:to,:r,NOW())")
            ->execute([
                ':t'=>$tenantId,
                ':f'=>$from,
                ':to'=>$to,
                ':r'=>json_encode($report, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
            ]);

        return ['ok'=>true,'report'=>$report];
    }
}
