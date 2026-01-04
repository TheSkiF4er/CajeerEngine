<?php
namespace Modules\docs;

use Template\Template;
use Support\MarkdownLite;

class Controller
{
    private function baseVars(string $title, string $desc = ''): array
    {
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
        ];
    }

    public function index(): void
    {
        $doc = (string)($_GET['doc'] ?? '');

        // Collect docs from repository root (high-signal files)
        $rootFiles = [
            ROOT_PATH . '/README.md' => 'README.md',
            ROOT_PATH . '/CHANGELOG.md' => 'CHANGELOG.md',
            ROOT_PATH . '/SECURITY.md' => 'SECURITY.md',
            ROOT_PATH . '/CONTRIBUTING.md' => 'CONTRIBUTING.md',
            ROOT_PATH . '/CODE_OF_CONDUCT.md' => 'CODE_OF_CONDUCT.md',
            ROOT_PATH . '/NOTICE' => 'NOTICE',
            ROOT_PATH . '/LICENSE' => 'LICENSE',
        ];

        $items = [];

        foreach ($rootFiles as $path => $label) {
            if (is_file($path)) {
                $display = preg_replace('/\.(md|txt)$/i', '', $label) ?? $label;
                $items[] = [
                    'key' => 'root:' . $label,
                    'label' => $display,
                    'path' => $path,
                    'group' => 'Repository',
                ];
            }
        }

        // Collect docs/* recursively
        $base = ROOT_PATH . '/docs';
        if (is_dir($base)) {
            $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($base));
            foreach ($rii as $file) {
                /** @var \SplFileInfo $file */
                if (!$file->isFile()) continue;

                $rel = str_replace('\\', '/', substr($file->getPathname(), strlen($base) + 1));
                if (!preg_match('/\.(md|txt)$/i', $rel)) continue;

                $first = explode('/', $rel, 2)[0] ?? 'docs';
                $group = ($first === $rel) ? 'Docs' : strtoupper($first);

                $items[] = [
                    'key' => 'docs:' . $rel,
                    // Display: hide folder prefix and extension (.md/.txt) for readability
                    'label' => (function(string $rel): string {
                        $base = basename($rel);
                        $base = preg_replace('/\.(md|txt)$/i', '', $base) ?? $base;
                        return $base;
                    })($rel),
                    'path' => $file->getPathname(),
                    'group' => $group,
                ];
            }
        }

        // Sort by group then label
        usort($items, function($a, $b){
            $g = strcmp($a['group'], $b['group']);
            return $g !== 0 ? $g : strcmp($a['label'], $b['label']);
        });

        // Default doc: INSTALL_RU.md if present, else README.md, else first
        $selected = '';
        foreach ($items as $it) {
            if ($it['key'] === 'docs:INSTALL_RU.md') { $selected = $it['key']; break; }
        }
        if ($selected === '') {
            foreach ($items as $it) {
                if ($it['key'] === 'root:README.md') { $selected = $it['key']; break; }
            }
        }
        if ($selected === '' && !empty($items)) $selected = $items[0]['key'];

        if ($doc !== '') {
            foreach ($items as $it) {
                if ($it['key'] === $doc) { $selected = $doc; break; }
            }
        }

        $contentMd = '';
        $selectedLabel = 'Документация';
        foreach ($items as $it) {
            if ($it['key'] === $selected) {
                $selectedLabel = $it['label'];
                $contentMd = (string)@file_get_contents($it['path']);
                break;
            }
        }

        // Build sidebar HTML (grouped)
        $toc = '';
        $currentGroup = '';
        foreach ($items as $it) {
            if ($currentGroup !== $it['group']) {
                $currentGroup = $it['group'];
                $toc .= '<div class="ce-panel__group">' . htmlspecialchars($currentGroup, ENT_QUOTES, 'UTF-8') . '</div>';
            }

            $active = ($it['key'] === $selected) ? ' ce-panel__item--active' : '';
            $href = '/docs?doc=' . rawurlencode($it['key']);

            $label = htmlspecialchars($it['label'], ENT_QUOTES, 'UTF-8');
            $toc .= '<a class="ce-panel__item' . $active . '" href="' . $href . '">';
            $toc .= '<div class="ce-panel__name">' . $label . '</div>';
            $toc .= '</a>';
        }

        $tpl = new Template(theme: 'default');
        $tpl->render('docs.tpl', array_merge(
            $this->baseVars('Документация', 'Документация и руководство по CajeerEngine.'),
            [
                'selected_label' => $selectedLabel,
                'toc_html' => $toc,
                'content_html' => MarkdownLite::toHtml($contentMd),
                'doc_total' => (string)count($items),
            ]
        ));
    }
}
