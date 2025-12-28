<?php
namespace Marketplace;

use Database\DB;

class PackageManager
{
    protected array $cfg;
    public function __construct(array $cfg){ $this->cfg = $cfg; }

    public function installFromFile(string $filePath): array
    {
        if (!is_file($filePath)) throw new \Exception("Package file not found");

        $zip = new \ZipArchive();
        if ($zip->open($filePath) !== true) throw new \Exception("Package is not a valid zip");

        $manifestJson = $zip->getFromName('marketplace.json');
        if ($manifestJson === false) throw new \Exception("marketplace.json not found in package");

        $manifest = Manifest::fromJson($manifestJson);

        $this->validateManifest($manifest);
        $this->verifySignature($manifest);
        $this->checkDeps($manifest);

        $backup = $this->backupSnapshot($manifest);

        try {
            $this->applyPayload($zip, $manifest);
            $this->markInstalled($manifest);
            return ['ok'=>true,'installed'=>true,'backup'=>$backup];
        } catch (\Throwable $e) {
            $this->rollbackSnapshot($backup);
            return ['ok'=>false,'error'=>$e->getMessage(),'rolled_back'=>true];
        } finally {
            $zip->close();
        }
    }

    protected function validateManifest(Manifest $m): void
    {
        if (!in_array($m->type(), (array)($this->cfg['types'] ?? []), true)) {
            throw new \Exception("Unsupported package type: " . $m->type());
        }
        if ($m->name() === '' || $m->version() === '') throw new \Exception("Invalid package name/version");

        $engineVersion = $this->engineVersion();
        $constraint = $m->engineConstraint();
        if (!Semver::satisfies($engineVersion, $constraint)) {
            throw new \Exception("Engine version $engineVersion does not satisfy constraint $constraint");
        }
    }

    protected function verifySignature(Manifest $m): void
    {
        if (empty($this->cfg['require_signature'])) return;

        $pubId = $m->publisherId();
        if ($pubId === '') throw new \Exception("Publisher id required");
        $pubKey = $this->trustedKey($pubId);
        if ($pubKey === '') throw new \Exception("Publisher not trusted: $pubId");

        $sig = $m->signatureB64();
        if ($sig === '') throw new \Exception("Missing signature");
        $payload = $m->canonicalJson();

        if (!Signature::verifyEd25519($pubKey, $sig, $payload)) {
            throw new \Exception("Signature verification failed (ed25519). Ensure ext-sodium is installed.");
        }
    }

    protected function trustedKey(string $publisherId): string
    {
        $cfgList = (array)($this->cfg['trusted_publishers'] ?? []);
        if (isset($cfgList[$publisherId])) return (string)$cfgList[$publisherId];

        try {
            $pdo = DB::pdo();
            if (!$pdo) return '';
            $st = $pdo->prepare("SELECT pubkey_ed25519 FROM ce_marketplace_publishers WHERE publisher_id=:id AND trusted=1 LIMIT 1");
            $st->execute([':id'=>$publisherId]);
            $row = $st->fetch(\PDO::FETCH_ASSOC);
            return $row ? (string)$row['pubkey_ed25519'] : '';
        } catch (\Throwable $e) {
            return '';
        }
    }

    protected function checkDeps(Manifest $m): void
    {
        $deps = $m->dependencies();
        if (!$deps) return;

        $pdo = DB::pdo();
        if (!$pdo) return;

        foreach ($deps as $dep => $constraint) {
            $type = 'plugin'; $name = $dep;
            if (str_contains($dep, '/')) [$type,$name] = explode('/', $dep, 2);

            $st = $pdo->prepare("SELECT version FROM ce_marketplace_packages WHERE type=:t AND name=:n LIMIT 1");
            $st->execute([':t'=>$type, ':n'=>$name]);
            $row = $st->fetch(\PDO::FETCH_ASSOC);
            if (!$row) throw new \Exception("Missing dependency: $type/$name ($constraint)");
            if (!Semver::satisfies((string)$row['version'], (string)$constraint)) {
                throw new \Exception("Dependency constraint failed: $type/$name requires $constraint, installed ".$row['version']);
            }
        }
    }

    protected function applyPayload(\ZipArchive $zip, Manifest $m): void
    {
        $baseMap = ['plugin'=>'plugins','theme'=>'themes','ui_block'=>'ui_blocks','content_type'=>'content_types'];
        $folder = $baseMap[$m->type()] ?? null;
        if (!$folder) throw new \Exception("Unknown type mapping");

        $prefix = 'payload/' . $folder . '/' . $m->name() . '/';
        $targetBase = ROOT_PATH . '/' . $folder . '/' . $m->name() . '/';
        if (!is_dir($targetBase)) @mkdir($targetBase, 0775, true);

        for ($i=0; $i<$zip->numFiles; $i++) {
            $stat = $zip->statIndex($i);
            $name = $stat['name'];
            if (!str_starts_with($name, $prefix)) continue;

            $rel = substr($name, strlen($prefix));
            if ($rel === '') continue;

            $dest = $targetBase . $rel;
            if (str_ends_with($name, '/')) {
                @mkdir($dest, 0775, true);
                continue;
            }
            $dir = dirname($dest);
            if (!is_dir($dir)) @mkdir($dir, 0775, true);
            $data = $zip->getFromIndex($i);
            file_put_contents($dest, $data);
        }
    }

    protected function markInstalled(Manifest $m): void
    {
        $pdo = DB::pdo();
        if (!$pdo) return;
        $meta = json_encode($m->raw, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        $pdo->prepare("INSERT INTO ce_marketplace_packages(type,name,version,title,publisher_id,signature_required,installed_at,updated_at,meta_json)
            VALUES(:t,:n,:v,:title,:pub,1,NOW(),NOW(),:meta)
            ON DUPLICATE KEY UPDATE version=:v2,title=:title2,publisher_id=:pub2,updated_at=NOW(),meta_json=:meta2")
            ->execute([
                ':t'=>$m->type(),':n'=>$m->name(),':v'=>$m->version(),':title'=>$m->title(),':pub'=>$m->publisherId(),':meta'=>$meta,
                ':v2'=>$m->version(),':title2'=>$m->title(),':pub2'=>$m->publisherId(),':meta2'=>$meta,
            ]);
    }

    protected function engineVersion(): string
    {
        $vfile = ROOT_PATH . '/system/version.txt';
        return is_file($vfile) ? trim((string)file_get_contents($vfile)) : '0.0.0';
    }

    protected function backupSnapshot(Manifest $m): array
    {
        $map = ['plugin'=>'plugins','theme'=>'themes','ui_block'=>'ui_blocks','content_type'=>'content_types'];
        $folder = $map[$m->type()] ?? 'plugins';
        $target = ROOT_PATH . '/' . $folder . '/' . $m->name();
        $backupDir = ROOT_PATH . '/storage/backups/marketplace/' . date('Ymd_His') . '_' . $m->type() . '_' . $m->name();

        if (!is_dir(dirname($backupDir))) @mkdir(dirname($backupDir), 0775, true);
        if (is_dir($target)) $this->copyDir($target, $backupDir);
        else @mkdir($backupDir, 0775, true);

        return ['path'=>$backupDir,'target'=>$target];
    }

    protected function rollbackSnapshot(array $backup): void
    {
        $target = $backup['target'] ?? ''; $path = $backup['path'] ?? '';
        if (!$target || !$path) return;
        if (is_dir($target)) $this->rmDir($target);
        $this->copyDir($path, $target);
    }

    protected function copyDir(string $src, string $dst): void
    {
        $src=rtrim($src,'/'); $dst=rtrim($dst,'/');
        if (!is_dir($dst)) @mkdir($dst, 0775, true);
        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($src, \FilesystemIterator::SKIP_DOTS));
        foreach ($it as $file) {
            $rel = substr($file->getPathname(), strlen($src)+1);
            $dest = $dst . '/' . $rel;
            $dir = dirname($dest);
            if (!is_dir($dir)) @mkdir($dir, 0775, true);
            if ($file->isDir()) @mkdir($dest, 0775, true);
            else copy($file->getPathname(), $dest);
        }
    }

    protected function rmDir(string $dir): void
    {
        if (!is_dir($dir)) return;
        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($it as $file) $file->isDir() ? @rmdir($file->getPathname()) : @unlink($file->getPathname());
        @rmdir($dir);
    }
}
