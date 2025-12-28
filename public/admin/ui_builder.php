<?php
require_once __DIR__ . '/../../engine/bootstrap.php';
$cfg = is_file(ROOT_PATH.'/system/ui_builder.php') ? require ROOT_PATH.'/system/ui_builder.php' : ['enabled'=>false];
if (empty($cfg['enabled'])) { echo "UI Builder disabled in system/ui_builder.php"; exit; }
?>
<!doctype html>
<html lang="ru"><head>
<meta charset="utf-8"/><meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>CajeerEngine — UI Builder</title>
<style>
body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;margin:0;background:#0b1220;color:#e5e7eb}
header{padding:16px 20px;border-bottom:1px solid #1f2937;background:#0f172a;position:sticky;top:0}
.wrap{display:grid;grid-template-columns:340px 1fr;min-height:calc(100vh - 60px)}
.panel{border-right:1px solid #1f2937;padding:14px;background:#0f172a}
.canvas{padding:18px}
.card{background:#111827;border:1px solid #1f2937;border-radius:14px;padding:12px;margin-bottom:10px}
button{background:#2563eb;color:white;border:0;border-radius:10px;padding:10px 12px;cursor:pointer}
input,textarea{width:100%;box-sizing:border-box;background:#0b1220;border:1px solid #334155;color:#e5e7eb;border-radius:10px;padding:9px 10px}
.row{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.hint{opacity:.75;font-size:12px}
.dnd{border:1px dashed #334155;border-radius:12px;padding:10px;margin-top:10px}
.blk{padding:10px;border-radius:10px;border:1px solid #334155;background:#0b1220;margin:8px 0;cursor:grab}
.blk.drag{opacity:.6}
iframe{width:100%;height:76vh;border:1px solid #1f2937;border-radius:14px;background:white}
</style></head><body>
<header><b>UI Builder</b> <span class="hint">v2.2 — Drag & Drop, JSON → render, Preview</span></header>
<div class="wrap">
  <div class="panel">
    <div class="card">
      <label>Content ID</label><input id="contentId" value="1"/>
      <div class="hint" style="margin-top:8px">Content должен существовать в ce_content_items.</div>
      <div style="margin-top:10px" class="row"><button onclick="loadLayout()">Load</button><button onclick="saveLayout()">Save</button></div>
      <div style="margin-top:10px" class="row"><button onclick="preview()">Preview</button><button onclick="exportJson()">Export</button></div>
    </div>
    <div class="card"><b>Blocks</b><div class="hint">Перетащите блок в секцию.</div><div id="blocks"></div></div>
    <div class="card">
      <b>Section</b>
      <label>Class</label><input id="secClass" value="container py-6"/>
      <div class="row" style="margin-top:10px"><div><label>Cols</label><input id="secCols" value="12"/></div><div><label>Gap (rem)</label><input id="secGap" value="4"/></div></div>
      <button style="margin-top:10px;width:100%" onclick="applySection()">Apply</button>
    </div>
    <div class="card"><b>Selected block</b><div class="hint">Редактирование props (JSON).</div>
      <textarea id="props" rows="10">{}</textarea>
      <button style="margin-top:10px;width:100%" onclick="applyProps()">Apply props</button></div>
  </div>
  <div class="canvas">
    <div class="card"><b>Section blocks</b><div class="dnd" id="section"></div></div>
    <div class="card"><b>Preview</b><iframe id="frame"></iframe></div>
  </div>
</div>

<script>
let layout={version:1,title:"Page",sections:[{id:"sec_1",class:"container py-6",grid:{cols:12,gap:4},blocks:[]}]};
let selectedIndex=-1;
function api(path, method="GET", body=null){
  return fetch(path,{method,headers:{"Content-Type":"application/json","Authorization":"Bearer dev-token"},body:body?JSON.stringify(body):null}).then(r=>r.json());
}
function renderBlocksPalette(list){
  const el=document.getElementById("blocks"); el.innerHTML="";
  list.forEach(b=>{
    const div=document.createElement("div"); div.className="blk"; div.draggable=true; div.textContent=b.title+" ("+b.type+")";
    div.addEventListener("dragstart", e=>{ div.classList.add("drag"); e.dataTransfer.setData("text/plain", b.type); });
    div.addEventListener("dragend", ()=>div.classList.remove("drag"));
    el.appendChild(div);
  });
}
function drawSection(){
  const sec=layout.sections[0]; const el=document.getElementById("section"); el.innerHTML="";
  sec.blocks.forEach((b,i)=>{
    const item=document.createElement("div"); item.className="blk";
    item.innerHTML="<b>"+b.type+"</b> <span class='hint'>span "+(b.col?.span||12)+"</span>";
    item.onclick=()=>{ selectedIndex=i; document.getElementById("props").value=JSON.stringify(b.props||{},null,2); };
    el.appendChild(item);
  });
  el.ondragover=e=>e.preventDefault();
  el.ondrop=e=>{
    e.preventDefault();
    const type=e.dataTransfer.getData("text/plain");
    const block={type,id:"b_"+Math.random().toString(16).slice(2),col:{span:12},props:{}};
    if(type==="text") block.props={html:"<p>Text</p>"};
    if(type==="image") block.props={src:"",alt:"",class:"w-full rounded-2xl"};
    if(type==="gallery") block.props={images:[],cols:3,gap:1};
    if(type==="form") block.props={action:"/",method:"post",fields:[{type:"text",name:"email",label:"Email"}],submit_label:"Send"};
    if(type==="html") block.props={html:"<div>Custom</div>"};
    if(type==="module") block.props={name:"news",action:"index",params:{}};
    sec.blocks.push(block);
    drawSection();
  };
}
function applySection(){
  const sec=layout.sections[0];
  sec.class=document.getElementById("secClass").value;
  sec.grid.cols=parseInt(document.getElementById("secCols").value||"12",10);
  sec.grid.gap=parseInt(document.getElementById("secGap").value||"4",10);
  preview();
}
function applyProps(){
  if(selectedIndex<0) return;
  try{ layout.sections[0].blocks[selectedIndex].props=JSON.parse(document.getElementById("props").value||"{}"); preview(); }
  catch(e){ alert("Invalid JSON"); }
}
function loadLayout(){
  const id=document.getElementById("contentId").value;
  api("/api/v1/ui/get?content_id="+encodeURIComponent(id)).then(r=>{ if(r.ok) layout=r.data; drawSection(); preview(); });
}
function saveLayout(){
  const id=document.getElementById("contentId").value;
  api("/api/v1/ui/save?content_id="+encodeURIComponent(id),"POST",layout).then(r=>alert(r.ok?"Saved":"Error"));
}
function preview(){
  api("/api/v1/ui/preview","POST",layout).then(r=>{
    const doc=document.getElementById("frame").contentWindow.document;
    doc.open(); doc.write("<!doctype html><html><head><meta charset='utf-8'><title>Preview</title></head><body>"+(r.html||"")+"</body></html>"); doc.close();
  });
}
function exportJson(){
  const a=document.createElement("a");
  a.href=URL.createObjectURL(new Blob([JSON.stringify(layout,null,2)],{type:"application/json"}));
  a.download="layout.json"; a.click();
}
api("/api/v1/ui/blocks").then(r=>{ if(r.ok) renderBlocksPalette(r.data); });
drawSection(); preview();
</script>
</body></html>
