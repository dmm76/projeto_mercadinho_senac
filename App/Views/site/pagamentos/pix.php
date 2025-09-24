<?php

/** @var array{id:int,codigo_externo:?string,total:float,status:string,criado_em:string} $pedido */
/** @var array<string,mixed> $payment */

$codigo = $pedido['codigo_externo'] ?? null;
$pedidoLabel = $codigo ?: '#' . ($pedido['id'] ?? '');
$total = (float)($pedido['total'] ?? 0);
$status = (string)($payment['status'] ?? 'pending');
$statusDetail = (string)($payment['status_detail'] ?? '');
$qrBase64 = $payment['qr_code_base64'] ?? null;
$qrText = $payment['qr_code'] ?? null;
$ticketUrl = $payment['ticket_url'] ?? null;
$expiresAt = $payment['date_of_expiration'] ?? null;

$statusDetailMap = [
    'pending_waiting_transfer' => 'Aguardando transferência',
    'pending_waiting_payment'  => 'Aguardando pagamento',
    'pending_contingency'      => 'Processando pagamento',
    'pending_review_manual'    => 'Em revisão manual',
    'approved'                 => 'Pagamento aprovado',
    'rejected_other_reason'    => 'Pagamento recusado',
];
$statusDetailLabel = '';
if ($statusDetail !== '') {
    $normalized = str_replace(['-', '_'], ' ', strtolower($statusDetail));
    $statusDetailLabel = $statusDetailMap[$statusDetail] ?? ucwords($normalized);
}

$h = static fn($value) => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>
<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title><?= $h($title ?? 'Pagamento PIX') ?></title>
    <link rel="stylesheet" href="<?= \App\Core\Url::to('/assets/css/bootstrap.min.css') ?>" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?= \App\Core\Url::to('/assets/site/css/style.css') ?>" />
    <style>
    .pix-card {
        max-width: 520px;
    }

    .pix-status {
        font-size: 0.95rem;
    }

    .qr-wrapper img {
        max-width: 320px;
        width: 100%;
        height: auto;
    }

    .code-box {
        word-break: break-all;
    }
    </style>
</head>

<body>
    <div class="d-flex flex-column wrapper" id="pix-container"
        data-status-url="<?= $h(\App\Core\Url::to('/pagamentos/pix/' . $pedido['id'] . '/status')) ?>"
        data-expires-at="<?= $expiresAt ? $h($expiresAt) : '' ?>">
        <?php require __DIR__ . '/../../partials/navbar.php'; ?>

        <main class="flex-fill">
            <div class="container py-4">
                <nav class="mb-3" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= \App\Core\Url::to('/conta/pedidos') ?>">Meus pedidos</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Pagamento PIX</li>
                    </ol>
                </nav>

                <div class="row g-4 justify-content-center">
                    <div class="col-12 col-lg-6">
                        <div class="card shadow pix-card mx-auto">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h1 class="h5 mb-1">Pedido <?= $h($pedidoLabel) ?></h1>
                                        <div class="text-muted small">Total: R$ <?= number_format($total, 2, ',', '.') ?></div>
                                    </div>
                                    <span class="badge bg-secondary" id="pix-status">Status: <?= $h($status) ?></span>
                                </div>

                                <p>Escaneie o QR code abaixo com o app do seu banco ou copie o código para concluir o
                                    pagamento via PIX.</p>

                                <div class="qr-wrapper text-center mb-3">
                                    <?php if ($qrBase64): ?>
                                        <img src="data:image/png;base64,<?= $h($qrBase64) ?>" alt="QR Code PIX" id="pix-qr" />
                                    <?php else: ?>
                                        <div class="alert alert-warning">Não foi possível carregar o QR code. Tente novamente em instantes.</div>
                                    <?php endif; ?>
                                </div>

                                <?php if ($ticketUrl): ?>
                                    <div class="mb-3 text-center">
                                        <a class="btn btn-outline-primary btn-sm" href="<?= $h($ticketUrl) ?>" target="_blank" rel="noopener">Abrir guia Mercado Pago</a>
                                    </div>
                                <?php endif; ?>

                                <div class="mb-3">
                                    <label class="form-label">Código copia e cola</label>
                                    <div class="input-group">
                                        <textarea class="form-control code-box" id="pix-code" rows="3" readonly><?= $qrText ? $h($qrText) : '' ?></textarea>
                                        <button class="btn btn-outline-secondary" type="button" id="btn-copy">
                                            <i class="bi bi-clipboard"></i>
                                            Copiar
                                        </button>
                                    </div>
                                </div>

                                <div class="pix-status mb-3" id="pix-countdown"></div>

                                <?php if ($statusDetailLabel !== ''): ?>
                                    <div class="alert alert-info" id="pix-status-detail">Detalhes: <?= $h($statusDetailLabel) ?></div>
                                <?php else: ?>
                                    <div class="alert alert-info d-none" id="pix-status-detail"></div>
                                <?php endif; ?>

                                <div class="small text-muted">Assim que o pagamento for confirmado, atualizaremos o status do seu pedido automaticamente.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <?php require __DIR__ . '/../../partials/footer.php'; ?>
    </div>

    <script src="<?= \App\Core\Url::to('/assets/js/bootstrap.bundle.min.js') ?>"></script>
    <script>
    (function () {
        const container = document.getElementById('pix-container');
        if (!container) return;

        const statusUrl = container.getAttribute('data-status-url');
        const statusEl = document.getElementById('pix-status');
        const statusDetailEl = document.getElementById('pix-status-detail');
        const countdownEl = document.getElementById('pix-countdown');
        const qrImg = document.getElementById('pix-qr');
        const codeEl = document.getElementById('pix-code');

        const statusDetailMap = {
            pending_waiting_transfer: 'Aguardando transferência',
            pending_waiting_payment: 'Aguardando pagamento',
            pending_contingency: 'Processando pagamento',
            pending_review_manual: 'Em revisão manual',
            approved: 'Pagamento aprovado',
            rejected_other_reason: 'Pagamento recusado'
        };
        const formatStatusDetail = (code) => {
            if (!code) {
                return '';
            }
            const label = statusDetailMap[code];
            if (label) {
                return label;
            }
            const normalized = code.replace(/[-_]/g, ' ');
            return normalized.replace(/\b\w/g, (char) => char.toUpperCase());
        };

        const formatDate = (value) => {
            if (!value) return null;
            const parsed = new Date(value);
            return Number.isNaN(parsed.getTime()) ? null : parsed;
        };

        const expiresAt = formatDate(container.getAttribute('data-expires-at'));

        const pad = (n) => n.toString().padStart(2, '0');

        const updateCountdown = () => {
            if (!expiresAt || !countdownEl) return;
            const diff = expiresAt.getTime() - Date.now();
            if (diff <= 0) {
                countdownEl.textContent = 'O QR Code expirou. Gere um novo pedido se necessário.';
                return;
            }
            const totalSeconds = Math.floor(diff / 1000);
            const minutes = Math.floor(totalSeconds / 60);
            const seconds = totalSeconds % 60;
            countdownEl.textContent = `Expira em ${pad(minutes)}:${pad(seconds)}`;
        };

        updateCountdown();
        if (expiresAt) {
            setInterval(updateCountdown, 1000);
        }

        const poll = () => {
            if (!statusUrl) return;
            fetch(statusUrl, { headers: { 'Accept': 'application/json' } })
                .then((response) => response.json())
                .then((data) => {
                    if (!data || data.error) return;
                    const payment = data.payment || {};
                    if (statusEl && payment.status) {
                        statusEl.textContent = 'Status: ' + payment.status;
                        if (payment.status === 'approved') {
                            statusEl.classList.remove('bg-secondary');
                            statusEl.classList.add('bg-success');
                        }
                    }
                    if (statusDetailEl) {
                        const detailLabel = formatStatusDetail(payment.status_detail);
                        if (detailLabel) {
                            statusDetailEl.textContent = 'Detalhes: ' + detailLabel;
                            statusDetailEl.classList.remove('d-none');
                        } else {
                            statusDetailEl.classList.add('d-none');
                            statusDetailEl.textContent = '';
                        }
                    }
                    if (payment.qr_code_base64 && qrImg) {
                        qrImg.setAttribute('src', 'data:image/png;base64,' + payment.qr_code_base64);
                    }
                    if (payment.qr_code && codeEl && !codeEl.value) {
                        codeEl.value = payment.qr_code;
                    }
                })
                .catch(() => {});
        };

        setInterval(poll, 15000);
        poll();

        const btnCopy = document.getElementById('btn-copy');
        if (btnCopy && codeEl) {
            btnCopy.addEventListener('click', async () => {
                const text = codeEl.value;
                if (!text) return;
                try {
                    await navigator.clipboard.writeText(text);
                    btnCopy.innerHTML = '<i class="bi bi-clipboard-check"></i> Copiado!';
                    setTimeout(() => {
                        btnCopy.innerHTML = '<i class="bi bi-clipboard"></i> Copiar';
                    }, 2000);
                } catch (err) {
                    console.error(err);
                }
            });
        }
    })();
    </script>
</body>

</html>
