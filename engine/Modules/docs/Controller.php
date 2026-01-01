<?php
namespace Modules\docs;

use Template\Template;
use Support\MarkdownLite;

class Controller
{
    public function index(): void
    {
        $doc = (string)($_GET['doc'] ?? '');
        $base = ROOT_PATH . '/docs';
        $rootFiles = [
            ROOT_PATH . '/README.md' => 'README.md',
            ROOT_PATH . '/CHANGELOG.md' => 'CHANGELOG.md',
            ROOT_PATH . '/SECURITY.md' => 'SECURITY.md',
            ROOT_PATH . '/CONTRIBUTING.md' => 'CONTRIBUTING.md',
            ROOT_PATH . '/CODE_OF_CONDUCT.md' => 'CODE_OF_CONDUCT.md',
            ROOT_PATH . '/LICENSE' => 'LICENSE',
        ];

        $items = [];

        foreach ($rootFiles as $path => $label) {
            if (is_file($path)) {
                $items[] = ['key' => 'root:' . $label, 'label' => $label, 'path' => $path];
            }
        }

        if (is_dir($base)) {
            $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($base));
            foreach ($rii as $file) {
                /** @var \SplFileInfo $file */
                if (!$file->isFile()) continue;
                $rel = str_replace('\\', '/', substr($file->getPathname(), strlen($base) + 1));
                if (!preg_match('/\.(md|txt)$/i', $rel)) continue;
                $items[] = ['key' => 'docs:' . $rel, 'label' => $rel, 'path' => $file->getPathname()];
            }
        }

        usort($items, function($a, $b){ return strcmp($a['label'], $b['label']); });

        $selected = $items[0]['key'] ?? '';
        if ($doc !== '') {
            foreach ($items as $it) {
                if ($it['key'] === $doc) { $selected = $doc; break; }
            }
        }

        $contentMd = '';
        $title = 'Документация';
        foreach ($items as $it) {
            if ($it['key'] === $selected) {
                $title = 'Документация — ' . $it['label'];
                $contentMd = (string)@file_get_contents($it['path']);
                break;
            }
        }

        $toc = '';
        foreach ($items as $it) {
            $active = $it['key'] === $selected ? ' rg-active' : '';
            $toc .= '<a class="rg-list-item' . $active . '" href="/docs?doc=' . rawurlencode($it['key']) . '">' . htmlspecialchars($it['label'], ENT_QUOTES) . '</a>';
        }

        $tpl = new Template();
        $tpl->render('docs.tpl', [
            'title' => $title,
            'toc_html' => $toc,
            'content_html' => MarkdownLite::toHtml($contentMd),
        ]);
    }
}
