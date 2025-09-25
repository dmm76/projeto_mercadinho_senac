<?php
/** @var array $usuario autenticado, se o layout já injeta isso */
$title = 'PDV - Frente de Caixa';
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($title) ?></title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body { background:#0b1523; color:#e9eef7 }
    .pdv-wrap { min-height:100vh; display:flex; flex-direction:column; }
    .brand { letter-spacing:.5px; }
    .kbd { background:#111a2b; border:1px solid #1b2740; padding:.1rem .4rem; border-radius:.35rem; font-weight:600; }
    .panel { background:#0f1b2e; border:1px solid #1a2a47; border-radius:14px; }
    .table thead th { color:#9fb5d9; border-bottom-color:#233454; }
    .table tbody tr { border-color:#1f2f4e; }
    .table-striped>tbody>tr:nth-of-type(odd) { --bs-table-accent-bg: rgba(255,255,255,.02); }
    .totais .valor { font-size:1.6rem; font-weight:700; }
    .totais .lbl { color:#9fb5d9; font-size:.9rem }
    .busca-rapida:focus { box-shadow: 0 0 0 .2rem rgba(13,110,253,.25) }
    .teclas span { color:#9fb5d9 }
    .qtd-input { max-width:120px }
    .price-lg { font-size:2.2rem; font-weight:800; }
    .status-pill { padding:.25rem .6rem; border-radius:999px; font-size:.85rem; }
    .status-novo { background:#103b1a; color:#9de2ae; border:1px solid #19552a }
  </style>
</head>
<body>
<div class="pdv-wrap">

  <!-- Top bar -->
  <nav class="navbar navbar-dark" style="background:#0e1a2c; border-bottom:1px solid #1a2a47">
    <div class="container-fluid">
      <a class="navbar-brand brand" href="#">
        <strong>Mercadinho</strong> • PDV
      </a>

      <div class="d-none d-md-flex gap-3 align-items-center">
        <span class="status-pill status-novo" id="statusVenda">Venda nova</span>
        <span class="text-secondary">Turno: <span id="turnoId">#1</span></span>
        <span class="text-secondary">Terminal: <span id="terminalId">Caixa 01</span></span>
        <span class="text-secondary">Operador: <span id="operadorNome"><?= isset($usuario['nome']) ? htmlspecialchars($usuario['nome']) : 'Operador' ?></span></span>
        <button class="btn btn-sm btn-outline-light" id="btnNovaVenda" title="F2">
          Nova venda <span class="kbd">F2</span>
        </button>
      </div>
    </div>
  </nav>

  <!-- Conteúdo -->
  <div class="container-fluid my-3 my-md-4 flex-grow-1">

    <div class="row g-3">

      <!-- Lado esquerdo: busca e itens -->
      <div class="col-12 col-lg-8">
        <div class="panel p-3 p-md-4">

          <!-- Busca rápida -->
          <div class="row g-2 align-items-center mb-3">
            <div class="col-12 col-md-6">
              <input type="text" class="form-control form-control-lg busca-rapida" id="inputBusca"
                     placeholder="Buscar por EAN, SKU ou nome (F3)" autocomplete="off">
            </div>
            <div class="col-6 col-md-2">
              <input type="number" step="0.001" min="0.001" class="form-control qtd-input" id="inputQtd" value="1">
            </div>
            <div class="col-6 col-md-2 d-grid">
              <button class="btn btn-primary" id="btnAdicionar">Adicionar</button>
            </div>
            <div class="col-12 col-md-2 text-md-end">
              <small class="text-secondary">Atalhos: <span class="kbd">F3</span> Buscar • <span class="kbd">F4</span> Qtd</small>
            </div>
          </div>

          <!-- Resultado de busca (drop) -->
          <div id="resultadoBusca" class="list-group mb-3" style="display:none"></div>

          <!-- Tabela itens -->
          <div class="table-responsive">
            <table class="table table-sm table-striped align-middle text-light" id="tabelaItens">
              <thead>
              <tr>
                <th style="width:60px">#</th>
                <th>Produto</th>
                <th style="width:120px" class="text-end">Preço</th>
                <th style="width:120px" class="text-end">Qtd</th>
                <th style="width:120px" class="text-end">Subtotal</th>
                <th style="width:60px"></th>
              </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>

          <div class="d-flex justify-content-between">
            <div class="teclas small">
              <span><span class="kbd">F7</span> Desconto</span> •
              <span><span class="kbd">F8</span> Remover item</span> •
              <span><span class="kbd">F9</span> Pagamentos</span> •
              <span><span class="kbd">F10</span> Finalizar</span> •
              <span><span class="kbd">ESC</span> Cancelar</span>
            </div>
            <div>
              <button class="btn btn-outline-warning btn-sm me-2" id="btnDesconto">Desconto (F7)</button>
              <button class="btn btn-success" id="btnPagamentos">Ir para Pagamentos (F9)</button>
            </div>
          </div>

        </div>
      </div>

      <!-- Lado direito: totais -->
      <div class="col-12 col-lg-4">
        <div class="panel p-3 p-md-4 h-100">
          <div class="mb-4">
            <div class="text-secondary lbl">Subtotal</div>
            <div class="valor" id="vSubtotal">R$ 0,00</div>
          </div>
          <div class="mb-4">
            <div class="text-secondary lbl">Descontos</div>
            <div class="valor" id="vDescontos">R$ 0,00</div>
          </div>
          <div class="mb-3">
            <div class="text-secondary lbl">Total</div>
            <div class="price-lg" id="vTotal">R$ 0,00</div>
          </div>
          <hr class="border-secondary">
          <div class="mb-2">
            <div class="text-secondary lbl">Recebido</div>
            <div class="valor" id="vRecebido">R$ 0,00</div>
          </div>
          <div>
            <div class="text-secondary lbl">Troco</div>
            <div class="valor" id="vTroco">R$ 0,00</div>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- Rodapé mini -->
  <div class="py-2 text-center small text-secondary" style="border-top:1px solid #1a2a47">
    PDV Mercadinho • Senac • v0.1
  </div>
</div>

<!-- Modal Pagamentos -->
<div class="modal fade" id="modalPagamentos" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content" style="background:#0f1b2e; color:#e9eef7; border:1px solid #1a2a47">
      <div class="modal-header">
        <h5 class="modal-title">Pagamentos</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">

        <div class="row g-3">
          <div class="col-12 col-md-4">
            <label class="form-label">Tipo</label>
            <select class="form-select" id="pgTipo">
              <option value="dinheiro">Dinheiro</option>
              <option value="credito">Crédito</option>
              <option value="debito">Débito</option>
              <option value="pix">PIX</option>
              <option value="cheque">Cheque</option>
            </select>
          </div>
          <div class="col-12 col-md-4">
            <label class="form-label">Valor</label>
            <input type="number" step="0.01" min="0.01" class="form-control" id="pgValor">
          </div>
          <div class="col-12 col-md-4 d-grid align-items-end">
            <button class="btn btn-primary mt-4" id="btnIncluirPagamento">Incluir</button>
          </div>
        </div>

        <div class="table-responsive mt-3">
          <table class="table table-sm table-striped text-light" id="tabelaPagamentos">
            <thead><tr><th>Tipo</th><th class="text-end">Valor</th><th style="width:60px"></th></tr></thead>
            <tbody></tbody>
          </table>
        </div>

        <div class="d-flex justify-content-end mt-2">
          <button class="btn btn-success" id="btnFinalizar">Finalizar (F10)</button>
        </div>

      </div>
    </div>
  </div>
</div>

<!-- Modal Desconto -->
<div class="modal fade" id="modalDesconto" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="background:#0f1b2e; color:#e9eef7; border:1px solid #1a2a47">
      <div class="modal-header">
        <h5 class="modal-title">Desconto na venda</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <label class="form-label">Valor do desconto (R$)</label>
        <input type="number" min="0" step="0.01" class="form-control" id="descontoValor" value="0.00">
        <small class="text-secondary">Opcionalmente você pode implementar cupom depois (tabela <code>cupom</code>).</small>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" id="btnAplicarDesconto">Aplicar</button>
      </div>
    </div>
  </div>
</div>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const fmt = v => (v||0).toLocaleString('pt-BR',{style:'currency',currency:'BRL'});

let venda = {
  id: null,               // id do pedido (quando criado)
  itens: [],              // {id, nome, preco, qtd, subtotal}
  pagamentos: [],         // {tipo, valor}
  desconto: 0
};

const els = {
  busca: document.getElementById('inputBusca'),
  qtd: document.getElementById('inputQtd'),
  resultadoBusca: document.getElementById('resultadoBusca'),
  tabelaItens: document.querySelector('#tabelaItens tbody'),
  vSubtotal: document.getElementById('vSubtotal'),
  vDescontos: document.getElementById('vDescontos'),
  vTotal: document.getElementById('vTotal'),
  vRecebido: document.getElementById('vRecebido'),
  vTroco: document.getElementById('vTroco'),
  btnAdicionar: document.getElementById('btnAdicionar'),
  btnPagamentos: document.getElementById('btnPagamentos'),
  btnFinalizar: document.getElementById('btnFinalizar'),
  btnNovaVenda: document.getElementById('btnNovaVenda'),
  btnDesconto: document.getElementById('btnDesconto'),
  statusVenda: document.getElementById('statusVenda'),
};

function atualizarTotais(){
  const subtotal = venda.itens.reduce((s,i)=>s+(i.subtotal||0),0);
  const total = Math.max(0, subtotal - (venda.desconto||0));
  const recebido = venda.pagamentos.reduce((s,p)=>s+(+p.valor||0),0);
  const troco = Math.max(0, recebido - total);

  els.vSubtotal.textContent = fmt(subtotal);
  els.vDescontos.textContent = fmt(venda.desconto||0);
  els.vTotal.textContent = fmt(total);
  els.vRecebido.textContent = fmt(recebido);
  els.vTroco.textContent = fmt(troco);
}

function redesenharItens(){
  els.tabelaItens.innerHTML = '';
  venda.itens.forEach((it, idx)=>{
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${idx+1}</td>
      <td>${it.nome}<div class="small text-secondary">SKU/EAN: ${it.sku||it.ean||'-'}</div></td>
      <td class="text-end">${fmt(it.preco)}</td>
      <td class="text-end">
        <input type="number" step="0.001" min="0.001" value="${it.qtd}" class="form-control form-control-sm"
               data-idx="${idx}" oninput="alterarQtd(event)">
      </td>
      <td class="text-end">${fmt(it.subtotal)}</td>
      <td class="text-end">
        <button class="btn btn-sm btn-outline-danger" onclick="removerItem(${idx})" title="F8">X</button>
      </td>
    `;
    els.tabelaItens.appendChild(tr);
  });
  atualizarTotais();
}
window.alterarQtd = (ev)=>{
  const idx = +ev.target.dataset.idx;
  const qtd = Math.max(0.001, +ev.target.value||1);
  venda.itens[idx].qtd = qtd;
  venda.itens[idx].subtotal = qtd * venda.itens[idx].preco;
  redesenharItens();
};
window.removerItem = (idx)=>{
  venda.itens.splice(idx,1);
  redesenharItens();
};

async function buscarProdutos(q){
  // Endpoint provisório (implementaremos no controller): GET /pdv/api/produtos?q=...
  const url = `/pdv/api/produtos?q=${encodeURIComponent(q)}`;
  try{
    const r = await fetch(url);
    if(!r.ok) throw new Error('Busca falhou');
    return await r.json(); // esperado: [{id,nome,sku,ean,preco_venda}]
  }catch(e){
    console.error(e);
    return [];
  }
}

function mostrarResultados(lista){
  const box = els.resultadoBusca;
  box.innerHTML = '';
  if(!lista.length){ box.style.display='none'; return; }
  lista.forEach(p=>{
    const a = document.createElement('a');
    a.href="#";
    a.className="list-group-item list-group-item-action";
    a.innerHTML = `<div class="d-flex justify-content-between">
        <div><strong>${p.nome}</strong><div class="small text-secondary">${p.sku||''} ${p.ean? '• '+p.ean:''}</div></div>
        <div>${fmt(+p.preco_venda||0)}</div>
      </div>`;
    a.onclick=(ev)=>{ ev.preventDefault(); adicionarProduto(p); box.style.display='none'; els.busca.value=''; };
    box.appendChild(a);
  });
  box.style.display='block';
}

function adicionarProduto(p){
  const qtd = Math.max(0.001, +els.qtd.value||1);
  const preco = +p.preco_venda || 0;
  const item = {
    id: p.id, nome: p.nome, sku: p.sku, ean: p.ean,
    preco, qtd, subtotal: preco*qtd
  };
  venda.itens.push(item);
  redesenharItens();
  els.qtd.value = '1';
  els.busca.focus();
}

// Nova venda: cria pedido PDV (canal=PDV) no backend
async function novaVenda(){
  venda = { id:null, itens:[], pagamentos:[], desconto:0 };
  redesenharItens();
  els.statusVenda.textContent = 'Venda nova';
  try{
    const r = await fetch('/pdv/api/venda', { method:'POST' }); // cria pedido (retorna {id})
    if(r.ok){
      const data = await r.json();
      venda.id = data.id;
      els.statusVenda.textContent = 'Venda #'+venda.id;
    }
  }catch(e){ console.warn('Criar venda offline (mock).'); }
}

async function incluirPagamento(tipo, valor){
  if(!valor || valor<=0) return;
  venda.pagamentos.push({tipo,valor:+valor});
  // POST /pdv/api/venda/{id}/pagamentos  body:{tipo,valor}
  try{
    if(venda.id){
      await fetch(`/pdv/api/venda/${venda.id}/pagamentos`,{
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body: JSON.stringify({tipo,valor:+valor})
      });
    }
  }catch(e){ console.warn('Falha ao registrar pagamento'); }
  desenharPagamentos();
  atualizarTotais();
}

function desenharPagamentos(){
  const tb = document.querySelector('#tabelaPagamentos tbody');
  tb.innerHTML = '';
  venda.pagamentos.forEach((p,idx)=>{
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${p.tipo}</td>
      <td class="text-end">${fmt(p.valor)}</td>
      <td class="text-end"><button class="btn btn-sm btn-outline-danger" onclick="removerPagamento(${idx})">X</button></td>`;
    tb.appendChild(tr);
  });
}
window.removerPagamento = (idx)=>{
  venda.pagamentos.splice(idx,1);
  desenharPagamentos();
  atualizarTotais();
};

async function finalizarVenda(){
  // Envia itens + desconto para o backend, que:
  // - Atualiza pedido (subtotal/total)
  // - Cria item_pedido
  // - (Trigger) grava mov_caixa
  // - Opcional: gera pdv_pedido_meta (já criado na abertura)
  const payload = {
    itens: venda.itens.map(i=>({produto_id:i.id, quantidade:i.qtd, preco_unit:i.preco})),
    desconto: venda.desconto
  };
  try{
    if(venda.id){
      const r = await fetch(`/pdv/api/venda/${venda.id}/finalizar`,{
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body: JSON.stringify(payload)
      });
      if(r.ok){
        alert('Venda finalizada!');
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalPagamentos'));
        if(modal) modal.hide();
        await novaVenda();
        return;
      }
    }
  }catch(e){ console.error(e); }
  alert('Não foi possível finalizar agora.');
}

// Eventos UI
els.btnAdicionar.addEventListener('click', async ()=>{
  const q = els.busca.value.trim();
  if(!q) { els.busca.focus(); return; }
  const res = await buscarProdutos(q);
  if(res.length===1){ adicionarProduto(res[0]); }
  else { mostrarResultados(res); }
});

els.busca.addEventListener('input', async (ev)=>{
  const q = ev.target.value.trim();
  if(q.length < 2){ els.resultadoBusca.style.display='none'; return; }
  const res = await buscarProdutos(q);
  mostrarResultados(res);
});

els.btnPagamentos.addEventListener('click', ()=>{
  new bootstrap.Modal('#modalPagamentos').show();
  document.getElementById('pgValor').value = '';
  desenharPagamentos();
});

document.getElementById('btnIncluirPagamento').addEventListener('click', ()=>{
  incluirPagamento(document.getElementById('pgTipo').value, +document.getElementById('pgValor').value);
});

document.getElementById('btnFinalizar').addEventListener('click', finalizarVenda);

els.btnNovaVenda.addEventListener('click', novaVenda);

els.btnDesconto.addEventListener('click', ()=> new bootstrap.Modal('#modalDesconto').show());
document.getElementById('btnAplicarDesconto').addEventListener('click', ()=>{
  venda.desconto = Math.max(0, +document.getElementById('descontoValor').value||0);
  atualizarTotais();
  bootstrap.Modal.getInstance(document.getElementById('modalDesconto')).hide();
});

// Atalhos de teclado
document.addEventListener('keydown', (e)=>{
  if(e.key==='F2'){ e.preventDefault(); novaVenda(); }
  if(e.key==='F3'){ e.preventDefault(); els.busca.focus(); els.busca.select(); }
  if(e.key==='F4'){ e.preventDefault(); els.qtd.focus(); els.qtd.select(); }
  if(e.key==='F7'){ e.preventDefault(); els.btnDesconto.click(); }
  if(e.key==='F8'){
    e.preventDefault();
    if(venda.itens.length) removerItem(venda.itens.length-1);
  }
  if(e.key==='F9'){ e.preventDefault(); els.btnPagamentos.click(); }
  if(e.key==='F10'){ e.preventDefault(); finalizarVenda(); }
  if(e.key==='Escape'){
    e.preventDefault();
    if(confirm('Cancelar venda atual?')) novaVenda();
  }
});

// Inicialização
novaVenda();
</script>
</body>
</html>
