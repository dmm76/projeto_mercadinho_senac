<?php /** app/Views/site/checkout/index.php */ ?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title><?= htmlspecialchars($title ?? 'Checkout', ENT_QUOTES, 'UTF-8') ?></title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"/>
  <link rel="stylesheet" href="<?= \App\Core\Url::to('/assets/site/css/style.css') ?>"/>

  <style>
    .checkout-summary table td,
    .checkout-summary table th { font-size: .9rem; }
    .address-card input[type=radio] { margin-top: 6px; }
  </style>
</head>
<body>
<div class="d-flex flex-column wrapper">
  <?php require __DIR__ . '/../../partials/navbar.php'; ?>

  <?php
  $fmtQty = static function (float $qty): string {
      $txt = number_format($qty, 3, ',', '.');
      return rtrim(rtrim($txt, '0'), ',');
  };
  $h = static fn($v) => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
  $linhas = [];
  $subtotal = 0.0;
  if (is_array($carrinho)) {
      foreach ($carrinho as $item) {
          $produto = $item['produto'] ?? [];
          $nome    = $produto['nome'] ?? ($item['nome'] ?? 'Produto');
          $qtd     = isset($item['quantidade']) ? (float) $item['quantidade'] : (float) ($item['qty'] ?? 0);
          $preco   = isset($item['preco']) ? (float) $item['preco'] : (float) ($produto['preco_atual'] ?? 0);
          $linhaSubtotal = $preco * $qtd;
          $subtotal += $linhaSubtotal;
          $linhas[] = [
              'nome' => $nome,
              'quantidade' => $qtd,
              'preco' => $preco,
              'subtotal' => $linhaSubtotal,
          ];
      }
  }
  ?>

  <main class="flex-fill">
    <div class="container py-4">
      <h1 class="h3 mb-4">Finalizar compra</h1>

      <?php if (empty($linhas)): ?>
        <div class="alert alert-warning">
          Seu carrinho est&aacute; vazio.
          <a href="<?= \App\Core\Url::to('/carrinho') ?>" class="alert-link">Voltar ao carrinho</a>.
        </div>
      <?php else: ?>
        <form method="post" action="<?= \App\Core\Url::to('/checkout') ?>" class="row g-4">
          <?= \App\Core\Csrf::input() ?>

          <div class="col-12 col-lg-8">
            <div class="card shadow-sm mb-3">
              <div class="card-body">
                <h2 class="h6 text-uppercase text-muted mb-3">Entrega</h2>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="entrega" id="entrega_retirada" value="retirada" checked>
                  <label class="form-check-label" for="entrega_retirada">Retirar na loja</label>
                </div>
                <div class="form-check mt-2">
                  <input class="form-check-input" type="radio" name="entrega" id="entrega_entrega" value="entrega">
                  <label class="form-check-label" for="entrega_entrega">Receber em casa</label>
                </div>
              </div>
            </div>

            <div class="card shadow-sm mb-3" id="card-enderecos">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <h2 class="h6 text-uppercase text-muted mb-0">Endere&ccedil;o de entrega</h2>
                  <a href="<?= \App\Core\Url::to('/conta/enderecos/novo') ?>" class="btn btn-sm btn-danger">
                    <i class="bi bi-geo-alt me-1"></i> Novo endere&ccedil;o
                  </a>
                </div>

                <?php if (!empty($enderecos)): ?>
                  <?php
                    $temPrincipal = array_reduce(
                        $enderecos,
                        fn($carry, $e) => $carry || ((int) ($e['principal'] ?? 0) === 1),
                        false
                    );
                    $primeiro = true;
                  ?>
                  <fieldset id="fieldset-enderecos" class="row row-cols-1 g-3" disabled>
                    <?php foreach ($enderecos as $endereco):
                      $id = (int) ($endereco['id'] ?? 0);
                      $isPrincipal = (int) ($endereco['principal'] ?? 0) === 1;
                      $checked = $isPrincipal || (!$temPrincipal && $primeiro);
                      $primeiro = false;
                    ?>
                      <div class="col">
                        <label class="border rounded p-3 d-flex address-card">
                          <input type="radio" class="form-check-input me-3" name="endereco_id" value="<?= $id ?>" <?= $checked ? 'checked' : '' ?>>
                          <div>
                            <strong><?= $h($endereco['rotulo'] ?? 'Endere&ccedil;o') ?></strong>
                            <?php if ($isPrincipal): ?>
                              <span class="badge text-bg-secondary ms-2">Principal</span>
                            <?php endif; ?>
                            <div><?= $h($endereco['logradouro'] ?? '') ?>, <?= $h($endereco['numero'] ?? '') ?> <?= $h($endereco['complemento'] ?? '') ?></div>
                            <div><?= $h($endereco['bairro'] ?? '') ?> - <?= $h($endereco['cidade'] ?? '') ?>/<?= $h($endereco['uf'] ?? '') ?></div>
                            <div>CEP: <?= $h($endereco['cep'] ?? '') ?></div>
                          </div>
                        </label>
                      </div>
                    <?php endforeach; ?>
                  </fieldset>
                <?php else: ?>
                  <div class="alert alert-warning mb-0">
                    Voc&ecirc; ainda n&atilde;o cadastrou endere&ccedil;os.
                    <a href="<?= \App\Core\Url::to('/conta/enderecos/novo') ?>" class="alert-link">Adicionar agora</a>.
                  </div>
                <?php endif; ?>
              </div>
            </div>

            <div class="card shadow-sm">
              <div class="card-body">
                <h2 class="h6 text-uppercase text-muted mb-3">Pagamento</h2>
                <div class="row row-cols-1 row-cols-sm-2 g-3">
                  <div class="col">
                    <label class="border rounded p-3 w-100">
                      <input class="form-check-input me-2" type="radio" name="pagamento" value="na_entrega" checked>
                      Dinheiro na entrega
                    </label>
                  </div>
                  <div class="col">
                    <label class="border rounded p-3 w-100">
                      <input class="form-check-input me-2" type="radio" name="pagamento" value="pix">
                      PIX na entrega
                    </label>
                  </div>
                  <div class="col">
                    <label class="border rounded p-3 w-100">
                      <input class="form-check-input me-2" type="radio" name="pagamento" value="cartao">
                      Cart&atilde;o (maquininha)
                    </label>
                  </div>
                  <div class="col">
                    <label class="border rounded p-3 w-100">
                      <input class="form-check-input me-2" type="radio" name="pagamento" value="gateway">
                      Pagamento online (em breve)
                    </label>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-12 col-lg-4 checkout-summary">
            <div class="card shadow-sm">
              <div class="card-body">
                <h2 class="h6 text-uppercase text-muted mb-3">Resumo do pedido</h2>
                <div class="table-responsive">
                  <table class="table align-middle">
                    <thead>
                      <tr>
                        <th>Item</th>
                        <th class="text-end">Qtd</th>
                        <th class="text-end">Pre&ccedil;o</th>
                        <th class="text-end">Subtotal</th>
                      </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($linhas as $linha): ?>
                      <tr>
                        <td><?= $h($linha['nome']) ?></td>
                        <td class="text-end"><?= $fmtQty($linha['quantidade']) ?></td>
                        <td class="text-end">R$ <?= number_format($linha['preco'], 2, ',', '.') ?></td>
                        <td class="text-end">R$ <?= number_format($linha['subtotal'], 2, ',', '.') ?></td>
                      </tr>
                    <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>

                <hr>
                <div class="d-flex justify-content-between">
                  <span class="fw-semibold">Subtotal</span>
                  <span>R$ <?= number_format($subtotal, 2, ',', '.') ?></span>
                </div>
                <div class="text-muted small">Frete e descontos ser&atilde;o calculados na confirma&ccedil;&atilde;o.</div>

                <div class="d-grid mt-4">
                  <button class="btn btn-danger btn-lg" type="submit">Finalizar pedido</button>
                </div>
                <div class="text-center mt-3">
                  <a href="<?= \App\Core\Url::to('/carrinho') ?>" class="small">Voltar ao carrinho</a>
                </div>
              </div>
            </div>
          </div>
        </form>
      <?php endif; ?>
    </div>
  </main>

  <?php require __DIR__ . '/../../partials/footer.php'; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function(){
  const retirada = document.getElementById('entrega_retirada');
  const entrega = document.getElementById('entrega_entrega');
  const fieldset = document.getElementById('fieldset-enderecos');
  const toggle = () => {
    if (!fieldset) return;
    fieldset.disabled = retirada && retirada.checked;
  };
  if (retirada) retirada.addEventListener('change', toggle);
  if (entrega) entrega.addEventListener('change', toggle);
  toggle();
})();
</script>
</body>
</html>
