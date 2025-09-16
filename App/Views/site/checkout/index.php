<?php
// Variáveis esperadas: $enderecos (array), $carrinho (array)
// Helperzinho local:
$h = static fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');

// Totais do carrinho (fallback simples)
$subtotal = 0.0;
$itens = is_array($carrinho) ? $carrinho : [];
foreach ($itens as $i) {
    $qtd  = isset($i['quantidade']) ? (float)$i['quantidade'] : (float)($i['qty'] ?? 1);
    $preco= isset($i['preco']) ? (float)$i['preco'] : (float)($i['price'] ?? 0);
    $sub  = isset($i['subtotal']) ? (float)$i['subtotal'] : $qtd * $preco;
    $subtotal += $sub;
}
?>

<div class="container py-4">
  <h1 class="h4 mb-3">Checkout</h1>

  <?php if (empty($itens)): ?>
    <div class="alert alert-warning">
      Seu carrinho está vazio. <a href="/carrinho" class="alert-link">Voltar ao carrinho</a>.
    </div>
    <?php return; ?>
  <?php endif; ?>

  <form method="post" action="/checkout" class="row g-4">
    <input type="hidden" name="csrf" value="<?= \App\Core\Csrf::token() ?>">

    <!-- Coluna esquerda: Entrega/Endereço -->
    <div class="col-12 col-lg-8">
      <div class="card mb-3">
        <div class="card-body">
          <h2 class="h6 mb-3">Forma de entrega</h2>

          <div class="form-check">
            <input class="form-check-input" type="radio" name="entrega" id="entrega_retirada" value="retirada" checked>
            <label class="form-check-label" for="entrega_retirada">
              Retirada no local
            </label>
          </div>
          <div class="form-check mt-2">
            <input class="form-check-input" type="radio" name="entrega" id="entrega_entrega" value="entrega">
            <label class="form-check-label" for="entrega_entrega">
              Entrega em domicílio
            </label>
          </div>
        </div>
      </div>

      <div class="card mb-3" id="card-enderecos">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h2 class="h6 m-0">Endereço de entrega</h2>
            <a href="/conta/enderecos/novo" class="btn btn-sm btn-danger">+ Novo endereço</a>
          </div>

          <?php if (!empty($enderecos)): ?>
            <fieldset id="fieldset-enderecos" disabled>
              <?php
                // marcar principal por padrão; se não houver, marca o primeiro
                $temPrincipal = array_reduce($enderecos, fn($c,$e)=>$c || ((int)($e['principal'] ?? 0) === 1), false);
                $primeiro = true;
              ?>
              <?php foreach ($enderecos as $e): ?>
                <?php
                  $id   = (int)$e['id'];
                  $isPrincipal = (int)($e['principal'] ?? 0) === 1;
                  $checked = $isPrincipal || (!$temPrincipal && $primeiro);
                  $primeiro = false;
                ?>
                <label class="card p-3 d-block mb-2">
                  <div class="d-flex">
                    <div class="me-3 pt-1">
                      <input type="radio" name="endereco_id" value="<?= $id ?>" <?= $checked ? 'checked' : '' ?>>
                    </div>
                    <div>
                      <div class="fw-semibold">
                        <?= $h($e['rotulo'] ?? 'Endereço') ?>
                        <?php if ($isPrincipal): ?>
                          <span class="badge text-bg-secondary ms-2">Principal</span>
                        <?php endif; ?>
                      </div>
                      <div><?= $h($e['logradouro'] ?? '') ?>, <?= $h($e['numero'] ?? '') ?> <?= $h($e['complemento'] ?? '') ?></div>
                      <div><?= $h($e['bairro'] ?? '') ?> - <?= $h($e['cidade'] ?? '') ?>/<?= $h($e['uf'] ?? '') ?></div>
                      <div>CEP: <?= $h($e['cep'] ?? '') ?></div>
                    </div>
                  </div>
                </label>
              <?php endforeach; ?>
            </fieldset>
          <?php else: ?>
            <div class="alert alert-warning mb-0">
              Você ainda não possui endereços. <a href="/conta/enderecos/novo" class="alert-link">Adicionar agora</a>.
            </div>
          <?php endif; ?>
        </div>
      </div>

      <div class="card">
        <div class="card-body">
          <h2 class="h6 mb-3">Forma de pagamento</h2>

          <div class="row row-cols-1 row-cols-sm-2 g-2">
            <div class="col">
              <label class="form-check p-3 border rounded d-block">
                <input class="form-check-input me-2" type="radio" name="pagamento" value="na_entrega" checked>
                Pagar na entrega (dinheiro)
              </label>
            </div>
            <div class="col">
              <label class="form-check p-3 border rounded d-block">
                <input class="form-check-input me-2" type="radio" name="pagamento" value="pix">
                PIX (na entrega)
              </label>
            </div>
            <div class="col">
              <label class="form-check p-3 border rounded d-block">
                <input class="form-check-input me-2" type="radio" name="pagamento" value="cartao">
                Cartão (maquininha na entrega)
              </label>
            </div>
            <div class="col">
              <label class="form-check p-3 border rounded d-block">
                <input class="form-check-input me-2" type="radio" name="pagamento" value="gateway">
                Gateway online (futuro)
              </label>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Coluna direita: Resumo -->
    <div class="col-12 col-lg-4">
      <div class="card">
        <div class="card-body">
          <h2 class="h6 mb-3">Resumo do pedido</h2>

          <div class="table-responsive">
            <table class="table align-middle">
              <thead>
                <tr>
                  <th>Item</th>
                  <th class="text-end">Qtd</th>
                  <th class="text-end">Preço</th>
                  <th class="text-end">Subtotal</th>
                </tr>
              </thead>
              <tbody>
              <?php foreach ($itens as $i): ?>
                <?php
                  $nome = $i['nome'] ?? ($i['name'] ?? 'Produto');
                  $qtd  = isset($i['quantidade']) ? (float)$i['quantidade'] : (float)($i['qty'] ?? 1);
                  $preco= isset($i['preco']) ? (float)$i['preco'] : (float)($i['price'] ?? 0);
                  $sub  = isset($i['subtotal']) ? (float)$i['subtotal'] : $qtd * $preco;
                ?>
                <tr>
                  <td><?= $h($nome) ?></td>
                  <td class="text-end"><?= rtrim(rtrim(number_format($qtd, 3, ',', '.'), '0'), ',') ?></td>
                  <td class="text-end">R$ <?= number_format($preco, 2, ',', '.') ?></td>
                  <td class="text-end">R$ <?= number_format($sub,   2, ',', '.') ?></td>
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
          <!-- Frete/desconto podem ser calculados no back ao finalizar -->
          <div class="mt-3 d-grid">
            <button class="btn btn-primary btn-lg" type="submit">Finalizar pedido</button>
          </div>
          <div class="mt-2 text-center">
            <a href="/carrinho" class="small">Voltar ao carrinho</a>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>

<script>
(function () {
  const entregaRet = document.getElementById('entrega_retirada');
  const entregaEnt = document.getElementById('entrega_entrega');
  const fs = document.getElementById('fieldset-enderecos');

  function toggleEnderecos() {
    if (!fs) return;
    fs.disabled = entregaRet.checked; // desabilita quando retirada
  }
  if (entregaRet && entregaEnt) {
    entregaRet.addEventListener('change', toggleEnderecos);
    entregaEnt.addEventListener('change', toggleEnderecos);
    toggleEnderecos();
  }
})();
</script>
