<?php
require_once __DIR__ . '/../../engine/bootstrap.php';
$cfg = require ROOT_PATH . '/system/marketplace.php';
?>
<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>CajeerEngine — Marketplace</title>
  <style>
    body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;margin:0;background:#0b1220;color:#e5e7eb}
    header{padding:16px 20px;border-bottom:1px solid #1f2937;background:#0f172a;position:sticky;top:0}
    .wrap{max-width:1100px;margin:0 auto;padding:18px}
    .grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
    .card{background:#111827;border:1px solid #1f2937;border-radius:14px;padding:14px}
    input{width:100%;box-sizing:border-box;background:#0b1220;border:1px solid #334155;color:#e5e7eb;border-radius:10px;padding:9px 10px}
    button{background:#2563eb;color:#fff;border:0;border-radius:10px;padding:10px 12px;cursor:pointer}
    table{width:100%;border-collapse:collapse}
    th,td{border-bottom:1px solid #1f2937;padding:8px 6px;text-align:left;font-size:13px}
    .hint{opacity:.75;font-size:12px}
    .row{display:grid;grid-template-columns:1fr 1fr;gap:10px}
    .ok{color:#34d399}
    .bad{color:#f87171}
  </style>
</head>
<body>
<header>
  <b>Marketplace</b>
  <span class="hint">v2.3 — установка, зависимости, подписи, trusted publishers</span>
</header>

<div class="wrap">
  <div class="grid">
    <div class="card">
      <b>Установка пакета (.cajeerpkg)</b>
      <div class="hint" style="margin:8px 0 12px 0">
        Установка из админки (upload). Подпись пакета обязательна, если require_signature=true.
        Для проверки ed25519 рекомендуется PHP ext-sodium.
      </div>
      <form id="uploadForm">
        <input type="file" name="package" accept=".cajeerpkg,.zip" required/>
        <div style="margin-top:10px"><button type="submit">Install</button></div>
      </form>
      <div id="uploadRes" class="hint" style="margin-top:10px"></div>
    </div>

    <div class="card">
      <b>Trusted publisher</b>
      <div class="hint" style="margin:8px 0 12px 0">
        Добавьте ed25519 public key (base64) доверенного издателя.
      </div>
      <div class="row">
        <div><label class="hint">Publisher ID</label><input id="pubId" placeholder="cajeer-official"/></div>
        <div><label class="hint">Title</label><input id="pubTitle" placeholder="Cajeer Official"/></div>
      </div>
      <div style="margin-top:10px">
        <label class="hint">PubKey ED25519 (base64)</label>
        <input id="pubKey" placeholder="BASE64_ED25519_PUBKEY"/>
      </div>
      <div style="margin-top:10px"><button onclick="trustPublisher()">Trust</button></div>
      <div id="trustRes" class="hint" style="margin-top:10px"></div>
    </div>
  </div>

  <div class="card" style="margin-top:14px">
    <b>Установленные пакеты</b>
    <div class="hint" style="margin:8px 0 12px 0">Типы: plugins, themes, ui blocks, content types.</div>
    <table>
      <thead><tr><th>Type</th><th>Name</th><th>Version</th><th>Publisher</th><th>Installed</th></tr></thead>
      <tbody id="installed"></tbody>
    </table>
  </div>

  <div class="card" style="margin-top:14px">
    <b>Marketplace index (remote)</b>
    <div class="hint">Base URL: <?php echo htmlspecialchars((string)$cfg['base_url'], ENT_QUOTES); ?></div>
    <pre id="remote" style="white-space:pre-wrap;background:#0b1220;border:1px solid #1f2937;border-radius:12px;padding:12px;overflow:auto;max-height:320px"></pre>
  </div>
</div>

<script>
function api(path, method="GET", body=null, isForm=false){
  const opts = {method, headers: {"Authorization":"Bearer dev-token"}};
  if(!isForm) opts.headers["Content-Type"]="application/json";
  if(body) opts.body = isForm ? body : JSON.stringify(body);
  return fetch(path, opts).then(r=>r.json());
}

function loadInstalled(){
  api("/api/v1/marketplace/installed").then(r=>{
    const tb=document.getElementById("installed"); tb.innerHTML="";
    (r.data||[]).forEach(x=>{
      const tr=document.createElement("tr");
      tr.innerHTML="<td>"+x.type+"</td><td>"+x.name+"</td><td>"+x.version+"</td><td>"+(x.publisher_id||"")+"</td><td>"+(x.installed_at||"")+"</td>";
      tb.appendChild(tr);
    });
  });
}

function loadRemote(){
  api("/api/v1/marketplace/index").then(r=>{
    document.getElementById("remote").textContent = JSON.stringify(r.data||r, null, 2);
  }).catch(e=>{
    document.getElementById("remote").textContent = "Failed to fetch remote index (configure base_url).";
  });
}

document.getElementById("uploadForm").addEventListener("submit", (e)=>{
  e.preventDefault();
  const fd=new FormData(e.target);
  api("/api/v1/marketplace/upload-install", "POST", fd, true).then(r=>{
    document.getElementById("uploadRes").innerHTML = r.ok ? "<span class='ok'>OK</span> installed" : "<span class='bad'>ERROR</span> "+(r.error||"");
    loadInstalled();
  }).catch(err=>{
    document.getElementById("uploadRes").textContent = "Upload failed";
  });
});

function trustPublisher(){
  const fd=new FormData();
  fd.append("publisher_id", document.getElementById("pubId").value);
  fd.append("title", document.getElementById("pubTitle").value);
  fd.append("pubkey_ed25519", document.getElementById("pubKey").value);
  api("/api/v1/marketplace/trust-publisher", "POST", fd, true).then(r=>{
    document.getElementById("trustRes").innerHTML = r.ok ? "<span class='ok'>Trusted</span>" : "<span class='bad'>ERROR</span> "+(r.error||"");
  });
}

loadInstalled();
loadRemote();
</script>
</body>
</html>
