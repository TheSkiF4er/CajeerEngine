<?php
declare(strict_types=1);

namespace Modules\account;

use Support\Mailer;
use Template\Template;

final class Controller
{
    private string $usersDir;
    private string $usersFile;

    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $this->usersDir  = ROOT_PATH . '/storage/users';
        $this->usersFile = $this->usersDir . '/users.json';

        if (!is_dir($this->usersDir)) {
            @mkdir($this->usersDir, 0775, true);
        }
        if (!file_exists($this->usersFile)) {
            @file_put_contents($this->usersFile, json_encode(['users' => []], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
        }
    }

    private function baseUrl(): string
    {
        $env = (string)getenv('APP_BASE_URL');
        if ($env !== '') return rtrim($env, '/');

        $scheme = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http');
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return $scheme . '://' . $host;
    }

    private function csrf(): string
    {
        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(16));
        }
        return (string)$_SESSION['csrf'];
    }

    private function requireCsrf(): void
    {
        $token = (string)($_POST['csrf'] ?? '');
        if ($token === '' || !hash_equals((string)($_SESSION['csrf'] ?? ''), $token)) {
            $this->render('login.tpl', [
                'title' => 'Ошибка',
                'error' => 'Неверный CSRF-токен. Обновите страницу и повторите.',
                'csrf' => $this->csrf(),
            ]);
            exit;
        }
    }

    private function readUsers(): array
    {
        $raw = @file_get_contents($this->usersFile);
        $data = json_decode((string)$raw, true);
        if (!is_array($data) || !isset($data['users']) || !is_array($data['users'])) {
            $data = ['users' => []];
        }
        return $data;
    }

    private function writeUsers(array $data): void
    {
        @file_put_contents($this->usersFile, json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
    }

    private function findByEmail(array $data, string $email): ?array
    {
        foreach ($data['users'] as $u) {
            if (strtolower((string)$u['email']) === strtolower($email)) return $u;
        }
        return null;
    }

    private function updateUser(array &$data, array $user): void
    {
        foreach ($data['users'] as $i => $u) {
            if ((string)$u['id'] === (string)$user['id']) {
                $data['users'][$i] = $user;
                return;
            }
        }
        $data['users'][] = $user;
    }

    private function token(): string
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }

    private function hashToken(string $token): string
    {
        $salt = (string)getenv('APP_KEY');
        if ($salt === '') $salt = 'cajeerengine';
        return hash('sha256', $token . '|' . $salt);
    }

    private function render(string $tplFile, array $vars): void
    {
        $tpl = new Template(theme: 'default');
        $tpl->render($tplFile, $vars);
    }

    public function login(): void
    {
        $error = '';
        $notice = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->requireCsrf();

            $email = trim((string)($_POST['email'] ?? ''));
            $pass  = (string)($_POST['password'] ?? '');

            $data = $this->readUsers();
            $u = $this->findByEmail($data, $email);

            if (!$u || !password_verify($pass, (string)$u['password_hash'])) {
                $error = 'Неверный email или пароль.';
            } elseif (empty($u['email_verified'])) {
                $error = 'Почта не подтверждена. Проверьте письма или отправьте подтверждение повторно.';
                $notice = $email;
            } else {
                $_SESSION['user_id'] = (string)$u['id'];
                header('Location: /profile', true, 302);
                exit;
            }
        }

        $this->render('login.tpl', [
            'title' => 'Вход',
            'error' => $error,
            'prefill_email' => $notice,
            'csrf' => $this->csrf(),
        ]);
    }

    public function register(): void
    {
        $error = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->requireCsrf();

            $email = trim((string)($_POST['email'] ?? ''));
            $pass  = (string)($_POST['password'] ?? '');
            $pass2 = (string)($_POST['password2'] ?? '');

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Укажите корректный email.';
            } elseif (strlen($pass) < 8) {
                $error = 'Пароль должен быть не короче 8 символов.';
            } elseif ($pass !== $pass2) {
                $error = 'Пароли не совпадают.';
            } else {
                $data = $this->readUsers();
                if ($this->findByEmail($data, $email)) {
                    $error = 'Пользователь с таким email уже существует.';
                } else {
                    $id = bin2hex(random_bytes(8));
                    $verifyToken = $this->token();
                    $u = [
                        'id' => $id,
                        'email' => $email,
                        'password_hash' => password_hash($pass, PASSWORD_DEFAULT),
                        'created_at' => date('c'),
                        'email_verified' => false,
                        'verify_hash' => $this->hashToken($verifyToken),
                        'verify_expires' => time() + 24*3600,
                        'reset_hash' => null,
                        'reset_expires' => null,
                    ];

                    $data['users'][] = $u;
                    $this->writeUsers($data);

                    $link = $this->baseUrl() . '/verify?token=' . urlencode($verifyToken);
                    $html = '<h2>Подтверждение почты</h2>'
                          . '<p>Вы зарегистрировались в CajeerEngine. Подтвердите почту, перейдя по ссылке:</p>'
                          . '<p><a href="' . htmlspecialchars($link) . '">' . htmlspecialchars($link) . '</a></p>'
                          . '<p>Ссылка действует 24 часа.</p>';

                    Mailer::send($email, 'Подтверждение почты — CajeerEngine', $html);

                    $success = 'Регистрация завершена. На почту отправлено письмо с подтверждением.';
                }
            }
        }

        $this->render('register.tpl', [
            'title' => 'Регистрация',
            'error' => $error,
            'success' => $success,
            'csrf' => $this->csrf(),
        ]);
    }

    public function verify(): void
    {
        $token = trim((string)($_GET['token'] ?? ''));
        $ok = false;

        if ($token !== '') {
            $h = $this->hashToken($token);
            $data = $this->readUsers();

            foreach ($data['users'] as $i => $u) {
                if (!empty($u['verify_hash']) && hash_equals((string)$u['verify_hash'], $h)) {
                    $exp = (int)($u['verify_expires'] ?? 0);
                    if ($exp > 0 && time() <= $exp) {
                        $data['users'][$i]['email_verified'] = true;
                        $data['users'][$i]['verify_hash'] = null;
                        $data['users'][$i]['verify_expires'] = null;
                        $this->writeUsers($data);
                        $ok = true;
                    }
                    break;
                }
            }
        }

        $this->render('verify.tpl', [
            'title' => 'Подтверждение почты',
            'ok' => $ok ? '1' : '0',
        ]);
    }

    public function resendVerification(): void
    {
        $error = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->requireCsrf();
            $email = trim((string)($_POST['email'] ?? ''));

            $data = $this->readUsers();
            foreach ($data['users'] as $i => $u) {
                if (strtolower((string)$u['email']) === strtolower($email)) {
                    if (!empty($u['email_verified'])) {
                        $success = 'Почта уже подтверждена.';
                        break;
                    }
                    $verifyToken = $this->token();
                    $data['users'][$i]['verify_hash'] = $this->hashToken($verifyToken);
                    $data['users'][$i]['verify_expires'] = time() + 24*3600;
                    $this->writeUsers($data);

                    $link = $this->baseUrl() . '/verify?token=' . urlencode($verifyToken);
                    $html = '<h2>Подтверждение почты</h2>'
                          . '<p>Перейдите по ссылке, чтобы подтвердить почту:</p>'
                          . '<p><a href="' . htmlspecialchars($link) . '">' . htmlspecialchars($link) . '</a></p>'
                          . '<p>Ссылка действует 24 часа.</p>';
                    Mailer::send($email, 'Подтверждение почты — CajeerEngine', $html);

                    $success = 'Письмо с подтверждением отправлено.';
                    break;
                }
            }

            if ($success === '' && $error === '') {
                // Do not leak existence
                $success = 'Если email зарегистрирован, письмо с подтверждением будет отправлено.';
            }
        }

        $this->render('resend.tpl', [
            'title' => 'Повторная отправка подтверждения',
            'error' => $error,
            'success' => $success,
            'csrf' => $this->csrf(),
        ]);
    }

    public function forgot(): void
    {
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->requireCsrf();
            $email = trim((string)($_POST['email'] ?? ''));

            $data = $this->readUsers();
            foreach ($data['users'] as $i => $u) {
                if (strtolower((string)$u['email']) === strtolower($email)) {
                    $resetToken = $this->token();
                    $data['users'][$i]['reset_hash'] = $this->hashToken($resetToken);
                    $data['users'][$i]['reset_expires'] = time() + 3600;
                    $this->writeUsers($data);

                    $link = $this->baseUrl() . '/reset?token=' . urlencode($resetToken);
                    $html = '<h2>Восстановление пароля</h2>'
                          . '<p>Для сброса пароля перейдите по ссылке:</p>'
                          . '<p><a href="' . htmlspecialchars($link) . '">' . htmlspecialchars($link) . '</a></p>'
                          . '<p>Ссылка действует 1 час.</p>';
                    Mailer::send($email, 'Восстановление пароля — CajeerEngine', $html);
                    break;
                }
            }

            $success = 'Если email зарегистрирован, письмо для восстановления будет отправлено.';
        }

        $this->render('forgot.tpl', [
            'title' => 'Восстановление пароля',
            'success' => $success,
            'csrf' => $this->csrf(),
        ]);
    }

    public function reset(): void
    {
        $token = trim((string)($_GET['token'] ?? ($_POST['token'] ?? '')));
        $error = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->requireCsrf();
            $p1 = (string)($_POST['password'] ?? '');
            $p2 = (string)($_POST['password2'] ?? '');

            if ($token === '') {
                $error = 'Некорректная ссылка.';
            } elseif (strlen($p1) < 8) {
                $error = 'Пароль должен быть не короче 8 символов.';
            } elseif ($p1 !== $p2) {
                $error = 'Пароли не совпадают.';
            } else {
                $h = $this->hashToken($token);
                $data = $this->readUsers();
                $updated = false;

                foreach ($data['users'] as $i => $u) {
                    if (!empty($u['reset_hash']) && hash_equals((string)$u['reset_hash'], $h)) {
                        $exp = (int)($u['reset_expires'] ?? 0);
                        if ($exp > 0 && time() <= $exp) {
                            $data['users'][$i]['password_hash'] = password_hash($p1, PASSWORD_DEFAULT);
                            $data['users'][$i]['reset_hash'] = null;
                            $data['users'][$i]['reset_expires'] = null;
                            $this->writeUsers($data);
                            $updated = true;
                        }
                        break;
                    }
                }

                if ($updated) {
                    $success = 'Пароль обновлён. Теперь вы можете войти.';
                } else {
                    $error = 'Ссылка недействительна или истекла.';
                }
            }
        }

        $this->render('reset.tpl', [
            'title' => 'Сброс пароля',
            'token' => $token,
            'error' => $error,
            'success' => $success,
            'csrf' => $this->csrf(),
        ]);
    }

    public function profile(): void
    {
        $uid = (string)($_SESSION['user_id'] ?? '');
        if ($uid === '') {
            header('Location: /login', true, 302);
            exit;
        }

        $data = $this->readUsers();
        $user = null;
        foreach ($data['users'] as $u) {
            if ((string)$u['id'] === $uid) { $user = $u; break; }
        }
        if (!$user) {
            unset($_SESSION['user_id']);
            header('Location: /login', true, 302);
            exit;
        }

        $this->render('profile.tpl', [
            'title' => 'Профиль',
            'email' => (string)$user['email'],
            'verified' => !empty($user['email_verified']) ? '1' : '0',
            'csrf' => $this->csrf(),
        ]);
    }

    public function logout(): void
    {
        unset($_SESSION['user_id']);
        header('Location: /', true, 302);
        exit;
    }
}
