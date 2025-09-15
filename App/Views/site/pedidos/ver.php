<h1>Pedido #<?= (int)$pedido['id'] ?></h1>
<p><strong>Status:</strong> <?= strtoupper($pedido['status']) ?></p>
<table>
  <thead><tr><th>Produto</th><th>Qtd</th><th>Unitário</th><th>Subtotal</th></tr></thead>
  <tbody>
  <?php $soma = 0.0; foreach ($pedido['itens'] as $it): $sub = (float)$it['preco_unit'] * (float)$it['quantidade']; $soma += $sub; ?>
    <tr>
      <td><?= htmlspecialchars($it['nome']) ?> (<?= htmlspecialchars($it['unidade_sigla']) ?>)</td>
      <td><?= number_format((float)$it['quantidade'], 3, ',', '') ?></td>
      <td>R$ <?= number_format((float)$it['preco_unit'], 2, ',', '.') ?></td>
      <td>R$ <?= number_format($sub, 2, ',', '.') ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<p><strong>Subtotal:</strong> R$ <?= number_format($soma, 2, ',', '.') ?></p>
<p><strong>Total (pedido):</strong> R$ <?= number_format((float)$pedido['total'], 2, ',', '.') ?></p>
