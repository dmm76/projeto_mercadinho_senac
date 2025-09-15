<!doctype html><html lang="pt-br"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= htmlspecialchars($title ?? 'Unidade') ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body><div class="container py-4" style="max-width:520px">
<h1 class="h4 mb-3"><?= htmlspecialchars($title ?? 'Unidade') ?></h1>
<?php if ($m=\App\Core\Flash::get('error')): ?><div class="alert alert-danger"><?= htmlspecialchars($m) ?></div><?php endif; ?>
<?php $isEdit = isset($unidade)&&$unidade!==null; $action = $isEdit ? \App\Core\Url::to('/admin/unidades/editar') : \App\Core\Url::to('/admin/unidades/criar'); ?>
<form method="post" action="<?= $action ?>">
  <?= \App\Core\Csrf::input() ?>
  <?php if ($isEdit): ?><input type="hidden" name="id" value="<?= (int)$unidade->id ?>"><?php endif; ?>
  <div class="mb-3"><label class="form-label">Sigla (UN, KG, L...)</label>
    <input class="form-control" type="text" name="sigla" required value="<?= $isEdit? htmlspecialchars($unidade->sigla):'' ?>">
  </div>
  <div class="mb-3"><label class="form-label">Descrição</label>
    <input class="form-control" type="text" name="descricao" value="<?= $isEdit? htmlspecialchars($unidade->descricao ?? ''):'' ?>">
  </div>
  <button class="btn btn-success"><?= $isEdit?'Salvar':'Criar' ?></button>
  <a class="btn btn-secondary" href="<?= \App\Core\Url::to('/admin/unidades') ?>">Voltar</a>
</form>
</div></body></html>
