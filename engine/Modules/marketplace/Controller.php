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
    public function index(): void
    {
        $mdFile = ROOT_PATH . '/docs/MARKETPLACE_RU.md';
        $md = is_file($mdFile)
            ? (string)@file_get_contents($mdFile)
            : "# Marketplace\n\nДокументация не найдена: `docs/MARKETPLACE_RU.md`.";

        $html = MarkdownLite::toHtml($md);

        $tpl = new Template(theme: 'default');
        $tpl->render('marketplace.tpl', [
            'title' => 'Marketplace',
            'content_html' => $html,
        ]);
    }
}