<?php
namespace Updater;

class Updater
{
    private array $cfg;

    public function __construct()
    {
        $this->cfg = is_file(ROOT_PATH . '/system/updater.php')
          ? (array)require ROOT_PATH . '/system/updater.php'
          : [];
    }

    public function manifest(): Manifest
    {
        $src = $this->cfg['manifest'] ?? (ROOT_PATH . '/system/updates/manifest.json');
        $raw = null;

        if (is_string($src) && preg_match('/^https?:\/\//i', $src)) {
            $raw = @file_get_contents($src);
        } else {
            $raw = @file_get_contents((string)$src);
        }

        $data = json_decode((string)$raw, true);
        if (!is_array($data)) $data = ['channels'=>['stable'=>[],'beta'=>[]]];
        return new Manifest($data);
    }

    public function channel(): string
    {
        return (string)($this->cfg['channel'] ?? 'stable');
    }

    public function check(): array
    {
        $items = $this->manifest()->channel($this->channel());
        usort($items, fn($a,$b)=>strcmp((string)($b['version']??''), (string)($a['version']??'')));
        return $items;
    }

    public function backup(string $label='auto'): string
    {
        $bm = new BackupManager((string)($this->cfg['backups_path'] ?? (ROOT_PATH.'/storage/backups')));
        return $bm->create($label);
    }

    public function restore(string $backupZip): void
    {
        $bm = new BackupManager((string)($this->cfg['backups_path'] ?? (ROOT_PATH.'/storage/backups')));
        $bm->restore($backupZip);
    }

    public function apply(string $packageFile, string $label='before_update'): array
    {
        $backup = $this->backup($label);
        $applier = new Applier((string)($this->cfg['updates_path'] ?? (ROOT_PATH.'/storage/updates')));
        $res = $applier->apply($packageFile);
        $res['backup'] = $backup;
        return $res;
    }
}
