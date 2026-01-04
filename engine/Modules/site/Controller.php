<?php
declare(strict_types=1);

namespace Modules\site;

use Template\Template;
use Support\MarkdownLite;

final class Controller
{
    private function baseVars(string $title, string $desc = ''): array
    {
        $version = trim((string)@file_get_contents(ROOT_PATH . '/system/version.txt'));
        if ($version === '') $version = '0.0.0';

        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = (string)($_SERVER['HTTP_HOST'] ?? '');
        $uri  = (string)($_SERVER['REQUEST_URI'] ?? '');
        $canonical = ($host !== '' && $uri !== '') ? ($scheme . '://' . $host . $uri) : '';

        return [
            'app_version' => $version,
            'seo_title' => $title . ' — CajeerEngine',
            'seo_description' => $desc,
            'canonical' => $canonical,
        ];
    }

    public function api(): void
    {
        $tpl = new Template();
        $tpl->render('api.tpl', $this->baseVars('API', 'Headless API и точки интеграции CajeerEngine.'));
    }

    public function rarog(): void
    {
        $tpl = new Template();

        $mdPath = ROOT_PATH . '/docs/RAROG.md';
        $md = '';
        if (is_file($mdPath)) {
            $md = (string)@file_get_contents($mdPath);
        }

        // Fallback if docs file is missing
        if (trim($md) === '') {
            $md = "# Rarog\n\nДокументация не найдена. Откройте портал: https://rarog.cajeer.ru";
        }

        $vars = $this->baseVars('Rarog', 'Rarog — CSS‑framework и дизайн‑система: tokens + utilities + компоненты + JS‑ядро.');
        $vars['rarog_github'] = 'https://github.com/TheSkiF4er/Rarog';
        $vars['rarog_releases'] = 'https://github.com/TheSkiF4er/Rarog/releases';
        $vars['rarog_npm'] = 'https://www.npmjs.com/package/rarog';
        $vars['rarog_portal'] = 'https://rarog.cajeer.ru';
        $vars['rarog_docs'] = '/docs?doc=docs%3ARAROG.md';

        $vars['readme_html'] = MarkdownLite::toHtml($md);

        $tpl->render('rarog.tpl', $vars);
    }
}
