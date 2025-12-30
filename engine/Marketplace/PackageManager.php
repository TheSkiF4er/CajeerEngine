<?php
namespace Marketplace;

use Database\DB;
use Marketplace\V2\RegistryClient;
use Marketplace\Security\Signature;
use Observability\Logger;

class PackageManager
{
    public function __construct(protected array $cfg) {}

    public function ensureSchema(): void
    {
        $pdo = DB::pdo(); if(!$pdo) return;
        foreach (['platform_v3_0.sql','async_v3_1.sql','marketplace_v3_2.sql'] as $sqlFile) {
            $p = ROOT_PATH . '/system/sql/' . $sqlFile;
            if (is_file($p)) $pdo->exec(file_get_contents($p));
        }
    }

    public function registries(): array
    {
        return (array)($this->cfg['registries'] ?? []);
    }

    public function registryByName(string $name): ?array
    {
        foreach ($this->registries() as $r) {
            if (($r['name'] ?? '') === $name) return $r;
        }
        return null;
    }

    public function search(string $q, string $type = ''): array
    {
        $out = [];
        foreach ($this->registries() as $r) {
            if (!($r['enabled'] ?? true)) continue;
            $client = new RegistryClient($r);
            $res = $client->search($q, $type);
            if (!empty($res['items'])) {
                foreach ($res['items'] as $it) {
                    $it['_registry'] = $r['name'] ?? 'registry';
                    $out[] = $it;
                }
            }
        }
        return ['ok'=>true,'items'=>$out];
    }

    public function install(string $registryName, string $packageId, int $tenantId = 0): array
    {
        $this->ensureSchema();
        $pdo = DB::pdo(); if(!$pdo) return ['ok'=>false,'error'=>'db_required'];

        $reg = $this->registryByName($registryName);
        if (!$reg) return ['ok'=>false,'error'=>'registry_not_found'];

        $client = new RegistryClient($reg);
        $m = $client->fetchManifest($packageId);
        if (!$m['ok']) return $m;

        $manifest = (array)$m['manifest'];
        $pkgType = (string)($manifest['type'] ?? '');
        $slug = (string)($manifest['slug'] ?? '');
        $version = (string)($manifest['version'] ?? '');
        $publisherId = (string)($manifest['publisher']['id'] ?? '');

        if ($pkgType === '' || $slug === '' || $version === '' || $publisherId === '') {
            return ['ok'=>false,'error'=>'invalid_manifest'];
        }

        // signature enforcement
        $requireSig = (bool)($this->cfg['security']['require_signatures'] ?? true);
        $sig = (string)($manifest['signature']['ed25519_base64'] ?? '');
        $pubKey = $this->resolvePublisherKey($publisherId, $manifest);

        if ($requireSig) {
            if ($sig === '' || $pubKey === '') return ['ok'=>false,'error'=>'signature_required'];
            $payloadForSig = json_encode($manifest['signed_payload'] ?? $manifest, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            if (!Signature::verifyEd25519($payloadForSig, $sig, $pubKey)) {
                return ['ok'=>false,'error'=>'signature_invalid'];
            }
        }

        // download package bytes (.cajeerpkg)
        $d = $client->download($packageId);
        if (!$d['ok']) return $d;

        $bytes = $d['bytes'];
        $tmpDir = ROOT_PATH . '/storage/tmp/marketplace';
        if (!is_dir($tmpDir)) @mkdir($tmpDir, 0775, true);

        $hash = hash('sha256', $bytes);
        $pkgFile = $tmpDir . '/' . $slug . '-' . $version . '-' . $hash . '.cajeerpkg';
        file_put_contents($pkgFile, $bytes);

        $apply = $this->applyPackageFile($pkgFile, $manifest, $tenantId, $registryName);
        return $apply;
    }

    public function update(string $registryName, string $packageId, int $tenantId = 0): array
    {
        // update == install with backup/rollback
        return $this->install($registryName, $packageId, $tenantId);
    }

    public function uninstall(string $type, string $slug, int $tenantId = 0): array
    {
        $this->ensureSchema();
        $pdo = DB::pdo(); if(!$pdo) return ['ok'=>false,'error'=>'db_required'];

        $row = $pdo->query("SELECT * FROM ce_installed_packages WHERE tenant_id=".(int)$tenantId." AND type=".$pdo->quote($type)." AND slug=".$pdo->quote($slug)." LIMIT 1")
                   ->fetch(\PDO::FETCH_ASSOC);
        if (!$row) return ['ok'=>false,'error'=>'not_installed'];

        $installPath = $this->installPath($type, $slug);
        $backup = $this->backupPath($type, $slug);
        $stamp = date('Ymd_His');
        $snap = $backup . '/uninstall_' . $stamp;

        if (is_dir($installPath)) {
            $this->copyDir($installPath, $snap);
            $this->rmDir($installPath);
        }

        $pdo->prepare("DELETE FROM ce_installed_packages WHERE id=:id")->execute([':id'=>(int)$row['id']]);

        Logger::info('marketplace.uninstall', ['type'=>$type,'slug'=>$slug,'tenant'=>$tenantId]);
        return ['ok'=>true,'backup'=>$snap];
    }

    public function rollback(string $type, string $slug, string $backupFolder, int $tenantId = 0): array
    {
        $installPath = $this->installPath($type, $slug);
        if (!is_dir($backupFolder)) return ['ok'=>false,'error'=>'backup_not_found'];

        // replace current with backup snapshot
        if (is_dir($installPath)) $this->rmDir($installPath);
        $this->copyDir($backupFolder, $installPath);

        Logger::info('marketplace.rollback', ['type'=>$type,'slug'=>$slug,'from'=>$backupFolder]);
        return ['ok'=>true];
    }

    protected function applyPackageFile(string $pkgFile, array $manifest, int $tenantId, string $registryName): array
    {
        $type = (string)$manifest['type'];
        $slug = (string)$manifest['slug'];
        $version = (string)$manifest['version'];

        $installPath = $this->installPath($type, $slug);
        $backupBase = $this->backupPath($type, $slug);
        if (!is_dir($backupBase)) @mkdir($backupBase, 0775, true);

        $stamp = date('Ymd_His');
        $backupSnap = $backupBase . '/' . $stamp;

        // snapshot existing for rollback
        if (is_dir($installPath)) $this->copyDir($installPath, $backupSnap);

        // extract package (zip)
        $tmpExtract = ROOT_PATH . '/storage/tmp/marketplace_extract/' . $slug . '_' . $stamp;
        if (!is_dir($tmpExtract)) @mkdir($tmpExtract, 0775, true);

        $zip = new \ZipArchive();
        if ($zip->open($pkgFile) !== true) return ['ok'=>false,'error'=>'bad_package'];
        $zip->extractTo($tmpExtract);
        $zip->close();

        // expected: payload/...
        $payloadDir = $tmpExtract . '/payload';
        if (!is_dir($payloadDir)) return ['ok'=>false,'error'=>'payload_missing'];

        // install
        if (is_dir($installPath)) $this->rmDir($installPath);
        $this->copyDir($payloadDir, $installPath);

        // register in DB
        $this->ensureSchema();
        $pdo = DB::pdo(); if(!$pdo) return ['ok'=>false,'error'=>'db_required'];

        $pdo->prepare("INSERT INTO ce_installed_packages(tenant_id,type,slug,version,enabled,source_registry,manifest_json,installed_at,updated_at)
                       VALUES(:t,:ty,:s,:v,1,:r,:m,NOW(),NOW())
                       ON DUPLICATE KEY UPDATE version=:v2, manifest_json=:m2, updated_at=NOW()")
            ->execute([
                ':t'=>$tenantId, ':ty'=>$type, ':s'=>$slug, ':v'=>$version, ':r'=>$registryName,
                ':m'=>json_encode($manifest, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
                ':v2'=>$version, ':m2'=>json_encode($manifest, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
            ]);

        Logger::info('marketplace.install', ['type'=>$type,'slug'=>$slug,'version'=>$version,'backup'=>$backupSnap]);
        $this->emitBilling('package.installed', ['type'=>$type,'slug'=>$slug,'version'=>$version,'tenant_id'=>$tenantId]);

        return ['ok'=>true,'installed_path'=>$installPath,'backup'=>$backupSnap];
    }

    protected function resolvePublisherKey(string $publisherId, array $manifest): string
    {
        // 1) config trusted map
        $map = (array)($this->cfg['security']['trusted_publishers'] ?? []);
        if (!empty($map[$publisherId])) return (string)$map[$publisherId];

        // 2) manifest inline (for verified publishers with key)
        $k = (string)($manifest['publisher']['public_key_base64'] ?? '');
        return $k;
    }

    protected function installPath(string $type, string $slug): string
    {
        return match ($type) {
            'theme' => ROOT_PATH . '/themes/' . $slug,
            'ui-block' => ROOT_PATH . '/ui/blocks/' . $slug,
            'content-type' => ROOT_PATH . '/content/types/' . $slug,
            default => ROOT_PATH . '/plugins/' . $slug,
        };
    }

    protected function backupPath(string $type, string $slug): string
    {
        return ROOT_PATH . '/storage/backups/marketplace/' . $type . '/' . $slug;
    }

    protected function emitBilling(string $event, array $payload): void
    {
        $b = (array)($this->cfg['billing'] ?? []);
        if (!($b['enabled'] ?? false)) return;

        $url = (string)($b['webhook_url'] ?? '');
        $secret = (string)($b['secret'] ?? '');
        if ($url === '' || $secret === '') return;

        $body = json_encode(['event'=>$event,'payload'=>$payload,'ts'=>time()], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        $sig = hash_hmac('sha256', $body, $secret);

        // best-effort webhook (non-blocking not implemented in skeleton)
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'X-Cajeer-Signature: ' . $sig,
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_exec($ch);
        curl_close($ch);
    }

    protected function copyDir(string $src, string $dst): void
    {
        if (!is_dir($dst)) @mkdir($dst, 0775, true);
        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($src, \FilesystemIterator::SKIP_DOTS));
        foreach ($it as $file) {
            $target = $dst . '/' . $it->getSubPathName();
            if ($file->isDir()) {
                if (!is_dir($target)) @mkdir($target, 0775, true);
            } else {
                if (!is_dir(dirname($target))) @mkdir(dirname($target), 0775, true);
                copy((string)$file, $target);
            }
        }
    }

    protected function rmDir(string $dir): void
    {
        if (!is_dir($dir)) return;
        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($it as $file) {
            $file->isDir() ? rmdir((string)$file) : unlink((string)$file);
        }
        @rmdir($dir);
    }
}
