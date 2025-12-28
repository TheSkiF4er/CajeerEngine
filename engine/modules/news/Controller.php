<?php
namespace Modules\news;

use Template\Template;
use Content\Repository\ContentRepository;
use Content\Repository\CategoryRepository;
use Core\Request;

class Controller
{
    public function index(): void
    {
        $page = (int)Request::query('page', 1);
        $per = (int)Request::query('per', 10);
        $q = (string)Request::query('q', '');
        $catSlug = (string)Request::query('cat', '');
        $sort = (string)Request::query('sort', 'created_at');
        $dir = (string)Request::query('dir', 'DESC');

        $catRepo = new CategoryRepository();
        $cat = $catSlug ? $catRepo->findBySlug($catSlug) : null;

        $repo = new ContentRepository();
        $opts = [
            'page' => $page,
            'per_page' => $per,
            'status' => 'published',
            'q' => $q,
            'sort' => $sort,
            'dir' => $dir,
        ];
        if ($cat) $opts['category_id'] = $cat->id;

        $items = $repo->list('news', $opts);
        $total = $repo->count('news', $opts);
        $pages = max(1, (int)ceil($total / max(1, $per)));

        $tpl = new Template(theme: 'default');
        $tpl->render('news_list.tpl', [
            'title' => 'Новости',
            'items' => $items,
            'items_html' => \Content\View::renderList($items, 'news'),
            'pagination_html' => \Content\View::pagination($page, $pages, '/news?'),
            'page' => $page,
            'pages' => $pages,
            'q' => $q,
            'cat' => $catSlug,
            'sort' => $sort,
            'dir' => $dir,
        ]);
    }

    public function view(): void
    {
        $slug = (string)Request::query('slug', '');
        $repo = new ContentRepository();
        $item = $repo->findBySlug('news', $slug);

        $tpl = new Template(theme: 'default');
        if (!$item) {
            $tpl->render('error.tpl', [
                'title' => 'Не найдено',
                'message' => 'Новость не найдена',
            ]);
            return;
        }

        $tpl->render('news_full.tpl', [
            'title' => $item->title,
            'content' => $item->content,
            'excerpt' => $item->excerpt,
            'fields' => json_encode($item->fields, JSON_UNESCAPED_UNICODE),
        ]);
    }
}
