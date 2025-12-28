<?php
namespace Modules\api;
use Core\Response;

class Controller
{
    public function ping(){ Response::json(['ok'=>true,'pong'=>true,'version'=>trim(@file_get_contents(ROOT_PATH.'/system/version.txt'))]); }

    public function contentIndex(){ \API\Auth::requireScope('content.read'); $r=new \Content\ContentRepository(); Response::json(['ok'=>true,'data'=>$r->list($_GET)]); }
    public function contentGet(){ \API\Auth::requireScope('content.read'); $id=(int)($_GET['id']??0); $r=new \Content\ContentRepository(); $it=$r->get($id); if(!$it) Response::json(['ok'=>false,'error'=>'not_found'],404); Response::json(['ok'=>true,'data'=>$it]); }
    public function contentCreate(){ \API\Auth::requireScope('content.write'); $d=json_decode(file_get_contents('php://input'),true)?:[]; $r=new \Content\ContentRepository(); Response::json(['ok'=>true,'data'=>$r->create($d)],201); }
    public function contentUpdate(){ \API\Auth::requireScope('content.write'); $id=(int)($_GET['id']??0); $d=json_decode(file_get_contents('php://input'),true)?:[]; $r=new \Content\ContentRepository(); $it=$r->update($id,$d); if(!$it) Response::json(['ok'=>false,'error'=>'not_found'],404); Response::json(['ok'=>true,'data'=>$it]); }
    public function contentDelete(){ \API\Auth::requireScope('content.write'); $id=(int)($_GET['id']??0); $r=new \Content\ContentRepository(); Response::json(['ok'=>true,'deleted'=>$r->delete($id)]); }
    public function contentPublish(){ \API\Auth::requireScope('content.write'); $id=(int)($_GET['id']??0); $r=new \Content\ContentRepository(); $it=$r->publish($id); if(!$it) Response::json(['ok'=>false,'error'=>'not_found'],404); Response::json(['ok'=>true,'data'=>$it]); }

    public function categoriesIndex(){ \API\Auth::requireScope('content.read'); $r=new \Content\CategoryRepository(); Response::json(['ok'=>true,'data'=>$r->list()]); }
    public function typesIndex(){ \API\Auth::requireScope('content.read'); $r=new \Content\TypeRepository(); Response::json(['ok'=>true,'data'=>$r->all()]); }

    public function adminMe(){ \API\Auth::requireScope('admin.read'); Response::json(['ok'=>true,'actor'=>\API\Auth::actor(),'token'=>\API\Auth::token()]); }
    public function adminStats(){ \API\Auth::requireScope('admin.read'); $pdo=\Database\DB::pdo(); $cnt=(int)$pdo->query("SELECT COUNT(*) FROM ce_content_items")->fetchColumn(); $cat=(int)$pdo->query("SELECT COUNT(*) FROM ce_categories")->fetchColumn(); Response::json(['ok'=>true,'stats'=>['content_items'=>$cnt,'categories'=>$cat]]); }
    // UI Builder (v2.2)
    public function uiBlocks()
    {
        \API\Auth::requireScope('admin.read');
        $r = new \UIBuilder\Renderer();
        Response::json(['ok'=>true,'data'=>$r->registry()->list()]);
    }

    public function uiGet()
    {
        \API\Auth::requireScope('admin.read');
        $id = (int)($_GET['content_id'] ?? 0);
        $repo = new \UIBuilder\Repository();
        $layout = $repo->get($id);
        if (!$layout) $layout = \UIBuilder\Schema::defaultLayout('Page');
        Response::json(['ok'=>true,'data'=>$layout]);
    }

    public function uiSave()
    {
        \API\Auth::requireScope('admin.write');
        $id = (int)($_GET['content_id'] ?? 0);
        $layout = json_decode(file_get_contents('php://input'), true) ?: [];
        $repo = new \UIBuilder\Repository();
        $repo->save($id, $layout);
        Response::json(['ok'=>true,'saved'=>true]);
    }

    public function uiPreview()
    {
        \API\Auth::requireScope('admin.read');
        $layout = json_decode(file_get_contents('php://input'), true) ?: [];
        $renderer = new \UIBuilder\Renderer();
        $html = $renderer->render($layout, []);
        Response::json(['ok'=>true,'html'=>$html]);
    }

}
