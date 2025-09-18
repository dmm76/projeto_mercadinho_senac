<?php

use App\Core\Url;
use App\Core\Auth;

/** @var array{id:int,nome:string,email:string,perfil:string,ativo:int}|null $user */
$user = $user ?? Auth::user();
$h = static fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$nome = $h($user['nome'] ?? 'Cliente');

$totalPedidos = (int)($totalPedidos ?? 0);
$qtdEnderecos = (int)($qtdEnderecos ?? 0);
if (!isset($cartCount) || !is_numeric($cartCount)) {
  $cart = $_SESSION['carrinho'] ?? [];
  $cartCount = 0;
  if (is_array($cart)) {
    foreach ($cart as $item) {
      $qty = $item['quantidade'] ?? 1;
      $cartCount += is_numeric($qty) ? (int) $qty : 1;
    }
  }
}
$cartCount = max(0, (int) $cartCount);
/** @var array<int,array{id:int,codigo_externo?:?string,codigo?:?string,status:string,total:float,criado_em:string}> $ultimosPedidos */
$ultimosPedidos = $ultimosPedidos ?? [];

$statusBadge = static function (string $status) use ($h): string {
  $key = strtolower(trim($status));
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
  $label = $status !== '' ? $status : 'pendente';
  $label = str_replace(['_', '-'], ' ', $label);
  if (function_exists('mb_convert_case')) {
    $label = mb_convert_case($label, MB_CASE_TITLE, 'UTF-8');
  } else {
    $label = ucwords(strtolower($label));
  }
  return '<span class="badge rounded-pill ' . $h($classes) . '">' . $h($label) . '</span>';
};
?>
<!doctype html>
<html lang="pt-br">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title><?= $h($title ?? 'Minha Conta') ?></title>
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

    <?php require dirname(__DIR__) . '/partials/navbar.php'; ?>

    <main class="flex-fill">
      <div class="container py-3">
        <div class="row g-3">
          <div class="col-12 col-lg-3">
            <?php require dirname(__DIR__) . '/partials/conta-sidebar.php'; ?>
          </div>

          <div class="col-12 col-lg-9">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <h1 class="h4 mb-0">Olá, <?= $nome ?> ??</h1>
            </div>

            <div class="row g-3 mb-4">
              <div class="col-12 col-md-4">
                <div class="card h-100 shadow-sm">
                  <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                      <div>
                        <div class="text-muted small">Pedidos</div>
                        <div class="display-6"><?= $totalPedidos ?></div>
                      </div>
                      <i class="bi bi-bag" style="font-size:1.6rem"></i>
                    </div>
                  </div>
                  <div class="card-footer bg-transparent">
                    <a class="small text-decoration-none"
                      href="<?= Url::to('/conta/pedidos') ?>">Ver todos os pedidos ?</a>
                  </div>
                </div>
              </div>

              <div class="col-12 col-md-4">
                <div class="card h-100 shadow-sm">
                  <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                      <div>
                        <div class="text-muted small">Endereços</div>
                        <div class="display-6"><?= $qtdEnderecos ?></div>
                      </div>
                      <i class="bi bi-geo-alt" style="font-size:1.6rem"></i>
                    </div>
                  </div>
                  <div class="card-footer bg-transparent d-flex gap-3">
                    <a class="small text-decoration-none"
                      href="<?= Url::to('/conta/enderecos') ?>">Gerenciar endereços</a>
                    <span class="text-muted"></span>
                    <a class="small text-decoration-none" href="<?= Url::to('/conta/dados') ?>">Meus
                      dados</a>
                  </div>
                </div>
              </div>

              <div class="col-12 col-md-4">
                <div class="card h-100 shadow-sm">
                  <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                      <div>
                        <div class="text-muted small">Itens no carrinho</div>
                        <div class="display-6"><?= $cartCount ?></div>
                      </div>
                      <i class="bi bi-cart" style="font-size:1.6rem"></i>
                    </div>
                  </div>
                  <div class="card-footer bg-transparent">
                    <a class="small text-decoration-none" href="<?= Url::to('/carrinho') ?>">Ir para
                      o carrinho ?</a>
                  </div>
                </div>
              </div>
            </div>

            <div class="card shadow-sm">
              <div class="card-header bg-white"><strong>Últimos pedidos</strong></div>
              <div class="card-body p-0">
                <?php if (!empty($ultimosPedidos)): ?>
                  <div class="table-responsive">
                    <table class="table table-hover table-sm align-middle mb-0">
                      <thead class="table-light">
                        <tr>
                          <th>#</th>
                          <th>Código</th>
                          <th>Data</th>
                          <th>Status</th>
                          <th class="text-end">Total</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($ultimosPedidos as $pedido): ?>
                          <?php
                          $codigo = $pedido['codigo'] ?? $pedido['codigo_externo'] ?? '';
                          $data = isset($pedido['criado_em']) ? date('d/m/Y H:i', strtotime($pedido['criado_em'])) : '';
                          ?>
                          <tr>
                            <td><?= (int)($pedido['id'] ?? 0) ?></td>
                            <td><code><?= $h($codigo) ?></code></td>
                            <td><?= $h($data) ?></td>
                            <td><?= $statusBadge((string)($pedido['status'] ?? '')) ?></td>
                            <td class="text-end">R$
                              <?= number_format((float)($pedido['total'] ?? 0), 2, ',', '.') ?>
                            </td>
                            <td class="text-end">
                              <a href="<?= Url::to('/conta/pedidos/ver') . '?id=' . (int)($pedido['id'] ?? 0) ?>"
                                class="btn btn-sm btn-outline-primary">Ver</a>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                <?php else: ?>
                  <div class="p-4">
                    <p class="mb-2">Você ainda n�o realizou pedidos.</p>
                    <a href="<?= Url::to('/') ?>" class="btn btn-primary btn-sm">Começar a comprar</a>
                  </div>
                <?php endif; ?>
              </div>
            </div>

          </div>
        </div>
      </div>
    </main>

    <?php require dirname(__DIR__) . '/partials/footer.php'; ?>
  </div>

  <script src="<?= \App\Core\Url::to('/assets/js/bootstrap.bundle.min.js') ?>"></script>
  <script src="<?= Url::to('/assets/site/js/script.js') ?>"></script>
</body>

</html>