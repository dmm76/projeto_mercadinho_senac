<?php

use App\Core\Url;

/** @var array<int,array{id:int,codigo:?string,status:string,total:float,criado_em:string}> $pedidos */
$pedidos = $pedidos ?? [];
$h = static fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$statusBadge = static function (string $status) use ($h): string {
    $key = strtolower(trim($status));
    $styles = [
        'pendente' => 'bg-warning text-dark',
        'aguardando_pagamento' => 'bg-warning text-dark',
        'aguardando' => 'bg-warning text-dark',
        'pago' => 'bg-success text-white',
        'enviado' => 'bg-primary text-white',
        'em_transporte' => 'bg-info text-dark',
        'em_preparo' => 'bg-info text-dark',
        'preparando' => 'bg-info text-dark',
        'pronto' => 'bg-secondary text-white',
        'entregue' => 'bg-success text-white',
        'finalizado' => 'bg-success text-white',
        'cancelado' => 'bg-danger text-white',
        'novo' => 'bg-secondary text-white',
    ];
    $classes = $styles[$key] ?? 'bg-secondary text-white';

    $label = $status !== '' ? $status : 'pendente';
    $label = str_replace(['_', '-'], ' ', $label);
    if (function_exists('mb_convert_case')) {
        $label = mb_convert_case($label, MB_CASE_TITLE, 'UTF-8');
    } else {
        $label = ucwords(strtolower($label));
    }

    return '<span class="badge rounded-pill px-3 ' . $h($classes) . '">' . $h($label) . '</span>';
};
?>
<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title><?= $h($title ?? 'Meus Pedidos') ?></title>
    <link rel="stylesheet" href="<?= \App\Core\Url::to('/assets/css/bootstrap.min.css') ?>" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?= Url::to('/assets/site/css/style.css') ?>" />
    <style>
    .sidebar-sticky {
        position: sticky;
        top: 1rem;
    }
    </style>
</head>

<body>
    <div class="d-flex flex-column wrapper">
        <?php require __DIR__ . '/../../partials/navbar.php'; ?>

        <main class="flex-fill">
            <div class="container py-3">
                <div class="row g-3">
                    <div class="col-12 col-lg-3">
                        <?php require __DIR__ . '/../../partials/conta-sidebar.php'; ?>
                    </div>

                    <div class="col-12 col-lg-9">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h1 class="h4 mb-0">Meus Pedidos</h1>
                        </div>

                        <div class="card shadow-sm">
                            <div class="card-body p-0">
                                <?php if (!empty($pedidos)): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Código</th>
                                                <th>Data</th>
                                                <th>Status</th>
                                                <th class="text-end">Total</th>
                                                <th class="text-end">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($pedidos as $pedido): ?>
                                            <tr>
                                                <td><?= (int)($pedido['id'] ?? 0) ?></td>
                                                <td><code><?= $h($pedido['codigo'] ?? '') ?></code></td>
                                                <td><?= $h(date('d/m/Y H:i', strtotime($pedido['criado_em']))) ?></td>
                                                <td><?= $statusBadge((string)($pedido['status'] ?? '')) ?></td>
                                                <td class="text-end">R$
                                                    <?= number_format((float)($pedido['total'] ?? 0), 2, ',', '.') ?>
                                                </td>
                                                <td class="text-end">
                                                    <a href="<?= Url::to('/conta/pedidos/ver') . '?id=' . (int)($pedido['id'] ?? 0) ?>"
                                                        class="btn btn-sm btn-outline-primary">Ver</a>
                                                    <a href="<?= Url::to('/conta/pedidos/nota') . '?id=' . (int)($pedido['id'] ?? 0) ?>"
                                                        class="btn btn-sm btn-outline-secondary" target="_blank"
                                                        rel="noopener">Nota</a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <?php if (!empty($paginacao)): ?>
                                <div class="p-3 border-top d-flex justify-content-between align-items-center">
                                    <?= $paginacao ?>
                                </div>
                                <?php endif; ?>
                                <?php else: ?>
                                <div class="p-4">
                                    <p class="mb-2">Você ainda não possui pedidos.</p>
                                    <a href="<?= Url::to('/') ?>" class="btn btn-primary btn-sm">Ver produtos</a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </main>

        <?php require __DIR__ . '/../../partials/footer.php'; ?>
    </div>

    <script src="<?= \App\Core\Url::to('/assets/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= Url::to('/assets/site/js/script.js') ?>"></script>
</body>

</html>