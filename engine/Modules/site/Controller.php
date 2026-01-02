<?php
namespace Modules\site;

use Template\Template;
use Support\MarkdownLite;

class Controller
{
    protected function baseVars(string $title, string $desc = ''): array
    {
        $version = trim((string)@file_get_contents(ROOT_PATH . '/system/version.txt')) ?: '0.0.0';
        $canonical = (isset($_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI']))
            ? ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']
            : '';

        return [
            'seo_title' => $title . ' — CajeerEngine',
            'seo_description' => $desc,
            'seo_canonical' => $canonical,
            'seo_og' => '',
            'seo_twitter' => '',
            'head_extra' => '',
            'body_extra' => '',
            'runtime_mode' => 'web',
            'app_version' => $version,
        ];
    }

    public function docs(): void
    {
        // kept for backward compatibility (if someone routes /docs to site/docs)
        $tpl = new Template(theme: 'default');
        $tpl->render('docs.tpl', $this->baseVars('Документация', 'Документация и быстрый старт CajeerEngine.'));
    }

    public function api(): void
    {
        // Render API docs from docs/API_V1_RU.md + auto-generated endpoints list
        $docPath = ROOT_PATH . '/docs/API_V1_RU.md';
        $docMd = is_file($docPath) ? (string)@file_get_contents($docPath) : '';

        // /api/v1 routes (public website router)
        $routesPath = ROOT_PATH . '/system/routes.php';
        $routesArr = is_file($routesPath) ? (include $routesPath) : [];
        $v1 = [];
        if (is_array($routesArr)) {
            foreach ($routesArr as $path => $handler) {
                if (!is_string($path)) continue;
                if (strpos($path, '/api/v1/') !== 0) continue;
                if (!is_array($handler) || count($handler) < 2) continue;
                $v1[] = ['path' => $path, 'handler' => $handler[0] . '::' . $handler[1]];
            }
        }

        // /api internal map (system/api.php)
        $apiMapPath = ROOT_PATH . '/system/api.php';
        $apiMap = is_file($apiMapPath) ? (include $apiMapPath) : [];
        $internal = [];
        $enabled = true;
        $tokensCount = 0;

        if (is_array($apiMap)) {
            $enabled = (bool)($apiMap['enabled'] ?? true);
            $tokens = $apiMap['tokens'] ?? [];
            $tokensCount = is_array($tokens) ? count($tokens) : 0;

            foreach ($apiMap as $path => $handler) {
                if (!is_string($path)) continue;
                if (strpos($path, '/api/') !== 0) continue;
                if (!is_array($handler) || count($handler) < 2) continue;
                $internal[] = ['path' => $path, 'handler' => $handler[0] . '::' . $handler[1]];
            }
        }

        $toList = function(array $rows): string {
            if (empty($rows)) return '<div class="ce-muted">Нет данных.</div>';
            usort($rows, function($a, $b){ return strcmp($a['path'], $b['path']); });

            $html = '';
            foreach ($rows as $r) {
                $p = htmlspecialchars((string)$r['path'], ENT_QUOTES, 'UTF-8');
                $h = htmlspecialchars((string)$r['handler'], ENT_QUOTES, 'UTF-8');
                $html .= '<div class="ce-api__row"><code>' . $p . '</code><span class="ce-muted ce-text-sm">' . $h . '</span></div>';
            }
            return $html;
        };

        $tpl = new Template(theme: 'default');
        $tpl->render('api.tpl', array_merge(
            $this->baseVars('API', 'Headless API и точки интеграции CajeerEngine.'),
            [
                'api_docs_html' => MarkdownLite::toHtml($docMd),
                'api_v1_html' => $toList($v1),
                'api_internal_html' => $toList($internal),
                'api_internal_enabled' => $enabled ? 'enabled' : 'disabled',
                'api_tokens_count' => (string)$tokensCount,
            ]
        ));
    }

    public function rarog(): void
    {
        $docPath = ROOT_PATH . '/docs/RAROG_RU.md';
        $docMd = is_file($docPath) ? (string)@file_get_contents($docPath) : '';

        $tpl = new Template(theme: 'default');
        $tpl->render('rarog.tpl', array_merge(
            $this->baseVars('Rarog', 'Rarog — UI / Design System: tokens, utilities, компоненты и JS‑ядро.'),
            [
                'rarog_doc_html' => MarkdownLite::toHtml($docMd),
            ]
        ));
    }
}
