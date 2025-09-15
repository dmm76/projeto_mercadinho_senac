<?php
use App\Core\Url;
use App\Core\Auth;

/** @var array{id:int,nome:string,email:string,perfil:string,ativo:int}|null $u */
$u = Auth::user();
$nome  = htmlspecialchars($u['nome']  ?? 'Administrador');
$email = htmlspecialchars($u['email'] ?? '');
?>
<aside class="sidebar-sticky">
  <div class="card shadow-sm mb-3">
    <div class="card-body d-flex align-items-center">
      <i class="bi bi-person-circle fs-2 me-2"></i>
      <div>
        <div class="fw-semibold"><?= $nome ?></div>
        <?php if ($email): ?><div class="text-muted small"><?= $email ?></div><?php endif; ?>
      </div>
    </div>
  </div>

  <div class="list-group shadow-sm">
    <a href="<?= Url::to('/admin') ?>" class="list-group-item list-group-item-action <?= Url::is('/admin') ? 'active' : '' ?>">
      <i class="bi bi-speedometer2 me-2"></i>Visão geral
    </a>
    <a href="<?= Url::to('/admin/produtos') ?>" class="list-group-item list-group-item-action <?= Url::is('/admin/produtos') ? 'active' : '' ?>">
      <i class="bi bi-box me-2"></i>Produtos
    </a>
    <a href="<?= Url::to('/admin/categorias') ?>" class="list-group-item list-group-item-action <?= Url::is('/admin/categorias') ? 'active' : '' ?>">
      <i class="bi bi-tags me-2"></i>Categorias
    </a>
    <a href="<?= Url::to('/admin/marcas') ?>" class="list-group-item list-group-item-action <?= Url::is('/admin/marcas') ? 'active' : '' ?>">
      <i class="bi bi-bookmark-star me-2"></i>Marcas
    </a>
    <a href="<?= Url::to('/admin/unidades') ?>" class="list-group-item list-group-item-action <?= Url::is('/admin/unidades') ? 'active' : '' ?>">
      <i class="bi bi-bounding-box-circles me-2"></i>Unidades
    </a>
    <a href="<?= Url::to('/admin/usuarios') ?>" class="list-group-item list-group-item-action <?= Url::is('/admin/usuarios') ? 'active' : '' ?>">
      <i class="bi bi-people me-2"></i>Usuários
    </a>
    <a href="<?= Url::to('/admin/contatos') ?>" class="list-group-item list-group-item-action <?= Url::is('/admin/contatos') ? 'active' : '' ?>">
      <i class="bi bi-envelope me-2"></i>Mensagens
    </a>
    <a href="<?= Url::to('/admin/pedidos') ?>" class="list-group-item list-group-item-action <?= Url::is('/admin/pedidos') ? 'active' : '' ?>">
      <i class="bi bi-receipt me-2"></i>Pedidos
    </a>
    <a href="<?= Url::to('/admin/config') ?>" class="list-group-item list-group-item-action <?= Url::is('/admin/config') ? 'active' : '' ?>">
      <i class="bi bi-gear me-2"></i>Configurações
    </a>
  </div>
</aside>
