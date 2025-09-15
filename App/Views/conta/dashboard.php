<?php

use App\Core\Url;
use App\Core\Auth;

/** @var array{id:int,nome:string,email:string,perfil:string,ativo:int}|null $user */
$user = $user ?? Auth::user();
$nome = htmlspecialchars($user['nome'] ?? 'Cliente');

$totalPedidos = (int)($totalPedidos ?? 0);
$qtdEnderecos = (int)($qtdEnderecos ?? 0);
$cartCount    = (int)($cartCount ?? ($_SESSION['cart_count'] ?? 0));

function status_badge(string $s): string
{
  $map = ['pago' => 'success', 'enviado' => 'primary', 'pendente' => 'warning', 'cancelado' => 'secondary'];
  $variant = $map[strtolower($s)] ?? 'light';
  return "<span class=\"badge bg-{$variant} text-uppercase\">" . htmlspecialchars($s) . "</span>";
}
?>
<!doctype html>
<html lang="pt-br">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title><?= htmlspecialchars($title ?? 'Minha Conta') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <link rel="stylesheet" href="<?= Url::to('/assets/site/css/style.css') ?>" />
  <style>
    .sidebar-sticky {
      position: sticky;
      top: 1rem
    }
  </style>
</head>

<body>
  <div class="d-flex flex-column wrapper">

    <?php require dirname(__DIR__) . '/partials/navbar.php'; ?>

    <main class="flex-fill">
      <div class="container py-3">
        <div class="row g-3">
          <!-- Sidebar -->
          <div class="col-12 col-lg-3">
            <?php require dirname(__DIR__) . '/partials/conta-sidebar.php'; ?>
          </div>

          <!-- Conteúdo -->
          <div class="col-12 col-lg-9">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <h1 class="h4 mb-0">Olá, <?= $nome ?> 👋</h1>
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
                      href="<?= Url::to('/conta/pedidos') ?>">Ver todos os pedidos →</a>
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
                    <span class="text-muted">·</span>
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
                      o carrinho →</a>
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
                        <?php foreach ($ultimosPedidos as $p): ?>
                          <tr>
                            <td><?= (int)$p['id'] ?></td>
                            <td><code><?= htmlspecialchars($p['codigo']) ?></code></td>
                            <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($p['criado_em']))) ?>
                            </td>
                            <td><?= status_badge($p['status']) ?></td>
                            <td class="text-end">R$
                              <?= number_format((float)$p['total'], 2, ',', '.') ?></td>
                            <td class="text-end"><a
                                href="<?= Url::to('/conta/pedidos/ver') . '?id=' . (int)$p['id'] ?>"
                                class="btn btn-sm btn-outline-primary">Ver</a></td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                <?php else: ?>
                  <div class="p-4">
                    <p class="mb-2">Você ainda não realizou pedidos.</p>
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
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="<?= Url::to('/assets/site/js/script.js') ?>"></script>
</body>

</html>