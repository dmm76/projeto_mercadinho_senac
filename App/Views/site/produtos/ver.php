<?php
/** app/Views/site/produtos/ver.php */
$h = static fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$step = (!empty($produto['peso_variavel']) || (($produto['unidade_sigla'] ?? '') === 'KG')) ? '0.001' : '1';
$valorInicial = ($step === '1') ? '1' : '0.100';
$preco = (float)($produto['preco_atual'] ?? 0);
$imgUrl = null;
if (!empty($produto['imagem'])) {
  $imgUrl = \App\Core\Url::to('/' . ltrim($produto['imagem'], '/'));
}
?>

<div class="container py-4">
  <div class="row g-4">
    <div class="col-12 col-md-5">
      <?php if ($imgUrl): ?>
        <img src="<?= $imgUrl ?>" alt="<?= $h($produto['nome']) ?>" class="img-fluid rounded border">
      <?php else: ?>
        <div class="bg-light border rounded d-flex align-items-center justify-content-center" style="height:300px">
          <span class="text-muted">Sem imagem</span>
        </div>
      <?php endif; ?>
    </div>

    <div class="col-12 col-md-7">
      <h1 class="h4 mb-2"><?= $h($produto['nome']) ?></h1>

      <?php if (!empty($produto['descricao'])): ?>
        <p class="text-muted"><?= nl2br($h($produto['descricao'])) ?></p>
      <?php endif; ?>

      <p class="mb-1"><strong>Unidade:</strong> <?= $h($produto['unidade_sigla'] ?? '') ?></p>
      <p class="fs-4 fw-semibold mb-3">
        R$ <?= number_format($preco, 2, ',', '.') ?>
        <?php if (($produto['unidade_sigla'] ?? '') === 'KG'): ?>
          <small class="text-muted">/ kg</small>
        <?php endif; ?>
      </p>

      <form method="post" action="<?= \App\Core\Url::to('/carrinho/adicionar/' . (int)$produto['id']) ?>" class="row gy-2 gx-2 align-items-end">
        <input type="hidden" name="csrf" value="<?= \App\Core\Csrf::token() ?>">

        <div class="col-auto">
          <label for="qtd" class="form-label mb-1">Quantidade</label>
          <input id="qtd" name="quantidade" type="number"
                 class="form-control"
                 value="<?= $valorInicial ?>"
                 min="<?= $step ?>" step="<?= $step ?>"
                 style="max-width: 140px">
        </div>

        <div class="col-auto">
          <button type="submit" class="btn btn-danger">
            <i class="bi bi-cart-plus me-1"></i> Adicionar ao carrinho
          </button>
        </div>

        <div class="col-12">
          <small class="text-muted">
            <?php if ($step === '1'): ?>
              Ajuste a quantidade em unidades inteiras.
            <?php else: ?>
              Produto por peso: ajuste em passos de <?= str_replace('.', ',', $step) ?> kg.
            <?php endif; ?>
          </small>
        </div>
      </form>

      <div class="mt-3">
        <a href="<?= \App\Core\Url::to('/produtos') ?>" class="btn btn-outline-secondary btn-sm">
          <i class="bi bi-arrow-left"></i> Voltar para produtos
        </a>
      </div>
    </div>
  </div>
</div>
