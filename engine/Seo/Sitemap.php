<?php
namespace Seo;

use Core\Config;
use Database\Connection;

class Sitemap
{
    public static function generate(): string
    {
        $base = rtrim((string)Config::get('seo.base_url', 'http://localhost'), '/');
        $urls = [];

        // home
        $urls[] = ['loc' => $base . '/', 'changefreq'=>'daily', 'priority'=>'1.0'];

        // content: news + pages
        try {
            $pdo = Connection::pdo();
            $st = $pdo->query("SELECT type, slug, updated_at FROM content WHERE status='published' ORDER BY updated_at DESC LIMIT 5000");
            foreach ($st->fetchAll() as $r) {
                $loc = $r['type'] === 'page'
                    ? $base . '/page?slug=' . rawurlencode((string)$r['slug'])
                    : $base . '/news/view?slug=' . rawurlencode((string)$r['slug']);
                $urls[] = ['loc'=>$loc, 'lastmod'=>substr((string)$r['updated_at'],0,10), 'changefreq'=>'weekly', 'priority'=>'0.7'];
            }
        } catch (\Throwable $e) {
            // DB might not be ready; still produce minimal sitemap
        }

        $xml = ['<?xml version="1.0" encoding="UTF-8"?>', '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'];
        foreach ($urls as $u) {
            $xml[] = '  <url>';
            $xml[] = '    <loc>' . htmlspecialchars($u['loc'], ENT_QUOTES, 'UTF-8') . '</loc>';
            if (!empty($u['lastmod'])) $xml[] = '    <lastmod>' . htmlspecialchars($u['lastmod'], ENT_QUOTES, 'UTF-8') . '</lastmod>';
            if (!empty($u['changefreq'])) $xml[] = '    <changefreq>' . htmlspecialchars($u['changefreq'], ENT_QUOTES, 'UTF-8') . '</changefreq>';
            if (!empty($u['priority'])) $xml[] = '    <priority>' . htmlspecialchars($u['priority'], ENT_QUOTES, 'UTF-8') . '</priority>';
            $xml[] = '  </url>';
        }
        $xml[] = '</urlset>';
        return implode("\n", $xml);
    }
}
