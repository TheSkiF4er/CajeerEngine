<?php
require_once __DIR__ . '/../../engine/bootstrap.php';
?>
<!doctype html>
<html lang="ru">
<head>
<meta charset="utf-8"/><meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>CajeerEngine — Platform</title>
<style>
body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;margin:0;background:#0b1220;color:#e5e7eb}
header{padding:16px 20px;border-bottom:1px solid #1f2937;background:#0f172a;position:sticky;top:0}
.wrap{max-width:1100px;margin:0 auto;padding:18px}
.grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.card{background:#111827;border:1px solid #1f2937;border-radius:14px;padding:14px}
input{width:100%;box-sizing:border-box;background:#0b1220;border:1px solid #334155;color:#e5e7eb;border-radius:10px;padding:9px 10px}
button{background:#2563eb;color:#fff;border:0;border-radius:10px;padding:10px 12px;cursor:pointer}
pre{white-space:pre-wrap;background:#0b1220;border:1px solid #1f2937;border-radius:12px;padding:12px;overflow:auto;max-height:420px}
.hint{opacity:.75;font-size:12px}
.row{display:grid;grid-template-columns:1fr 1fr;gap:10px}
</style>
</head>
<body>
<header><b>Platform mode</b> <span class="hint">v2.5 — multi-tenant, site isolation, metrics, autoupdate hooks</span></header>
<div class="wrap">
  <div class="grid">
    <div class="card">
      <b>Create tenant</b>
      <div class="row" style="margin-top:10px">
        <div><label class="hint">Slug</label><input id="tSlug" placeholder="acme"/></div>
        <div><label class="hint">Title</label><input id="tTitle" placeholder="ACME Inc."/></div>
      </div>
      <div style="margin-top:10px"><button onclick="createTenant()">Create</button></div>
      <pre id="tRes"></pre>
    </div>

    <div class="card">
      <b>Create site</b>
      <div class="row" style="margin-top:10px">
        <div><label class="hint">Tenant ID</label><input id="sTenant" placeholder="1"/></div>
        <div><label class="hint">Host</label><input id="sHost" placeholder="acme.example.com"/></div>
      </div>
      <div style="margin-top:10px"><label class="hint">Title</label><input id="sTitle" placeholder="ACME Site"/></div>
      <div style="margin-top:10px"><button onclick="createSite()">Create</button></div>
      <pre id="sRes"></pre>
    </div>
  </div>

  <div class="card" style="margin-top:14px">
    <b>Health</b>
    <button onclick="health()">Check</button>
    <pre id="hRes"></pre>
  </div>
</div>

<script>
function api(path, method="GET", body=null, isForm=false){
  const opts = {method, headers: {"Authorization":"Bearer dev-token"}};
  if(!isForm) opts.headers["Content-Type"]="application/json";
  if(body) opts.body = isForm ? body : JSON.stringify(body);
  return fetch(path, opts).then(r=>r.json());
}
function createTenant(){
  const fd=new FormData();
  fd.append("slug", document.getElementById("tSlug").value);
  fd.append("title", document.getElementById("tTitle").value);
  api("/api/v1/platform/tenant/create","POST",fd,true).then(r=>{document.getElementById("tRes").textContent=JSON.stringify(r,null,2);});
}
function createSite(){
  const fd=new FormData();
  fd.append("tenant_id", document.getElementById("sTenant").value);
  fd.append("host", document.getElementById("sHost").value);
  fd.append("title", document.getElementById("sTitle").value);
  api("/api/v1/platform/site/create","POST",fd,true).then(r=>{document.getElementById("sRes").textContent=JSON.stringify(r,null,2);});
}
function health(){ api("/api/v1/platform/health").then(r=>{document.getElementById("hRes").textContent=JSON.stringify(r,null,2);}); }
</script>
</body></html>
