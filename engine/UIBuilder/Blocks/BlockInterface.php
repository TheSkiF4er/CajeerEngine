<?php
namespace UIBuilder\Blocks;
interface BlockInterface { public function type(): string; public function title(): string; public function schema(): array; public function render(array $props, array $context=[]): string; }
