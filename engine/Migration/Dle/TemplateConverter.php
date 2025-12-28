<?php
namespace Migration\Dle;

use Migration\Report;

class TemplateConverter
{
    public function convertDir(string $srcDir, string $dstDir, Report $r): void
    {
        $srcDir = rtrim($srcDir, '/');
        $dstDir = rtrim($dstDir, '/');

        if (!is_dir($srcDir)) {
            $r->error('Каталог шаблонов DLE не найден', ['dir'=>$srcDir]);
            return;
        }
        if (!is_dir($dstDir)) @mkdir($dstDir, 0775, true);

        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($srcDir, \FilesystemIterator::SKIP_DOTS));
        foreach ($it as $f) {
            if (!$f->isFile()) continue;
            if (strtolower($f->getExtension()) !== 'tpl') continue;

            $rel = substr($f->getPathname(), strlen($srcDir) + 1);
            $out = $dstDir . '/' . $rel;
            @mkdir(dirname($out), 0775, true);

            $src = (string)file_get_contents($f->getPathname());
            [$converted, $warnings] = $this->convertOne($src);

            file_put_contents($out, $converted);
            foreach ($warnings as $w) $r->warn($w, ['file'=>$rel]);

            $r->info('Сконвертирован шаблон', ['file'=>$rel]);
        }
    }

    /**
     * Best-effort conversion:
     * - сохраняем .tpl структуру DLE
     * - минимально маппим популярные плейсхолдеры на переменные CajeerEngine
     */
    public function convertOne(string $tpl): array
    {
        $warnings = [];

        $map = [
            '{THEME}' => '{theme_url}',
            '{home-url}' => '{base_url}',
            '{headers}' => '{meta_tags}',
        ];
        foreach ($map as $k=>$v) {
            if (stripos($tpl, $k) !== false) $tpl = str_ireplace($k, $v, $tpl);
        }

        if (stripos($tpl, '[not-logged]') !== false) $warnings[] = 'Найден блок [not-logged] — адаптируется через DLE Tag Adapter (best-effort).';
        if (stripos($tpl, '[logged]') !== false) $warnings[] = 'Найден блок [logged] — адаптируется через DLE Tag Adapter (best-effort).';

        return [$tpl, $warnings];
    }
}
