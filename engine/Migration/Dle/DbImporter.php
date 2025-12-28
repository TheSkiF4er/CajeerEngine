<?php
namespace Migration\Dle;

use Migration\Report;
use Database\Connection as TargetConnection;

class DbImporter
{
    public function import(array $cfg, Report $r): void
    {
        $pdo = Connection::connect($cfg['db']);
        $prefix = (string)($cfg['prefix'] ?? 'dle_');
        $import = (array)($cfg['import'] ?? []);

        $target = TargetConnection::pdo();

        if (($import['categories'] ?? true) === true) $this->importCategories($pdo, $target, $prefix, $r);
        if (($import['users'] ?? true) === true) $this->importUsers($pdo, $target, $prefix, $r);
        if (($import['posts'] ?? true) === true) $this->importPosts($pdo, $target, $prefix, $r);
        if (($import['static_pages'] ?? true) === true) $this->importStatic($pdo, $target, $prefix, $r);
    }

    private function importCategories(\PDO $src, \PDO $dst, string $prefix, Report $r): void
    {
        $table = $prefix.'category';
        try { $rows = $src->query("SELECT * FROM `{$table}`")->fetchAll(); }
        catch (\Throwable $e) { $r->warn('Не удалось прочитать категории DLE', ['error'=>$e->getMessage()]); return; }

        $cnt = 0;
        foreach ($rows as $row) {
            $name = (string)($row['name'] ?? $row['cat_name'] ?? '');
            $slug = (string)($row['alt_name'] ?? $row['slug'] ?? '');
            $parent = (int)($row['parentid'] ?? $row['parent_id'] ?? 0);
            if ($name === '') continue;
            if ($slug === '') $slug = $this->slug($name);

            $st = $dst->prepare("INSERT INTO categories (title, slug, parent_id) VALUES (:t,:s,:p)
              ON DUPLICATE KEY UPDATE title=VALUES(title), parent_id=VALUES(parent_id)");
            $st->execute([':t'=>$name, ':s'=>$slug, ':p'=>$parent ?: null]);
            $cnt++;
        }
        $r->info('Импорт категорий завершён', ['count'=>$cnt]);
    }

    private function importUsers(\PDO $src, \PDO $dst, string $prefix, Report $r): void
    {
        $table = $prefix.'users';
        try { $rows = $src->query("SELECT * FROM `{$table}` LIMIT 50000")->fetchAll(); }
        catch (\Throwable $e) { $r->warn('Не удалось прочитать пользователей DLE', ['error'=>$e->getMessage()]); return; }

        $cnt = 0;
        foreach ($rows as $row) {
            $username = (string)($row['name'] ?? $row['username'] ?? '');
            if ($username === '') continue;

            $tmp = bin2hex(random_bytes(8));
            $hash = password_hash($tmp, PASSWORD_BCRYPT);

            $st = $dst->prepare("INSERT INTO users (username, password_hash, group_id, created_at)
              VALUES (:u,:h,:g,NOW())
              ON DUPLICATE KEY UPDATE username=VALUES(username)");
            $st->execute([':u'=>$username, ':h'=>$hash, ':g'=>3]);
            $cnt++;
        }
        $r->warn('Пароли DLE не импортируются: назначены временные (нужен сброс)', ['count'=>$cnt]);
    }

    private function importPosts(\PDO $src, \PDO $dst, string $prefix, Report $r): void
    {
        $table = $prefix.'post';
        try { $rows = $src->query("SELECT * FROM `{$table}` ORDER BY id ASC LIMIT 100000")->fetchAll(); }
        catch (\Throwable $e) { $r->warn('Не удалось прочитать посты DLE', ['error'=>$e->getMessage()]); return; }

        $cnt = 0;
        foreach ($rows as $row) {
            $title = (string)($row['title'] ?? '');
            $slug = (string)($row['alt_name'] ?? '');
            $content = (string)($row['full_story'] ?? $row['story'] ?? '');
            $excerpt = (string)($row['short_story'] ?? '');
            $created = (string)($row['date'] ?? date('Y-m-d H:i:s'));
            $updated = $created;

            if ($title === '') continue;
            if ($slug === '') $slug = $this->slug($title);

            $status = ((int)($row['approve'] ?? 1) === 1) ? 'published' : 'draft';

            $st = $dst->prepare("INSERT INTO content (type, title, slug, excerpt, content, status, created_at, updated_at)
              VALUES ('news', :t, :s, :e, :c, :st, :ca, :ua)
              ON DUPLICATE KEY UPDATE title=VALUES(title), excerpt=VALUES(excerpt), content=VALUES(content), status=VALUES(status), updated_at=VALUES(updated_at)");
            $st->execute([
                ':t'=>$title, ':s'=>$slug, ':e'=>$excerpt, ':c'=>$content, ':st'=>$status,
                ':ca'=>$created, ':ua'=>$updated
            ]);
            $cnt++;
        }
        $r->info('Импорт новостей/постов завершён', ['count'=>$cnt]);
    }

    private function importStatic(\PDO $src, \PDO $dst, string $prefix, Report $r): void
    {
        $table = $prefix.'static';
        try { $rows = $src->query("SELECT * FROM `{$table}` ORDER BY id ASC LIMIT 50000")->fetchAll(); }
        catch (\Throwable $e) { $r->warn('Не удалось прочитать статические страницы DLE', ['error'=>$e->getMessage(), 'table'=>$table]); return; }

        $cnt = 0;
        foreach ($rows as $row) {
            $title = (string)($row['name'] ?? $row['title'] ?? '');
            $slug = (string)($row['alt_name'] ?? $row['slug'] ?? '');
            $content = (string)($row['template'] ?? $row['descr'] ?? $row['content'] ?? '');

            if ($title === '') continue;
            if ($slug === '') $slug = $this->slug($title);

            $st = $dst->prepare("INSERT INTO content (type, title, slug, excerpt, content, status, created_at, updated_at)
              VALUES ('page', :t, :s, '', :c, 'published', NOW(), NOW())
              ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content), updated_at=NOW()");
            $st->execute([':t'=>$title, ':s'=>$slug, ':c'=>$content]);
            $cnt++;
        }
        $r->info('Импорт статических страниц завершён', ['count'=>$cnt]);
    }

    private function slug(string $s): string
    {
        $s = mb_strtolower($s, 'UTF-8');
        $s = preg_replace('/[^a-z0-9\p{Cyrillic}]+/u', '-', $s);
        $s = trim($s, '-');
        return $s ?: 'item';
    }
}
