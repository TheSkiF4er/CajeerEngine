<?php
namespace Content;

use Content\Entity\ContentItem;

class View
{
    /**
     * Render list of items into HTML blocks (keeps .tpl syntax minimal).
     */
    public static function renderList(array $items, string $type = 'news'): string
    {
        $html = '';
        foreach ($items as $it) {
            if (!$it instanceof ContentItem) continue;
            $url = ($type === 'news')
                ? '/news/view?slug=' . rawurlencode($it->slug)
                : '/page?slug=' . rawurlencode($it->slug);

            $title = htmlspecialchars($it->title, ENT_QUOTES, 'UTF-8');
            $excerpt = htmlspecialchars($it->excerpt, ENT_QUOTES, 'UTF-8');

            $html .= '<div class="rg-card rg-mb-2"><div class="rg-card-body">';
            $html .= '<h3 class="rg-subtitle"><a href="'.$url.'">'.$title.'</a></h3>';
            $html .= '<div class="rg-text">'.$excerpt.'</div>';
            $html .= '</div></div>';
        }
        return $html ?: '<div class="rg-alert rg-alert-secondary">Пусто</div>';
    }

    public static function pagination(int $page, int $pages, string $baseUrl): string
    {
        if ($pages <= 1) return '';
        $html = '<nav class="rg-mt-2">';
        for ($p = 1; $p <= $pages; $p++) {
            $active = $p === $page ? ' rg-btn-primary' : ' rg-btn-secondary';
            $html .= '<a class="rg-btn'.$active.' rg-mr-1" href="'.$baseUrl.'page='.$p.'">'.$p.'</a>';
        }
        $html .= '</nav>';
        return $html;
    }
}
