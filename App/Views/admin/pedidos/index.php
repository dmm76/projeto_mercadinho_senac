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
  <link rel="stylesheet" href="<?= \App\Core\Url::to('/assets/css/bootstrap.min.css') ?>" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <link rel="stylesheet" href="<?= Url::to('/assets/site/css/style.css') ?>" />
  <style>
    .sidebar-sticky {
      position: sticky;
      top: 1rem;
    }

    .orders-actions {
      display: inline-flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: flex-end;
      gap: 0.35rem;
    }

    .orders-actions .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      white-space: nowrap;
      padding: 0.2rem 0.55rem;
      font-size: 0.8rem;
      line-height: 1.1;
    }

    .table-actions-cell {
      width: 1%;
      white-space: nowrap;
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
                  <a href="<?= $url ?>"
                    class="btn btn-outline-secondary<?= $ativo ? ' active' : '' ?>"><?= $label ?></a>
                <?php endforeach; ?>
              </div>
            </div>

            <?php if ($msg = Flash::get('success')): ?>
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $h($msg) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"
                  aria-label="Fechar"></button>
              </div>
            <?php endif; ?>
            <?php if ($msg = Flash::get('error')): ?>
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= $h($msg) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"
                  aria-label="Fechar"></button>
              </div>
            <?php endif; ?>

            <form class="row gy-2 gx-2 mb-3" method="get" action="<?= Url::to('/admin/pedidos') ?>">
              <input type="hidden" name="status" value="<?= $h($statusAtual) ?>">
              <div class="col-sm-12 col-md-5 col-lg-4">
                <input type="text" name="q" value="<?= $h($filters['q'] ?? '') ?>" class="form-control"
                  placeholder="Buscar por cliente, nº do pedido ou produto">
              </div>
              <div class="col-sm-6 col-md-3 col-lg-3">
                <input type="date" name="de" value="<?= $h($filters['de'] ?? '') ?>"
                  class="form-control">
              </div>
              <div class="col-sm-6 col-md-3 col-lg-3">
                <input type="date" name="ate" value="<?= $h($filters['ate'] ?? '') ?>"
                  class="form-control">
              </div>
              <div class="col-sm-12 col-md-2 col-lg-2 d-grid">
                <button class="btn btn-outline-secondary"><i class="bi bi-search me-1"></i>
                  Filtrar</button>
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
                      <th class="text-end table-actions-cell">Ações</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($pedidos as $pedido): ?>
                      <?php
                      $statusOriginal = trim((string)($pedido['status'] ?? 'pendente'));
                      $status = strtolower($statusOriginal);
                      $statusKey = strtolower(preg_replace('/[^a-z0-9]+/i', '_', $statusOriginal));
                      $badgeMap = [
                        'pendente' => 'bg-warning text-dark',
                        'pago' => 'bg-success text-white',
                        'enviado' => 'bg-primary text-white',
                        'em_transporte' => 'bg-info text-dark',
                        'transporte' => 'bg-info text-dark',
                        'em_preparo' => 'bg-info text-dark',
                        'preparando' => 'bg-info text-dark',
                        'em_andamento' => 'bg-info text-dark',
                        'pronto' => 'bg-secondary text-white',
                        'pronto_para_retirada' => 'bg-secondary text-white',
                        'entregue' => 'bg-success text-white',
                        'finalizado' => 'bg-success text-white',
                        'cancelado' => 'bg-danger text-white',
                      ];
                      $badgeClass = $badgeMap[$statusKey] ?? 'bg-secondary text-white';
                      $statusLabel = $statusOriginal !== '' ? $statusOriginal : 'Pendente';
                      if (function_exists('mb_convert_case')) {
                        $statusLabel = mb_convert_case($statusLabel, MB_CASE_TITLE, 'UTF-8');
                      } else {
                        $statusLabel = ucwords(strtolower($statusLabel));
                      }
                      ?>
                      <tr>
                        <td><?= (int)($pedido['id'] ?? 0) ?></td>
                        <td><?= $h($pedido['cliente'] ?? '-') ?></td>
                        <td><?= $h($pedido['pagamento'] ?? 'na entrega') ?></td>
                        <td>
                          <span
                            class="badge rounded-pill px-3 <?= $badgeClass ?>"><?= $h($statusLabel) ?></span>
                        </td>
                        <td class="text-end"><?= (int)($pedido['qtd_itens'] ?? 0) ?></td>
                        <td class="text-end">R$
                          <?= number_format((float)($pedido['total'] ?? 0), 2, ',', '.') ?></td>
                        <td class="text-end"><?= $h($pedido['criado_em'] ?? '-') ?></td>
                        <td class="text-end table-actions-cell">
                          <div class="orders-actions">
                            <a class="btn btn-outline-secondary btn-sm"
                              href="<?= Url::to('/admin/pedidos/' . (int)($pedido['id'] ?? 0)) ?>">Ver</a>
                            <?php if ($status === 'pendente'): ?>
                              <a class="btn btn-outline-success btn-sm"
                                href="<?= Url::to('/admin/pedidos/' . (int)$pedido['id'] . '/marcar-pago') ?>">Marcar
                                pago</a>
                            <?php endif; ?>
                            <?php if ($status === 'pago'): ?>
                              <a class="btn btn-outline-primary btn-sm"
                                href="<?= Url::to('/admin/pedidos/' . (int)$pedido['id'] . '/enviar') ?>">Enviar</a>
                            <?php endif; ?>
                            <?php if ($status !== 'cancelado'): ?>
                              <a class="btn btn-outline-danger btn-sm"
                                href="<?= Url::to('/admin/pedidos/' . (int)$pedido['id'] . '/cancelar') ?>">Cancelar</a>
                            <?php endif; ?>
                          </div>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                    <?php if (empty($pedidos)): ?>
                      <tr>
                        <td colspan="8" class="text-center text-muted py-4">Nenhum pedido
                          encontrado.</td>
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

  <script src="<?= \App\Core\Url::to('/assets/js/bootstrap.bundle.min.js') ?>"></script>
</body>

</html>