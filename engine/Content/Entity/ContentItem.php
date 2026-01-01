<?php
namespace Content\Entity;

class ContentItem
{
    public function __construct(
        public int $id,
        public string $type,           // news|page
        public int $category_id,
        public string $title,
        public string $slug,
        public string $excerpt,
        public string $content,
        public array $fields,          // custom fields (JSON)
        public string $status,         // published|draft
        public string $created_at,
        public string $updated_at
    ) {}
}
