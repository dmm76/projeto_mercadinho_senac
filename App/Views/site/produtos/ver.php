<h1><?= htmlspecialchars($produto['nome']) ?></h1>
<?php if (!empty($produto['imagem'])): ?>
  <img src="/<?= htmlspecialchars($produto['imagem']) ?>" alt="<?= htmlspecialchars($produto['nome']) ?>">
<?php endif; ?>
<p><?= nl2br(htmlspecialchars($produto['descricao'] ?? '')) ?></p>
<p><strong>Unidade:</strong> <?= htmlspecialchars($produto['unidade_sigla']) ?></p>
<p><strong>Preço:</strong> R$ <?= number_format((float)$produto['preco_atual'], 2, ',', '.') ?></p>
<form method="post" action="/carrinho/adicionar/<?= (int)$produto['id'] ?>">
  <?php $step = ($produto['peso_variavel'] || $produto['unidade_sigla']==='KG') ? '0.001' : '1'; ?>
  <label>Quantidade</label>
  <input type="number" name="quantidade" value="<?= $step==='1'?'1':'0.100' ?>" min="<?= $step ?>" step="<?= $step ?>">
  <button type="submit">Adicionar ao carrinho</button>
</form>
