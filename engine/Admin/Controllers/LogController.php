<?php
namespace Admin\Controllers;

use Database\Connection;

class LogController
{
    public function index(): void
    {
        $pdo = Connection::pdo();
        $rows = $pdo->query('SELECT * FROM action_logs ORDER BY id DESC LIMIT 200')->fetchAll();

        $trs = '';
        foreach ($rows as $r) {
            $trs .= '<tr>
              <td>'.(int)$r['id'].'</td>
              <td>'.htmlspecialchars($r['created_at'],ENT_QUOTES,'UTF-8').'</td>
              <td><b>'.htmlspecialchars($r['username'],ENT_QUOTES,'UTF-8').'</b></td>
              <td>'.htmlspecialchars($r['action'],ENT_QUOTES,'UTF-8').'</td>
              <td>'.htmlspecialchars($r['entity'],ENT_QUOTES,'UTF-8').' #'.htmlspecialchars((string)$r['entity_id'],ENT_QUOTES,'UTF-8').'</td>
              <td><code>'.htmlspecialchars((string)$r['ip'],ENT_QUOTES,'UTF-8').'</code></td>
            </tr>';
        }
        if (!$trs) $trs = '<tr><td colspan="6">Пусто</td></tr>';

        (new UiController())->render('Logs', '
        <div class="rg-container rg-mt-3">
          <div class="rg-card"><div class="rg-card-body">
            <div class="rg-btn-group">
              <a class="rg-btn rg-btn-secondary" href="/admin">← Назад</a>
            </div>
            <h1 class="rg-title rg-mt-2">Логи действий</h1>
            <table class="rg-table rg-table-striped">
              <thead><tr><th>ID</th><th>Дата</th><th>User</th><th>Action</th><th>Entity</th><th>IP</th></tr></thead>
              <tbody>'.$trs.'</tbody>
            </table>
          </div></div>
        </div>');
    }
}
