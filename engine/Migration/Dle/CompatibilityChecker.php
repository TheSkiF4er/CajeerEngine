<?php
namespace Migration\Dle;

use Migration\Report;

class CompatibilityChecker
{
    public function check(array $cfg): Report
    {
        $r = new Report();
        try {
            $pdo = Connection::connect($cfg['db']);
            $prefix = (string)($cfg['prefix'] ?? 'dle_');

            $need = [$prefix.'post', $prefix.'category', $prefix.'users'];
            foreach ($need as $t) {
                try {
                    $pdo->query("SELECT 1 FROM `{$t}` LIMIT 1");
                    $r->info("Таблица найдена: {$t}");
                } catch (\Throwable $e) {
                    $r->warn("Таблица не найдена или недоступна: {$t}", ['error'=>$e->getMessage()]);
                }
            }

            try {
                $st = $pdo->query("SHOW VARIABLES LIKE 'character_set_database'");
                $row = $st->fetch();
                if ($row && isset($row['Value']) && !str_contains(strtolower((string)$row['Value']), 'utf8')) {
                    $r->warn('База DLE не в UTF-8, возможны проблемы с кодировкой', ['character_set_database'=>$row['Value']]);
                }
            } catch (\Throwable $e) {}

            $r->info('Проверка совместимости завершена');
        } catch (\Throwable $e) {
            $r->error('Ошибка подключения к DLE', ['error'=>$e->getMessage()]);
        }

        return $r;
    }

    public function scanTemplates(string $templatesDir, Report $r): void
    {
        if (!is_dir($templatesDir)) {
            $r->warn('Каталог шаблонов DLE не найден', ['dir'=>$templatesDir]);
            return;
        }

        $needReview = ['{custom ', '{ajax', '{calendar', '[aviable=', '[available='];

        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($templatesDir, \FilesystemIterator::SKIP_DOTS));
        foreach ($it as $f) {
            if (!$f->isFile()) continue;
            if (strtolower($f->getExtension()) !== 'tpl') continue;
            $txt = (string)@file_get_contents($f->getPathname());
            foreach ($needReview as $needle) {
                if (stripos($txt, $needle) !== false) {
                    $r->warn('Шаблон содержит потенциально несовместимый тег/блок: '.$needle, ['file'=>$f->getPathname()]);
                }
            }
        }
    }
}
