<!doctype html><html lang="pt-br"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= htmlspecialchars($title ?? 'Marca') ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body><div class="container py-4" style="max-width:520px">
<h1 class="h4 mb-3"><?= htmlspecialchars($title ?? 'Marca') ?></h1>
<?php if ($m=\App\Core\Flash::get('error')): ?><div class="alert alert-danger"><?= htmlspecialchars($m) ?></div><?php endif; ?>
<?php $isEdit = isset($marca)&&$marca!==null; $action = $isEdit ? \App\Core\Url::to('/admin/marcas/editar') : \App\Core\Url::to('/admin/marcas/criar'); ?>
<form method="post" action="<?= $action ?>">
  <?= \App\Core\Csrf::input() ?>
  <?php if ($isEdit): ?><input type="hidden" name="id" value="<?= (int)$marca->id ?>"><?php endif; ?>
  <div class="mb-3"><label class="form-label">Nome</label>
    <input class="form-control" type="text" name="nome" required value="<?= $isEdit? htmlspecialchars($marca->nome):'' ?>">
  </div>
  <button class="btn btn-success"><?= $isEdit?'Salvar':'Criar' ?></button>
  <a class="btn btn-secondary" href="<?= \App\Core\Url::to('/admin/marcas') ?>">Voltar</a>
</form>
</div></body></html>
