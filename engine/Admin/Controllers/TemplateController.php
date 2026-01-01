<?php
namespace Admin\Controllers;

use Security\Session;
use Admin\ActionLog;

class TemplateController
{
    private function templateRoot(): string
    {
        return ROOT_PATH . '/templates';
    }

    public function index(): void
    {
        Session::start();

        $root = $this->templateRoot();
        $files = [];

        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($root));
        foreach ($it as $f) {
            if (!$f->isFile()) continue;
            if (strtolower($f->getExtension()) !== 'tpl') continue;
            $rel = str_replace($root . '/', '', $f->getPathname());
            $files[] = $rel;
        }
        sort($files);

        $list = '<ul class="rg-list">';
        foreach ($files as $rel) {
            $list .= '<li><a href="/admin/templates/edit?file='.rawurlencode($rel).'">'.htmlspecialchars($rel,ENT_QUOTES,'UTF-8').'</a></li>';
        }
        $list .= '</ul>';

        (new UiController())->render('Templates', '
        <div class="rg-container rg-mt-3">
          <div class="rg-card"><div class="rg-card-body">
            <div class="rg-btn-group">
              <a class="rg-btn rg-btn-secondary" href="/admin">← Назад</a>
            </div>
            <h1 class="rg-title rg-mt-2">Шаблоны (.tpl)</h1>
            '.$list.'
          </div></div>
        </div>');
    }

    public function edit(): void
    {
        Session::start();
        $root = $this->templateRoot();
        $file = (string)($_GET['file'] ?? '');
        $safe = str_replace(['..','\\'], ['','/'], $file);
        $path = $root . '/' . $safe;

        if (!is_file($path) || strtolower(pathinfo($path, PATHINFO_EXTENSION)) !== 'tpl') {
            (new UiController())->error('Файл не найден', 404);
            return;
        }

        $msg = '';
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            if (!Session::verifyCsrf($_POST['csrf'] ?? null)) { (new UiController())->error('CSRF token mismatch',400); return; }
            $content = (string)($_POST['content'] ?? '');
            file_put_contents($path, $content);
            $msg = 'Сохранено';
            ActionLog::write('template_save', 'template', null, ['file'=>$safe]);
        }

        $content = (string)file_get_contents($path);

        (new UiController())->render('Template edit', '
        <div class="rg-container rg-mt-3">
          <div class="rg-card"><div class="rg-card-body">
            <div class="rg-btn-group">
              <a class="rg-btn rg-btn-secondary" href="/admin/templates">← К списку</a>
            </div>
            <h1 class="rg-title rg-mt-2">Редактор: '.htmlspecialchars($safe,ENT_QUOTES,'UTF-8').'</h1>
            '.($msg ? '<div class="rg-alert rg-alert-success">'.$msg.'</div>' : '').'
            <form method="post">
              <input type="hidden" name="csrf" value="'.htmlspecialchars(Session::csrf(),ENT_QUOTES,'UTF-8').'">
              <textarea class="rg-textarea" name="content" rows="22" style="font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;">'.htmlspecialchars($content,ENT_QUOTES,'UTF-8').'</textarea>
              <button class="rg-btn rg-btn-primary rg-mt-2" type="submit">Сохранить</button>
            </form>
          </div></div>
        </div>');
    }
}
