<?php
namespace Modules\seo;

use Core\Response;
use Seo\Sitemap;

class Controller
{
    public function sitemap(): void
    {
        header('Content-Type: application/xml; charset=utf-8');
        echo Sitemap::generate();
    }

    public function robots(): void
    {
        header('Content-Type: text/plain; charset=utf-8');
        $file = ROOT_PATH . '/public/robots.txt';
        if (is_file($file)) {
            echo (string)file_get_contents($file);
            return;
        }
        echo "User-agent: *\nAllow: /\nSitemap: /sitemap.xml\n";
    }
}
