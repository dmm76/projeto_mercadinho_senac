<?php

use App\Core\Url;

/** @var array{id:int,codigo_externo:?string,status:string,entrega:string,pagamento:string,subtotal:float,frete:float,desconto:float,total:float,criado_em:string} $pedido */
/** @var array<int,array{id:int,produto_id:int,nome:string,quantidade:float,preco:float,subtotal:float}> $itens */
/** @var array<string,mixed>|null $pix */

$pedido = $pedido ?? [];
$itens  = $itens ?? [];
$pix    = $pix ?? null;

$pagamento = (string)($pedido['pagamento'] ?? 'na_entrega');
$pagamentoLabels = [
    'na_entrega' => 'Dinheiro na entrega',
    'pix'        => 'PIX (Mercado Pago)',
    'cartao'     => 'Cartao na entrega',
    'gateway'    => 'Pagamento online',
];
$pagamentoLabel = $pagamentoLabels[$pagamento] ?? ucwords(str_replace('_', ' ', $pagamento));

$pixStatusMap = [
    'pending'               => 'Pendente',
    'pending_waiting_transfer' => 'Aguardando transferencia',
    'pending_waiting_payment'  => 'Aguardando pagamento',
    'pending_contingency'      => 'Processando pagamento',
    'pending_review_manual'    => 'Em revisao manual',
    'approved'              => 'Aprovado',
    'authorized'            => 'Autorizado',
    'in_process'            => 'Em processo',
    'rejected'              => 'Rejeitado',
    'cancelled'             => 'Cancelado',
    'refunded'              => 'Estornado',
    'charged_back'          => 'Chargeback',
    'expired'               => 'Expirado',
];
$pixDetailMap = [
    'pending_waiting_transfer' => 'Aguardando transferencia',
    'pending_waiting_payment'  => 'Aguardando pagamento',
    'pending_contingency'      => 'Processando pagamento',
    'pending_review_manual'    => 'Em revisao manual',
    'accredited'               => 'Pagamento confirmado',
    'cc_rejected_other_reason' => 'Pagamento rejeitado',
    'rejected_other_reason'    => 'Pagamento rejeitado',
    'expired'                  => 'PIX expirado',
];

$pixStatusLabel = '';
$pixDetailLabel = '';
$pixExpiresLabel = '';
$pixQrBase64 = null;
$pixQrText = null;
$pixTicket = null;
if ($pix && is_array($pix)) {
    $pixStatus = (string)($pix['status'] ?? '');
    $pixDetail = (string)($pix['status_detail'] ?? '');
    if ($pixStatus !== '') {
        $pixStatusLabel = $pixStatusMap[$pixStatus] ?? ucwords(str_replace('_', ' ', $pixStatus));
    }
    if ($pixDetail !== '') {
        $pixDetailLabel = $pixDetailMap[$pixDetail] ?? ucwords(str_replace('_', ' ', $pixDetail));
    }
    if (!empty($pix['expires_at'])) {
        $ts = strtotime((string)$pix['expires_at']);
        if ($ts) {
            $pixExpiresLabel = date('d/m/Y H:i', $ts);
        }
    }
    $pixQrBase64 = $pix['qr_code_base64'] ?? null;
    $pixQrText   = $pix['qr_code'] ?? null;
    $pixTicket   = $pix['ticket_url'] ?? null;
}

function status_badge(string $s): string
{
    $key = strtolower(trim($s));
    $map = [
        'pendente' => 'bg-warning text-dark',
        'aguardando_pagamento' => 'bg-warning text-dark',
        'aguardando' => 'bg-warning text-dark',
        'pago' => 'bg-success text-white',
        'enviado' => 'bg-primary text-white',
        'em_transporte' => 'bg-info text-dark',
        'transporte' => 'bg-info text-dark',
        'em_preparo' => 'bg-info text-dark',
        'preparando' => 'bg-info text-dark',
        'em_andamento' => 'bg-info text-dark',
        'pronto' => 'bg-secondary text-white',
        'entregue' => 'bg-success text-white',
        'finalizado' => 'bg-success text-white',
        'cancelado' => 'bg-danger text-white',
        'novo' => 'bg-secondary text-white',
    ];
    $classes = $map[$key] ?? 'bg-secondary text-white';
    $label = $s !== '' ? $s : 'pendente';
    $label = str_replace(['_', '-'], ' ', $label);
    if (function_exists('mb_convert_case')) {
        $label = mb_convert_case($label, MB_CASE_TITLE, 'UTF-8');
    } else {
        $label = ucwords(strtolower($label));
    }
    return '<span class="badge rounded-pill ' . htmlspecialchars($classes, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</span>';
}
?>
<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Pedido #<?= (int)$pedido['id'] ?></title>
    <link rel="stylesheet" href="<?= \App\Core\Url::to('/assets/css/bootstrap.min.css') ?>" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?= Url::to('/assets/site/css/style.css') ?>" />
    <style>
    .sidebar-sticky {
        position: sticky;
        top: 1rem;
    }

    .pix-mini {
        max-width: 180px;
    }
    </style>
</head>

<body>
    <div class="d-flex flex-column wrapper">

        <?php require dirname(__DIR__, 2) . '/partials/navbar.php'; ?>

        <main class="flex-fill">
            <div class="container py-3">
                <div class="row g-3">
                    <div class="col-12 col-lg-3">
                        <?php require dirname(__DIR__, 2) . '/partials/conta-sidebar.php'; ?>
                    </div>

                    <div class="col-12 col-lg-9">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h1 class="h4 mb-0">
                                Pedido
                                <?= $pedido['codigo'] ? '<code>' . htmlspecialchars((string)$pedido['codigo'], ENT_QUOTES, 'UTF-8') . '</code>' : '#' . (int)$pedido['id'] ?>
                            </h1>
                            <a href="<?= Url::to('/conta/pedidos') ?>" class="btn btn-outline-secondary btn-sm">Voltar</a>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-12 col-lg-6">
                                <div class="card shadow-sm h-100">
                                    <div class="card-body">
                                        <div><strong>Status:</strong> <?= status_badge((string)($pedido['status'] ?? '')) ?></div>
                                        <div><strong>Data:</strong> <?= htmlspecialchars(date('d/m/Y H:i', strtotime((string)$pedido['criado_em'])), ENT_QUOTES, 'UTF-8') ?></div>
                                        <div><strong>Pagamento:</strong> <?= htmlspecialchars($pagamentoLabel, ENT_QUOTES, 'UTF-8') ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="card shadow-sm h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between"><span>Subtotal</span><span>R$ <?= number_format((float)$pedido['subtotal'], 2, ',', '.') ?></span></div>
                                        <div class="d-flex justify-content-between"><span>Frete</span><span>R$ <?= number_format((float)$pedido['frete'], 2, ',', '.') ?></span></div>
                                        <div class="d-flex justify-content-between"><span>Desconto</span><span>R$ <?= number_format((float)$pedido['desconto'], 2, ',', '.') ?></span></div>
                                        <hr class="my-2">
                                        <div class="d-flex justify-content-between fw-semibold"><span>Total</span><span>R$ <?= number_format((float)$pedido['total'], 2, ',', '.') ?></span></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow-sm mb-3">
                            <div class="card-header bg-white fw-semibold">Pagamento</div>
                            <div class="card-body">
                                <?php if ($pagamento === 'pix'): ?>
                                    <?php if ($pix): ?>
                                        <div class="row g-3 align-items-center">
                                            <div class="col-12 col-md-6">
                                                <div><strong>Status:</strong> <?= htmlspecialchars($pixStatusLabel ?: 'Desconhecido', ENT_QUOTES, 'UTF-8') ?></div>
                                                <?php if ($pixDetailLabel !== ''): ?>
                                                    <div><strong>Detalhe:</strong> <?= htmlspecialchars($pixDetailLabel, ENT_QUOTES, 'UTF-8') ?></div>
                                                <?php endif; ?>
                                                <?php if ($pixExpiresLabel !== ''): ?>
                                                    <div><strong>Expira em:</strong> <?= htmlspecialchars($pixExpiresLabel, ENT_QUOTES, 'UTF-8') ?></div>
                                                <?php endif; ?>
                                                <div class="mt-2 d-flex gap-2 flex-wrap">
                                                    <a class="btn btn-outline-danger btn-sm" href="<?= Url::to('/pagamentos/pix/' . (int)$pedido['id']) ?>">Abrir tela PIX</a>
                                                    <?php if ($pixTicket): ?>
                                                        <a class="btn btn-outline-secondary btn-sm" href="<?= htmlspecialchars((string)$pixTicket, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener">Abrir no Mercado Pago</a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <?php if ($pixQrBase64): ?>
                                                    <div class="mb-2">
                                                        <img class="pix-mini img-fluid border rounded" src="data:image/png;base64,<?= htmlspecialchars((string)$pixQrBase64, ENT_QUOTES, 'UTF-8') ?>" alt="QR PIX">
                                                    </div>
                                                <?php endif; ?>
                                                <?php if ($pixQrText): ?>
                                                    <div class="input-group input-group-sm">
                                                        <textarea class="form-control" id="pix-code-mini" rows="3" readonly><?= htmlspecialchars((string)$pixQrText, ENT_QUOTES, 'UTF-8') ?></textarea>
                                                        <button class="btn btn-outline-secondary" type="button" id="btn-copy-mini">Copiar</button>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info mb-3">Nenhum pagamento PIX foi gerado ainda para este pedido.</div>
                                        <a class="btn btn-danger" href="<?= Url::to('/pagamentos/pix/' . (int)$pedido['id']) ?>">Gerar PIX agora</a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div class="text-muted">Este pedido usa a forma "<?= htmlspecialchars($pagamentoLabel, ENT_QUOTES, 'UTF-8') ?>". Nenhuma acao online e necessaria no momento.</div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="card shadow-sm">
                            <div class="card-header bg-white"><strong>Itens</strong></div>
                            <div class="card-body p-0">
                                <?php if (!empty($itens)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Produto</th>
                                                    <th class="text-end">Qtd</th>
                                                    <th class="text-end">Preco</th>
                                                    <th class="text-end">Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($itens as $i): ?>
                                                    <tr>
                                                        <td><?= (int)$i['produto_id'] ?></td>
                                                        <td><?= htmlspecialchars((string)$i['nome'], ENT_QUOTES, 'UTF-8') ?></td>
                                                        <td class="text-end"><?= number_format((float)$i['quantidade'], 2, ',', '.') ?></td>
                                                        <td class="text-end">R$ <?= number_format((float)$i['preco'], 2, ',', '.') ?></td>
                                                        <td class="text-end">R$ <?= number_format((float)$i['subtotal'], 2, ',', '.') ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="p-3">Sem itens para este pedido.</div>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </main>

        <?php require dirname(__DIR__, 2) . '/partials/footer.php'; ?>

    </div>
    <script src="<?= \App\Core\Url::to('/assets/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= Url::to('/assets/site/js/script.js') ?>"></script>
    <script>
    (function () {
        const copyBtn = document.getElementById('btn-copy-mini');
        const textarea = document.getElementById('pix-code-mini');
        if (!copyBtn || !textarea) return;
        copyBtn.addEventListener('click', async () => {
            const text = textarea.value;
            if (!text) return;
            try {
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    await navigator.clipboard.writeText(text);
                } else {
                    textarea.select();
                    document.execCommand('copy');
                }
                copyBtn.textContent = 'Copiado!';
                setTimeout(() => {
                    copyBtn.textContent = 'Copiar';
                }, 2000);
            } catch (err) {
                console.error(err);
            }
        });
    })();
    </script>
</body>

</html>
