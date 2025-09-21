<?php
$isEdit = isset($categoria) && $categoria !== null;
$action = $isEdit ? \App\Core\Url::to('/admin/categorias/editar') : \App\Core\Url::to('/admin/categorias/criar');
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($title ?? 'Categoria') ?></title>
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
            <h1 class="h4 mb-0"><?= htmlspecialchars($title ?? 'Categoria') ?></h1>
            <a class="btn btn-outline-secondary" href="<?= \App\Core\Url::to('/admin/categorias') ?>">Voltar</a>
          </div>
          <?php if ($m = \App\Core\Flash::get('error')): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($m) ?></div>
          <?php endif; ?>
          <form method="post" action="<?= $action ?>" class="card shadow-sm">
            <div class="card-body">
              <?= \App\Core\Csrf::input() ?>
              <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?= (int)$categoria->id ?>">
              <?php endif; ?>
              <div class="mb-3">
                <label class="form-label" for="categoria-nome">Nome</label>
                <input id="categoria-nome" class="form-control" type="text" name="nome" required value="<?= $isEdit ? htmlspecialchars($categoria->nome) : '' ?>">
              </div>
              <div class="mb-3">
                <label class="form-label" for="categoria-slug">Slug</label>
                <input id="categoria-slug" class="form-control" type="text" name="slug" placeholder="auto se vazio" value="<?= $isEdit ? htmlspecialchars($categoria->slug) : '' ?>">
              </div>
              <div class="row g-2 mb-3">
                <div class="col-sm-6">
                  <label class="form-label" for="categoria-ordem">Ordem</label>
                  <input id="categoria-ordem" class="form-control" type="number" name="ordem" value="<?= $isEdit && $categoria->ordem !== null ? (int)$categoria->ordem : '' ?>">
                </div>
                <div class="col-sm-6 d-flex align-items-end">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="ativa" id="categoria-ativa" <?= $isEdit ? ($categoria->ativa ? 'checked' : '') : 'checked' ?>>
                    <label class="form-check-label" for="categoria-ativa">Ativa</label>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-footer bg-white d-flex justify-content-between">
              <button class="btn btn-success" type="submit"><?= $isEdit ? 'Salvar' : 'Criar' ?></button>
              <a class="btn btn-secondary" href="<?= \App\Core\Url::to('/admin/categorias') ?>">Cancelar</a>
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
