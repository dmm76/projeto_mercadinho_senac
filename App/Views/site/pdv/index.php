<?php

declare(strict_types=1);

/** @var array $usuario autenticado, se o layout ja injeta isso */
/** @var array $pdvTurno turno do PDV, se injetado pelo controller */

$usuario = (isset($usuario) && is_array($usuario)) ? $usuario : [];
$pdvTurno = (isset($pdvTurno) && is_array($pdvTurno)) ? $pdvTurno : [];

$title = 'PDV - Frente de Caixa';

/** Helper para env/escape/url (robusto em prod) */
$e = static fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');

/** Fallback para Url::to() caso a classe não esteja carregada (ex.: acesso direto à view sem autoload) */
$urlTo = static function (string $path) {
    $path = '/' . ltrim($path, '/');
    if (class_exists(\App\Core\Url::class) && method_exists(\App\Core\Url::class, 'to')) {
        try {
            return \App\Core\Url::to($path);
        } catch (\Throwable) {
            // cai para fallback se algo der errado no helper
        }
    }
    return $path; // fallback absoluto simples
};

/** Operador/terminal: defaults seguros */
$operadorId   = isset($usuario['id']) ? (int)$usuario['id'] : 1;
$operadorNome = isset($usuario['nome']) ? (string)$usuario['nome'] : 'Operador';
$clienteId    = isset($usuario['cliente_id']) ? (int)$usuario['cliente_id'] : 1;

$terminalId   = !empty($pdvTurno['terminal_id']) ? (int)$pdvTurno['terminal_id'] : 1;
$terminalNome = !empty($pdvTurno['terminal_nome']) ? (string)$pdvTurno['terminal_nome'] : 'Caixa 01';
$turnoId      = isset($pdvTurno['turno_id']) ? (int)$pdvTurno['turno_id'] : 0;
$caixaId      = isset($pdvTurno['caixa_id']) ? (int)$pdvTurno['caixa_id'] : 0;

/** Se vier operador no turno e não tiver no $usuario, usa o do turno */
if (!empty($pdvTurno['operador_id']) && empty($usuario['id'])) {
    $operadorId = (int)$pdvTurno['operador_id'];
}
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

<body class="pdv-body pdv-venda" data-view="venda" data-operador-id="<?= $operadorId ?>"
    data-operador-nome="<?= htmlspecialchars($operadorNome, ENT_QUOTES, 'UTF-8') ?>" data-cliente-id="<?= $clienteId ?>"
    data-terminal-id="<?= $terminalId ?>"
    data-terminal-nome="<?= htmlspecialchars($terminalNome, ENT_QUOTES, 'UTF-8') ?>"
    data-turno-id="<?= $turnoId ?: '' ?>" data-caixa-id="<?= $caixaId ?: '' ?>"
    data-url-abrir="<?= $urlTo('/pdv/abrir') ?>" data-url-pagamentos="<?= \App\Core\Url::to('/pdv/pagamentos') ?>">

    <div class="pdv-wrap">
        <!-- Topbar -->
        <nav class="navbar pdv-navbar">
            <div class="container-fluid">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 w-100">
                    <div class="d-flex align-items-baseline gap-2">
                        <div class="nav-brand-title">Mercado Borba Gato</div>
                        <span class="nav-brand-sub">&bull; Frente de Caixa</span>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-3 text-end">
                        <span class="navbar-info">PDV <strong>#<?= $terminalId ?></strong></span>
                        <span class="navbar-info">Operador <strong
                                id="operadorNome"><?= htmlspecialchars($operadorNome) ?></strong></span>
                        <span class="navbar-info">Terminal <strong
                                id="terminalNome"><?= htmlspecialchars($terminalNome) ?></strong></span>
                        <span class="navbar-info" id="ticketDataHora"></span>
                        <span class="status-pill status-novo" id="statusVenda" data-status="novo">Venda nova</span>
                        <button class="btn btn-sm btn-outline-primary" id="btnNovaVenda" title="F2">
                            Nova venda <span class="kbd">F2</span>
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <main class="pdv-main container-fluid py-3 flex-grow-1">
            <div class="pdv-grid">
                <!-- Coluna esquerda: acoes e entrada -->
                <aside class="glass p-3">
                    <div class="mb-3 d-grid gap-2">
                        <button class="btn btn-action btn-primary" id="btnFocusBusca" type="button">Registrar item <span
                                class="kbd">F3</span></button>
                        <button class="btn btn-action btn-outline-secondary" type="button" id="btnCliente">Cliente /
                            CPF</button>
                        <button class="btn btn-action btn-outline-secondary" type="button" id="btnCancelarItem">Cancelar
                            item</button>
                        <button class="btn btn-action btn-outline-secondary" type="button"
                            id="btnConsultarProduto">Consultar produto</button>
                        <button class="btn btn-action btn-outline-secondary" id="btnDesconto" type="button">Desconto na
                            venda <span class="kbd">F7</span></button>
                        <button class="btn btn-action btn-outline-secondary" type="button"
                            id="btnOrcamento">Orcamento</button>
                        <?php if (empty($turnoId)): ?>
                            <button class="btn btn-action btn-success" type="button" id="btnAbrirCaixa">
                                Abrir caixa
                            </button>
                        <?php endif; ?>
                    </div>
                    <div class="panel-soft p-3">
                        <label class="field-label mb-1">Descricao / Codigo</label>
                        <input type="text" class="form-control form-control-lg busca-rapida" id="inputBusca"
                            placeholder="EAN, SKU ou nome" autocomplete="off">
                        <div class="row g-2 mt-2">
                            <div class="col-6">
                                <label class="field-label mb-1">Quantidade</label>
                                <input type="number" step="0.001" min="0.001" class="form-control form-control-lg"
                                    id="inputQtd" value="1" placeholder="Qtd.">
                            </div>
                            <div class="col-6 d-grid">
                                <label class="field-label mb-1">&nbsp;</label>
                                <button class="btn btn-primary btn-lg w-100" id="btnAdicionar">Adicionar
                                    produto</button>
                            </div>
                        </div>
                        <div id="resultadoBusca" class="list-group mt-3"></div>
                    </div>
                </aside>

                <!-- Coluna central: cupom/itens -->
                <section class="panel p-3 d-flex flex-column">
                    <header class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                        <div>
                            <div class="ticket-company fw-bold">Mercado Borba Gato</div>
                            <div class="ticket-address text-secondary small">R. das Tipuanas, 250, Borba Gato - Maringá
                                - PR, 87060-130</div>
                        </div>
                        <div class="text-end small">
                            <div><span class="kbd">F9</span> Pagamentos</div>
                            <div><span class="kbd">F10</span> Finalizar</div>
                        </div>
                    </header>

                    <div class="table-responsive flex-grow-1 mt-3">
                        <table class="table table-sm table-striped align-middle text-dark" id="tabelaItens">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:60px">Item</th>
                                    <th style="width:90px">Codigo</th>
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

                    <footer class="mt-3 d-flex flex-wrap align-items-center gap-3">
                        <div class="d-flex flex-wrap gap-4">
                            <div class="text-dark">Itens <span id="resumoItens" class="fw-semibold">0 itens</span></div>
                            <div class="text-dark">Subtotal <span id="vSubtotal" class="fw-semibold">R$ 0,00</span>
                            </div>
                            <div class="text-dark">Desconto <span id="vDescontos" class="fw-semibold">R$ 0,00</span>
                            </div>
                            <div class="text-dark">Recebido <span id="resumoRecebido" class="fw-semibold">R$ 0,00</span>
                            </div>
                            <div class="text-dark">Troco <span id="vTroco" class="fw-semibold">R$ 0,00</span></div>
                            <div class="text-dark">Falta <span id="resumoFalta" class="fw-semibold">R$ 0,00</span></div>
                        </div>
                        <div class="ticket-total ms-lg-auto">
                            <div class="ticket-total-label text-secondary">Total</div>
                            <div class="ticket-total-value" id="vTotal">R$ 0,00</div>
                        </div>
                    </footer>
                </section>

                <!-- Coluna direita: destaque e acoes finais -->
                <aside class="glass p-3 d-flex flex-column">
                    <div class="mb-3">
                        <div class="field-label mb-2">Produto em destaque</div>
                        <div id="infoProdutoAtual"
                            class="product-photo d-flex align-items-center justify-content-center">
                            <div class="text-secondary small">Nenhum produto selecionado.</div>
                        </div>
                    </div>

                    <div class="panel-soft p-3">
                        <div class="field-label mb-2">Acoes da venda</div>
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-secondary" type="button" id="btnAbrirResumo">Resumo por
                                forma</button>
                            <button class="btn btn-outline-secondary" type="button" id="btnImprimirPrevia"
                                disabled>Pre-visualizar cupom</button>
                            <a class="btn btn-payments btn-primary" id="btnPagamentos"
                                href="<?= \App\Core\Url::to('/pdv/pagamentos') ?>"
                                data-href="<?= \App\Core\Url::to('/pdv/pagamentos') ?>">
                                Ir para pagamentos <span class="kbd">F9</span>
                            </a>
                        </div>
                    </div>
                </aside>
            </div>
        </main>
    </div>

    <!-- Modal desconto (inalterado para compatibilidade) -->
    <div class="modal fade" id="modalDesconto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content pdv-modal">
                <div class="modal-header">
                    <h5 class="modal-title">Desconto na venda</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Fechar"></button>
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

    <!-- Modal abrir caixa -->
    <div class="modal fade" id="modalAbrirCaixa" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content pdv-modal" id="formAbrirCaixa">
                <div class="modal-header">
                    <h5 class="modal-title">Abrir caixa</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Troco inicial (R$)</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="abrirTroco" value="100.00">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Terminal</label>
                        <input type="number" min="1" class="form-control" id="abrirTerminal"
                            value="<?= (int)$terminalId ?>">
                        <div class="form-text">Ex.: 1 para “Caixa 01”.</div>
                    </div>
                    <input type="hidden" id="abrirOperador" value="<?= (int)$operadorId ?>">
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancelar</button>
                    <button class="btn btn-primary" type="submit">Abrir</button>
                </div>
            </form>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="<?= \App\Core\Url::to('/assets/site/pdv/pdv.js') ?>" defer></script>
</body>

</html>