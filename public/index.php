<?php
require_once __DIR__ . '/../engine/bootstrap.php';

$cfg = is_file(ROOT_PATH . '/system/frontend.php') ? require ROOT_PATH . '/system/frontend.php' : [];
$runtime = new \Frontend\FrontendRuntime($cfg);

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

$res = $runtime->handle($uri, function() use ($uri) {
    // kernel boot already dispatches router for web requests; for ISR we render via template or response capture foundation.
    // In this skeleton we return a minimal placeholder body (origin renderer should be plugged in).
    return [
      'status' => 200,
      'content_type' => 'text/html',
      'body' => "<!doctype html><html><head><meta charset=\"utf-8\"><title>CajeerEngine</title></head><body><h1>CajeerEngine Frontend Runtime</h1><p>URI: ".htmlspecialchars($uri)."</p><p>Origin renderer hook: TODO</p></body></html>",
      'surrogate_keys' => 'uri:' . $uri,
    ];
});

http_response_code((int)($res['status'] ?? 200));
header('Content-Type: ' . (string)($res['content_type'] ?? 'text/html'));
$hdrs = $runtime->cdnHeaders($res['surrogate_keys'] ?? null);
foreach ($hdrs as $k => $v) header($k . ': ' . $v);
echo (string)($res['body'] ?? '');
