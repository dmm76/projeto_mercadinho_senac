<h1>Produtos</h1>
<div class="grid">
  <?php foreach ($produtos as $p): ?>
    <div class="card">
      <a href="/produtos/<?= (int)$p['id'] ?>">
        <?php if (!empty($p['imagem'])): ?>
          <img src="/<?= htmlspecialchars($p['imagem']) ?>" alt="<?= htmlspecialchars($p['nome']) ?>">
        <?php endif; ?>
        <h3><?= htmlspecialchars($p['nome']) ?> (<?= htmlspecialchars($p['unidade_sigla']) ?>)</h3>
      </a>
      <p>R$ <?= number_format((float)$p['preco_atual'], 2, ',', '.') ?>
        <?php if (!is_null($p['preco_promocional']) && 
                  (is_null($p['inicio_promo']) || $p['inicio_promo'] <= date('Y-m-d H:i:s')) &&
                  (is_null($p['fim_promo'])    || $p['fim_promo']    >= date('Y-m-d H:i:s'))): ?>
          <small><s>R$ <?= number_format((float)$p['preco_venda'], 2, ',', '.') ?></s></small>
        <?php endif; ?>
      </p>
      <form method="post" action="/carrinho/adicionar/<?= (int)$p['id'] ?>">
        <?php $step = ($p['peso_variavel'] || $p['unidade_sigla']==='KG') ? '0.001' : '1'; ?>
        <input type="number" name="quantidade" value="<?= $step==='1'?'1':'0.100' ?>" min="<?= $step ?>" step="<?= $step ?>">
        <button type="submit">Adicionar ao carrinho</button>
      </form>
    </div>
  <?php endforeach; ?>
</div>
