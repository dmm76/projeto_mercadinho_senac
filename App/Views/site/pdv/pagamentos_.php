<?php

/** @var array $usuario autenticado, se o layout ja injeta isso */
$usuario = (isset($usuario) && is_array($usuario)) ? $usuario : [];
$title = 'PDV - Pagamentos';

$operadorId = isset($usuario['id']) ? (int) $usuario['id'] : 1;
$operadorNome = isset($usuario['nome']) ? (string) $usuario['nome'] : 'Operador';
$clienteId = isset($usuario['cliente_id']) ? (int) $usuario['cliente_id'] : 1;
$terminalId = 1;
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

<body class="pdv-body pdv-pagamentos" data-view="pagamentos" data-operador-id="<?= $operadorId ?>"
    data-operador-nome="<?= htmlspecialchars($operadorNome, ENT_QUOTES, 'UTF-8') ?>" data-cliente-id="<?= $clienteId ?>"
    data-terminal-id="<?= $terminalId ?>"
    data-terminal-nome="<?= htmlspecialchars($terminalNome, ENT_QUOTES, 'UTF-8') ?>"
    data-url-venda="<?= \App\Core\Url::to('/pdv') ?>">

    <div class="pdv-wrap">
        <nav class="navbar pdv-navbar">
            <div class="container-fluid">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div class="nav-brand-title">Mercado Borba Gato <span class="nav-brand-sub">&bull; Pagamentos</span>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-3 text-end">
                        <span class="navbar-info">PDV <strong>#<?= $terminalId ?></strong></span>
                        <span class="navbar-info">Operador
                            <strong><?= htmlspecialchars($operadorNome) ?></strong></span>
                        <span class="navbar-info">Venda <strong id="pagamentoVendaId">-</strong></span>
                        <span class="status-pill status-andamento" id="statusVenda"
                            data-status="andamento">Recebendo</span>
                        <button class="btn btn-sm btn-outline-light" id="btnVoltarVenda" type="button">
                            Voltar para itens <span class="kbd">Esc</span>
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <main class="pdv-main container-fluid py-3 flex-grow-1">
            <div class="pagamentos-grid">
                <section class="panel painel-pagamentos-form">
                    <div class="panel-soft p-3">
                        <div class="panel-title-sm mb-3">Cadastrar pagamento</div>
                        <div class="row g-3 align-items-end">
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="pgTipo">Tipo</label>
                                <select class="form-select" id="pgTipo">
                                    <option value="dinheiro">Dinheiro</option>
                                    <option value="credito">Credito</option>
                                    <option value="debito">Debito</option>
                                    <option value="pix">PIX</option>
                                    <option value="cheque">Cheque</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label" for="pgValor">Valor</label>
                                <input type="number" step="0.01" min="0.01" class="form-control" id="pgValor"
                                    placeholder="0,00">
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary w-100" id="btnIncluirPagamento" type="button">Adicionar
                                    forma</button>
                            </div>
                        </div>
                    </div>

                    <div class="panel-soft p-3 mt-3 flex-grow-1">
                        <div class="panel-title-sm mb-2">Pagamentos adicionados</div>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped text-light" id="tabelaPagamentos">
                                <thead>
                                    <tr>
                                        <th>Tipo</th>
                                        <th class="text-end">Valor</th>
                                        <th style="width:70px"></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <ul class="pagamento-list mt-3" id="listaPagamentos">
                            <li class="text-secondary small">Nenhum pagamento adicionado.</li>
                        </ul>
                    </div>
                </section>

                <aside class="panel painel-pagamentos-resumo">
                    <div class="panel-soft p-3">
                        <div class="panel-title-sm mb-2">Totais por forma</div>
                        <div class="resumo-grid" id="resumoPorTipo">
                            <div class="mini-card">
                                <span class="label">Dinheiro</span>
                                <span class="value" id="totalDinheiro">R$ 0,00</span>
                            </div>
                            <div class="mini-card">
                                <span class="label">Credito</span>
                                <span class="value" id="totalCredito">R$ 0,00</span>
                            </div>
                            <div class="mini-card">
                                <span class="label">Debito</span>
                                <span class="value" id="totalDebito">R$ 0,00</span>
                            </div>
                            <div class="mini-card">
                                <span class="label">PIX</span>
                                <span class="value" id="totalPix">R$ 0,00</span>
                            </div>
                            <div class="mini-card">
                                <span class="label">Cheque</span>
                                <span class="value" id="totalCheque">R$ 0,00</span>
                            </div>
                        </div>
                    </div>

                    <div class="panel-soft p-3 mt-3">
                        <div class="panel-title-sm mb-2">Resumo financeiro</div>
                        <div class="pagamento-resumo">
                            <div class="resumo-linha">
                                <span>Total da venda</span>
                                <strong id="pgResumoTotal">R$ 0,00</strong>
                            </div>
                            <div class="resumo-linha">
                                <span>Recebido</span>
                                <strong id="pgResumoRecebido">R$ 0,00</strong>
                            </div>
                            <div class="resumo-linha">
                                <span>Falta receber</span>
                                <strong id="pgResumoFalta">R$ 0,00</strong>
                            </div>
                            <div class="resumo-linha">
                                <span>Troco previsto</span>
                                <strong id="pgResumoTroco">R$ 0,00</strong>
                            </div>
                            <div id="pgResumoMensagem" class="resumo-alerta mt-2" style="display:none"></div>
                        </div>
                    </div>

                    <div class="panel-soft p-3 mt-3">
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-light" id="btnPreviewResumo" type="button">Pré-visualizar
                                cupom</button>
                            <button class="btn btn-secondary" id="btnFinalizar" type="button" disabled>Finalizar venda
                                (F10)</button>
                        </div>
                    </div>
                </aside>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="<?= \App\Core\Url::to('/assets/site/pdv/pdv.js') ?>" defer></script>

</body>

</html>