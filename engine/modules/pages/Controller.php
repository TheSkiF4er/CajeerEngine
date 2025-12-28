<?php
namespace Modules\pages;

use Template\Template;
use Content\Repository\ContentRepository;
use Core\Request;

class Controller
{
    public function view(): void
    {
        $slug = (string)Request::query('slug', 'about');
        $repo = new ContentRepository();
        $item = $repo->findBySlug('page', $slug);

        $tpl = new Template(theme: 'default');
        if (!$item) {
            $tpl->render('error.tpl', [
                'title' => 'Не найдено',
                'message' => 'Страница не найдена',
            ]);
            return;
        }

        $tpl->render('page.tpl', [
            'title' => $item->title,
            'content' => $item->content,
            'fields' => json_encode($item->fields, JSON_UNESCAPED_UNICODE),
        ]);
    }
}
