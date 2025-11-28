// public/js/app.js
const apiUrl = '/?action=api';

async function api(action, payload = {}) {
  payload.op = action;
  const res = await fetch(apiUrl, {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify(payload)
  });
  return res.json();
}

function el(id){return document.getElementById(id)}
function showResult(data){
  const box = el('resultBox');
  box.classList.remove('hidden');
  if (!data || !data.analysis) {
    box.innerHTML = '<div>Sem dados</div>'; return;
  }
  const a = data.analysis;
  box.innerHTML = `
    <div><strong>URL:</strong> <a href="${escapeHtml(a.url)}" target="_blank" rel="noopener noreferrer">${escapeHtml(a.url)}</a></div>
    <div><strong>Nível:</strong> <span class="level">${a.level.toUpperCase()}</span></div>
    <div><strong>Score:</strong> ${a.score}</div>
    <div class="note"><em>Razões: ${a.reasons.join(', ') || 'Nenhuma detectada'}</em></div>
    <div style="margin-top:8px"><small>Analisado em ${new Date(a.analyzed_at).toLocaleString()}</small></div>
  `;
}

function escapeHtml(s){ return String(s).replace(/[&<>"']/g, c=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[c])); }

async function loadList(){
  const box = el('listBox');
  box.innerHTML = 'Carregando...';
  const res = await fetch('/?action=api');
  const json = await res.json();
  if (!json.success) { box.innerHTML = 'Erro ao carregar'; return; }
  const arr = json.data || [];
  if (!arr.length) { box.innerHTML = '<div><em>Nenhum item salvo</em></div>'; return; }
  const html = arr.map(it=>{
    const a = it.analysis || {};
    return `<div class="hist-item">
      <div class="hist-left">
        <div class="hist-url">${escapeHtml(it.url)}</div>
        <div class="note">${escapeHtml(it.note||'')}</div>
        <small>${new Date(it.created_at).toLocaleString()}</small>
      </div>
      <div style="min-width:150px;text-align:right">
        <div class="badge ${a.level}">${a.level || ''}</div>
        <div class="controls" style="margin-top:8px">
          <button class="icon-btn" onclick="openAnalyze('${encodeURIComponent(it.url)}')">Analisar</button>
          <button class="icon-btn" onclick="editItem(${it.id})">Editar</button>
          <button class="icon-btn" onclick="deleteItem(${it.id})">Remover</button>
        </div>
      </div>
    </div>`;
  }).join('');
  box.innerHTML = html;
}

async function analyzeNow(url){
  const res = await api('analyze', {url});
  if (res.success) showResult(res);
  else alert('Erro na análise');
}

async function saveNow(){
  const url = el('urlInput').value.trim();
  const note = el('noteInput').value.trim();
  if(!url){ alert('Cole a URL'); return; }
  const res = await api('create', {url, note});
  if (res.success) {
    showResult(res);
    await loadList();
  } else {
    alert(res.message || 'Erro ao salvar');
  }
}

function openAnalyze(url){
  analyzeNow(decodeURIComponent(url));
}

async function deleteItem(id){
  if(!confirm('Excluir este item?')) return;
  const res = await api('delete',{id});
  if (res.success) loadList();
  else alert('Erro ao remover');
}

function editItem(id){
  const newNote = prompt('Observação (deixe em branco para remover):');
  if (newNote === null) return;
  api('update',{id, note:newNote}).then(res=>{
    if (res.success) loadList();
    else alert('Erro ao editar');
  });
}

document.addEventListener('DOMContentLoaded', ()=>{
  el('btnAnalyze').addEventListener('click', ()=>{
    const u = el('urlInput').value.trim();
    if (!u) { alert('Cole a URL'); return; }
    analyzeNow(u);
  });
  el('btnSave').addEventListener('click', saveNow);
  loadList();

  // register service worker
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/public/sw.js').then(()=>console.log('SW registrado')).catch(()=>console.log('SW falhou'));
  }
});