<?php
namespace Migration\Dle;

use Migration\Report;

class DleMigrator
{
    public function check(array $cfg, ?string $tplDir = null): Report
    {
        $checker = new CompatibilityChecker();
        $r = $checker->check($cfg);
        if ($tplDir) $checker->scanTemplates($tplDir, $r);
        return $r;
    }

    public function importDb(array $cfg, Report $r): void
    {
        (new DbImporter())->import($cfg, $r);
    }

    public function convertTemplates(string $srcDir, string $dstDir, Report $r): void
    {
        (new TemplateConverter())->convertDir($srcDir, $dstDir, $r);
    }
}
