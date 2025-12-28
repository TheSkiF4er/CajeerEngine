<?php
namespace Content\Repository;

use Database\Connection;
use Content\Entity\ContentItem;

class ContentRepository
{
    public function findBySlug(string $type, string $slug): ?ContentItem
    {
        $pdo = Connection::pdo();
        $st = $pdo->prepare('SELECT * FROM content WHERE type = :type AND slug = :slug LIMIT 1');
        $st->execute(['type'=>$type, 'slug'=>$slug]);
        $r = $st->fetch();
        return $r ? $this->map($r) : null;
    }

    public function list(string $type, array $opts = []): array
    {
        $pdo = Connection::pdo();

        $page = max(1, (int)($opts['page'] ?? 1));
        $perPage = max(1, min(50, (int)($opts['per_page'] ?? 10)));
        $offset = ($page - 1) * $perPage;

        $where = ['type = :type'];
        $params = ['type' => $type];

        if (!empty($opts['status'])) {
            $where[] = 'status = :status';
            $params['status'] = $opts['status'];
        }

        if (!empty($opts['category_id'])) {
            $where[] = 'category_id = :cat';
            $params['cat'] = (int)$opts['category_id'];
        }

        if (!empty($opts['q'])) {
            $where[] = '(title LIKE :q OR content LIKE :q)';
            $params['q'] = '%' . $opts['q'] . '%';
        }

        $sort = $opts['sort'] ?? 'created_at';
        $dir = strtoupper($opts['dir'] ?? 'DESC');
        $allowedSort = ['created_at','updated_at','title','id'];
        if (!in_array($sort, $allowedSort, true)) $sort = 'created_at';
        if (!in_array($dir, ['ASC','DESC'], true)) $dir = 'DESC';

        $sql = 'SELECT * FROM content WHERE ' . implode(' AND ', $where)
             . " ORDER BY {$sort} {$dir} LIMIT {$perPage} OFFSET {$offset}";
        $st = $pdo->prepare($sql);
        $st->execute($params);
        $rows = $st->fetchAll();

        return array_map([$this, 'map'], $rows);
    }

    public function count(string $type, array $opts = []): int
    {
        $pdo = Connection::pdo();

        $where = ['type = :type'];
        $params = ['type' => $type];

        if (!empty($opts['status'])) { $where[] = 'status = :status'; $params['status'] = $opts['status']; }
        if (!empty($opts['category_id'])) { $where[] = 'category_id = :cat'; $params['cat'] = (int)$opts['category_id']; }
        if (!empty($opts['q'])) { $where[] = '(title LIKE :q OR content LIKE :q)'; $params['q'] = '%' . $opts['q'] . '%'; }

        $sql = 'SELECT COUNT(*) c FROM content WHERE ' . implode(' AND ', $where);
        $st = $pdo->prepare($sql);
        $st->execute($params);
        $r = $st->fetch();
        return (int)($r['c'] ?? 0);
    }

    private function map(array $r): ContentItem
    {
        $fields = [];
        if (!empty($r['fields_json'])) {
            $decoded = json_decode((string)$r['fields_json'], true);
            if (is_array($decoded)) $fields = $decoded;
        }

        return new ContentItem(
            (int)$r['id'],
            (string)$r['type'],
            (int)$r['category_id'],
            (string)$r['title'],
            (string)$r['slug'],
            (string)($r['excerpt'] ?? ''),
            (string)($r['content'] ?? ''),
            $fields,
            (string)$r['status'],
            (string)$r['created_at'],
            (string)$r['updated_at']
        );
    }
}
