<?php
namespace Updater;

use Cache\Cache;

class Applier
{
    public function __construct(private string $updatesPath)
    {
        $this->updatesPath = rtrim($this->updatesPath, '/');
        if (!is_dir($this->updatesPath)) @mkdir($this->updatesPath, 0775, true);
    }

    public function apply(string $packageFile): array
    {
        $meta = PackageReader::read($packageFile);
        $manifest = $meta['manifest'];
        $id = (string)($manifest['id'] ?? basename($packageFile));

        $z = new \ZipArchive();
        if ($z->open($packageFile) !== true) throw new \RuntimeException('Cannot open package: '.$packageFile);

        $checksRaw = $z->getFromName('checks.json');
        if ($checksRaw !== false) {
            $checks = json_decode($checksRaw, true);
            if (is_array($checks)) $this->runChecks($checks);
        }

        $pre = $z->getFromName('scripts/pre.php');
        if ($pre !== false) $this->runHook($pre, $manifest);

        $root = realpath(ROOT_PATH);
        for ($i=0; $i<$z->numFiles; $i++) {
            $name = $z->getNameIndex($i);
            if (!$name) continue;
            if (!str_starts_with($name, 'files/')) continue;
            if (str_ends_with($name, '/')) continue;

            $rel = substr($name, strlen('files/'));
            $dst = $root . '/' . $rel;
            @mkdir(dirname($dst), 0775, true);
            $content = $z->getFromIndex($i);
            file_put_contents($dst, $content);
        }

        $post = $z->getFromName('scripts/post.php');
        if ($post !== false) $this->runHook($post, $manifest);

        Cache::clear();
        $z->close();

        $applied = $this->updatesPath . '/applied_' . date('Ymd_His') . '_' . preg_replace('/[^a-z0-9_-]+/i','-', $id) . '.json';
        file_put_contents($applied, json_encode([
            'id'=>$id,
            'applied_at'=>date('c'),
            'package'=>$packageFile,
            'manifest'=>$manifest
        ], JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));

        return ['applied_file'=>$applied,'id'=>$id];
    }

    private function runHook(string $php, array $manifest): void
    {
        $hook = function() use ($php, $manifest) {
            $MANIFEST = $manifest;
            eval('?>' . $php);
        };
        $hook();
    }

    private function runChecks(array $checks): void
    {
        if (isset($checks['require_php'])) {
            $min = (string)$checks['require_php'];
            if (version_compare(PHP_VERSION, $min, '<')) {
                throw new \RuntimeException('PHP version too low. Need >= ' . $min . ', current ' . PHP_VERSION);
            }
        }
        if (isset($checks['require_files']) && is_array($checks['require_files'])) {
            foreach ($checks['require_files'] as $f) {
                $path = ROOT_PATH . '/' . ltrim((string)$f, '/');
                if (!is_file($path)) throw new \RuntimeException('Required file missing: '.$f);
            }
        }
    }
}
