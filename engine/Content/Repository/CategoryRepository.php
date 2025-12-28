<?php
namespace Content\Repository;

use Database\Connection;
use Content\Entity\Category;

class CategoryRepository
{
    public function findBySlug(string $slug): ?Category
    {
        $pdo = Connection::pdo();
        $st = $pdo->prepare('SELECT * FROM categories WHERE slug = :slug LIMIT 1');
        $st->execute(['slug' => $slug]);
        $r = $st->fetch();
        return $r ? $this->map($r) : null;
    }

    public function find(int $id): ?Category
    {
        $pdo = Connection::pdo();
        $st = $pdo->prepare('SELECT * FROM categories WHERE id = :id LIMIT 1');
        $st->execute(['id' => $id]);
        $r = $st->fetch();
        return $r ? $this->map($r) : null;
    }

    /**
     * Return flat list ordered by parent_id, sort_order, title.
     */
    public function listAll(bool $onlyActive = true): array
    {
        $pdo = Connection::pdo();
        $sql = 'SELECT * FROM categories';
        if ($onlyActive) $sql .= ' WHERE is_active = 1';
        $sql .= ' ORDER BY parent_id IS NULL DESC, parent_id ASC, sort_order ASC, title ASC';
        $rows = $pdo->query($sql)->fetchAll();
        return array_map([$this, 'map'], $rows);
    }

    /**
     * Build tree: each node => ['cat'=>Category, 'children'=>[]]
     */
    public function tree(bool $onlyActive = true): array
    {
        $cats = $this->listAll($onlyActive);
        $byParent = [];
        foreach ($cats as $c) {
            $pid = $c->parent_id ?? 0;
            $byParent[$pid][] = $c;
        }
        $build = function($pid) use (&$build, &$byParent) {
            $out = [];
            foreach ($byParent[$pid] ?? [] as $c) {
                $out[] = ['cat'=>$c, 'children'=>$build($c->id)];
            }
            return $out;
        };
        return $build(0);
    }

    private function map(array $r): Category
    {
        return new Category(
            (int)$r['id'],
            $r['parent_id'] !== null ? (int)$r['parent_id'] : null,
            (string)$r['title'],
            (string)$r['slug'],
            (int)$r['sort_order'],
            (bool)$r['is_active']
        );
    }
}
