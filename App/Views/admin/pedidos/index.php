<?php
use App\Core\Url;
use App\Core\Flash;

/** @var array<int,array{id:int,cliente:string,pagamento:string,status:string,qtd_itens:int,total:float,criado_em:string}> $pedidos */
$pedidos = $pedidos ?? [];
$filters = $filters ?? ['status' => '', 'q' => '', 'de' => '', 'ate' => ''];
$h = static fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$statusAtual = strtolower((string)($filters['status'] ?? ''));
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title><?= $h($title ?? 'Pedidos') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <link rel="stylesheet" href="<?= Url::to('/assets/site/css/style.css') ?>" />
  <style>
    .sidebar-sticky { position: sticky; top: 1rem; }
  </style>
</head>
<body>
<div class="d-flex flex-column wrapper">
  <?php require dirname(__DIR__, 2) . '/partials/navbar.php'; ?>

  <main class="flex-fill">
    <div class="container py-3">
      <div class="row g-3">
        <div class="col-12 col-lg-3">
          <?php require dirname(__DIR__, 2) . '/partials/admin-sidebar.php'; ?>
        </div>

        <div class="col-12 col-lg-9">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <h1 class="h4 mb-0">Pedidos</h1>
            <div class="btn-group">
              <?php
              $abas = [
                '' => 'Todos',
                'pendente' => 'Pendentes',
                'pago' => 'Pagos',
                'enviado' => 'Enviados',
                'cancelado' => 'Cancelados',
              ];
              foreach ($abas as $valor => $label):
                $url = Url::to('/admin/pedidos' . ($valor !== '' ? ('?status=' . $valor) : ''));
                $ativo = $statusAtual === $valor;
              ?>
                <a href="<?= $url ?>" class="btn btn-outline-secondary<?= $ativo ? ' active' : '' ?>"><?= $label ?></a>
              <?php endforeach; ?>
            </div>
          </div>

          <?php if ($msg = Flash::get('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <?= $h($msg) ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
          <?php endif; ?>
          <?php if ($msg = Flash::get('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <?= $h($msg) ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
          <?php endif; ?>

          <form class="row gy-2 gx-2 mb-3" method="get" action="<?= Url::to('/admin/pedidos') ?>">
            <input type="hidden" name="status" value="<?= $h($statusAtual) ?>">
            <div class="col-sm-12 col-md-5 col-lg-4">
              <input type="text" name="q" value="<?= $h($filters['q'] ?? '') ?>" class="form-control" placeholder="Buscar por cliente, nº do pedido ou produto">
            </div>
            <div class="col-sm-6 col-md-3 col-lg-3">
              <input type="date" name="de" value="<?= $h($filters['de'] ?? '') ?>" class="form-control">
            </div>
            <div class="col-sm-6 col-md-3 col-lg-3">
              <input type="date" name="ate" value="<?= $h($filters['ate'] ?? '') ?>" class="form-control">
            </div>
            <div class="col-sm-12 col-md-2 col-lg-2 d-grid">
              <button class="btn btn-outline-secondary"><i class="bi bi-search me-1"></i> Filtrar</button>
            </div>
          </form>

          <div class="card shadow-sm">
            <div class="table-responsive">
              <table class="table align-middle table-hover mb-0">
                <thead class="table-light">
                <tr>
                  <th>#</th>
                  <th>Cliente</th>
                  <th>Pagamento</th>
                  <th>Status</th>
                  <th class="text-end">Itens</th>
                  <th class="text-end">Total</th>
                  <th class="text-end">Criado em</th>
                  <th class="text-end">Ações</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($pedidos as $pedido): ?>
                  <?php $status = strtolower((string)($pedido['status'] ?? 'pendente')); ?>
                  <tr>
                    <td><?= (int)($pedido['id'] ?? 0) ?></td>
                    <td><?= $h($pedido['cliente'] ?? '-') ?></td>
                    <td><?= $h($pedido['pagamento'] ?? 'na entrega') ?></td>
                    <td>
                      <?php
                        $badge = match ($status) {
                          'pago' => 'text-bg-success',
                          'enviado' => 'text-bg-primary',
                          'cancelado' => 'text-bg-danger',
                          default => 'text-bg-warning'
                        };
                      ?>
                      <span class="badge <?= $badge ?>"><?= $h(ucfirst($status)) ?></span>
                    </td>
                    <td class="text-end"><?= (int)($pedido['qtd_itens'] ?? 0) ?></td>
                    <td class="text-end">R$ <?= number_format((float)($pedido['total'] ?? 0), 2, ',', '.') ?></td>
                    <td class="text-end"><?= $h($pedido['criado_em'] ?? '-') ?></td>
                    <td class="text-end">
                      <div class="btn-group btn-group-sm">
                        <a class="btn btn-outline-secondary" href="<?= Url::to('/admin/pedidos/' . (int)($pedido['id'] ?? 0)) ?>">Ver</a>
                        <?php if ($status === 'pendente'): ?>
                          <a class="btn btn-outline-success" href="<?= Url::to('/admin/pedidos/' . (int)$pedido['id'] . '/marcar-pago') ?>">Marcar pago</a>
                        <?php endif; ?>
                        <?php if ($status === 'pago'): ?>
                          <a class="btn btn-outline-primary" href="<?= Url::to('/admin/pedidos/' . (int)$pedido['id'] . '/enviar') ?>">Enviar</a>
                        <?php endif; ?>
                        <?php if ($status !== 'cancelado'): ?>
                          <a class="btn btn-outline-danger" href="<?= Url::to('/admin/pedidos/' . (int)$pedido['id'] . '/cancelar') ?>">Cancelar</a>
                        <?php endif; ?>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
                <?php if (empty($pedidos)): ?>
                  <tr>
                    <td colspan="8" class="text-center text-muted py-4">Nenhum pedido encontrado.</td>
                  </tr>
                <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <?php require dirname(__DIR__, 2) . '/partials/footer.php'; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


