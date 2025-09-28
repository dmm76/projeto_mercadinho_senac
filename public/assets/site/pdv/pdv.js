const fmt = (value) =>
  (value || 0).toLocaleString("pt-BR", {
    style: "currency",
    currency: "BRL",
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  });

const formatarQuantidade = (valor) => {
  const numero = Number(valor) || 0;
  if (Number.isInteger(numero)) return numero.toString();
  return numero
    .toFixed(3)
    .replace(/\.0+$/, "")
    .replace(/(\.\d*?)0+$/, "$1");
};

const vendaPadrao = () => ({
  id: null,
  itens: [],
  pagamentos: [],
  desconto: 0,
});

let venda = vendaPadrao();
const historico = [];

let statusAtual = { codigo: "novo", texto: "Venda nova" };
const STORAGE_KEY = "pdv.vendaAtual";
const viewAtual = document.body
  ? document.body.dataset.view || "venda"
  : "venda";

function salvarVendaEmSessao() {
  try {
    const vazio =
      !venda.id &&
      !venda.itens.length &&
      !venda.pagamentos.length &&
      !(venda.desconto > 0);
    if (vazio) {
      sessionStorage.removeItem(STORAGE_KEY);
      return;
    }
    const pacote = {
      venda: {
        id: venda.id,
        itens: venda.itens,
        pagamentos: venda.pagamentos,
        desconto: venda.desconto,
      },
      status: {
        codigo: statusAtual.codigo,
        texto: statusAtual.texto,
      },
    };
    sessionStorage.setItem(STORAGE_KEY, JSON.stringify(pacote));
  } catch (erro) {
    console.warn("Nao foi possivel salvar estado do PDV", erro);
  }
}

function carregarVendaDaSessao() {
  try {
    const bruto = sessionStorage.getItem(STORAGE_KEY);
    if (!bruto) return;
    const dados = JSON.parse(bruto);
    if (!dados || !dados.venda) return;
    const infoVenda = dados.venda;
    venda = {
      ...vendaPadrao(),
      id: infoVenda.id ?? null,
      desconto: infoVenda.desconto ?? 0,
      itens: Array.isArray(infoVenda.itens) ? infoVenda.itens : [],
      pagamentos: Array.isArray(infoVenda.pagamentos)
        ? infoVenda.pagamentos
        : [],
    };
    if (dados.status) {
      statusAtual = {
        codigo: dados.status.codigo || "novo",
        texto: dados.status.texto || "Venda nova",
      };
    }
  } catch (erro) {
    console.warn("Nao foi possivel carregar estado do PDV", erro);
    venda = vendaPadrao();
    statusAtual = { codigo: "novo", texto: "Venda nova" };
  }
}

function limparVendaDaSessao() {
  try {
    sessionStorage.removeItem(STORAGE_KEY);
  } catch (erro) {
    console.warn("Nao foi possivel limpar estado do PDV", erro);
  }
}

function destinoPagamentos() {
  if (document.body) {
    return (
      document.body.dataset.urlPagamentos ||
      (els.btnPagamentos
        ? els.btnPagamentos.dataset.href ||
          els.btnPagamentos.getAttribute("href")
        : null)
    );
  }
  return null;
}

function destinoVendaPrincipal() {
  if (document.body) {
    return document.body.dataset.urlVenda || "/pdv";
  }
  return "/pdv";
}

function contextoPdvAtual() {
  if (!document.body) return {};
  const ds = document.body.dataset || {};
  const parse = (valor, fallback = null) => {
    const numero = Number(valor);
    if (Number.isFinite(numero) && numero > 0) return numero;
    return fallback;
  };

  return {
    clienteId: parse(ds.clienteId, 1),
    operadorId: parse(ds.operadorId, 1),
    terminalId: parse(ds.terminalId, 1),
    turnoId: parse(ds.turnoId, null),
    caixaId: parse(ds.caixaId, null),
  };
}

function navegarParaPagamentos() {
  if (!venda.itens.length) {
    alert("Inclua ao menos um item antes de ir para pagamentos.");
    return;
  }
  const url = destinoPagamentos();
  if (!url) return;
  salvarVendaEmSessao();
  window.location.href = url;
}

function voltarParaItens() {
  const url = destinoVendaPrincipal();
  salvarVendaEmSessao();
  window.location.href = url;
}

const els = {
  busca: document.getElementById("inputBusca"),
  qtd: document.getElementById("inputQtd"),
  resultadoBusca: document.getElementById("resultadoBusca"),
  tabelaItens: document.querySelector("#tabelaItens tbody"),
  tabelaPagamentos: document.querySelector("#tabelaPagamentos tbody"),
  vSubtotal: document.getElementById("vSubtotal"),
  vDescontos: document.getElementById("vDescontos"),
  vTotal: document.getElementById("vTotal"),
  vRecebido: document.getElementById("resumoRecebido"),
  vTroco: document.getElementById("vTroco"),
  resumoFalta: document.getElementById("resumoFalta"),
  resumoItens: document.getElementById("resumoItens"),
  listaPagamentos: document.getElementById("listaPagamentos"),
  btnAdicionar: document.getElementById("btnAdicionar"),
  btnPagamentos: document.getElementById("btnPagamentos"),
  btnFinalizar: document.getElementById("btnFinalizar"),
  btnNovaVenda: document.getElementById("btnNovaVenda"),
  btnDesconto: document.getElementById("btnDesconto"),
  btnAbrirResumo: document.getElementById("btnAbrirResumo"),
  btnFocusBusca: document.getElementById("btnFocusBusca"),
  btnCancelarItem: document.getElementById("btnCancelarItem"),
  btnCliente: document.getElementById("btnCliente"),
  btnConsultarProduto: document.getElementById("btnConsultarProduto"),
  btnOrcamento: document.getElementById("btnOrcamento"),
  statusVenda: document.getElementById("statusVenda"),
  historico: document.getElementById("historicoEventos"),
  infoProdutoAtual: document.getElementById("infoProdutoAtual"),
  ticketDataHora: document.getElementById("ticketDataHora"),
  totaisPorTipo: {
    dinheiro: document.getElementById("totalDinheiro"),
    credito: document.getElementById("totalCredito"),
    debito: document.getElementById("totalDebito"),
    pix: document.getElementById("totalPix"),
    cheque: document.getElementById("totalCheque"),
  },
  pgValor: document.getElementById("pgValor"),
  pgTipo: document.getElementById("pgTipo"),
  pgResumoTotal: document.getElementById("pgResumoTotal"),
  pgResumoRecebido: document.getElementById("pgResumoRecebido"),
  pgResumoFalta: document.getElementById("pgResumoFalta"),
  pgResumoTroco: document.getElementById("pgResumoTroco"),
  pgResumoMensagem: document.getElementById("pgResumoMensagem"),
};

const nomeTipoPagamento = {
  dinheiro: "Dinheiro",
  credito: "Credito",
  debito: "Debito",
  pix: "PIX",
  cheque: "Cheque",
};

const calcularResumoFinanceiro = () => {
  const subtotal = venda.itens.reduce(
    (soma, item) => soma + (item.subtotal || 0),
    0
  );
  const quantidade = venda.itens.reduce(
    (soma, item) => soma + (item.qtd || 0),
    0
  );
  const total = Math.max(0, subtotal - (venda.desconto || 0));
  const recebido = venda.pagamentos.reduce(
    (soma, item) => soma + (+item.valor || 0),
    0
  );
  const falta = Math.max(0, total - recebido);
  const troco = falta === 0 ? Math.max(0, recebido - total) : 0;
  return { subtotal, quantidade, total, recebido, falta, troco };
};

const vendaEstaVazia = () =>
  venda.itens.length === 0 &&
  venda.pagamentos.length === 0 &&
  !(venda.desconto > 0);

async function cancelarVendaVaziaNoServidor() {
  if (!venda.id) return true;
  try {
    const resposta = await fetch(`/pdv/api/venda/${venda.id}/cancelar`, {
      method: "POST",
    });
    if (resposta.ok) return true;
    if (resposta.status === 422) return false;
  } catch (erro) {
    console.warn("Falha ao cancelar venda vazia", erro);
  }
  return false;
}

async function garantirVendaCriada() {
  if (venda.id) return true;
  try {
    const ctx = contextoPdvAtual();
    if (!ctx.turnoId) {
      alert('Nenhum turno aberto foi encontrado. Abra o caixa antes de iniciar vendas.');
      return false;
    }
    const payload = {
      cliente_id: ctx.clienteId || 1,
      operador_id: ctx.operadorId || 1,
      terminal_id: ctx.terminalId || 1,
      turno_id: ctx.turnoId,
    };
    const resposta = await fetch("/pdv/api/venda", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload),
    });
    if (!resposta.ok) throw new Error("Falha ao criar venda");
    const dados = await resposta.json();
    venda.id = dados.id;
    salvarVendaEmSessao();
    atualizarStatusVenda("andamento", `Venda #${venda.id}`);
    registrarEvento("SISTEMA", `Venda ${venda.id} aberta.`);
    return true;
  } catch (erro) {
    console.error(erro);
    alert("Nao foi possivel abrir a venda. Tente novamente.");
    return false;
  }
}

function limparHistorico() {
  historico.length = 0;
  if (els.historico) {
    els.historico.innerHTML =
      '<div class="text-secondary small">Nenhum evento ainda.</div>';
  }
}

function registrarEvento(tipo, descricao) {
  const entrada = { tipo, descricao, hora: new Date() };
  historico.unshift(entrada);
  if (historico.length > 20) historico.pop();
  if (!els.historico) return;
  els.historico.innerHTML = "";
  historico.forEach((item) => {
    const linha = document.createElement("div");
    linha.className = "historico-item";

    const badge = document.createElement("span");
    badge.className = "badge";
    badge.textContent = item.tipo;

    const bloco = document.createElement("div");
    const titulo = document.createElement("div");
    titulo.className = "fw-semibold";
    titulo.textContent = item.descricao;

    const tempo = document.createElement("div");
    tempo.className = "text-secondary small";
    const h = String(item.hora.getHours()).padStart(2, "0");
    const m = String(item.hora.getMinutes()).padStart(2, "0");
    const s = String(item.hora.getSeconds()).padStart(2, "0");
    tempo.textContent = `${h}:${m}:${s}`;

    bloco.appendChild(titulo);
    bloco.appendChild(tempo);

    linha.appendChild(badge);
    linha.appendChild(bloco);
    els.historico.appendChild(linha);
  });
}

function atualizarStatusVenda(status, mensagem, skipPersist = false) {
  statusAtual = { codigo: status, texto: mensagem };
  if (els.statusVenda) {
    els.statusVenda.dataset.status = status;
    els.statusVenda.textContent = mensagem;
    els.statusVenda.classList.remove(
      "status-novo",
      "status-andamento",
      "status-finalizado"
    );
    if (status === "novo") {
      els.statusVenda.classList.add("status-novo");
    } else if (status === "andamento") {
      els.statusVenda.classList.add("status-andamento");
    } else if (status === "finalizado") {
      els.statusVenda.classList.add("status-finalizado");
    }
  }
  if (!skipPersist) salvarVendaEmSessao();
  const vendaIdEl = document.getElementById("pagamentoVendaId");
  if (vendaIdEl) {
    vendaIdEl.textContent = venda.id ? `#${venda.id}` : "-";
  }
}

function atualizarTotais() {
  const resumo = calcularResumoFinanceiro();
  if (els.vSubtotal) els.vSubtotal.textContent = fmt(resumo.subtotal);
  if (els.vDescontos) els.vDescontos.textContent = fmt(venda.desconto || 0);
  if (els.vTotal) els.vTotal.textContent = fmt(resumo.total);
  if (els.vRecebido) els.vRecebido.textContent = fmt(resumo.recebido);
  if (els.vTroco) els.vTroco.textContent = fmt(resumo.troco);
  if (els.resumoFalta) els.resumoFalta.textContent = fmt(resumo.falta);
  if (els.resumoItens) {
    const textoQtd = `${formatarQuantidade(resumo.quantidade)} ${
      resumo.quantidade === 1 ? "item" : "itens"
    }`;
    els.resumoItens.textContent = textoQtd;
  }
  atualizarResumoModal(resumo);
}

function atualizarResumoModal(resumo) {
  if (els.pgResumoTotal) els.pgResumoTotal.textContent = fmt(resumo.total);
  if (els.pgResumoRecebido)
    els.pgResumoRecebido.textContent = fmt(resumo.recebido);
  if (els.pgResumoFalta) els.pgResumoFalta.textContent = fmt(resumo.falta);
  if (els.pgResumoTroco) els.pgResumoTroco.textContent = fmt(resumo.troco);

  if (els.btnFinalizar) {
    const podeFinalizar = venda.itens.length > 0 && resumo.falta === 0;
    els.btnFinalizar.disabled = !podeFinalizar;
    els.btnFinalizar.classList.toggle("btn-success", podeFinalizar);
    els.btnFinalizar.classList.toggle("btn-secondary", !podeFinalizar);
  }

  if (els.pgResumoMensagem) {
    if (resumo.falta > 0) {
      els.pgResumoMensagem.style.display = "block";
      els.pgResumoMensagem.className = "resumo-alerta text-warning";
      els.pgResumoMensagem.textContent = `Faltam ${fmt(
        resumo.falta
      )} para finalizar.`;
    } else if (resumo.troco > 0) {
      els.pgResumoMensagem.style.display = "block";
      els.pgResumoMensagem.className = "resumo-alerta text-info";
      els.pgResumoMensagem.textContent = `Troco previsto: ${fmt(
        resumo.troco
      )}.`;
    } else {
      els.pgResumoMensagem.style.display = "none";
    }
  }
}

function atualizarResumoPagamentos() {
  if (els.listaPagamentos) {
    if (!venda.pagamentos.length) {
      els.listaPagamentos.innerHTML =
        '<li class="text-secondary small">Nenhum pagamento adicionado.</li>';
    } else {
      els.listaPagamentos.innerHTML = "";
      venda.pagamentos.forEach((pgto) => {
        const li = document.createElement("li");
        const spanNome = document.createElement("span");
        spanNome.textContent = nomeTipoPagamento[pgto.tipo] || pgto.tipo;
        const spanValor = document.createElement("span");
        spanValor.textContent = fmt(+pgto.valor || 0);
        li.appendChild(spanNome);
        li.appendChild(spanValor);
        els.listaPagamentos.appendChild(li);
      });
    }
  }

  const totais = {
    dinheiro: 0,
    credito: 0,
    debito: 0,
    pix: 0,
    cheque: 0,
  };

  venda.pagamentos.forEach((pg) => {
    const chave = (pg.tipo || "").toLowerCase();
    if (Object.prototype.hasOwnProperty.call(totais, chave)) {
      totais[chave] += +pg.valor || 0;
    }
  });

  Object.entries(els.totaisPorTipo).forEach(([tipo, elemento]) => {
    if (!elemento) return;
    elemento.textContent = fmt(totais[tipo] || 0);
  });

  atualizarResumoModal(calcularResumoFinanceiro());
}

function atualizarProdutoAtual() {
  if (!els.infoProdutoAtual) return;
  if (!venda.itens.length) {
    els.infoProdutoAtual.textContent = "Utilize a busca para selecionar";
    return;
  }
  const ultimo = venda.itens[venda.itens.length - 1];
  const detalhes = `${ultimo.nome} • ${formatarQuantidade(ultimo.qtd)} x ${fmt(
    ultimo.preco
  )}`;
  els.infoProdutoAtual.textContent = detalhes;
}

function redesenharItens() {
  if (!els.tabelaItens) return;
  els.tabelaItens.innerHTML = "";
  venda.itens.forEach((item, index) => {
    const tr = document.createElement("tr");
    tr.innerHTML = `
      <td>${index + 1}</td>
      <td>${item.sku || item.ean || item.id || "-"}</td>
      <td>${item.nome}</td>
      <td class="text-end">${fmt(item.preco)}</td>
      <td class="text-end">
        <input type="number" step="0.001" min="0.001" value="${
          item.qtd
        }" class="form-control form-control-sm"
               data-idx="${index}" oninput="alterarQtd(event)">
      </td>
      <td class="text-end">${fmt(item.subtotal)}</td>
      <td class="text-end">
        <button class="btn btn-sm btn-outline-danger" onclick="removerItem(${index})" title="F8">X</button>
      </td>`;
    els.tabelaItens.appendChild(tr);
  });
  atualizarProdutoAtual();
  atualizarTotais();
  salvarVendaEmSessao();
}

window.alterarQtd = (evento) => {
  const idx = Number(evento.target.dataset.idx);
  if (Number.isNaN(idx) || !venda.itens[idx]) return;
  const quantidade = Math.max(0.001, +evento.target.value || 1);
  venda.itens[idx].qtd = quantidade;
  venda.itens[idx].subtotal = quantidade * venda.itens[idx].preco;
  registrarEvento(
    "ITEM",
    `Quantidade ajustada (${formatarQuantidade(quantidade)}) em ${
      venda.itens[idx].nome
    }`
  );
  redesenharItens();
};

window.removerItem = (idx) => {
  if (!venda.itens[idx]) return;
  registrarEvento("ITEM", `Item removido: ${venda.itens[idx].nome}`);
  venda.itens.splice(idx, 1);
  redesenharItens();
};

async function buscarProdutos(termo) {
  const url = `/pdv/api/produtos?q=${encodeURIComponent(termo)}`;
  try {
    const resposta = await fetch(url);
    if (!resposta.ok) throw new Error("Busca falhou");
    return await resposta.json();
  } catch (erro) {
    console.error(erro);
    return [];
  }
}

function mostrarResultados(lista) {
  if (!els.resultadoBusca) return;
  els.resultadoBusca.innerHTML = "";
  if (!lista.length) {
    els.resultadoBusca.style.display = "none";
    return;
  }
  lista.forEach((produto) => {
    const anchor = document.createElement("a");
    anchor.href = "#";
    anchor.className = "list-group-item list-group-item-action";
    const infoSku = produto.sku || "";
    const infoEan = produto.ean ? ` • ${produto.ean}` : "";
    anchor.innerHTML = `<div class="d-flex justify-content-between">
        <div><strong>${
          produto.nome
        }</strong><div class="small text-secondary">${infoSku}${infoEan}</div></div>
        <div>${fmt(+produto.preco_venda || 0)}</div>
      </div>`;
    anchor.onclick = async (ev) => {
      ev.preventDefault();
      await adicionarProduto(produto);
      els.resultadoBusca.style.display = "none";
      if (els.busca) els.busca.value = "";
    };
    els.resultadoBusca.appendChild(anchor);
  });
  els.resultadoBusca.style.display = "block";
}

async function adicionarProduto(produto) {
  if (!produto) return;
  const quantidade = Math.max(0.001, +els.qtd.value || 1);
  if (!(await garantirVendaCriada())) return;
  const preco = +produto.preco_venda || 0;
  const item = {
    id: produto.id,
    nome: produto.nome,
    sku: produto.sku,
    ean: produto.ean,
    preco,
    qtd: quantidade,
    subtotal: preco * quantidade,
  };
  venda.itens.push(item);
  atualizarStatusVenda("andamento", `Venda #${venda.id}`);
  registrarEvento("ITEM", `Produto adicionado: ${produto.nome}`);
  redesenharItens();
  els.qtd.value = "1";
  if (els.busca) {
    els.busca.focus();
    els.busca.select();
  }
}

async function novaVenda(force = false) {
  if (venda.id) {
    if (!vendaEstaVazia() && !force) {
      alert(
        "Finalize a venda atual ou remova itens e pagamentos antes de iniciar outra."
      );
      return;
    }
    if (vendaEstaVazia()) {
      await cancelarVendaVaziaNoServidor();
    }
  }
  resetarEstadoVenda();
}

function resetarEstadoVenda() {
  venda = vendaPadrao();
  statusAtual = { codigo: "novo", texto: "Venda nova" };
  limparHistorico();
  atualizarStatusVenda(statusAtual.codigo, statusAtual.texto, true);
  atualizarProdutoAtual();
  redesenharItens();
  desenharPagamentos();
  limparVendaDaSessao();
}

async function incluirPagamento(tipo, valor) {
  if (!venda.itens.length) {
    alert("Adicione itens antes de registrar pagamentos.");
    return;
  }
  if (!valor || valor <= 0) {
    if (els.pgValor) els.pgValor.focus();
    return;
  }
  if (!(await garantirVendaCriada())) return;

  const resumoAntes = calcularResumoFinanceiro();
  const faltaAntes = resumoAntes.falta;
  let trocoPrevisto = 0;

  if (faltaAntes > 0 && valor > faltaAntes) {
    trocoPrevisto = valor - faltaAntes;
    const confirmar = confirm(
      `Receber ${fmt(valor)} gera troco de ${fmt(trocoPrevisto)}. Confirmar?`
    );
    if (!confirmar) return;
  }
  if (faltaAntes === 0) {
    trocoPrevisto = valor;
    const confirmar = confirm(
      `Ja temos o total recebido. Registrar mais ${fmt(valor)} (troco ${fmt(
        trocoPrevisto
      )})?`
    );
    if (!confirmar) return;
  }

  const pagamento = { tipo, valor: +valor };
  try {
    if (venda.id) {
      const resposta = await fetch(`/pdv/api/venda/${venda.id}/pagamentos`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(pagamento),
      });
      if (!resposta.ok) {
        if (resposta.status === 422) {
          const dados = await resposta.json().catch(() => ({}));
          alert(dados.error || "Pagamento rejeitado pelo servidor.");
          return;
        }
        const respostaTexto = await resposta.text().catch(() => "");
        throw new Error(
          `Status ${resposta.status} ${resposta.statusText} ${respostaTexto}`.trim()
        );
      }
    }
  } catch (erro) {
    console.warn("Falha ao registrar pagamento", erro);
    alert(
      `Nao foi possivel registrar o pagamento. Detalhes: ${
        erro.message || erro
      }`
    );
    return;
  }
  venda.pagamentos.push(pagamento);
  registrarEvento(
    "PAGAMENTO",
    `Pagamento ${nomeTipoPagamento[tipo] || tipo} de ${fmt(pagamento.valor)}`
  );
  desenharPagamentos();
  atualizarTotais();
  if (els.pgValor) {
    const resumo = calcularResumoFinanceiro();
    const sugerido = resumo.falta > 0 ? resumo.falta : 0;
    els.pgValor.value = sugerido > 0 ? sugerido.toFixed(2) : "";
    els.pgValor.focus();
    els.pgValor.select();
  }
}

function desenharPagamentos() {
  if (els.tabelaPagamentos) {
    els.tabelaPagamentos.innerHTML = "";
    venda.pagamentos.forEach((pgto, idx) => {
      const tr = document.createElement("tr");
      tr.innerHTML = `<td>${nomeTipoPagamento[pgto.tipo] || pgto.tipo}</td>
        <td class="text-end">${fmt(pgto.valor)}</td>
        <td class="text-end"><button class="btn btn-sm btn-outline-danger" onclick="removerPagamento(${idx})">Remover</button></td>`;
      els.tabelaPagamentos.appendChild(tr);
    });
  }
  atualizarResumoPagamentos();
  atualizarTotais();
  salvarVendaEmSessao();
}

window.removerPagamento = (idx) => {
  if (!venda.pagamentos[idx]) return;
  const removido = venda.pagamentos[idx];
  registrarEvento(
    "PAGAMENTO",
    `Pagamento removido (${nomeTipoPagamento[removido.tipo] || removido.tipo})`
  );
  venda.pagamentos.splice(idx, 1);
  desenharPagamentos();
  atualizarTotais();
};

async function finalizarVenda() {
  if (!venda.itens.length) {
    alert("Inclua ao menos um item.");
    return;
  }
  const resumo = calcularResumoFinanceiro();
  if (resumo.falta > 0) {
    alert(`Ainda faltam ${fmt(resumo.falta)} para finalizar.`);
    return;
  }
  if (resumo.troco > 0) {
    const confirmarTroco = confirm(
      `Troco previsto de ${fmt(resumo.troco)}. Confirmar finalizacao?`
    );
    if (!confirmarTroco) return;
  }
  if (!(await garantirVendaCriada())) return;

  const payload = {
    itens: venda.itens.map((item) => ({
      produto_id: item.id,
      quantidade: item.qtd,
      preco_unit: item.preco,
    })),
    desconto: venda.desconto,
  };

  try {
    const resposta = await fetch(`/pdv/api/venda/${venda.id}/finalizar`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload),
    });
    if (resposta.ok) {
      atualizarStatusVenda("finalizado", `Venda #${venda.id} finalizada`);
      registrarEvento("SISTEMA", `Venda ${venda.id} finalizada.`);
      const dados = await resposta.json().catch(() => ({}));
      if (dados && dados.troco && dados.troco > 0) {
        alert(`Venda finalizada! Troco: ${fmt(dados.troco)}`);
      } else {
        alert("Venda finalizada!");
      }
      resetarEstadoVenda();
      if (viewAtual === "pagamentos") {
        // volta para a tela principal do PDV após finalizar
        setTimeout(() => voltarParaItens(), 0);
      }
      return;
    }
    if (resposta.status === 422) {
      const erro = await resposta.json().catch(() => ({}));
      alert(erro.error || "Pagamentos insuficientes para finalizar.");
      return;
    }
    const erroTexto = await resposta.text().catch(() => "");
    console.warn(
      "Resposta inesperada ao finalizar venda",
      erroTexto || resposta.statusText
    );
  } catch (erro) {
    console.error(erro);
  }
  alert("Nao foi possivel finalizar agora.");
}

function aplicarDesconto(valor) {
  venda.desconto = Math.max(0, +valor || 0);
  registrarEvento("DESCONTO", `Desconto aplicado: ${fmt(venda.desconto)}`);
  atualizarTotais();
  salvarVendaEmSessao();
}

function atualizarRelogio() {
  if (!els.ticketDataHora) return;
  const agora = new Date();
  const data = agora.toLocaleDateString("pt-BR");
  const hora = agora.toLocaleTimeString("pt-BR", {
    hour: "2-digit",
    minute: "2-digit",
    second: "2-digit",
  });
  els.ticketDataHora.textContent = `${data} • ${hora}`;
}

function configurarEventos() {
  try {
    if (els.btnAdicionar) {
      els.btnAdicionar.addEventListener("click", async () => {
        const termo = els.busca ? els.busca.value.trim() : "";
        if (!termo) {
          if (els.busca) els.busca.focus();
          return;
        }
        const resultados = await buscarProdutos(termo);
        if (resultados.length === 1) {
          await adicionarProduto(resultados[0]);
        } else {
          mostrarResultados(resultados);
        }
      });
    }

    if (els.busca) {
      els.busca.addEventListener("input", async (ev) => {
        const termo = ev.target.value.trim();
        if (termo.length < 2) {
          if (els.resultadoBusca) els.resultadoBusca.style.display = "none";
          return;
        }
        const resultados = await buscarProdutos(termo);
        mostrarResultados(resultados);
      });
    }

    if (els.btnPagamentos) {
      els.btnPagamentos.addEventListener("click", (evento) => {
        evento.preventDefault();
        navegarParaPagamentos();
      });
    }

    const btnIncluirPagamento = document.getElementById("btnIncluirPagamento");
    if (btnIncluirPagamento) {
      btnIncluirPagamento.addEventListener("click", () => {
        incluirPagamento(
          els.pgTipo ? els.pgTipo.value : "dinheiro",
          els.pgValor ? +els.pgValor.value : 0
        );
      });
    }

    if (els.pgValor) {
      els.pgValor.addEventListener("keydown", (ev) => {
        if (ev.key === "Enter") {
          ev.preventDefault();
          incluirPagamento(
            els.pgTipo ? els.pgTipo.value : "dinheiro",
            els.pgValor ? +els.pgValor.value : 0
          );
        }
      });
    }

    if (els.btnFinalizar) {
      els.btnFinalizar.addEventListener("click", finalizarVenda);
    }

    if (els.btnNovaVenda) {
      els.btnNovaVenda.addEventListener("click", () => {
        novaVenda();
      });
    }

    if (els.btnDesconto) {
      els.btnDesconto.addEventListener("click", () => {
        if (window.bootstrap && typeof bootstrap.Modal === "function") {
          new bootstrap.Modal("#modalDesconto").show();
        } else {
          alert(
            "Nao foi possivel abrir o modal de desconto porque o Bootstrap nao carregou."
          );
        }
      });
    }

    const btnAplicarDesconto = document.getElementById("btnAplicarDesconto");
    if (btnAplicarDesconto) {
      btnAplicarDesconto.addEventListener("click", () => {
        const campo = document.getElementById("descontoValor");
        aplicarDesconto(campo ? campo.value : 0);
        if (window.bootstrap && typeof bootstrap.Modal === "function") {
          const modal = bootstrap.Modal.getInstance(
            document.getElementById("modalDesconto")
          );
          if (modal) modal.hide();
        }
      });
    }

    if (els.btnAbrirResumo) {
      els.btnAbrirResumo.addEventListener("click", () => {
        const bloco = document.getElementById("resumoPorTipo");
        if (bloco) bloco.scrollIntoView({ behavior: "smooth", block: "start" });
      });
    }

    if (els.btnFocusBusca) {
      els.btnFocusBusca.addEventListener("click", () => {
        if (els.busca) {
          els.busca.focus();
          els.busca.select();
        }
      });
    }

    if (els.btnCancelarItem) {
      els.btnCancelarItem.addEventListener("click", () => {
        if (!venda.itens.length) {
          alert("Nenhum item para remover.");
          return;
        }
        removerItem(venda.itens.length - 1);
      });
    }

    if (els.btnCliente) {
      els.btnCliente.addEventListener("click", () => {
        alert("Funcionalidade de cliente/CPF em desenvolvimento.");
      });
    }

    if (els.btnConsultarProduto) {
      els.btnConsultarProduto.addEventListener("click", () => {
        if (els.busca) {
          els.busca.focus();
          els.busca.select();
        }
      });
    }

    if (els.btnOrcamento) {
      els.btnOrcamento.addEventListener("click", () => {
        alert("Funcionalidade de orcamento em desenvolvimento.");
      });
    }

    const btnVoltarVenda = document.getElementById("btnVoltarVenda");
    if (btnVoltarVenda) {
      btnVoltarVenda.addEventListener("click", (evento) => {
        evento.preventDefault();
        voltarParaItens();
      });
    }

    document.addEventListener("keydown", async (evento) => {
      if (evento.key === "F2") {
        evento.preventDefault();
        await novaVenda();
      }
      if (evento.key === "F3") {
        evento.preventDefault();
        if (els.busca) {
          els.busca.focus();
          els.busca.select();
        }
      }
      if (evento.key === "F4") {
        evento.preventDefault();
        if (els.qtd) {
          els.qtd.focus();
          els.qtd.select();
        }
      }
      if (evento.key === "F7") {
        evento.preventDefault();
        if (els.btnDesconto) els.btnDesconto.click();
      }
      if (evento.key === "F8") {
        evento.preventDefault();
        if (venda.itens.length) window.removerItem(venda.itens.length - 1);
      }
      if (evento.key === "F9") {
        evento.preventDefault();
        if (viewAtual === "pagamentos") {
          if (els.pgValor) {
            els.pgValor.focus();
            els.pgValor.select();
          }
        } else {
          navegarParaPagamentos();
        }
      }
      if (evento.key === "F10") {
        evento.preventDefault();
        if (viewAtual === "pagamentos") {
          finalizarVenda();
        } else {
          navegarParaPagamentos();
        }
      }
      if (evento.key === "Escape") {
        evento.preventDefault();
        if (viewAtual === "pagamentos") {
          voltarParaItens();
          return;
        }
        if (vendaEstaVazia()) {
          await novaVenda(true);
        } else {
          alert(
            "Remova itens e pagamentos ou finalize a venda antes de cancelar."
          );
        }
      }
    });
  } catch (erro) {
    console.error("PDV: falha ao configurar eventos", erro);
    alert(
      "O PDV encontrou um erro ao carregar os controles. Abra o console do navegador para detalhes."
    );
  }
}

function inicializar() {
  try {
    carregarVendaDaSessao();
    atualizarRelogio();
    setInterval(atualizarRelogio, 1000);
    configurarEventos();
    if (
      venda.id ||
      venda.itens.length ||
      venda.pagamentos.length ||
      venda.desconto
    ) {
      atualizarStatusVenda(statusAtual.codigo, statusAtual.texto, true);
      redesenharItens();
      desenharPagamentos();
    } else {
      resetarEstadoVenda();
    }
    console.info("PDV inicializado", { viewAtual, vendaId: venda.id });
  } catch (erro) {
    console.error("PDV: falha na inicializacao", erro);
    alert(
      "O PDV encontrou um erro ao iniciar. Veja o console do navegador para detalhes."
    );
  }
}

inicializar();
