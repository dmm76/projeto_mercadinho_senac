<?php

use App\Core\Url;
use App\Core\Flash;

/** @var array<string,mixed> $pedido */
/** @var array<int,array<string,mixed>> $itens */

$pedido = $pedido ?? [];
$itens = $itens ?? [];

$h = static fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$status = strtolower((string)($pedido['status'] ?? 'pendente'));
$id = (int)($pedido['id'] ?? 0);

$canMarcarPago = $status === 'pendente';
$canEnviar = $status === 'pago';
$canCancelar = $status !== 'cancelado';

function badge_for_status(string $status): string
{
    $map = [
        'pendente' => 'warning',
        'pago' => 'success',
        'enviado' => 'primary',
        'cancelado' => 'secondary',
    ];
    $key = strtolower($status);
    $variant = $map[$key] ?? 'light';
    $label = htmlspecialchars(ucfirst($key), ENT_QUOTES, 'UTF-8');
    return "<span class=\"badge bg-{$variant}\">{$label}</span>";
}
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= $h($title ?? 'Pedido') ?></title>
  <link rel="stylesheet" href="<?= \App\Core\Url::to('/assets/css/bootstrap.min.css') ?>" />
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
            <div>
              <h1 class="h4 mb-0">Pedido <?= $pedido['codigo_externo'] ? '<code>' . $h($pedido['codigo_externo']) . '</code>' : '#' . $id ?></h1>
              <div class="text-muted small">Criado em <?= $h($pedido['criado_em'] ?? '-') ?></div>
            </div>
            <a href="<?= Url::to('/admin/pedidos') ?>" class="btn btn-outline-secondary btn-sm">Voltar</a>
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

          <div class="card shadow-sm mb-3">
            <div class="card-body">
              <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center">
                <div>
                  <div><strong>Status:</strong> <?= badge_for_status($status) ?></div>
                  <div><strong>Pagamento:</strong> <?= $h($pedido['pagamento'] ?? '-') ?></div>
                  <div><strong>Entrega:</strong> <?= $h($pedido['entrega'] ?? '-') ?></div>
                </div>
                <div class="btn-group">
                  <a class="btn btn-outline-secondary btn-sm" href="<?= Url::to('/admin/pedidos') ?>">Listar</a>
                  <?php if ($canMarcarPago): ?>
                    <a class="btn btn-outline-success btn-sm" href="<?= Url::to('/admin/pedidos/' . $id . '/marcar-pago') ?>">Marcar pago</a>
                  <?php endif; ?>
                  <?php if ($canEnviar): ?>
                    <a class="btn btn-outline-primary btn-sm" href="<?= Url::to('/admin/pedidos/' . $id . '/enviar') ?>">Enviar</a>
                  <?php endif; ?>
                  <?php if ($canCancelar): ?>
                    <a class="btn btn-outline-danger btn-sm" href="<?= Url::to('/admin/pedidos/' . $id . '/cancelar') ?>" onclick="return confirm('Confirmar cancelamento?');">Cancelar</a>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <div class="card shadow-sm h-100">
                <div class="card-header bg-white"><strong>Resumo do pedido</strong></div>
                <div class="card-body">
                  <div class="d-flex justify-content-between"><span>Subtotal</span><span>R$ <?= number_format((float)($pedido['subtotal'] ?? 0), 2, ',', '.') ?></span></div>
                  <div class="d-flex justify-content-between"><span>Frete</span><span>R$ <?= number_format((float)($pedido['frete'] ?? 0), 2, ',', '.') ?></span></div>
                  <div class="d-flex justify-content-between"><span>Desconto</span><span>R$ <?= number_format((float)($pedido['desconto'] ?? 0), 2, ',', '.') ?></span></div>
                  <hr class="my-2">
                  <div class="d-flex justify-content-between fw-semibold"><span>Total</span><span>R$ <?= number_format((float)($pedido['total'] ?? 0), 2, ',', '.') ?></span></div>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="card shadow-sm h-100">
                <div class="card-header bg-white"><strong>Cliente</strong></div>
                <div class="card-body">
                  <div><strong>Nome:</strong> <?= $h($pedido['cliente_nome'] ?? 'Nao informado') ?></div>
                  <div><strong>Email:</strong> <?= $h($pedido['cliente_email'] ?? '-') ?></div>
                  <div><strong>Telefone:</strong> <?= $h($pedido['cliente_telefone'] ?? '-') ?></div>
                </div>
              </div>
            </div>
          </div>

          <div class="card shadow-sm mb-3">
            <div class="card-header bg-white"><strong>Endereco de entrega</strong></div>
            <div class="card-body">
              <?php if (!empty($pedido['logradouro'])): ?>
                <?php if (!empty($pedido['endereco_rotulo'])): ?>
                  <div><?= $h($pedido['endereco_rotulo']) ?></div>
                <?php endif; ?>
                <div><?= $h(($pedido['logradouro'] ?? '') . ', ' . ($pedido['numero'] ?? 's/n')) ?></div>
                <div><?= $h(($pedido['bairro'] ?? '-') . ' - ' . ($pedido['cidade'] ?? '-') . '/' . ($pedido['uf'] ?? '-')) ?></div>
                <div>CEP: <?= $h($pedido['cep'] ?? '-') ?></div>
              <?php else: ?>
                <div>Sem endereco vinculado (retirada ou dado removido).</div>
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
                    <?php foreach ($itens as $item): ?>
                      <tr>
                        <td><?= (int)($item['produto_id'] ?? 0) ?></td>
                        <td><?= $h($item['nome'] ?? '-') ?></td>
                        <td class="text-end"><?= number_format((float)($item['quantidade'] ?? 0), 0, '', '') ?></td>
                        <td class="text-end">R$ <?= number_format((float)($item['preco'] ?? 0), 2, ',', '.') ?></td>
                        <td class="text-end">R$ <?= number_format((float)($item['subtotal'] ?? 0), 2, ',', '.') ?></td>
                      </tr>
                    <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              <?php else: ?>
                <div class="p-3 text-muted">Nenhum item registrado neste pedido.</div>
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
</body>
</html>

