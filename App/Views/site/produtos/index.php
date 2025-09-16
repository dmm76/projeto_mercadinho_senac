<?php
/** app/Views/site/produtos/index.php */
$h = static fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
?>
<div class="container py-4">
  <h1 class="h4 mb-3">Produtos</h1>

  <?php if (empty($produtos)): ?>
    <div class="alert alert-warning">Nenhum produto encontrado.</div>
    <a href="<?= \App\Core\Url::to('/') ?>" class="btn btn-outline-secondary">Voltar</a>
    <?php return; ?>
  <?php endif; ?>

  <div class="row g-3">
    <?php foreach ($produtos as $produto): ?>
      <?php
        $id    = (int)($produto['id'] ?? 0);
        $nome  = $produto['nome'] ?? 'Produto';
        $desc  = $produto['descricao'] ?? '';
        $preco = (float)($produto['preco_atual'] ?? 0);
        $img   = !empty($produto['imagem']) ? \App\Core\Url::to('/' . ltrim($produto['imagem'], '/')) : null;
        $isKg  = (!empty($produto['peso_variavel']) || (($produto['unidade_sigla'] ?? '') === 'KG'));
        $step  = $isKg ? '0.001' : '1';
        $valorInicial = $isKg ? '0.250' : '1';
      ?>
      <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2">
        <div class="card text-center bg-light h-100">
          <a href="<?= \App\Core\Url::to('/produtos/' . $id) ?>" class="d-block">
            <?php if ($img): ?>
              <img src="<?= $img ?>" class="card-img-top" alt="<?= $h($nome) ?>">
            <?php else: ?>
              <div class="bg-white border-bottom d-flex align-items-center justify-content-center" style="height:160px">
                <span class="text-muted small">Sem imagem</span>
              </div>
            <?php endif; ?>
          </a>

          <div class="card-header">R$ <?= number_format($preco, 2, ',', '.') ?></div>

          <div class="card-body">
            <h5 class="card-title mb-1"><?= $h($nome) ?></h5>
            <?php if ($desc): ?>
              <p class="card-text text-muted" style="display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;">
                <?= $h($desc) ?>
              </p>
            <?php endif; ?>
          </div>

          <div class="card-footer">
            <form method="post" action="<?= \App\Core\Url::to('/carrinho/adicionar/' . $id) ?>" class="d-grid gap-2">
              <input type="hidden" name="csrf" value="<?= \App\Core\Csrf::token() ?>">
              <div class="input-group input-group-sm mb-2">
                <span class="input-group-text">Qtd</span>
                <input type="number" name="quantidade" class="form-control"
                       value="<?= $valorInicial ?>" min="<?= $step ?>" step="<?= $step ?>">
                <?php if ($isKg): ?>
                  <span class="input-group-text">kg</span>
                <?php endif; ?>
              </div>
              <button class="btn btn-danger">
                <i class="bi bi-cart-plus me-1"></i> Adicionar ao carrinho
              </button>
            </form>
            <a class="small d-inline-block mt-2" href="<?= \App\Core\Url::to('/produtos/' . $id) ?>">Ver produto</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>
