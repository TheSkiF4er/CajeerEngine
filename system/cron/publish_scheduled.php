<?php
// v2.4 workflow scheduler: publish scheduled items
define('ROOT_PATH', dirname(__DIR__, 2));
require_once ROOT_PATH . '/engine/bootstrap.php';

$cfg = require ROOT_PATH . '/system/config.php';
\Database\DB::connect($cfg['db']);
$pdo = \Database\DB::pdo();
if (!$pdo) { echo "DB required\n"; exit(1); }

// Ensure columns exist; in real deployment use migrations
try { $pdo->exec("ALTER TABLE ce_content_items ADD COLUMN workflow_state VARCHAR(20) NOT NULL DEFAULT 'draft'"); } catch (\Throwable $e) {}
try { $pdo->exec("ALTER TABLE ce_content_items ADD COLUMN published_at DATETIME NULL"); } catch (\Throwable $e) {}
try { $pdo->exec("ALTER TABLE ce_content_items ADD COLUMN scheduled_at DATETIME NULL"); } catch (\Throwable $e) {}

$now = date('Y-m-d H:i:s');
$st = $pdo->prepare("UPDATE ce_content_items
    SET workflow_state='published', published_at=COALESCE(published_at, :now)
    WHERE scheduled_at IS NOT NULL AND scheduled_at <= :now AND workflow_state != 'published'");
$st->execute([':now'=>$now]);

echo "OK: scheduled publishing processed\n";
