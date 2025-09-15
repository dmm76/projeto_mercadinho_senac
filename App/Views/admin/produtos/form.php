<?php
$categorias = $categorias ?? [];
$marcas     = $marcas     ?? [];
$unidades   = $unidades   ?? [];
$estoque    = $estoque    ?? null;
?>

<!doctype html><html lang="pt-br"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= htmlspecialchars($title ?? 'Produto') ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head><body><div class="container py-4" style="max-width:900px">
<h1 class="h4 mb-3"><?= htmlspecialchars($title ?? 'Produto') ?></h1>
<?php if ($m=\App\Core\Flash::get('error')): ?><div class="alert alert-danger"><?= htmlspecialchars($m) ?></div><?php endif; ?>

<?php
$isEdit = isset($produto) && $produto !== null;
$action = $isEdit ? \App\Core\Url::to('/admin/produtos/editar') : \App\Core\Url::to('/admin/produtos/criar');
?>

<form method="post" action="<?= $action ?>" enctype="multipart/form-data">
  <?= \App\Core\Csrf::input() ?>
  <?php if ($isEdit): ?><input type="hidden" name="id" value="<?= (int)$produto->id ?>"><?php endif; ?>

  <div class="row g-3">
    <div class="col-md-6">
      <label class="form-label">Nome*</label>
      <input class="form-control" name="nome" required value="<?= $isEdit? htmlspecialchars($produto->nome):'' ?>">
    </div>
    <div class="col-md-3">
      <label class="form-label">SKU*</label>
      <input class="form-control" name="sku" required value="<?= $isEdit? htmlspecialchars($produto->sku):'' ?>">
    </div>
    <div class="col-md-3">
      <label class="form-label">EAN</label>
      <input class="form-control" name="ean" value="<?= $isEdit? htmlspecialchars($produto->ean ?? ''):'' ?>">
    </div>

    <div class="col-md-4">
      <label class="form-label">Categoria*</label>
      <select class="form-select" name="categoria_id" required>
        <option value="">Selecione...</option>
        <?php foreach ($categorias as $c): ?>
          <option value="<?= (int)$c->id ?>" <?= $isEdit && $produto->categoriaId===$c->id ? 'selected':'' ?>>
            <?= htmlspecialchars($c->nome) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-4">
      <label class="form-label">Marca</label>
      <select class="form-select" name="marca_id">
        <option value="">(sem marca)</option>
        <?php foreach ($marcas as $m): ?>
          <option value="<?= (int)$m->id ?>" <?= $isEdit && $produto->marcaId===$m->id ? 'selected':'' ?>>
            <?= htmlspecialchars($m->nome) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-4">
      <label class="form-label">Unidade*</label>
      <select class="form-select" name="unidade_id" required>
        <option value="">Selecione...</option>
        <?php foreach ($unidades as $u): ?>
          <option value="<?= (int)$u->id ?>" <?= $isEdit && $produto->unidadeId===$u->id ? 'selected':'' ?>>
            <?= htmlspecialchars($u->sigla) ?><?= $u->descricao ? ' - '.htmlspecialchars($u->descricao):'' ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-12">
      <label class="form-label">Descrição</label>
      <textarea class="form-control" name="descricao" rows="3"><?= $isEdit? htmlspecialchars($produto->descricao ?? ''):'' ?></textarea>
    </div>

    <div class="col-md-4">
      <label class="form-label">Imagem (JPG/PNG até 2MB)</label>
      <input class="form-control" type="file" name="imagem" accept=".jpg,.jpeg,.png">
      <?php if ($isEdit && $produto->imagem): ?>
        <div class="form-text">Atual: <a target="_blank" href="<?= \App\Core\Url::to('/').'/'.htmlspecialchars($produto->imagem) ?>">ver</a></div>
      <?php endif; ?>
    </div>
    <div class="col-md-4 d-flex align-items-end">
      <div class="form-check me-3">
        <input class="form-check-input" type="checkbox" name="ativo" id="ativo" <?= $isEdit ? ($produto->ativo ? 'checked':'') : 'checked' ?>>
        <label class="form-check-label" for="ativo">Ativo</label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" name="peso_variavel" id="peso" <?= $isEdit && $produto->pesoVariavel ? 'checked':'' ?>>
        <label class="form-check-label" for="peso">Peso variável</label>
      </div>
    </div>

    <hr class="mt-2 mb-1">

    <div class="col-md-3">
      <label class="form-label">Preço venda* (R$)</label>
      <input class="form-control" name="preco_venda" type="number" step="0.01" min="0" <?= $isEdit ? '' : 'required' ?>>
      <?php if ($isEdit): ?><div class="form-text">Preencha para registrar novo preço.</div><?php endif; ?>
    </div>
    <div class="col-md-3">
      <label class="form-label">Preço promocional (R$)</label>
      <input class="form-control" name="preco_promocional" type="number" step="0.01" min="0">
    </div>
    <div class="col-md-3">
      <label class="form-label">Início promo</label>
      <input class="form-control" name="inicio_promo" type="datetime-local">
    </div>
    <div class="col-md-3">
      <label class="form-label">Fim promo</label>
      <input class="form-control" name="fim_promo" type="datetime-local">
    </div>

    <div class="col-md-3">
      <label class="form-label">Estoque inicial (qtd)</label>
      <input class="form-control" name="estoque_qtd" type="number" step="0.001" min="0" value="<?= $isEdit && $estoque ? number_format($estoque['quantidade'],3,'.','') : '' ?>">
    </div>
    <div class="col-md-3">
      <label class="form-label">Estoque mínimo</label>
      <input class="form-control" name="estoque_min" type="number" step="0.001" min="0" value="<?= $isEdit && $estoque ? number_format($estoque['minimo'],3,'.','') : '' ?>">
    </div>

  </div><!-- /row -->

  <div class="mt-3">
    <button class="btn btn-success"><?= $isEdit ? 'Salvar' : 'Criar' ?></button>
    <a class="btn btn-secondary" href="<?= \App\Core\Url::to('/admin/produtos') ?>">Voltar</a>
  </div>
</form>
</div></body></html>
