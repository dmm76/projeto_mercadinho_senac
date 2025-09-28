<?php

/** @var array $usuario autenticado, se o layout ja injeta isso */
$usuario = (isset($usuario) && is_array($usuario)) ? $usuario : [];
$title = 'PDV - Frente de Caixa';

$operadorId = isset($usuario['id']) ? (int) $usuario['id'] : 1;
$operadorNome = isset($usuario['nome']) ? (string) $usuario['nome'] : 'Operador';
$clienteId = isset($usuario['cliente_id']) ? (int) $usuario['cliente_id'] : 1;
$terminalId = 1; // TODO: carregar do contexto real do PDV
$terminalNome = 'Caixa 01';
?>
<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title) ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= \App\Core\Url::to('/assets/site/pdv/pdv.css') ?>">
</head>

<body class="pdv-body pdv-venda" data-view="venda"
    data-operador-id="<?= $operadorId ?>"
    data-operador-nome="<?= htmlspecialchars($operadorNome, ENT_QUOTES, 'UTF-8') ?>"
    data-cliente-id="<?= $clienteId ?>"
    data-terminal-id="<?= $terminalId ?>"
    data-terminal-nome="<?= htmlspecialchars($terminalNome, ENT_QUOTES, 'UTF-8') ?>"
    data-url-pagamentos="<?= \App\Core\Url::to('/pdv/pagamentos') ?>">

    <div class="pdv-wrap">
        <nav class="navbar pdv-navbar">
            <div class="container-fluid">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div class="nav-brand-title">Mercado Borba Gato <span class="nav-brand-sub">&bull; Frente de Caixa</span></div>
                    <div class="d-flex flex-wrap align-items-center gap-3 text-end">
                        <span class="navbar-info">PDV <strong>#<?= $terminalId ?></strong></span>
                        <span class="navbar-info">Operador <strong id="operadorNome"><?= htmlspecialchars($operadorNome) ?></strong></span>
                        <span class="navbar-info">Terminal <strong id="terminalNome"><?= htmlspecialchars($terminalNome) ?></strong></span>
                        <span class="navbar-info">Turno <strong id="turnoId">#1</strong></span>
                        <span class="status-pill status-novo" id="statusVenda" data-status="novo">Venda nova</span>
                        <button class="btn btn-sm btn-outline-light" id="btnNovaVenda" title="F2">
                            Nova venda <span class="kbd">F2</span>
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <main class="pdv-main container-fluid py-3 flex-grow-1">
            <div class="pdv-grid pdv-grid-venda">
                <aside class="pdv-column-actions">
                    <div class="panel panel-actions h-100">
                        <div class="pdv-button-grid mb-2">
                            <button class="btn btn-action primary" id="btnFocusBusca" type="button">Registrar item <span class="kbd">F3</span></button>
                            <button class="btn btn-action" type="button" id="btnCliente">Cliente / CPF</button>
                            <button class="btn btn-action" type="button" id="btnCancelarItem">Cancelar item</button>
                            <button class="btn btn-action" type="button" id="btnConsultarProduto">Consultar produto</button>
                            <button class="btn btn-action" id="btnDesconto" type="button">Desconto na venda <span class="kbd">F7</span></button>
                            <button class="btn btn-action" type="button" id="btnOrcamento">Orcamento</button>
                        </div>

                        <div class="panel-soft p-3 pdv-fieldset flex-grow-1">
                            <input type="text" class="form-control form-control-lg busca-rapida" id="inputBusca"
                                placeholder="EAN, SKU ou nome" autocomplete="off">
                            <input type="number" step="0.001" min="0.001" class="form-control form-control-lg"
                                id="inputQtd" value="1" placeholder="Qtd.">

                            <button class="btn btn-primary btn-lg w-100 mt-3" id="btnAdicionar">Adicionar produto</button>

                            <div id="resultadoBusca" class="list-group mt-3"></div>
                        </div>
                    </div>
                </aside>

                <section class="pdv-column-center gap-3">
                    <div class="panel panel-ticket d-flex flex-column h-100">
                        <header class="ticket-header">
                            <div>
                                <div class="ticket-company">Mercado Borba Gato</div>
                                <div class="ticket-address">Av. Bras Leme, 1717 - Sao Paulo - SP</div>
                                <div class="ticket-datetime" id="ticketDataHora"></div>
                            </div>
                            <div class="ticket-meta text-end">
                                <div><span class="kbd">F9</span> Pagamentos</div>
                                <div><span class="kbd">F10</span> Finalizar</div>
                            </div>
                        </header>

                        <div class="table-responsive flex-grow-1 mt-3">
                            <table class="table table-sm table-striped align-middle text-light" id="tabelaItens">
                                <thead>
                                    <tr>
                                        <th style="width:60px">Item</th>
                                        <th style="width:80px">Codigo</th>
                                        <th>Descricao</th>
                                        <th class="text-end" style="width:110px">Preco</th>
                                        <th class="text-end" style="width:105px">Qtd</th>
                                        <th class="text-end" style="width:125px">Subtotal</th>
                                        <th style="width:60px"></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                        <footer class="ticket-footer mt-3">
                            <div class="ticket-summary">
                                <div class="summary-item">Itens <span id="resumoItens">0 itens</span></div>
                                <div class="summary-item">Subtotal <span id="vSubtotal">R$ 0,00</span></div>
                                <div class="summary-item">Desconto <span id="vDescontos">R$ 0,00</span></div>
                                <div class="summary-item">Recebido <span id="resumoRecebido">R$ 0,00</span></div>
                                <div class="summary-item">Troco <span id="vTroco">R$ 0,00</span></div>
                                <div class="summary-item">Falta <span id="resumoFalta">R$ 0,00</span></div>
                            </div>
                            <div class="ticket-total ms-lg-auto">
                                <div class="ticket-total-label">Total</div>
                                <div class="ticket-total-value" id="vTotal">R$ 0,00</div>
                            </div>
                        </footer>
                    </div>

                    <div class="panel panel-support">
                        <div class="row g-3">
                            <div class="col-12 col-lg-6">
                                <div class="panel-soft p-3 h-100">
                                    <div class="panel-title-sm mb-2">Produto em destaque</div>
                                    <div id="infoProdutoAtual" class="info-produto-atual">
                                        <div class="text-secondary small">Nenhum produto selecionado.</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="panel-soft p-3 h-100">
                                    <div class="panel-title-sm mb-2">Historico recente</div>
                                    <div class="historico-list flex-grow-1" id="historicoEventos">
                                        <div class="text-secondary small">Nenhum evento ainda.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="panel panel-bottom-actions">
                        <div class="d-flex flex-wrap gap-3 align-items-center justify-content-between">
                            <div>
                                <button class="btn btn-outline-light" id="btnAbrirResumo" type="button">Resumo por forma</button>
                            </div>
                            <div class="d-flex gap-3">
                                <button class="btn btn-outline-secondary" type="button" id="btnImprimirPrevia" disabled>Pre-visualizar cupom</button>
                                <a class="btn btn-payments" id="btnPagamentos" href="<?= \App\Core\Url::to('/pdv/pagamentos') ?>"
                                    data-href="<?= \App\Core\Url::to('/pdv/pagamentos') ?>">
                                    Ir para pagamentos <span class="kbd">F9</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <div class="modal fade" id="modalDesconto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content pdv-modal">
                <div class="modal-header">
                    <h5 class="modal-title">Desconto na venda</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">Valor do desconto (R$)</label>
                    <input type="number" min="0" step="0.01" class="form-control" id="descontoValor" value="0.00">
                    <small class="text-secondary">Futuras regras de cupom podem ser integradas aqui.</small>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" id="btnAplicarDesconto">Aplicar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="<?= \App\Core\Url::to('/assets/site/pdv/pdv.js') ?>" defer></script>

</body>

</html>
