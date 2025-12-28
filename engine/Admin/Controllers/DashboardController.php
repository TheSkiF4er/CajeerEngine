<?php
namespace Admin\Controllers;

use Security\Auth;

class DashboardController
{
    public function index(): void
    {
        $u = Auth::user();
        $ui = new UiController();

        $body = '
        <div class="rg-container rg-mt-3">
          <div class="rg-card">
            <div class="rg-card-body">
              <h1 class="rg-title">Админ-панель</h1>
              <div class="rg-alert rg-alert-info">Вы вошли как <b>'.htmlspecialchars($u['username']??'',ENT_QUOTES,'UTF-8').'</b> ('.htmlspecialchars($u['group_title']??'',ENT_QUOTES,'UTF-8').')</div>
              <div class="rg-btn-group rg-mt-2">
                <a class="rg-btn rg-btn-primary" href="/admin/content">Контент</a>
                <a class="rg-btn rg-btn-secondary" href="/admin/templates">Шаблоны</a>
                <a class="rg-btn rg-btn-secondary" href="/admin/users">Пользователи/Группы</a>
                <a class="rg-btn rg-btn-secondary" href="/admin/logs">Логи действий</a>
                <a class="rg-btn rg-btn-danger" href="/admin/logout">Выход</a>
              </div>
            </div>
          </div>
        </div>';

        $ui->render('Dashboard', $body);
    }
}
