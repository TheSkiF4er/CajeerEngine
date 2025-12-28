<?php
namespace Content\Entity;

class Category
{
    public function __construct(
        public int $id,
        public ?int $parent_id,
        public string $title,
        public string $slug,
        public int $sort_order,
        public bool $is_active
    ) {}
}
