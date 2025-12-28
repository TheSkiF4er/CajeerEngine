<?php
namespace UIBuilder;
class Renderer
{
    protected BlockRegistry $registry;
    public function __construct(?BlockRegistry $registry=null){ $this->registry = $registry ?: new BlockRegistry(); }
    public function registry(): BlockRegistry { return $this->registry; }

    public function render(array $layout, array $context=[]): string
    {
        $sections = (array)($layout['sections'] ?? []);
        $html = '';
        foreach ($sections as $sec) {
            $cls = htmlspecialchars((string)($sec['class'] ?? ''), ENT_QUOTES);
            $grid = (array)($sec['grid'] ?? []);
            $cols = max(1, (int)($grid['cols'] ?? 12));
            $gap = max(0, (int)($grid['gap'] ?? 4));
            $html .= '<section class="'.$cls.'">';
            $html .= '<div class="ce-uib-grid" style="display:grid;grid-template-columns:repeat('.$cols.',minmax(0,1fr));gap:'.$gap.'rem;">';
            foreach ((array)($sec['blocks'] ?? []) as $blk) {
                $type = (string)($blk['type'] ?? '');
                $span = max(1, min($cols, (int)(($blk['col']['span'] ?? 12))));
                $bcls = htmlspecialchars((string)($blk['class'] ?? ''), ENT_QUOTES);
                $html .= '<div class="ce-uib-block '.$bcls.'" style="grid-column: span '.$span.';">';
                $block = $this->registry->get($type);
                $html .= $block ? $block->render((array)($blk['props'] ?? []), $context) : ('<!-- Unknown block: '.htmlspecialchars($type,ENT_QUOTES).' -->');
                $html .= '</div>';
            }
            $html .= '</div></section>';
        }
        return $html;
    }
}
