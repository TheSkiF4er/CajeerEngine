<?php
namespace Core;

class Workspace
{
    public static function currentId(): int
    {
        $cfg = is_file(ROOT_PATH.'/system/workspaces.php') ? require ROOT_PATH.'/system/workspaces.php' : ['enabled'=>false,'default'=>1];
        if (empty($cfg['enabled'])) return 0;

        // Accept workspace by header or query param
        $ws = 0;
        if (!empty($_SERVER['HTTP_X_WORKSPACE_ID'])) $ws = (int)$_SERVER['HTTP_X_WORKSPACE_ID'];
        if (!$ws && isset($_GET['workspace_id'])) $ws = (int)$_GET['workspace_id'];
        if (!$ws) $ws = (int)($cfg['default'] ?? 1);
        return $ws;
    }
}
