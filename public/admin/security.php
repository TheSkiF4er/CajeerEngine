<?php
require_once __DIR__ . '/../../engine/bootstrap.php';
?>
<!doctype html>
<html lang="ru">
<head>
<meta charset="utf-8"/><meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>CajeerEngine — Security</title>
<style>
body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;margin:0;background:#0b1220;color:#e5e7eb}
header{padding:16px 20px;border-bottom:1px solid #1f2937;background:#0f172a;position:sticky;top:0}
.wrap{max-width:1100px;margin:0 auto;padding:18px}
.card{background:#111827;border:1px solid #1f2937;border-radius:14px;padding:14px;margin-bottom:14px}
button{background:#2563eb;color:#fff;border:0;border-radius:10px;padding:10px 12px;cursor:pointer}
pre{white-space:pre-wrap;background:#0b1220;border:1px solid #1f2937;border-radius:12px;padding:12px;overflow:auto;max-height:420px}
.hint{opacity:.75;font-size:12px}
</style>
</head>
<body>
<header><b>Enterprise & Security</b> <span class="hint">v2.4 — CSRF, rate limiting, IP filter, audit logs, workflow</span></header>
<div class="wrap">
  <div class="card">
    <b>CSRF Token</b>
    <div class="hint">Для SPA можно получить токен через API и отправлять в header `X-CSRF-Token`.</div>
    <button onclick="loadCsrf()">Get token</button>
    <pre id="csrf"></pre>
  </div>

  <div class="card">
    <b>Audit logs</b>
    <div class="hint">Последние события (требует Bearer token + scope admin.read).</div>
    <button onclick="loadAudit()">Load</button>
    <pre id="audit"></pre>
  </div>
</div>

<script>
function api(path){
  return fetch(path,{headers:{Authorization:"Bearer dev-token"}}).then(r=>r.json());
}
function loadCsrf(){ api("/api/v1/security/csrf").then(r=>{document.getElementById("csrf").textContent=JSON.stringify(r,null,2);}); }
function loadAudit(){ api("/api/v1/audit/list?limit=50").then(r=>{document.getElementById("audit").textContent=JSON.stringify(r,null,2);}); }
</script>
</body></html>
