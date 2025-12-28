<?php
namespace Admin\Controllers;

use Template\Template;

class UiController
{
    public function render(string $title, string $bodyHtml, array $vars = []): void
    {
        $tpl = new Template(theme: 'admin');
        $tpl->render('layout.tpl', array_merge([
            'title' => $title,
            'body' => $bodyHtml,
        ], $vars));
    }

    public function error(string $message, int $code = 400): void
    {
        http_response_code($code);
        $this->render('Ошибка', '<div class="rg-alert rg-alert-danger">'.htmlspecialchars($message, ENT_QUOTES, 'UTF-8').'</div>');
    }
}
