<?php
$categorias = $categorias ?? [];
$marcas     = $marcas     ?? [];
$unidades   = $unidades   ?? [];
$estoque    = $estoque    ?? null;
?>

<!doctype html><html lang="pt-br"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= htmlspecialchars($title ?? 'Produto') ?></title>
<link rel="stylesheet" href="<?= \App\Core\Url::to('/assets/css/bootstrap.min.css') ?>">
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
      <label class="form-label" for="produto-nome">Nome*</label>
      <input id="produto-nome" class="form-control" name="nome" required value="<?= $isEdit? htmlspecialchars($produto->nome):'' ?>">
    </div>
    <div class="col-md-3">
      <label class="form-label" for="produto-sku">SKU*</label>
      <input id="produto-sku" class="form-control" name="sku" required value="<?= $isEdit? htmlspecialchars($produto->sku):'' ?>">
    </div>
    <div class="col-md-3">
      <label class="form-label" for="produto-ean">EAN</label>
      <input id="produto-ean" class="form-control" name="ean" value="<?= $isEdit? htmlspecialchars($produto->ean ?? ''):'' ?>">
    </div>

    <div class="col-md-4">
      <label class="form-label" for="produto-categoria">Categoria*</label>
      <select id="produto-categoria" class="form-select" name="categoria_id" required>
        <option value="">Selecione...</option>
        <?php foreach ($categorias as $c): ?>
          <option value="<?= (int)$c->id ?>" <?= $isEdit && $produto->categoriaId===$c->id ? 'selected':'' ?>>
            <?= htmlspecialchars($c->nome) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-4">
      <label class="form-label" for="produto-marca">Marca</label>
      <select id="produto-marca" class="form-select" name="marca_id">
        <option value="">(sem marca)</option>
        <?php foreach ($marcas as $m): ?>
          <option value="<?= (int)$m->id ?>" <?= $isEdit && $produto->marcaId===$m->id ? 'selected':'' ?>>
            <?= htmlspecialchars($m->nome) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-4">
      <label class="form-label" for="produto-unidade">Unidade*</label>
      <select id="produto-unidade" class="form-select" name="unidade_id" required>
        <option value="">Selecione...</option>
        <?php foreach ($unidades as $u): ?>
          <option value="<?= (int)$u->id ?>" <?= $isEdit && $produto->unidadeId===$u->id ? 'selected':'' ?>>
            <?= htmlspecialchars($u->sigla) ?><?= $u->descricao ? ' - '.htmlspecialchars($u->descricao):'' ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-12">
      <label class="form-label" for="produto-descricao">Descricao</label>
      <textarea id="produto-descricao" class="form-control" name="descricao" rows="3"><?= $isEdit? htmlspecialchars($produto->descricao ?? ''):'' ?></textarea>
    </div>

    <div class="col-md-4">
      <label class="form-label" for="produto-imagem">Imagem (JPG/PNG/WebP ate 2MB)</label>
      <input id="produto-imagem" class="form-control" type="file" name="imagem" accept="image/jpeg,image/png,image/webp">
      <?php if ($isEdit && $produto->imagem): ?>
        <div class="form-text">Atual: <a target="_blank" href="<?= \App\Core\Url::to('/').'/'.htmlspecialchars($produto->imagem) ?>">ver</a></div>
      <?php endif; ?>
    </div>
    <div class="col-md-4 d-flex align-items-end">
      <div class="form-check me-3">
        <input class="form-check-input" type="checkbox" name="ativo" id="produto-ativo" <?= $isEdit ? ($produto->ativo ? 'checked':'') : 'checked' ?>>
        <label class="form-check-label" for="produto-ativo">Ativo</label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" name="peso_variavel" id="produto-peso" <?= $isEdit && $produto->pesoVariavel ? 'checked':'' ?>>
        <label class="form-check-label" for="produto-peso">Peso variavel</label>
      </div>
    </div>

    <hr class="mt-2 mb-1">

    <div class="col-md-3">
      <label class="form-label" for="produto-preco-venda">Preco venda* (R$)</label>
      <input id="produto-preco-venda" class="form-control" name="preco_venda" type="number" step="0.01" min="0" <?= $isEdit ? '' : 'required' ?>>
      <?php if ($isEdit): ?><div class="form-text">Preencha para registrar novo preco.</div><?php endif; ?>
    </div>
    <div class="col-md-3">
      <label class="form-label" for="produto-preco-promocional">Preco promocional (R$)</label>
      <input id="produto-preco-promocional" class="form-control" name="preco_promocional" type="number" step="0.01" min="0">
    </div>
    <div class="col-md-3">
      <label class="form-label" for="produto-inicio-promo">Inicio promo</label>
      <input id="produto-inicio-promo" class="form-control" name="inicio_promo" type="datetime-local">
    </div>
    <div class="col-md-3">
      <label class="form-label" for="produto-fim-promo">Fim promo</label>
      <input id="produto-fim-promo" class="form-control" name="fim_promo" type="datetime-local">
    </div>

    <div class="col-md-3">
      <label class="form-label" for="produto-estoque-qtd">Estoque inicial (qtd)</label>
      <input id="produto-estoque-qtd" class="form-control" name="estoque_qtd" type="number" step="0.001" min="0" value="<?= $isEdit && $estoque ? number_format($estoque['quantidade'],3,'.','') : '' ?>">
    </div>
    <div class="col-md-3">
      <label class="form-label" for="produto-estoque-min">Estoque minimo</label>
      <input id="produto-estoque-min" class="form-control" name="estoque_min" type="number" step="0.001" min="0" value="<?= $isEdit && $estoque ? number_format($estoque['minimo'],3,'.','') : '' ?>">
    </div>

  </div><!-- /row -->

  <div class="mt-3">
    <button class="btn btn-success"><?= $isEdit ? 'Salvar' : 'Criar' ?></button>
    <a class="btn btn-secondary" href="<?= \App\Core\Url::to('/admin/produtos') ?>">Voltar</a>
  </div>
</form>
</div></body></html>

