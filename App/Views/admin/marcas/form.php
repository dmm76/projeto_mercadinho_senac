<?php
$isEdit = isset($marca) && $marca !== null;
$action = $isEdit ? \App\Core\Url::to('/admin/marcas/editar') : \App\Core\Url::to('/admin/marcas/criar');
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($title ?? 'Marca') ?></title>
  <link rel="stylesheet" href="<?= \App\Core\Url::to('/assets/css/bootstrap.min.css') ?>" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <link rel="stylesheet" href="<?= \App\Core\Url::to('/assets/site/css/style.css') ?>" />
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
        <div class="col-12 col-lg-3">
          <?php require __DIR__ . '/../../partials/admin-sidebar.php'; ?>
        </div>
        <div class="col-12 col-lg-9">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <h1 class="h4 mb-0"><?= htmlspecialchars($title ?? 'Marca') ?></h1>
            <a class="btn btn-outline-secondary" href="<?= \App\Core\Url::to('/admin/marcas') ?>">Voltar</a>
          </div>
          <?php if ($m = \App\Core\Flash::get('error')): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($m) ?></div>
          <?php endif; ?>
          <form method="post" action="<?= $action ?>" class="card shadow-sm">
            <div class="card-body">
              <?= \App\Core\Csrf::input() ?>
              <?php if ($isEdit): ?><input type="hidden" name="id" value="<?= (int)$marca->id ?>"><?php endif; ?>
              <div class="mb-3">
                <label class="form-label" for="marca-nome">Nome</label>
                <input id="marca-nome" class="form-control" type="text" name="nome" required value="<?= $isEdit ? htmlspecialchars($marca->nome) : '' ?>">
              </div>
            </div>
            <div class="card-footer bg-white d-flex justify-content-between">
              <button class="btn btn-success" type="submit"><?= $isEdit ? 'Salvar' : 'Criar' ?></button>
              <a class="btn btn-secondary" href="<?= \App\Core\Url::to('/admin/marcas') ?>">Cancelar</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </main>
  <?php require __DIR__ . '/../../partials/footer.php'; ?>
</div>
<script src="<?= \App\Core\Url::to('/assets/js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= \App\Core\Url::to('/assets/site/js/script.js') ?>"></script>
</body>
</html>
