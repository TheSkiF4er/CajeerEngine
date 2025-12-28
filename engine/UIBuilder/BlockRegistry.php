<?php
namespace UIBuilder;
use UIBuilder\Blocks\BlockInterface;
use UIBuilder\Blocks\TextBlock;
use UIBuilder\Blocks\ImageBlock;
use UIBuilder\Blocks\GalleryBlock;
use UIBuilder\Blocks\FormBlock;
use UIBuilder\Blocks\HtmlBlock;
use UIBuilder\Blocks\ModuleBlock;
class BlockRegistry
{
    /** @var array<string, BlockInterface> */ protected array $blocks = [];
    public function __construct()
    {
        $this->register(new TextBlock());
        $this->register(new ImageBlock());
        $this->register(new GalleryBlock());
        $this->register(new FormBlock());
        $this->register(new HtmlBlock());
        $this->register(new ModuleBlock());
    }
    public function register(BlockInterface $block): void { $this->blocks[$block->type()] = $block; }
    public function get(string $type): ?BlockInterface { return $this->blocks[$type] ?? null; }
    public function list(): array
    {
        $out = [];
        foreach ($this->blocks as $type => $blk) $out[] = ['type'=>$type,'title'=>$blk->title(),'schema'=>$blk->schema()];
        return $out;
    }
}
