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

    // Marketplace (v2.3)
    public function marketplaceIndex()
    {
        \API\Auth::requireScope('admin.read');
        $cfg = require ROOT_PATH . '/system/marketplace.php';
        $client = new \Marketplace\Client($cfg);
        $data = $client->index();
        Response::json(['ok'=>true,'data'=>$data]);
    }

    public function marketplaceInstalled()
    {
        \API\Auth::requireScope('admin.read');
        $pdo = \Database\DB::pdo();
        $rows = [];
        if ($pdo) {
            $rows = $pdo->query("SELECT type,name,version,title,publisher_id,installed_at,updated_at FROM ce_marketplace_packages ORDER BY type,name")->fetchAll(\PDO::FETCH_ASSOC);
        }
        Response::json(['ok'=>true,'data'=>$rows]);
    }

    public function marketplaceUploadInstall()
    {
        \API\Auth::requireScope('admin.write');
        $cfg = require ROOT_PATH . '/system/marketplace.php';
        if (empty($cfg['allow_local_upload'])) Response::json(['ok'=>false,'error'=>'local_upload_disabled']);
        if (empty($_FILES['package']['tmp_name'])) Response::json(['ok'=>false,'error'=>'no_file']);

        $dbCfg = require ROOT_PATH . '/system/config.php';
        \Database\DB::connect($dbCfg['db']);
        $pdo = \Database\DB::pdo();
        if ($pdo) $pdo->exec(file_get_contents(ROOT_PATH . '/system/sql/marketplace_v2_3.sql'));

        $mgr = new \Marketplace\PackageManager($cfg);
        $res = $mgr->installFromFile($_FILES['package']['tmp_name']);
        Response::json($res);
    }

    public function marketplaceTrustPublisher()
    {
        \API\Auth::requireScope('admin.write');
        $id = trim((string)($_POST['publisher_id'] ?? ''));
        $key = trim((string)($_POST['pubkey_ed25519'] ?? ''));
        $title = trim((string)($_POST['title'] ?? $id));
        if ($id === '' || $key === '') Response::json(['ok'=>false,'error'=>'invalid_input']);

        $dbCfg = require ROOT_PATH . '/system/config.php';
        \Database\DB::connect($dbCfg['db']);
        $pdo = \Database\DB::pdo();
        if ($pdo) {
            $pdo->exec(file_get_contents(ROOT_PATH . '/system/sql/marketplace_v2_3.sql'));
            $pdo->prepare("INSERT INTO ce_marketplace_publishers(publisher_id,title,pubkey_ed25519,trusted,created_at,updated_at)
              VALUES(:id,:t,:k,1,NOW(),NOW())
              ON DUPLICATE KEY UPDATE title=:t2,pubkey_ed25519=:k2,trusted=1,updated_at=NOW()")
              ->execute([':id'=>$id,':t'=>$title,':k'=>$key,':t2'=>$title,':k2'=>$key]);
        }
        Response::json(['ok'=>true,'trusted'=>true]);
    }

    // Security / Enterprise (v2.4)
    public function csrfToken()
    {
        $sec = is_file(ROOT_PATH.'/system/security.php') ? require ROOT_PATH.'/system/security.php' : [];
        $t = \Security\CSRF::token((array)($sec['csrf'] ?? []));
        Response::json(['ok'=>true,'token'=>$t]);
    }

    public function auditList()
    {
        \API\Auth::requireScope('admin.read');
        $pdo = \Database\DB::pdo();
        $rows = [];
        if ($pdo) {
            $pdo->exec("CREATE TABLE IF NOT EXISTS ce_audit_logs (
                id BIGINT NOT NULL AUTO_INCREMENT,
                created_at DATETIME NULL,
                user_id INT NULL,
                workspace_id INT NULL,
                action VARCHAR(190) NOT NULL,
                ip VARCHAR(64) NULL,
                user_agent VARCHAR(255) NULL,
                context_json MEDIUMTEXT NULL,
                PRIMARY KEY (id),
                KEY idx_action (action),
                KEY idx_user (user_id),
                KEY idx_ws (workspace_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            $limit = max(1, min(200, (int)($_GET['limit'] ?? 50)));
            $rows = $pdo->query("SELECT id,created_at,user_id,workspace_id,action,ip,user_agent,context_json FROM ce_audit_logs ORDER BY id DESC LIMIT $limit")->fetchAll(\PDO::FETCH_ASSOC);
        }
        Response::json(['ok'=>true,'data'=>$rows]);
    }

    public function workflowTransition()
    {
        \API\Auth::requireScope('admin.write');
        $id = (int)($_GET['content_id'] ?? 0);
        $to = (string)($_GET['to'] ?? '');
        $ws = \Core\Workspace::currentId();

        $user = \API\Auth::user();
        if (!\Permissions\Policy::allows($user, 'content.workflow', ['workspace_id'=>$ws,'content_id'=>$id])) {
            Response::json(['ok'=>false,'error'=>'forbidden']);
        }

        $pdo = \Database\DB::pdo();
        if (!$pdo) Response::json(['ok'=>false,'error'=>'db_required']);

        // read current state
        $st = $pdo->prepare("SELECT workflow_state FROM ce_content_items WHERE id=:id LIMIT 1");
        $st->execute([':id'=>$id]);
        $row = $st->fetch(\PDO::FETCH_ASSOC);
        $from = (string)($row['workflow_state'] ?? 'draft');

        if (!\Workflow\Workflow::canTransition($from, $to)) {
            Response::json(['ok'=>false,'error'=>'invalid_transition','from'=>$from,'to'=>$to]);
        }

        $publishedAt = null;
        if ($to === \Workflow\Workflow::PUBLISHED) $publishedAt = date('Y-m-d H:i:s');

        $st = $pdo->prepare("UPDATE ce_content_items SET workflow_state=:s, published_at=COALESCE(:p, published_at) WHERE id=:id");
        $st->execute([':s'=>$to, ':p'=>$publishedAt, ':id'=>$id]);

        \Audit\AuditLogger::log('workflow.transition', ['user_id'=>$user['id'] ?? null,'workspace_id'=>$ws,'content_id'=>$id,'from'=>$from,'to'=>$to]);
        Response::json(['ok'=>true,'from'=>$from,'to'=>$to]);
    }

    public function workflowSchedule()
    {
        \API\Auth::requireScope('admin.write');
        $id = (int)($_GET['content_id'] ?? 0);
        $at = (string)($_GET['at'] ?? ''); // Y-m-d H:i:s
        $ws = \Core\Workspace::currentId();
        $user = \API\Auth::user();

        if (!\Permissions\Policy::allows($user, 'content.schedule', ['workspace_id'=>$ws,'content_id'=>$id])) {
            Response::json(['ok'=>false,'error'=>'forbidden']);
        }

        $pdo = \Database\DB::pdo();
        if (!$pdo) Response::json(['ok'=>false,'error'=>'db_required']);

        $st = $pdo->prepare("UPDATE ce_content_items SET scheduled_at=:a WHERE id=:id");
        $st->execute([':a'=>$at, ':id'=>$id]);

        \Audit\AuditLogger::log('workflow.schedule', ['user_id'=>$user['id'] ?? null,'workspace_id'=>$ws,'content_id'=>$id,'at'=>$at]);
        Response::json(['ok'=>true,'scheduled_at'=>$at]);
    }

    // Platform (v2.5)
    public function platformHealth()
    {
        Response::json(\AutoUpdate\Health::ok());
    }

    public function platformContext()
    {
        \API\Auth::requireScope('admin.read');
        Response::json([
            'ok'=>true,
            'tenant_id'=>\Platform\Context::tenantId(),
            'site_id'=>\Platform\Context::siteId(),
        ]);
    }

    public function platformTenantCreate()
    {
        \API\Auth::requireScope('admin.write');
        $pdo = \Database\DB::pdo();
        if (!$pdo) Response::json(['ok'=>false,'error'=>'db_required']);
        $pdo->exec(file_get_contents(ROOT_PATH . '/system/sql/platform_v2_5.sql'));

        $slug = trim((string)($_POST['slug'] ?? ''));
        $title = trim((string)($_POST['title'] ?? $slug));
        if ($slug === '') Response::json(['ok'=>false,'error'=>'invalid_slug']);

        $pdo->prepare("INSERT INTO ce_tenants(slug,title,plan,status,created_at,updated_at) VALUES(:s,:t,'free','active',NOW(),NOW())")
            ->execute([':s'=>$slug, ':t'=>$title]);
        Response::json(['ok'=>true,'tenant_id'=>(int)$pdo->lastInsertId()]);
    }

    public function platformSiteCreate()
    {
        \API\Auth::requireScope('admin.write');
        $pdo = \Database\DB::pdo();
        if (!$pdo) Response::json(['ok'=>false,'error'=>'db_required']);
        $pdo->exec(file_get_contents(ROOT_PATH . '/system/sql/platform_v2_5.sql'));

        $tenantId = (int)($_POST['tenant_id'] ?? 0);
        $host = trim((string)($_POST['host'] ?? ''));
        $title = trim((string)($_POST['title'] ?? $host));
        if ($tenantId<=0 || $host==='') Response::json(['ok'=>false,'error'=>'invalid_input']);

        $pdo->prepare("INSERT INTO ce_sites(tenant_id,title,host,status,created_at,updated_at) VALUES(:tid,:t,:h,'active',NOW(),NOW())")
            ->execute([':tid'=>$tenantId, ':t'=>$title, ':h'=>$host]);

        $siteId = (int)$pdo->lastInsertId();
        $pdo->prepare("INSERT INTO ce_tenant_domains(tenant_id,site_id,host,created_at) VALUES(:tid,:sid,:h,NOW())
            ON DUPLICATE KEY UPDATE tenant_id=:tid2, site_id=:sid2")
            ->execute([':tid'=>$tenantId,':sid'=>$siteId,':h'=>$host,':tid2'=>$tenantId,':sid2'=>$siteId]);

        Response::json(['ok'=>true,'site_id'=>$siteId]);
    }

    public function platformUsage()
    {
        \API\Auth::requireScope('admin.read');
        $pdo = \Database\DB::pdo();
        if (!$pdo) Response::json(['ok'=>false,'error'=>'db_required']);

        $tenantId = (int)($_GET['tenant_id'] ?? 0);
        if ($tenantId<=0) Response::json(['ok'=>false,'error'=>'tenant_required']);
        $date = (string)($_GET['date'] ?? date('Y-m-d'));

        $st = $pdo->prepare("SELECT metric_key, metric_value, site_id FROM ce_usage_metrics WHERE tenant_id=:t AND bucket_date=:d ORDER BY metric_key");
        $st->execute([':t'=>$tenantId, ':d'=>$date]);
        $rows = $st->fetchAll(\PDO::FETCH_ASSOC);
        Response::json(['ok'=>true,'data'=>$rows]);
    }

    // Observability (v2.6)
    public function healthLive()
    {
        Response::json(\Observability\Health::live());
    }

    public function healthReady()
    {
        Response::json(\Observability\Health::ready());
    }

    public function metrics()
    {
        $obs = is_file(ROOT_PATH.'/system/observability.php') ? require ROOT_PATH.'/system/observability.php' : [];
        if (empty($obs['metrics']['enabled'])) {
            header('HTTP/1.1 404 Not Found'); echo 'metrics disabled'; exit;
        }
        header('Content-Type: text/plain; version=0.0.4');
        echo \Observability\Metrics::renderPrometheus();
        exit;
    }

    // Backup & Restore (v2.6)
    public function backupExport()
    {
        \API\Auth::requireScope('admin.read');
        $cfg = require ROOT_PATH . '/system/config.php';
        \Database\DB::connect($cfg['db']);
        $pdo = \Database\DB::pdo();
        if (!$pdo) Response::json(['ok'=>false,'error'=>'db_required']);

        $tables = ['ce_content_items','ce_content_categories','ce_content_fields','ce_users','ce_rbac_roles','ce_rbac_role_permissions','ce_rbac_user_roles'];
        $dump = ['meta'=>['version'=>trim((string)file_get_contents(ROOT_PATH.'/system/version.txt')),'ts'=>date('c')],'tables'=>[]];

        foreach ($tables as $t) {
            try { $dump['tables'][$t] = $pdo->query("SELECT * FROM `$t`")->fetchAll(\PDO::FETCH_ASSOC); }
            catch (\Throwable $e) { $dump['tables'][$t] = ['__error__'=>$e->getMessage()]; }
        }

        $settings = [];
        foreach (['config.php','security.php','platform.php','marketplace.php','workspaces.php','env.php','observability.php'] as $f) {
            $p = ROOT_PATH . '/system/' . $f;
            if (is_file($p)) $settings[$f] = file_get_contents($p);
        }
        $dump['settings_files'] = $settings;

        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="cajeer-backup.json"');
        echo json_encode($dump, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        exit;
    }

    public function backupImport()
    {
        \API\Auth::requireScope('admin.write');
        $cfg = require ROOT_PATH . '/system/config.php';
        \Database\DB::connect($cfg['db']);
        $pdo = \Database\DB::pdo();
        if (!$pdo) Response::json(['ok'=>false,'error'=>'db_required']);

        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);
        if (!is_array($data) || empty($data['tables'])) Response::json(['ok'=>false,'error'=>'invalid_backup']);

        if (($_SERVER['HTTP_X_RESTORE_CONFIRM'] ?? '') !== 'YES') {
            Response::json(['ok'=>false,'error'=>'restore_confirm_required','hint'=>'Set header X-Restore-Confirm: YES']);
        }

        $restored = [];
        foreach ($data['tables'] as $table => $rows) {
            if (!is_array($rows) || isset($rows['__error__'])) continue;
            try {
                $pdo->exec("DELETE FROM `$table`");
                foreach ($rows as $row) {
                    if (!is_array($row)) continue;
                    $cols = array_keys($row);
                    $place = array_map(fn($c)=>':'.$c, $cols);
                    $sql = "INSERT INTO `$table` (`".implode('`,`',$cols)."`) VALUES (".implode(',',$place).")";
                    $st = $pdo->prepare($sql);
                    $params = [];
                    foreach ($row as $k=>$v) $params[':'.$k] = $v;
                    $st->execute($params);
                }
                $restored[] = $table;
            } catch (\Throwable $e) {}
        }

        \Audit\AuditLogger::log('backup.import', ['tables'=>$restored]);
        Response::json(['ok'=>true,'restored'=>$restored]);
    
    // Marketplace v2.7
    public function marketplaceSync()
    {
        \API\Auth::requireScope('admin.write');
        $cfg = require ROOT_PATH . '/system/config.php';
        \Database\DB::connect($cfg['db']);
        $mp = is_file(ROOT_PATH.'/system/marketplace.php') ? require ROOT_PATH.'/system/marketplace.php' : ['enabled'=>false];
        if (empty($mp['enabled'])) Response::json(['ok'=>false,'error'=>'marketplace_disabled']);
        \Marketplace\MarketplaceService::ensureSchema();
        $res = \Marketplace\MarketplaceService::syncRegistries($mp);
        Response::json($res);
    }

    public function marketplaceSearch()
    {
        \API\Auth::requireScope('admin.read');
        $cfg = require ROOT_PATH . '/system/config.php';
        \Database\DB::connect($cfg['db']);
        $q = (string)($_GET['q'] ?? '');
        $type = isset($_GET['type']) ? (string)$_GET['type'] : null;
        $items = \Marketplace\MarketplaceService::searchLocal($q, $type);
        Response::json(['ok'=>true,'items'=>$items]);
    }

    public function marketplaceRate()
    {
        \API\Auth::requireScope('admin.write');
        $cfg = require ROOT_PATH . '/system/config.php';
        \Database\DB::connect($cfg['db']);
        \Marketplace\MarketplaceService::ensureSchema();
        $pdo = \Database\DB::pdo();

        $pid = (int)($_POST['package_id'] ?? 0);
        $rating = (int)($_POST['rating'] ?? 0);
        $comment = (string)($_POST['comment'] ?? '');
        if ($pid<=0 || $rating<1 || $rating>5) Response::json(['ok'=>false,'error'=>'invalid_input']);

        $pdo->prepare("INSERT INTO ce_marketplace_ratings(package_id,user_id,rating,comment,created_at) VALUES(:p,NULL,:r,:c,NOW())")
            ->execute([':p'=>$pid,':r'=>$rating,':c'=>$comment]);

        $row = $pdo->query("SELECT AVG(rating) a, COUNT(*) c FROM ce_marketplace_ratings WHERE package_id=".(int)$pid)->fetch(\PDO::FETCH_ASSOC);
        $avg = (float)($row['a'] ?? 0); $cnt = (int)($row['c'] ?? 0);
        $pdo->prepare("UPDATE ce_marketplace_packages SET rating_avg=:a, rating_count=:c WHERE id=:id")
            ->execute([':a'=>$avg,':c'=>$cnt,':id'=>$pid]);

        Response::json(['ok'=>true,'rating_avg'=>$avg,'rating_count'=>$cnt]);
    }

    public function marketplacePreflight()
    {
        \API\Auth::requireScope('admin.read');
        $cfg = require ROOT_PATH . '/system/config.php';
        \Database\DB::connect($cfg['db']);
        \Marketplace\MarketplaceService::ensureSchema();
        $pdo = \Database\DB::pdo();

        $packageId = (int)($_GET['package_id'] ?? 0);
        if ($packageId<=0) Response::json(['ok'=>false,'error'=>'package_id_required']);

        $p = $pdo->query("SELECT manifest_json,is_paid,license,price FROM ce_marketplace_packages WHERE id=".(int)$packageId." LIMIT 1")->fetch(\PDO::FETCH_ASSOC);
        if (!$p) Response::json(['ok'=>false,'error'=>'not_found']);

        $manifest = json_decode((string)($p['manifest_json'] ?? ''), true);
        if (!is_array($manifest)) Response::json(['ok'=>false,'error'=>'invalid_manifest']);

        $mp = is_file(ROOT_PATH.'/system/marketplace.php') ? require ROOT_PATH.'/system/marketplace.php' : [];
        $sec = (array)($mp['security'] ?? []);

        if (!empty($sec['require_signature'])) {
            $sig = (string)($manifest['signature'] ?? '');
            $payload = (string)($manifest['signed_payload'] ?? '');
            $pubKey = '';
            $pubFile = (string)($mp['registries']['official']['public_key'] ?? '');
            if ($pubFile && is_file($pubFile)) $pubKey = file_get_contents($pubFile);
            if (!$pubKey || !$payload || !$sig) Response::json(['ok'=>false,'error'=>'signature_required']);
            if (!\Marketplace\Signature::verify($pubKey, $payload, $sig)) Response::json(['ok'=>false,'error'=>'signature_invalid']);
        }

        $tenantId = (int)($_SERVER['CE_TENANT_ID'] ?? 0);
        $ent = \Marketplace\Monetization::checkEntitlement($p, $tenantId);
        if (empty($ent['ok'])) Response::json($ent);

        $res = \Marketplace\Sandbox::preflight($manifest);
        Response::json($res);
    }
    // Enterprise SaaS & Compliance v2.9
    public function tenantSetStatus()
    {
        \API\Auth::requireScope('admin.write');
        $cfg = require ROOT_PATH . '/system/config.php';
        \Database\DB::connect($cfg['db']);
        \Security\AuditTrail::ensureSchema();

        $tenantId = (int)($_POST['tenant_id'] ?? 0);
        $status = (string)($_POST['status'] ?? '');
        if ($tenantId<=0 || $status==='') Response::json(['ok'=>false,'error'=>'tenant_id_and_status_required']);

        Response::json(\SaaS\TenantManager::setStatus($tenantId, $status));
    }

    public function tenantQuotasSet()
    {
        \API\Auth::requireScope('admin.write');
        $cfg = require ROOT_PATH . '/system/config.php';
        \Database\DB::connect($cfg['db']);
        \Security\AuditTrail::ensureSchema();

        $tenantId = (int)($_POST['tenant_id'] ?? 0);
        $raw = (string)($_POST['quotas_json'] ?? '');
        $enforced = (int)($_POST['enforced'] ?? 1) === 1;

        $quotas = json_decode($raw, true);
        if ($tenantId<=0 || !is_array($quotas)) Response::json(['ok'=>false,'error'=>'tenant_id_and_valid_quotas_json_required']);

        Response::json(\SaaS\TenantManager::setQuotas($tenantId, $quotas, $enforced));
    }

    public function auditVerify()
    {
        \API\Auth::requireScope('admin.read');
        $cfg = require ROOT_PATH . '/system/config.php';
        \Database\DB::connect($cfg['db']);
        Response::json(\Security\AuditTrail::verifyChain(5000));
    }

    public function ssoProviderCreate()
    {
        \API\Auth::requireScope('admin.write');
        $cfg = require ROOT_PATH . '/system/config.php';
        \Database\DB::connect($cfg['db']);
        \Security\AuditTrail::ensureSchema();

        $tenantId = (int)($_POST['tenant_id'] ?? 0);
        $type = (string)($_POST['type'] ?? '');
        $name = (string)($_POST['name'] ?? '');
        $cfgJson = (string)($_POST['config_json'] ?? '{}');
        $enabled = (int)($_POST['enabled'] ?? 1) === 1;

        $config = json_decode($cfgJson, true);
        if ($tenantId<=0 || $type==='' || $name==='' || !is_array($config)) Response::json(['ok'=>false,'error'=>'invalid_input']);

        Response::json(\Security\SSO::upsertProvider($tenantId, $type, $name, $config, $enabled));
    }

    public function mfaList()
    {
        \API\Auth::requireScope('admin.read');
        $cfg = require ROOT_PATH . '/system/config.php';
        \Database\DB::connect($cfg['db']);

        $userId = (int)($_GET['user_id'] ?? 0);
        if ($userId<=0) Response::json(['ok'=>false,'error'=>'user_id_required']);
        Response::json(['ok'=>true,'items'=>\Security\MFA::listFactors($userId)]);
    }

    public function gdprQueue()
    {
        \API\Auth::requireScope('admin.write');
        $cfg = require ROOT_PATH . '/system/config.php';
        \Database\DB::connect($cfg['db']);
        \Security\AuditTrail::ensureSchema();

        $tenantId = (int)($_POST['tenant_id'] ?? 0);
        $userId = (int)($_POST['user_id'] ?? 0);
        $type = (string)($_POST['type'] ?? 'export');

        if ($tenantId<=0 || $userId<=0) Response::json(['ok'=>false,'error'=>'tenant_id_and_user_id_required']);
        Response::json(\Compliance\GDPR::queueReport($tenantId, $userId, $type));
    }

    public function gdprRun()
    {
        \API\Auth::requireScope('admin.write');
        $cfg = require ROOT_PATH . '/system/config.php';
        \Database\DB::connect($cfg['db']);
        \Security\AuditTrail::ensureSchema();

        $reportId = (int)($_POST['report_id'] ?? 0);
        if ($reportId<=0) Response::json(['ok'=>false,'error'=>'report_id_required']);
        Response::json(\Compliance\GDPR::runReport($reportId));
    }

    public function incidentCreate()
    {
        \API\Auth::requireScope('admin.write');
        $cfg = require ROOT_PATH . '/system/config.php';
        \Database\DB::connect($cfg['db']);
        \Security\AuditTrail::ensureSchema();

        $tenantId = (int)($_POST['tenant_id'] ?? 0);
        $severity = (string)($_POST['severity'] ?? 'info');
        $title = (string)($_POST['title'] ?? '');
        $details = (string)($_POST['details'] ?? '');

        if ($tenantId<=0 || $title==='') Response::json(['ok'=>false,'error'=>'tenant_id_and_title_required']);

        $pdo = \Database\DB::pdo();
        $pdo->prepare("INSERT INTO ce_incidents(tenant_id,severity,title,details,status,created_at,updated_at)
                       VALUES(:t,:s,:ti,:d,'open',NOW(),NOW())")
            ->execute([':t'=>$tenantId,':s'=>$severity,':ti'=>$title,':d'=>$details]);

        $id = (int)$pdo->lastInsertId();
        \Security\AuditTrail::append('incident.create', ['severity'=>$severity,'title'=>$title,'id'=>$id], 'tenant:'.$tenantId);
        \Ops\Hooks::incident('incident.created', ['tenant_id'=>$tenantId,'incident_id'=>$id,'severity'=>$severity,'title'=>$title]);

        Response::json(['ok'=>true,'incident_id'=>$id]);
    }

    public function accessReportList()
    {
        \API\Auth::requireScope('admin.read');
        $cfg = require ROOT_PATH . '/system/config.php';
        \Database\DB::connect($cfg['db']);

        $tenantId = (int)($_GET['tenant_id'] ?? 0);
        if ($tenantId<=0) Response::json(['ok'=>false,'error'=>'tenant_id_required']);

        $pdo = \Database\DB::pdo();
        $items = $pdo->query("SELECT id,user_id,report_type,status,created_at,updated_at FROM ce_access_reports WHERE tenant_id=".(int)$tenantId." ORDER BY id DESC LIMIT 100")->fetchAll(\PDO::FETCH_ASSOC);
        Response::json(['ok'=>true,'items'=>$items]);
    }

}
