<?php
namespace Modules\category;

use Template\Template;
use Content\Repository\CategoryRepository;
use Content\Repository\ContentRepository;
use Core\Request;

class Controller
{
    public function view(): void
    {
        $slug = (string)Request::query('slug', '');
        $type = (string)Request::query('type', 'news'); // news|page
        $page = (int)Request::query('page', 1);
        $per = (int)Request::query('per', 10);

        $catRepo = new CategoryRepository();
        $cat = $catRepo->findBySlug($slug);

        $tpl = new Template(theme: 'default');
        if (!$cat) {
            $tpl->render('error.tpl', [
                'title' => 'Не найдено',
                'message' => 'Категория не найдена',
            ]);
            return;
        }

        $repo = new ContentRepository();
        $opts = [
            'page' => $page,
            'per_page' => $per,
            'status' => 'published',
            'category_id' => $cat->id,
            'sort' => 'created_at',
            'dir' => 'DESC',
        ];
        $items = $repo->list($type, $opts);
        $total = $repo->count($type, $opts);
        $pages = max(1, (int)ceil($total / max(1, $per)));

        $tpl->render('category.tpl', [
            'title' => 'Категория: ' . $cat->title,
            'cat_title' => $cat->title,
            'cat_slug' => $cat->slug,
            'items' => $items,
            'items_html' => \Content\View::renderList($items, $type),
            'pagination_html' => \Content\View::pagination($page, $pages, '/category?slug='.rawurlencode($cat->slug).'&type='.rawurlencode($type).'&'),
            'type' => $type,
            'page' => $page,
            'pages' => $pages,
        ]);
    }
}
