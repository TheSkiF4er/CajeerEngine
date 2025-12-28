<?php
namespace Updater;

class BackupManager
{
    public function __construct(private string $backupsPath)
    {
        $this->backupsPath = rtrim($this->backupsPath, '/');
        if (!is_dir($this->backupsPath)) @mkdir($this->backupsPath, 0775, true);
    }

    public function create(string $label = 'auto'): string
    {
        $id = date('Ymd_His') . '_' . preg_replace('/[^a-z0-9_-]+/i','-', $label);
        $file = $this->backupsPath . '/backup_' . $id . '.zip';

        $z = new \ZipArchive();
        if ($z->open($file, \ZipArchive::CREATE) !== true) {
            throw new \RuntimeException('Cannot create backup: '.$file);
        }

        $root = realpath(ROOT_PATH);
        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS));
        foreach ($it as $f) {
            if (!$f->isFile()) continue;

            $path = $f->getPathname();

            // skip runtime folders
            if (str_contains($path, DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR)) continue;
            if (str_contains($path, DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'backups' . DIRECTORY_SEPARATOR)) continue;
            if (str_contains($path, DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'updates' . DIRECTORY_SEPARATOR)) continue;

            $rel = substr($path, strlen($root) + 1);
            $z->addFile($path, $rel);
        }

        $z->close();
        return $file;
    }

    public function restore(string $backupZip): void
    {
        if (!is_file($backupZip)) throw new \RuntimeException('Backup not found: '.$backupZip);

        $z = new \ZipArchive();
        if ($z->open($backupZip) !== true) throw new \RuntimeException('Cannot open backup: '.$backupZip);

        $root = realpath(ROOT_PATH);
        $z->extractTo($root);
        $z->close();
    }
}
