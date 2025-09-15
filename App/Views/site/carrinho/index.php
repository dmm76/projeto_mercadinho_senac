<h1>Seu carrinho</h1>

<?php if (!$carrinho): ?>
  <p>Seu carrinho está vazio.</p>
<?php else: ?>
<table>
  <thead>
    <tr><th>Produto</th><th>Qtd</th><th>Preço</th><th>Subtotal</th><th></th></tr>
  </thead>
  <tbody>
  <?php foreach ($carrinho as $pid => $it): ?>
    <?php $p = $it['produto']; $q = (float)$it['quantidade']; $sub = (float)$p['preco_atual'] * $q; ?>
    <tr>
      <td><?= htmlspecialchars($p['nome']) ?></td>
      <td>
        <form method="post" action="/carrinho/atualizar/<?= (int)$pid ?>">
          <?php $step = ($p['peso_variavel'] || $p['unidade_sigla']==='KG') ? '0.001' : '1'; ?>
          <input type="number" name="quantidade" value="<?= number_format($q, $step==='1'?0:3, '.', '') ?>" min="<?= $step ?>" step="<?= $step ?>">
          <button type="submit">Atualizar</button>
        </form>
      </td>
      <td>R$ <?= number_format((float)$p['preco_atual'], 2, ',', '.') ?></td>
      <td>R$ <?= number_format($sub, 2, ',', '.') ?></td>
      <td>
        <form method="post" action="/carrinho/remover/<?= (int)$pid ?>">
          <button type="submit">Remover</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>

<h3>Total: R$ <?= number_format((float)$total, 2, ',', '.') ?></h3>

<form method="post" action="/checkout">
  <!-- se tiver endereços, liste aqui -->
  <!-- <select name="endereco_id"> ... </select> -->
  <input type="hidden" name="entrega" value="retirada">
  <input type="hidden" name="pagamento" value="na_entrega">
  <button type="submit">Finalizar pedido</button>
</form>
<?php endif; ?>
