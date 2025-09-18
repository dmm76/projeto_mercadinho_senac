<?php
/** app/Views/site/produtos/ver.php */

use App\Core\Csrf;
use App\Core\Url;

$produto = $produto ?? [];
$title = $title ?? ($produto['nome'] ?? 'Produto');
$h = static fn($value) => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

$unidadeSigla = strtoupper((string) ($produto['unidade_sigla'] ?? ''));
$isPesoVariavel = !empty($produto['peso_variavel']) || $unidadeSigla === 'KG';
$step = $isPesoVariavel ? 0.001 : 1;
$valorInicial = $isPesoVariavel ? 0.250 : 1;
$precoVenda = (float) ($produto['preco_venda'] ?? 0);
$precoAtual = (float) ($produto['preco_atual'] ?? ($precoVenda > 0 ? $precoVenda : 0));
$temPromo = $precoVenda > 0 && $precoAtual > 0 && $precoAtual < $precoVenda;
$estoque = $produto['estoque_qtd'] ?? null;

$gallery = [];
$baseImage = null;
if (!empty($produto['imagem'])) {
    $baseImage = Url::to('/' . ltrim((string) $produto['imagem'], '/'));
    $gallery[] = $baseImage;
}

$possibleKeys = ['galeria', 'imagens', 'imagens_extra'];
foreach ($possibleKeys as $key) {
    if (empty($produto[$key])) {
        continue;
    }
    $raw = $produto[$key];
    $list = [];
    if (is_string($raw)) {
        $parts = preg_split('/[\\r\\n;,]+/', $raw) ?: [];
        $list = array_filter(array_map('trim', $parts));
    } elseif (is_array($raw)) {
        $list = array_filter(array_map('trim', $raw));
    }
    foreach ($list as $item) {
        if ($item === '') {
            continue;
        }
        $gallery[] = Url::to('/' . ltrim($item, '/'));
    }
}

foreach ($produto as $key => $value) {
    if (!is_string($value) || trim($value) === '') {
        continue;
    }
    if ($key === 'imagem') {
        continue;
    }
    if (stripos($key, 'imagem') !== false || stripos($key, 'foto') !== false) {
        $gallery[] = Url::to('/' . ltrim($value, '/'));
    }
}

$gallery = array_values(array_unique($gallery));
if (empty($gallery)) {
    $placeholder = Url::to('/assets/site/img/produtos.jpg');
    $gallery[] = $placeholder;
    $baseImage = $placeholder;
} else {
    $baseImage = $gallery[0];
}

$currentUrl = $_SERVER['REQUEST_URI'] ?? Url::to('/produtos/' . (int) ($produto['id'] ?? 0));
$clienteId = $clienteId ?? null;
$isFavorito = !empty($isFavorito);
?>

<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title><?= $h($title) ?> - Mercadinho Borba Gato</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"/>
  <link rel="stylesheet" href="<?= Url::to('/assets/site/css/style.css') ?>"/>
  <style>
    .product-hero {
      border-radius: 18px;
    }
    .product-hero img {
      object-fit: contain;
    }
    .product-thumb-btn {
      border: 2px solid transparent;
      border-radius: 12px;
      overflow: hidden;
      padding: 0;
      background-color: #fff;
    }
    .product-thumb-btn.active,
    .product-thumb-btn:focus {
      border-color: #dc3545;
      box-shadow: 0 0 0 .25rem rgba(220, 53, 69, 0.25);
    }
    .product-thumb-img {
      width: 90px;
      height: 70px;
      object-fit: cover;
    }
    .product-meta {
      font-size: 0.9rem;
    }
  </style>
</head>
<body>
<div class="d-flex flex-column wrapper">
  <?php require __DIR__ . '/../../partials/navbar.php'; ?>

  <main class="flex-fill">
    <div class="container py-4">
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div class="small text-muted">
          <a class="text-decoration-none text-muted" href="<?= Url::to('/') ?>">
            <i class="bi bi-house"></i> Principal
          </a>
          <span class="mx-2">/</span>
          <a class="text-decoration-none text-muted" href="<?= Url::to('/produtos') ?>">Produtos</a>
          <span class="mx-2">/</span>
          <span class="text-danger fw-semibold"><?= $h($produto['nome'] ?? 'Produto') ?></span>
        </div>
        <a class="btn btn-outline-secondary btn-sm" href="<?= Url::to('/produtos') ?>">
          <i class="bi bi-arrow-left"></i> Voltar aos produtos
        </a>
      </div>

      <div class="row g-4 align-items-start">
        <div class="col-12 col-lg-6">
          <div class="card border-0 shadow-sm product-hero">
            <div class="ratio ratio-4x3 bg-light d-flex align-items-center justify-content-center">
              <img id="productMainImage" src="<?= $h($baseImage) ?>" alt="<?= $h($produto['nome'] ?? 'Produto') ?>" class="w-100 h-100 p-3">
            </div>
          </div>

          <?php if (count($gallery) > 1): ?>
            <div class="d-flex flex-wrap gap-2 mt-3" id="productThumbs">
              <?php foreach ($gallery as $idx => $img): ?>
                <button type="button" class="product-thumb-btn <?= $idx === 0 ? 'active' : '' ?>" data-image="<?= $h($img) ?>">
                  <img src="<?= $h($img) ?>" alt="Miniatura do produto" class="product-thumb-img">
                </button>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>

        <div class="col-12 col-lg-6">
          <h1 class="display-6 fw-semibold mb-3"><?= $h($produto['nome'] ?? 'Produto') ?></h1>

          <?php if (!empty($produto['descricao'])): ?>
            <p class="text-muted fs-6"><?= nl2br($h($produto['descricao'])) ?></p>
          <?php endif; ?>

          <div class="d-flex align-items-baseline gap-3 mb-3">
            <div>
              <div class="text-danger fw-bold" style="font-size: 2rem;">
                R$ <?= number_format($precoAtual, 2, ',', '.') ?>
                <?php if ($isPesoVariavel): ?>
                  <small class="text-muted fs-6">/ kg</small>
                <?php endif; ?>
              </div>
              <?php if ($temPromo): ?>
                <div class="text-muted">
                  <small class="text-decoration-line-through">R$ <?= number_format($precoVenda, 2, ',', '.') ?></small>
                </div>
              <?php endif; ?>
            </div>
            <?php if ($estoque !== null): ?>
              <span class="badge bg-light text-dark border product-meta">Estoque: <?= $h((string) $estoque) ?></span>
            <?php endif; ?>
          </div>

          <dl class="row gy-1 product-meta">
            <?php if (!empty($produto['sku'])): ?>
              <dt class="col-sm-3">SKU</dt>
              <dd class="col-sm-9 text-muted"><?= $h($produto['sku']) ?></dd>
            <?php endif; ?>
            <?php if (!empty($produto['ean'])): ?>
              <dt class="col-sm-3">EAN</dt>
              <dd class="col-sm-9 text-muted"><?= $h($produto['ean']) ?></dd>
            <?php endif; ?>
            <?php if ($unidadeSigla !== ''): ?>
              <dt class="col-sm-3">Unidade</dt>
              <dd class="col-sm-9 text-muted"><?= $h($unidadeSigla) ?></dd>
            <?php endif; ?>
          </dl>

          <div class="d-flex flex-wrap gap-3 align-items-end mt-4">
            <form method="post" action="<?= Url::to('/carrinho/adicionar/' . (int) ($produto['id'] ?? 0)) ?>" class="d-flex flex-wrap align-items-end gap-3">
              <?= Csrf::input() ?>
              <div class="">
                <label for="qtd" class="form-label mb-1">Quantidade</label>
                <input id="qtd" name="quantidade" type="number" class="form-control" value="<?= number_format($valorInicial, $step === 1 ? 0 : 3, '.', '') ?>" min="<?= $step ?>" step="<?= $step ?>" style="max-width: 140px;">
              </div>
              <button type="submit" class="btn btn-danger btn-lg px-4">
                <i class="bi bi-cart-plus me-2"></i>Adicionar ao Carrinho
              </button>
            </form>

            <?php if ($clienteId !== null): ?>
              <form method="post" action="<?= Url::to('/favoritos/toggle') ?>" class="d-flex">
                <?= Csrf::input() ?>
                <input type="hidden" name="produto_id" value="<?= (int) ($produto['id'] ?? 0) ?>">
                <input type="hidden" name="redirect" value="<?= $h($currentUrl) ?>">
                <button type="submit" class="btn btn-outline-danger btn-lg px-4 <?= $isFavorito ? 'active' : '' ?>">
                  <i class="bi <?= $isFavorito ? 'bi-heart-fill' : 'bi-heart' ?> me-2"></i><?= $isFavorito ? 'Remover dos Favoritos' : 'Adicionar aos Favoritos' ?>
                </button>
              </form>
            <?php else: ?>
              <a class="btn btn-outline-danger btn-lg px-4" href="<?= Url::to('/login') ?>">
                <i class="bi bi-heart me-2"></i>Adicionar aos Favoritos
              </a>
            <?php endif; ?>
          </div>

          <?php if (!$isPesoVariavel): ?>
            <small class="text-muted d-block mt-3">Informe a quantidade desejada em unidades inteiras.</small>
          <?php else: ?>
            <small class="text-muted d-block mt-3">Produto por peso: ajuste a quantidade em passos de <?= str_replace('.', ',', (string) $step) ?> kg.</small>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </main>

  <?php require __DIR__ . '/../../partials/footer.php'; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  (function () {
    var mainImage = document.getElementById('productMainImage');
    var thumbsWrapper = document.getElementById('productThumbs');
    if (!mainImage || !thumbsWrapper) {
      return;
    }
    var buttons = thumbsWrapper.querySelectorAll('.product-thumb-btn');
    Array.prototype.forEach.call(buttons, function (btn) {
      btn.addEventListener('click', function () {
        var newImage = btn.getAttribute('data-image');
        if (!newImage || mainImage.getAttribute('src') === newImage) {
          return;
        }
        mainImage.setAttribute('src', newImage);
        Array.prototype.forEach.call(buttons, function (other) {
          other.classList.remove('active');
        });
        btn.classList.add('active');
      });
    });
  })();
</script>
</body>
</html>
