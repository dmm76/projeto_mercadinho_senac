<?php /** @var array{title?:string, metrics: array<string,int>, links: array<string,string>} $this */ ?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title><?= htmlspecialchars($title ?? 'Dashboard Admin') ?></title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"/>
  <link rel="stylesheet" href="<?= \App\Core\Url::to('/assets/site/css/style.css') ?>"/>

  <style>
    .sidebar-sticky { position: sticky; top: 1rem; }
  </style>
</head>
<body>
<div class="d-flex flex-column wrapper">

  <?php require __DIR__ . '/../../partials/navbar.php'; ?>

  <main class="flex-fill">
    <div class="container py-3">
      <div class="row g-3">
        <!-- Sidebar -->
        <div class="col-12 col-lg-3">
          <?php require __DIR__ . '/../../partials/admin-sidebar.php'; ?>
        </div>

        <!-- Conteúdo -->
        <div class="col-12 col-lg-9">
          <h1 class="h4 mb-4">Painel do Administrador</h1>

          <div class="row g-3 mb-4">
            <div class="col-sm-6 col-lg-3">
              <div class="card shadow-sm h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                  <div>
                    <div class="text-muted small">Produtos</div>
                    <div class="fs-4 fw-bold"><?= (int)($metrics['produtos'] ?? 0) ?></div>
                  </div>
                  <i class="bi bi-box fs-1 text-secondary"></i>
                </div>
              </div>
            </div>

            <div class="col-sm-6 col-lg-3">
              <div class="card shadow-sm h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                  <div>
                    <div class="text-muted small">Estoque baixo</div>
                    <div class="fs-4 fw-bold"><?= (int)($metrics['estoque_baixo'] ?? 0) ?></div>
                  </div>
                  <i class="bi bi-exclamation-triangle fs-1 text-warning"></i>
                </div>
              </div>
            </div>
          </div>

          <h2 class="h5 mb-3">Atalhos</h2>
          <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-danger" href="<?= $links['produtos']   ?? '#' ?>">Produtos</a>
            <a class="btn btn-outline-secondary" href="<?= $links['categorias'] ?? '#' ?>">Categorias</a>
            <a class="btn btn-outline-secondary" href="<?= $links['marcas']     ?? '#' ?>">Marcas</a>
            <a class="btn btn-outline-secondary" href="<?= $links['unidades']   ?? '#' ?>">Unidades</a>
          </div>
        </div>
      </div>
    </div>
  </main>

  <?php require __DIR__ . '/../../partials/footer.php'; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= \App\Core\Url::to('/assets/site/js/script.js') ?>"></script>
</body>
</html>
