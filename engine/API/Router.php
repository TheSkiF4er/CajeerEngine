<?php
namespace API;

use Core\Response;

class Router
{
    public static function handle(string $path): void
    {
        $path = rtrim($path, '/');
        if ($path === '') $path = '/';

        if ($path === '/api') {
            Response::json(['ok'=>true,'name'=>'CajeerEngine API','version'=>trim(@file_get_contents(ROOT_PATH.'/system/version.txt'))]);
        }

        if ($path === '/api/v1/site') {
            Auth::require('read');
            $site = \Sites\SiteManager::resolve();
            Response::json(['ok'=>true,'site'=>['key'=>$site->key,'title'=>$site->title(),'base_url'=>$site->baseUrl(),'theme'=>$site->theme()]]);
        }

        if ($path === '/api/v1/content/list') {
            Auth::require('read');
            $type = (string)($_GET['type'] ?? 'news');
            $page = max(1, (int)($_GET['page'] ?? 1));
            $per = max(1, min(100, (int)($_GET['per_page'] ?? 20)));
            Response::json(['ok'=>true,'type'=>$type,'page'=>$page,'per_page'=>$per,'items'=>[]]);
        }

        if ($path === '/api/v1/marketplace/status') {
            Auth::require('read');
            $c = new \Marketplace\Client();
            Response::json(['ok'=>true,'marketplace'=>$c->status()]);
        }

        http_response_code(404);
        Response::json(['ok'=>false,'error'=>'Not Found']);
    }
}
