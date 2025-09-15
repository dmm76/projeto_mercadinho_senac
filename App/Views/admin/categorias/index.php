<?php /** @var array{title?:string} $this */ ?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title><?= htmlspecialchars($title ?? 'Categorias') ?></title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"/>
  <link rel="stylesheet" href="<?= \App\Core\Url::to('/assets/site/css/style.css') ?>"/>

  <style>.sidebar-sticky{position:sticky;top:1rem}</style>
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
          <div class="d-flex align-items-center justify-content-between mb-3">
            <h1 class="h4 mb-0">Categorias</h1>
            <a class="btn btn-danger" href="<?= \App\Core\Url::to('/admin/categorias/criar') ?>">
              <i class="bi bi-plus-lg me-1"></i> Nova Categoria
            </a>
          </div>

          <?php if ($m = \App\Core\Flash::get('success')): ?>
            <div class="alert alert-success"><?= htmlspecialchars($m) ?></div>
          <?php endif; ?>
          <?php if ($m = \App\Core\Flash::get('error')): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($m) ?></div>
          <?php endif; ?>

          <div class="table-responsive shadow-sm">
            <table class="table table-striped align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th>ID</th><th>Nome</th><th>Slug</th><th>Ativa</th><th>Ordem</th><th class="text-end">Ações</th>
                </tr>
              </thead>
              <tbody>
              <?php foreach (($categorias ?? []) as $c): ?>
                <tr>
                  <td><?= (int)$c->id ?></td>
                  <td><?= htmlspecialchars($c->nome) ?></td>
                  <td><?= htmlspecialchars($c->slug) ?></td>
                  <td><?= $c->ativa ? 'Sim' : 'Não' ?></td>
                  <td><?= $c->ordem === null ? '-' : (int)$c->ordem ?></td>
                  <td class="text-end">
                    <a class="btn btn-sm btn-secondary" href="<?= \App\Core\Url::to('/admin/categorias/editar') . '?id=' . (int)$c->id ?>">
                      <i class="bi bi-pencil-square"></i> Editar
                    </a>
                    <form method="post" action="<?= \App\Core\Url::to('/admin/categorias/excluir') ?>" class="d-inline" onsubmit="return confirm('Excluir?')">
                      <?= \App\Core\Csrf::input() ?>
                      <input type="hidden" name="id" value="<?= (int)$c->id ?>">
                      <button class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-trash3"></i> Excluir
                      </button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
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
