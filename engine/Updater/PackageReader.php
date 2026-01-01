<?php
namespace Updater;

class PackageReader
{
    public static function read(string $file): array
    {
        if (!is_file($file)) throw new \RuntimeException('Package not found: '.$file);

        $zip = new \ZipArchive();
        if ($zip->open($file) !== true) throw new \RuntimeException('Cannot open package: '.$file);

        $manifestRaw = $zip->getFromName('manifest.json');
        if ($manifestRaw === false) throw new \RuntimeException('manifest.json missing in package');

        $manifest = json_decode($manifestRaw, true);
        if (!is_array($manifest)) throw new \RuntimeException('manifest.json invalid');

        $type = (string)($manifest['type'] ?? 'pkg');
        $zip->close();

        return ['manifest'=>$manifest,'type'=>$type,'file'=>$file];
    }
}
