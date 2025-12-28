<?php
namespace Admin\Controllers;

use Content\Repository\ContentRepository;
use Database\Connection;
use Security\Session;
use Admin\ActionLog;

class ContentController
{
    public function index(): void
    {
        Session::start();
        $type = (string)($_GET['type'] ?? 'news'); // news|page
        $q = (string)($_GET['q'] ?? '');
        $page = max(1, (int)($_GET['page'] ?? 1));
        $per = max(1, min(50, (int)($_GET['per'] ?? 15)));
        $sort = (string)($_GET['sort'] ?? 'created_at');
        $dir = (string)($_GET['dir'] ?? 'DESC');

        // Bulk
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            if (!Session::verifyCsrf($_POST['csrf'] ?? null)) {
                (new UiController())->error('CSRF token mismatch', 400); return;
            }
            $ids = array_map('intval', (array)($_POST['ids'] ?? []));
            $action = (string)($_POST['bulk'] ?? '');
            if ($ids && in_array($action, ['publish','draft','delete'], true)) {
                $pdo = Connection::pdo();
                if ($action === 'delete') {
                    $in = implode(',', array_fill(0, count($ids), '?'));
                    $st = $pdo->prepare("DELETE FROM content WHERE id IN ($in)");
                    $st->execute($ids);
                } else {
                    $status = $action === 'publish' ? 'published' : 'draft';
                    $in = implode(',', array_fill(0, count($ids), '?'));
                    $st = $pdo->prepare("UPDATE content SET status=?, updated_at=NOW() WHERE id IN ($in)");
                    $st->execute(array_merge([$status], $ids));
                }
                ActionLog::write('bulk', 'content', null, ['action'=>$action,'ids'=>$ids,'type'=>$type]);
            }
            header('Location: /admin/content?type='.rawurlencode($type));
            exit;
        }

        $repo = new ContentRepository();
        $opts = [
            'page' => $page, 'per_page'=>$per, 'q'=>$q,
            'sort'=>$sort, 'dir'=>$dir
        ];
        $items = $repo->list($type, $opts);
        $total = $repo->count($type, $opts);
        $pages = max(1, (int)ceil($total / max(1, $per)));

        $rows = '';
        foreach ($items as $it) {
            $rows .= '<tr>
              <td><input type="checkbox" name="ids[]" value="'.(int)$it->id.'"></td>
              <td><b>'.htmlspecialchars($it->title,ENT_QUOTES,'UTF-8').'</b><div class="rg-text-muted">slug: '.htmlspecialchars($it->slug,ENT_QUOTES,'UTF-8').'</div></td>
              <td>'.htmlspecialchars($it->status,ENT_QUOTES,'UTF-8').'</td>
              <td>'.htmlspecialchars($it->created_at,ENT_QUOTES,'UTF-8').'</td>
              <td>
                <a class="rg-btn rg-btn-secondary" href="/admin/content/edit?id='.(int)$it->id.'">Редактировать</a>
                <a class="rg-btn rg-btn-danger" href="/admin/content/delete?id='.(int)$it->id.'&csrf='.htmlspecialchars(Session::csrf(),ENT_QUOTES,'UTF-8').'">Удалить</a>
              </td>
            </tr>';
        }
        if (!$rows) $rows = '<tr><td colspan="5">Пусто</td></tr>';

        $pager = '';
        if ($pages > 1) {
            for ($p=1;$p<=$pages;$p++) {
                $cls = $p===$page ? 'rg-btn-primary' : 'rg-btn-secondary';
                $pager .= '<a class="rg-btn '.$cls.' rg-mr-1" href="/admin/content?type='.rawurlencode($type).'&page='.$p.'">'. $p .'</a>';
            }
        }

        $ui = new UiController();
        $body = '
        <div class="rg-container rg-mt-3">
          <div class="rg-card">
            <div class="rg-card-body">
              <div class="rg-btn-group">
                <a class="rg-btn rg-btn-secondary" href="/admin">← Назад</a>
                <a class="rg-btn rg-btn-primary" href="/admin/content/create?type='.htmlspecialchars($type,ENT_QUOTES,'UTF-8').'">Создать</a>
              </div>

              <h1 class="rg-title rg-mt-2">Контент</h1>

              <form class="rg-mt-2" method="get" action="/admin/content">
                <input type="hidden" name="type" value="'.htmlspecialchars($type,ENT_QUOTES,'UTF-8').'">
                <input class="rg-input" name="q" value="'.htmlspecialchars($q,ENT_QUOTES,'UTF-8').'" placeholder="Поиск">
                <button class="rg-btn rg-btn-secondary rg-mt-2" type="submit">Найти</button>
              </form>

              <form class="rg-mt-2" method="post" action="/admin/content?type='.htmlspecialchars($type,ENT_QUOTES,'UTF-8').'">
                <input type="hidden" name="csrf" value="'.htmlspecialchars(Session::csrf(),ENT_QUOTES,'UTF-8').'">

                <div class="rg-btn-group rg-mb-2">
                  <select class="rg-select" name="bulk">
                    <option value="">Массовое действие</option>
                    <option value="publish">Опубликовать</option>
                    <option value="draft">В черновики</option>
                    <option value="delete">Удалить</option>
                  </select>
                  <button class="rg-btn rg-btn-secondary" type="submit">Применить</button>
                </div>

                <table class="rg-table rg-table-striped">
                  <thead><tr>
                    <th></th><th>Заголовок</th><th>Статус</th><th>Создано</th><th>Действия</th>
                  </tr></thead>
                  <tbody>'.$rows.'</tbody>
                </table>
              </form>

              <div class="rg-mt-2">'.$pager.'</div>
            </div>
          </div>
        </div>';

        $ui->render('Content', $body);
    }

    public function create(): void
    {
        Session::start();
        $type = (string)($_GET['type'] ?? 'news');

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            if (!Session::verifyCsrf($_POST['csrf'] ?? null)) { (new UiController())->error('CSRF token mismatch',400); return; }

            $title = trim((string)($_POST['title'] ?? ''));
            $slug = trim((string)($_POST['slug'] ?? ''));
            $excerpt = (string)($_POST['excerpt'] ?? '');
            $content = (string)($_POST['content'] ?? '');
            $status = (string)($_POST['status'] ?? 'published');
            $fields_json = (string)($_POST['fields_json'] ?? '{}');

            $pdo = Connection::pdo();
            $st = $pdo->prepare('INSERT INTO content (type, category_id, title, slug, excerpt, content, fields_json, status, created_at, updated_at) VALUES (:t,0,:title,:slug,:ex,:c,:f,:s,NOW(),NOW())');
            $st->execute([
                't'=>$type,'title'=>$title,'slug'=>$slug,'ex'=>$excerpt,'c'=>$content,'f'=>$fields_json,'s'=>$status
            ]);
            $id = (int)$pdo->lastInsertId();
            ActionLog::write('create', 'content', $id, ['type'=>$type,'slug'=>$slug]);

            header('Location: /admin/content/edit?id='.$id);
            exit;
        }

        $ui = new UiController();
        $body = $this->form('Создать', $type, [
            'id'=>0,'title'=>'','slug'=>'','excerpt'=>'','content'=>'','status'=>'published','fields_json'=>'{}'
        ]);
        $ui->render('Create', $body);
    }

    public function edit(): void
    {
        Session::start();
        $id = (int)($_GET['id'] ?? 0);
        $pdo = Connection::pdo();

        $st = $pdo->prepare('SELECT * FROM content WHERE id=:id LIMIT 1');
        $st->execute(['id'=>$id]);
        $row = $st->fetch();
        if (!$row) { (new UiController())->error('Запись не найдена',404); return; }

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            if (!Session::verifyCsrf($_POST['csrf'] ?? null)) { (new UiController())->error('CSRF token mismatch',400); return; }

            $title = trim((string)($_POST['title'] ?? ''));
            $slug = trim((string)($_POST['slug'] ?? ''));
            $excerpt = (string)($_POST['excerpt'] ?? '');
            $content = (string)($_POST['content'] ?? '');
            $status = (string)($_POST['status'] ?? 'published');
            $fields_json = (string)($_POST['fields_json'] ?? '{}');

            $st2 = $pdo->prepare('UPDATE content SET title=:title, slug=:slug, excerpt=:ex, content=:c, fields_json=:f, status=:s, updated_at=NOW() WHERE id=:id');
            $st2->execute(['title'=>$title,'slug'=>$slug,'ex'=>$excerpt,'c'=>$content,'f'=>$fields_json,'s'=>$status,'id'=>$id]);
            ActionLog::write('update', 'content', $id, ['slug'=>$slug]);

            header('Location: /admin/content/edit?id='.$id);
            exit;
        }

        $ui = new UiController();
        $body = $this->form('Редактировать', (string)$row['type'], [
            'id'=>$id,
            'title'=>$row['title'],
            'slug'=>$row['slug'],
            'excerpt'=>$row['excerpt'] ?? '',
            'content'=>$row['content'] ?? '',
            'status'=>$row['status'],
            'fields_json'=>$row['fields_json'] ?? '{}'
        ]);
        $ui->render('Edit', $body);
    }

    public function delete(): void
    {
        Session::start();
        if (!Session::verifyCsrf($_GET['csrf'] ?? null)) { (new UiController())->error('CSRF token mismatch',400); return; }
        $id = (int)($_GET['id'] ?? 0);
        $pdo = Connection::pdo();
        $st = $pdo->prepare('DELETE FROM content WHERE id=:id');
        $st->execute(['id'=>$id]);
        ActionLog::write('delete', 'content', $id, []);
        header('Location: /admin/content');
        exit;
    }

    private function form(string $h, string $type, array $d): string
    {
        $csrf = htmlspecialchars(Session::csrf(), ENT_QUOTES, 'UTF-8');
        $id = (int)$d['id'];
        return '
        <div class="rg-container rg-mt-3">
          <div class="rg-card">
            <div class="rg-card-body">
              <div class="rg-btn-group">
                <a class="rg-btn rg-btn-secondary" href="/admin/content?type='.htmlspecialchars($type,ENT_QUOTES,'UTF-8').'">← К списку</a>
              </div>

              <h1 class="rg-title rg-mt-2">'.$h.' ('.htmlspecialchars($type,ENT_QUOTES,'UTF-8').')</h1>

              <form method="post">
                <input type="hidden" name="csrf" value="'.$csrf.'">

                <label class="rg-label">Заголовок</label>
                <input class="rg-input" name="title" value="'.htmlspecialchars((string)$d['title'],ENT_QUOTES,'UTF-8').'" required>

                <label class="rg-label rg-mt-2">Slug</label>
                <input class="rg-input" name="slug" value="'.htmlspecialchars((string)$d['slug'],ENT_QUOTES,'UTF-8').'" required>

                <label class="rg-label rg-mt-2">Статус</label>
                <select class="rg-select" name="status">
                  <option value="published" '.(((string)$d['status']==='published')?'selected':'').'>published</option>
                  <option value="draft" '.(((string)$d['status']==='draft')?'selected':'').'>draft</option>
                </select>

                <label class="rg-label rg-mt-2">Excerpt</label>
                <textarea class="rg-textarea" name="excerpt" rows="3">'.htmlspecialchars((string)$d['excerpt'],ENT_QUOTES,'UTF-8').'</textarea>

                <label class="rg-label rg-mt-2">Content</label>
                <textarea class="rg-textarea" name="content" rows="10">'.htmlspecialchars((string)$d['content'],ENT_QUOTES,'UTF-8').'</textarea>

                <label class="rg-label rg-mt-2">Доп. поля (JSON)</label>
                <textarea class="rg-textarea" name="fields_json" rows="6">'.htmlspecialchars((string)$d['fields_json'],ENT_QUOTES,'UTF-8').'</textarea>

                <button class="rg-btn rg-btn-primary rg-mt-3" type="submit">Сохранить</button>
              </form>
            </div>
          </div>
        </div>';
    }
}
