<?php
namespace Admin\Controllers;

use Database\Connection;
use Security\Session;
use Admin\ActionLog;

class UserController
{
    public function index(): void
    {
        Session::start();
        $pdo = Connection::pdo();

        $users = $pdo->query('SELECT u.id,u.username,u.group_id,g.title as group_title FROM users u LEFT JOIN groups g ON g.id=u.group_id ORDER BY u.id ASC')->fetchAll();
        $groups = $pdo->query('SELECT * FROM groups ORDER BY id ASC')->fetchAll();

        $urows = '';
        foreach ($users as $u) {
            $urows .= '<tr>
              <td>'.(int)$u['id'].'</td>
              <td><b>'.htmlspecialchars($u['username'],ENT_QUOTES,'UTF-8').'</b></td>
              <td>'.(int)$u['group_id'].' — '.htmlspecialchars($u['group_title']??'',ENT_QUOTES,'UTF-8').'</td>
              <td><a class="rg-btn rg-btn-secondary" href="/admin/users/edit?id='.(int)$u['id'].'">Изменить</a></td>
            </tr>';
        }
        if (!$urows) $urows = '<tr><td colspan="4">Пусто</td></tr>';

        $grows = '';
        foreach ($groups as $g) {
            $grows .= '<tr>
              <td>'.(int)$g['id'].'</td>
              <td><b>'.htmlspecialchars($g['title'],ENT_QUOTES,'UTF-8').'</b></td>
              <td><code>'.htmlspecialchars($g['slug'],ENT_QUOTES,'UTF-8').'</code></td>
            </tr>';
        }

        (new UiController())->render('Users', '
        <div class="rg-container rg-mt-3">
          <div class="rg-card"><div class="rg-card-body">
            <div class="rg-btn-group">
              <a class="rg-btn rg-btn-secondary" href="/admin">← Назад</a>
            </div>
            <h1 class="rg-title rg-mt-2">Пользователи</h1>
            <table class="rg-table rg-table-striped">
              <thead><tr><th>ID</th><th>Логин</th><th>Группа</th><th></th></tr></thead>
              <tbody>'.$urows.'</tbody>
            </table>

            <h2 class="rg-subtitle rg-mt-3">Группы</h2>
            <table class="rg-table rg-table-striped">
              <thead><tr><th>ID</th><th>Название</th><th>Slug</th></tr></thead>
              <tbody>'.$grows.'</tbody>
            </table>
          </div></div>
        </div>');
    }

    public function edit(): void
    {
        Session::start();
        $id = (int)($_GET['id'] ?? 0);
        $pdo = Connection::pdo();

        $st = $pdo->prepare('SELECT * FROM users WHERE id=:id LIMIT 1');
        $st->execute(['id'=>$id]);
        $u = $st->fetch();
        if (!$u) { (new UiController())->error('Пользователь не найден',404); return; }

        $groups = $pdo->query('SELECT * FROM groups ORDER BY id ASC')->fetchAll();

        $msg = '';
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            if (!Session::verifyCsrf($_POST['csrf'] ?? null)) { (new UiController())->error('CSRF token mismatch',400); return; }
            $group_id = (int)($_POST['group_id'] ?? (int)$u['group_id']);
            $pwd = (string)($_POST['new_password'] ?? '');

            if ($pwd !== '') {
                $hash = password_hash($pwd, PASSWORD_DEFAULT);
                $st2 = $pdo->prepare('UPDATE users SET group_id=:g, password_hash=:h WHERE id=:id');
                $st2->execute(['g'=>$group_id,'h'=>$hash,'id'=>$id]);
                $msg = 'Сохранено (пароль обновлён)';
                ActionLog::write('user_update', 'user', $id, ['group_id'=>$group_id,'pwd'=>True]);
            } else {
                $st2 = $pdo->prepare('UPDATE users SET group_id=:g WHERE id=:id');
                $st2->execute(['g'=>$group_id,'id'=>$id]);
                $msg = 'Сохранено';
                ActionLog::write('user_update', 'user', $id, ['group_id'=>$group_id]);
            }
            $st->execute(['id'=>$id]);
            $u = $st->fetch();
        }

        $opts = '';
        foreach ($groups as $g) {
            $sel = ((int)$g['id']===(int)$u['group_id']) ? 'selected' : '';
            $opts .= '<option value="'.(int)$g['id'].'" '.$sel.'>'.htmlspecialchars($g['title'],ENT_QUOTES,'UTF-8').'</option>';
        }

        (new UiController())->render('User edit', '
        <div class="rg-container rg-mt-3">
          <div class="rg-card"><div class="rg-card-body">
            <div class="rg-btn-group">
              <a class="rg-btn rg-btn-secondary" href="/admin/users">← К списку</a>
            </div>

            <h1 class="rg-title rg-mt-2">Пользователь: '.htmlspecialchars($u['username'],ENT_QUOTES,'UTF-8').'</h1>
            '.($msg ? '<div class="rg-alert rg-alert-success">'.$msg.'</div>' : '').'

            <form method="post">
              <input type="hidden" name="csrf" value="'.htmlspecialchars(Session::csrf(),ENT_QUOTES,'UTF-8').'">

              <label class="rg-label">Группа</label>
              <select class="rg-select" name="group_id">'.$opts.'</select>

              <label class="rg-label rg-mt-2">Новый пароль (опционально)</label>
              <input class="rg-input" name="new_password" type="password" autocomplete="new-password">

              <button class="rg-btn rg-btn-primary rg-mt-3" type="submit">Сохранить</button>
            </form>
          </div></div>
        </div>');
    }
}
