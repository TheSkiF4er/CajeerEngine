<?php
namespace Modules\marketplace;

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
            'title' => $title,
        ];
    }

    public function index(): void
    {
        $mdPath = ROOT_PATH . '/docs/MARKETPLACE_RU.md';
        $md = is_file($mdPath) ? (string)file_get_contents($mdPath) : "# Marketplace\n\nКаталог расширений, тем и пакетов для CajeerEngine.";
        $html = MarkdownLite::render($md);

        $tpl = new Template(theme: 'default');
        $tpl->render('marketplace.tpl', array_merge(
            $this->baseVars('Marketplace', 'Каталог расширений, тем и пакетов для CajeerEngine'),
            [
                'content_html' => $html,
            ]
        ));
    }
}
