<?php
namespace Ops;

use Observability\Logger;

class Hooks
{
    public static function sla(string $event, array $data = []): void
    {
        $cfg = is_file(ROOT_PATH.'/system/ops.php') ? require ROOT_PATH.'/system/ops.php' : [];
        $sla = (array)($cfg['sla'] ?? []);
        if (empty($sla['enabled']) || empty($sla['webhook_url'])) return;

        // foundation: do not send network calls in skeleton; just log intent
        Logger::info('sla.hook', ['event'=>$event,'data'=>$data,'url'=>$sla['webhook_url']]);
    }

    public static function incident(string $event, array $data = []): void
    {
        $cfg = is_file(ROOT_PATH.'/system/ops.php') ? require ROOT_PATH.'/system/ops.php' : [];
        $inc = (array)($cfg['incident'] ?? []);
        if (empty($inc['enabled']) || empty($inc['webhook_url'])) return;

        Logger::warn('incident.hook', ['event'=>$event,'data'=>$data,'url'=>$inc['webhook_url']]);
    }
}
