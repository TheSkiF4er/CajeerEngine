<?php
namespace Admin\Controllers;

use Security\Auth;
use Security\Session;

class AuthController
{
    public function login(): void
    {
        Session::start();

        $error = '';
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            if (!Session::verifyCsrf($_POST['csrf'] ?? null)) {
                $error = 'CSRF token mismatch';
            } else {
                $u = (string)($_POST['username'] ?? '');
                $p = (string)($_POST['password'] ?? '');
                if (Auth::login($u, $p)) {
                    header('Location: /admin');
                    exit;
                }
                $error = 'Неверный логин или пароль';
            }
        }

        $ui = new UiController();
        $form = '
        <div class="rg-container rg-mt-4" style="max-width:520px">
          <div class="rg-card">
            <div class="rg-card-body">
              <h1 class="rg-title">Вход в админку</h1>'
              . ($error ? '<div class="rg-alert rg-alert-danger">'.htmlspecialchars($error,ENT_QUOTES,'UTF-8').'</div>' : '') .
              '<form method="post" action="/admin/login">
                <input type="hidden" name="csrf" value="'.htmlspecialchars(Session::csrf(),ENT_QUOTES,'UTF-8').'">
                <label class="rg-label">Логин</label>
                <input class="rg-input" name="username" autocomplete="username" required>
                <label class="rg-label rg-mt-2">Пароль</label>
                <input class="rg-input" type="password" name="password" autocomplete="current-password" required>
                <button class="rg-btn rg-btn-primary rg-mt-3" type="submit">Войти</button>
              </form>
              <div class="rg-mt-3 rg-text-muted">
                По умолчанию: <b>admin</b> / <b>admin</b> (после seed).
              </div>
            </div>
          </div>
        </div>';

        $ui->render('Login', $form);
    }

    public function logout(): void
    {
        Auth::logout();
        header('Location: /admin/login');
        exit;
    }
}
