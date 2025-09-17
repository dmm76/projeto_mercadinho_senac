<?php
use App\Core\Url;
use App\Core\Flash;

/** @var array<int,array{id:int,nome:string,email:string,assunto:string,status:string,criada_em:string}> $mensagens */
$mensagens = $mensagens ?? [];
$filters = $filters ?? ['q' => '', 'status' => ''];
$h = static fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title><?= $h($title ?? 'Mensagens') ?></title>
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
            <h1 class="h4 mb-0">Mensagens de contato</h1>
            <a href="<?= Url::to('/admin/mensagens?status=aberta') ?>" class="btn btn-outline-danger">
              <i class="bi bi-inbox"></i> Abertas
            </a>
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

          <form class="row gy-2 gx-2 mb-3" method="get" action="<?= Url::to('/admin/mensagens') ?>">
            <div class="col-sm-6 col-md-5">
              <input type="text" name="q" value="<?= $h($filters['q'] ?? '') ?>" class="form-control" placeholder="Buscar por cliente, e-mail ou mensagem">
            </div>
            <div class="col-sm-4 col-md-3">
              <?php $status = $filters['status'] ?? ''; ?>
              <select name="status" class="form-select">
                <option value=""<?= $status === '' ? ' selected' : '' ?>>Todos os status</option>
                <option value="aberta"<?= $status === 'aberta' ? ' selected' : '' ?>>Aberta</option>
                <option value="respondida"<?= $status === 'respondida' ? ' selected' : '' ?>>Respondida</option>
                <option value="arquivada"<?= $status === 'arquivada' ? ' selected' : '' ?>>Arquivada</option>
              </select>
            </div>
            <div class="col-sm-2 d-grid">
              <button class="btn btn-outline-secondary"><i class="bi bi-search me-1"></i> Filtrar</button>
            </div>
          </form>

          <div class="card shadow-sm">
            <div class="table-responsive">
              <table class="table align-middle table-hover mb-0">
                <thead class="table-light">
                <tr>
                  <th>ID</th>
                  <th>Cliente</th>
                  <th>E-mail</th>
                  <th>Mensagem</th>
                  <th>Status</th>
                  <th class="text-end">Recebida em</th>
                  <th class="text-end">Ações</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($mensagens as $mensagem): ?>
                  <?php $status = $mensagem['status'] ?? 'aberta'; ?>
                  <tr>
                    <td><?= (int)($mensagem['id'] ?? 0) ?></td>
                    <td><?= $h($mensagem['nome'] ?? '') ?></td>
                    <td><?= $h($mensagem['email'] ?? '') ?></td>
                    <td><?= $h($mensagem['assunto_preview'] ?? '') ?></td>
                    <td>
                      <span class="badge <?= $status === 'aberta' ? 'text-bg-warning' : ($status === 'respondida' ? 'text-bg-success' : 'text-bg-secondary') ?>">
                        <?= $h(ucfirst($status)) ?>
                      </span>
                    </td>
                    <td class="text-end"><?= $h($mensagem['criada_em'] ?? '-') ?></td>
                    <td class="text-end">
                      <div class="btn-group btn-group-sm">
                        <a class="btn btn-outline-secondary" href="<?= Url::to('/admin/mensagens/' . (int)($mensagem['id'] ?? 0)) ?>">Ver</a>
                        <?php if ($status !== 'respondida'): ?>
                          <a class="btn btn-outline-primary" href="<?= Url::to('/admin/mensagens/' . (int)$mensagem['id'] . '/responder') ?>">Responder</a>
                        <?php endif; ?>
                        <?php if ($status !== 'arquivada'): ?>
                          <a class="btn btn-outline-dark" href="<?= Url::to('/admin/mensagens/' . (int)$mensagem['id'] . '/arquivar') ?>">Arquivar</a>
                        <?php endif; ?>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
                <?php if (empty($mensagens)): ?>
                  <tr>
                    <td colspan="7" class="text-center text-muted py-4">Nenhuma mensagem encontrada.</td>
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








