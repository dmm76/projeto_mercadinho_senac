<h1>Meus pedidos</h1>
<?php if (!$pedidos): ?>
  <p>Você ainda não tem pedidos.</p>
<?php else: ?>
<ul>
  <?php foreach ($pedidos as $p): ?>
    <li>
      <a href="/meus-pedidos/<?= (int)$p['id'] ?>">
        Pedido #<?= (int)$p['id'] ?> — <?= strtoupper($p['status']) ?> —
        Total: R$ <?= number_format((float)$p['total'], 2, ',', '.') ?> —
        <?= date('d/m/Y H:i', strtotime($p['criado_em'])) ?>
      </a>
    </li>
  <?php endforeach; ?>
</ul>
<?php endif; ?>
