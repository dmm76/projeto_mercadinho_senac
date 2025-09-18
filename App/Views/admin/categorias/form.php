<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($title ?? 'Categoria') ?></title>
  <link rel="stylesheet" href="<?= \App\Core\Url::to('/assets/css/bootstrap.min.css') ?>">
</head>
<body>
<div class="container py-4" style="max-width:640px">
  <h1 class="h4 mb-3"><?= htmlspecialchars($title ?? 'Categoria') ?></h1>

  <?php if ($m = \App\Core\Flash::get('error')): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($m) ?></div>
  <?php endif; ?>

  <?php
    $isEdit = isset($categoria) && $categoria !== null;
    $action = $isEdit ? \App\Core\Url::to('/admin/categorias/editar') : \App\Core\Url::to('/admin/categorias/criar');
  ?>
  <form method="post" action="<?= $action ?>">
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
      <div class="col-6">
        <label class="form-label" for="categoria-ordem">Ordem</label>
        <input id="categoria-ordem" class="form-control" type="number" name="ordem" value="<?= $isEdit && $categoria->ordem!==null ? (int)$categoria->ordem : '' ?>">
      </div>
      <div class="col-6 d-flex align-items-end">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="ativa" id="categoria-ativa" <?= $isEdit ? ($categoria->ativa ? 'checked' : '') : 'checked' ?>>
          <label class="form-check-label" for="categoria-ativa">Ativa</label>
        </div>
      </div>
    </div>

    <button class="btn btn-success"><?= $isEdit ? 'Salvar' : 'Criar' ?></button>
    <a class="btn btn-secondary" href="<?= \App\Core\Url::to('/admin/categorias') ?>">Voltar</a>
  </form>
</div>
</body>
</html>
