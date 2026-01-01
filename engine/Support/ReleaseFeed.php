<?php
declare(strict_types=1);

namespace Support;

/**
 * Builds a "release news" feed from CHANGELOG.md so the showcase website always
 * matches the current engine version (system/version.txt).
 */
final class ReleaseFeed
{
    /**
     * @return array<int, array{version:string, slug:string, title:string, body_md:string, excerpt:string}>
     */
    public static function fromChangelog(string $path): array
    {
        if (!is_file($path)) return [];

        $md = (string)file_get_contents($path);
        $md = str_replace("\r\n", "\n", $md);
        $lines = explode("\n", $md);

        $items = [];
        $current = null;

        $flush = function() use (&$items, &$current) {
            if (!$current) return;
            $body = trim($current['body_md'] ?? '');
            $excerpt = '';
            if ($body !== '') {
                $paras = preg_split('/\n\s*\n/', $body) ?: [];
                $excerpt = trim((string)($paras[0] ?? ''));
            }
            $current['body_md'] = $body;
            $current['excerpt'] = $excerpt;
            $items[] = $current;
            $current = null;
        };

        foreach ($lines as $line) {
            // Version headings: ## 4.0.0, ## v4.0.0, ## [4.0.0] - ...
            if (preg_match('/^\s*##\s*\[?v?(\d+\.\d+\.\d+)[^\]]*\]?\s*(.*)$/', $line, $m)) {
                $flush();
                $ver = $m[1];
                $tail = trim($m[2]);
                $title = $tail !== '' ? "v{$ver} {$tail}" : "v{$ver}";
                $slug = 'v' . str_replace('.', '-', $ver);
                $current = [
                    'version' => $ver,
                    'slug' => $slug,
                    'title' => $title,
                    'body_md' => '',
                    'excerpt' => ''
                ];
                continue;
            }

            if ($current) {
                $current['body_md'] .= $line . "\n";
            }
        }

        $flush();

        // newest first
        return $items;
    }

    public static function findBySlug(array $items, string $slug): ?array
    {
        foreach ($items as $it) {
            if (($it['slug'] ?? '') === $slug) return $it;
        }
        return null;
    }
}
