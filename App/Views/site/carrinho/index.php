<?php

/** app/Views/site/carrinho/index.php */ ?>
<!doctype html>
<html lang="pt-br">

<head>
  <meta charset="utf-8" />
  <meta name="description" content="Mercadinho Borba Gato: supermercado online com ofertas atualizadas, entrega rapida e catalogo completo de produtos para o dia a dia." />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title><?= htmlspecialchars($title ?? 'Carrinho de Compras') ?></title>

  <link rel="stylesheet" href="<?= \App\Core\Url::to('/assets/css/bootstrap.min.css') ?>" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <link rel="stylesheet" href="<?= \App\Core\Url::to('/assets/site/css/style.css') ?>" />

  <style>
    .cart-item-img {
      object-fit: cover;
      width: 100%;
      height: 140px;
      border-radius: .5rem;
    }

    @media (min-width:768px) {
      .cart-item-img {
        height: 160px;
      }
    }

    .cart-empty {
      max-width: 480px;
    }
  </style>
</head>

<body>
  <div class="d-flex flex-column wrapper">

    <?php require __DIR__ . '/../../partials/navbar.php'; ?>

    <main class="flex-fill">
      <div class="container py-4">
        <h1 class="h3 mb-4">Carrinho de Compras</h1>

        <?php if (empty($carrinho)): ?>
          <div class="alert alert-info cart-empty">
            <p class="mb-2">Seu carrinho ainda está vazio.</p>
            <a class="btn btn-outline-danger" href="<?= \App\Core\Url::to('/') ?>">
              <i class="bi bi-arrow-left me-1"></i> Ver produtos
            </a>
          </div>
        <?php else: ?>
          <ul class="list-group mb-4">
            <?php foreach ($carrinho as $produtoId => $item):
              $produto   = $item['produto'] ?? [];
              $quantidade = (float)($item['quantidade'] ?? 0);
              $preco      = (float)($produto['preco_atual'] ?? 0);
              $subtotal   = $preco * $quantidade;
              $pesoVariavel = !empty($produto['peso_variavel']) || (($produto['unidade_sigla'] ?? '') === 'KG');
              $step = $pesoVariavel ? 0.001 : 1;
              $imagemRel = $produto['imagem'] ?? null;
              $imagemUrl = $imagemRel
                ? \App\Core\Url::to('/' . ltrim($imagemRel, '/'))
                : \App\Core\Url::to('/assets/site/img/produtos.jpg');
            ?>
              <li class="list-group-item py-3">
                <div class="row g-3 align-items-center">
                  <div class="col-4 col-md-3 col-lg-2">
                    <img src="<?= htmlspecialchars($imagemUrl) ?>"
                      alt="<?= htmlspecialchars($produto['nome'] ?? 'Produto') ?>"
                      class="cart-item-img shadow-sm" />
                  </div>
                  <div class="col-8 col-md-5 col-lg-6">
                    <h4 class="h5 mb-1">
                      <a class="text-decoration-none text-danger"
                        href="<?= \App\Core\Url::to('/produtos/' . (int)$produtoId) ?>">
                        <?= htmlspecialchars($produto['nome'] ?? 'Produto sem nome') ?>
                      </a>
                    </h4>
                    <p class="text-muted mb-2">
                      <?= htmlspecialchars($produto['unidade_sigla'] ?? '') ?>
                      <?= !empty($produto['peso_variavel']) ? '(por peso)' : '' ?>
                    </p>
                    <div class="text-muted small">
                      <span>Preço unitário:</span>
                      <strong class="text-dark">R$ <?= number_format($preco, 2, ',', '.') ?></strong>
                    </div>
                  </div>
                  <div class="col-12 col-md-4 col-lg-4">
                    <form class="input-group input-group-sm mb-2" method="post"
                      action="<?= \App\Core\Url::to('/carrinho/atualizar/' . (int)$produtoId) ?>">
                      <?= \App\Core\Csrf::input() ?>
                      <button class="btn btn-outline-dark js-qty" type="button" data-step="<?= $step ?>"
                        data-target="qty-<?= (int)$produtoId ?>" data-dir="-1">
                        <i class="bi bi-caret-down"></i>
                      </button>
                      <input type="number" class="form-control text-center" aria-label="Quantidade para <?= htmlspecialchars($produto['nome'] ?? 'Produto') ?>"
                        id="qty-<?= (int)$produtoId ?>" name="quantidade"
                        value="<?= number_format($quantidade, $step === 1 ? 0 : 3, '.', '') ?>"
                        min="<?= $step ?>" step="<?= $step ?>">
                      <button class="btn btn-outline-dark js-qty" type="button" data-step="<?= $step ?>"
                        data-target="qty-<?= (int)$produtoId ?>" data-dir="1">
                        <i class="bi bi-caret-up"></i>
                      </button>
                      <button class="btn btn-outline-secondary ms-2" type="submit">Atualizar</button>
                    </form>
                    <div class="d-flex justify-content-between align-items-center">
                      <div>
                        <small class="text-muted d-block">Subtotal</small>
                        <strong>R$ <?= number_format($subtotal, 2, ',', '.') ?></strong>
                      </div>
                      <form method="post"
                        action="<?= \App\Core\Url::to('/carrinho/remover/' . (int)$produtoId) ?>">
                        <?= \App\Core\Csrf::input() ?>
                        <button class="btn btn-outline-danger btn-sm" type="submit">
                          <i class="bi bi-trash me-1"></i> Remover
                        </button>
                      </form>
                    </div>
                  </div>
                </div>
              </li>
            <?php endforeach; ?>
            <li class="list-group-item pt-3 pb-0">
              <div class="text-end">
                <h4 class="h5 text-dark">Total: R$ <?= number_format((float)$total, 2, ',', '.') ?></h4>
                <div class="mt-3">
                  <a href="<?= \App\Core\Url::to('/') ?>" class="btn btn-outline-success btn-lg">
                    <i class="bi bi-arrow-left me-1"></i> Continuar comprando
                  </a>
                  <a href="<?= \App\Core\Url::to('/checkout') ?>" class="btn btn-danger btn-lg ms-2">
                    Fechar compra
                  </a>
                </div>
              </div>
            </li>
          </ul>
        <?php endif; ?>
      </div>
    </main>

    <?php require __DIR__ . '/../../partials/footer.php'; ?>
  </div>

  <script src="<?= \App\Core\Url::to('/assets/js/bootstrap.bundle.min.js') ?>"></script>
  <script>
    (function() {
      const adjust = (btn, dir) => {
        const targetId = btn.getAttribute('data-target');
        const input = document.getElementById(targetId);
        if (!input) return;
        const step = parseFloat(btn.getAttribute('data-step')) || 1;
        const current = parseFloat(input.value || '0');
        const next = Math.max(step, current + dir * step);
        input.value = (step === 1 ? Math.round(next) : next.toFixed(3));
      };
      document.querySelectorAll('.js-qty').forEach(btn => {
        btn.addEventListener('click', () => {
          const dir = parseInt(btn.getAttribute('data-dir'), 10);
          adjust(btn, dir);
        });
      });
    })();
  </script>
</body>

</html>

