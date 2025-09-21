<?php

use App\Core\Url;

$pedido = $pedido ?? [];
$itens = $itens ?? [];
$statusInfo = $statusInfo ?? ['class' => 'bg-secondary text-white', 'label' => 'Status'];
$cliente = $cliente ?? [];
$entregaLinhas = $entregaLinhas ?? [];
$empresa = $empresa ?? [];

$formatMoney = static function (float $value): string {
    return 'R$ ' . number_format($value, 2, ',', '.');
};

$fmtQty = static function (float $qty): string {
    $txt = number_format($qty, 3, ',', '.');
    return rtrim(rtrim($txt, '0'), ',');
};

$codigo = $pedido['codigo'] ?? null;
$pedidoNumero = $codigo !== null && $codigo !== ''
    ? '<code>' . htmlspecialchars((string) $codigo, ENT_QUOTES, 'UTF-8') . '</code>'
    : '#' . (int)($pedido['id'] ?? 0);

$dataPedido = isset($pedido['criado_em']) ? date('d/m/Y H:i', strtotime((string) $pedido['criado_em'])) : '';
$pagamento = $pedido['pagamento'] ?? '-';

$clienteNome = htmlspecialchars((string)($cliente['nome'] ?? 'Cliente'), ENT_QUOTES, 'UTF-8');
$clienteEmail = (string)($cliente['email'] ?? '');
$clienteTelefone = (string)($cliente['telefone'] ?? '');

$empresaNome = htmlspecialchars((string)($empresa['nome'] ?? 'Mercadinho Borba Gato'), ENT_QUOTES, 'UTF-8');
$empresaEndereco = htmlspecialchars((string)($empresa['endereco'] ?? 'R. das Tipuanas, 250 - Maringa/PR'), ENT_QUOTES, 'UTF-8');
$empresaTelefone = (string)($empresa['telefone'] ?? '');
$empresaEmail = (string)($empresa['email'] ?? '');

?>
<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Nota do Pedido <?= htmlspecialchars((string)($pedido['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="<?= \App\Core\Url::to('/assets/css/bootstrap.min.css') ?>" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
    body {
        background: #f5f5f5;
        font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    }

    .invoice {
        max-width: 920px;
        margin: 32px auto;
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 15px 45px rgba(15, 23, 42, 0.10);
        padding: 32px;
    }

    .invoice h1 {
        font-size: 1.75rem;
    }

    .invoice small {
        color: #6c757d;
    }

    .badge-status {
        font-size: 0.875rem;
        padding: 0.45rem 0.85rem;
    }

    .table-items thead th {
        background: #f1f3f5;
    }

    .summary-box {
        border: 1px solid #e9ecef;
        border-radius: 10px;
        padding: 18px 20px;
        background: #fafafa;
    }

    @media print {
        body {
            background: #ffffff;
        }

        .invoice {
            box-shadow: none;
            border-radius: 0;
            margin: 0;
        }

        .no-print {
            display: none !important;
        }
    }
    </style>
</head>

<body>
    <div class="invoice">
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <h1 class="mb-1"><?= $empresaNome ?></h1>
                <div class="small mb-1"><?= $empresaEndereco ?></div>
                <?php if ($empresaTelefone !== ''): ?>
                <div class="small">Telefone: <?= htmlspecialchars($empresaTelefone, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
                <?php if ($empresaEmail !== ''): ?>
                <div class="small">Email: <?= htmlspecialchars($empresaEmail, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
            </div>
            <div class="text-end">
                <span
                    class="badge badge-status <?= htmlspecialchars((string)$statusInfo['class'], ENT_QUOTES, 'UTF-8') ?>">
                    <?= htmlspecialchars((string)$statusInfo['label'], ENT_QUOTES, 'UTF-8') ?>
                </span>
                <div class="fw-semibold mt-2">Pedido <?= $pedidoNumero ?></div>
                <?php if ($dataPedido !== ''): ?>
                <div class="small">Emitido em <?= htmlspecialchars($dataPedido, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="d-flex gap-2 mb-4 no-print">
            <a href="<?= Url::to('/conta/pedidos') ?>" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
            <button type="button" class="btn btn-danger btn-sm" onclick="window.print()">
                <i class="bi bi-printer"></i> Imprimir
            </button>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <h2 class="h6 text-uppercase text-muted">Cliente</h2>
                <div class="fw-semibold"><?= $clienteNome ?></div>
                <?php if ($clienteEmail !== ''): ?>
                <div class="small">Email: <?= htmlspecialchars($clienteEmail, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
                <?php if ($clienteTelefone !== ''): ?>
                <div class="small">Telefone: <?= htmlspecialchars($clienteTelefone, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <h2 class="h6 text-uppercase text-muted">Entrega</h2>
                <?php if (!empty($entregaLinhas)): ?>
                <?php foreach ($entregaLinhas as $linha): ?>
                <div class="small"><?= htmlspecialchars((string)$linha, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endforeach; ?>
                <?php else: ?>
                <div class="small text-muted">Sem endereco de entrega registrado.</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="summary-box mb-4">
            <div class="row g-3 align-items-center">
                <div class="col-sm">
                    <div class="text-uppercase text-muted small">Pagamento</div>
                    <div class="fw-semibold"><?= htmlspecialchars((string)$pagamento, ENT_QUOTES, 'UTF-8') ?></div>
                </div>
                <div class="col-sm">
                    <div class="text-uppercase text-muted small">Subtotal</div>
                    <div class="fw-semibold"><?= $formatMoney((float)($pedido['subtotal'] ?? 0)) ?></div>
                </div>
                <div class="col-sm">
                    <div class="text-uppercase text-muted small">Frete</div>
                    <div class="fw-semibold"><?= $formatMoney((float)($pedido['frete'] ?? 0)) ?></div>
                </div>
                <div class="col-sm">
                    <div class="text-uppercase text-muted small">Desconto</div>
                    <div class="fw-semibold"><?= $formatMoney((float)($pedido['desconto'] ?? 0)) ?></div>
                </div>
                <div class="col-sm">
                    <div class="text-uppercase text-muted small text-danger">Total</div>
                    <div class="fw-bold fs-5 text-danger"><?= $formatMoney((float)($pedido['total'] ?? 0)) ?></div>
                </div>
            </div>
        </div>

        <div class="table-responsive mb-4">
            <table class="table table-items table-sm align-middle">
                <thead>
                    <tr>
                        <th style="width:80px;">ID</th>
                        <th>Produto</th>
                        <th class="text-end" style="width:90px;">Qtd</th>
                        <th class="text-end" style="width:120px;">Preço</th>
                        <th class="text-end" style="width:140px;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($itens)): ?>
                    <?php foreach ($itens as $item): ?>
                    <tr>
                        <td><?= (int)($item['produto_id'] ?? 0) ?></td>
                        <td><?= htmlspecialchars((string)($item['nome'] ?? 'Item'), ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="text-end"><?= $fmtQty((float)($item['quantidade'] ?? 0)) ?></td>
                        <td class="text-end"><?= $formatMoney((float)($item['preco'] ?? 0)) ?></td>
                        <td class="text-end"><?= $formatMoney((float)($item['subtotal'] ?? 0)) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">Nenhum item neste pedido.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="small text-muted">
            Esta nota serve como comprovante simplificado do pedido. Para mais detalhes acesse a area "Meus Pedidos" no
            site.
        </div>
    </div>

    <script src="<?= \App\Core\Url::to('/assets/js/bootstrap.bundle.min.js') ?>"></script>
</body>

</html>