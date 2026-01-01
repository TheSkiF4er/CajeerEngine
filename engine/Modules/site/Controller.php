<?php
namespace Modules\site;

use Template\Template;

class Controller
{
    protected function baseVars(string $title, string $desc = ''): array
    {
        $version = trim((string)@file_get_contents(ROOT_PATH . '/system/version.txt')) ?: '0.0.0';
        $canonical = (isset($_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI']))
            ? ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']
            : '';

        return [
            'seo_title' => $title . ' — CajeerEngine',
            'seo_description' => $desc ?: 'CajeerEngine — CMS-платформа нового поколения.',
            'seo_canonical' => $canonical,
            'seo_og' => '',
            'seo_twitter' => '',
            'head_extra' => '',
            'body_extra' => '',
            'year' => date('Y'),
            'runtime_mode' => 'web',
            'app_version' => $version,
        ];
    }

    public function docs(): void
    {
        $tpl = new Template();
        $tpl->render('docs.tpl', $this->baseVars('Документация', 'Документация и быстрый старт CajeerEngine.'));
    }

    public function api(): void
    {
        $tpl = new Template();
        $tpl->render('api.tpl', $this->baseVars('API', 'Headless API и точки интеграции CajeerEngine.'));
    }

    public function rarog(): void
    {
        $tpl = new Template();
        $tpl->render('rarog.tpl', $this->baseVars('Rarog', 'Rarog UI — официальный UI-слой и дизайн-система для CajeerEngine.'));
    }
}
