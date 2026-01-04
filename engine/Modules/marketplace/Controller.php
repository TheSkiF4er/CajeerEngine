<?php
declare(strict_types=1);

namespace Modules\marketplace;

use Template\Template;
use Support\MarkdownLite;

/**
 * Marketplace landing page.
 *
 * IMPORTANT:
 * Core\Response::view() в этой кодовой базе просто выводит строку (echo) и завершает выполнение.
 * Для рендера .tpl используйте Template\Template, как сделано в других модулях (news/docs).
 */
final class Controller
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
        $mdFile = ROOT_PATH . '/docs/MARKETPLACE_RU.md';
        $md = is_file($mdFile)
            ? (string)@file_get_contents($mdFile)
            : "# Ресурсы\n\nДокументация не найдена: `docs/MARKETPLACE_RU.md`.";

        $html = MarkdownLite::toHtml($md);

        $tpl = new Template(theme: 'default');
        $tpl->render('marketplace.tpl', array_merge(
            $this->baseVars('Ресурсы', 'Каталог расширений, тем и модулей для CajeerEngine.'),
            [
                'content_html' => $html,
            ]
        ));
    }
}
