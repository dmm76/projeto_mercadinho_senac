<?php /** app/Views/site/produtos/index.php */ ?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title><?= htmlspecialchars($title ?? 'Produtos') ?></title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"/>
  <link rel="stylesheet" href="<?= \App\Core\Url::to('/assets/site/css/style.css') ?>"/>

  <style>
    .product-card img{object-fit:cover; height:190px;}
    .product-desc{display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden; min-height:4.5rem;}
  </style>
</head>
<body>
<div class="d-flex flex-column wrapper">
  <?php require __DIR__ . '/../../partials/navbar.php'; ?>

  <main class="flex-fill">
    <div class="container py-4">
      <div class="d-flex flex-wrap align-items-center mb-4 gap-2">
        <h1 class="h3 mb-0 flex-grow-1">Produtos</h1>
        <form class="d-flex" method="get" action="<?= \App\Core\Url::to('/buscar') ?>" role="search" style="max-width:320px;">
          <input class="form-control me-2" type="search" placeholder="Buscar produtos" name="q" value="<?= htmlspecialchars($_GET['q'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
          <button class="btn btn-outline-danger" type="submit"><i class="bi bi-search"></i></button>
        </form>
      </div>

      <?php if (empty($produtos)): ?>
        <div class="alert alert-warning">
          Nenhum produto encontrado.
          <a class="alert-link" href="<?= \App\Core\Url::to('/') ?>">Voltar para a página inicial</a>.
        </div>
      <?php else: ?>
        <div class="row g-4">
          <?php foreach ($produtos as $produto):
            $id    = (int)($produto['id'] ?? 0);
            $nome  = $produto['nome'] ?? 'Produto';
            $desc  = $produto['descricao'] ?? '';
            $preco = (float)($produto['preco_atual'] ?? 0);
            $imgRel = $produto['imagem'] ?? null;
            $img   = $imgRel ? \App\Core\Url::to('/' . ltrim($imgRel, '/')) : \App\Core\Url::to('/assets/site/img/produtos.jpg');
            $isKg  = (!empty($produto['peso_variavel']) || (($produto['unidade_sigla'] ?? '') === 'KG'));
            $step  = $isKg ? 0.001 : 1;
            $valorInicial = $isKg ? 0.250 : 1;
          ?>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
              <div class="card h-100 shadow-sm product-card">
                <a href="<?= \App\Core\Url::to('/produtos/' . $id) ?>" class="text-decoration-none">
                  <img src="<?= htmlspecialchars($img, ENT_QUOTES, 'UTF-8') ?>" class="card-img-top" alt="<?= htmlspecialchars($nome, ENT_QUOTES, 'UTF-8') ?>">
                </a>
                <div class="card-body d-flex flex-column">
                  <h5 class="card-title text-danger">R$ <?= number_format($preco, 2, ',', '.') ?><?= $isKg ? '<small class="text-muted"> / kg</small>' : '' ?></h5>
                  <h6 class="card-subtitle mb-2 fw-semibold"><?= htmlspecialchars($nome, ENT_QUOTES, 'UTF-8') ?></h6>
                  <?php if ($desc): ?>
                    <p class="card-text text-muted product-desc mb-3"><?= htmlspecialchars($desc, ENT_QUOTES, 'UTF-8') ?></p>
                  <?php else: ?>
                    <p class="product-desc mb-3 text-muted">&nbsp;</p>
                  <?php endif; ?>
                  <div class="mt-auto">
                    <form method="post" action="<?= \App\Core\Url::to('/carrinho/adicionar/' . $id) ?>" class="d-grid gap-2">
                      <?= \App\Core\Csrf::input() ?>
                      <div class="input-group input-group-sm">
                        <span class="input-group-text">Qtd</span>
                        <input type="number" name="quantidade" class="form-control" value="<?= number_format($valorInicial, $step === 1 ? 0 : 3, '.', '') ?>" min="<?= $step ?>" step="<?= $step ?>">
                        <?php if ($isKg): ?>
                          <span class="input-group-text">kg</span>
                        <?php endif; ?>
                      </div>
                      <button class="btn btn-danger">
                        <i class="bi bi-cart-plus me-1"></i> Adicionar ao carrinho
                      </button>
                    </form>
                    <a class="btn btn-link btn-sm text-decoration-none mt-2" href="<?= \App\Core\Url::to('/produtos/' . $id) ?>">
                      Ver detalhes
                    </a>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </main>

  <?php require __DIR__ . '/../../partials/footer.php'; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
