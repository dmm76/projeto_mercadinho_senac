<?php
use App\Core\Url;
use App\Core\Auth;

$u = Auth::user();
$act = fn(string $p) => Url::is($p) ? 'active' : '';
?>
<aside class="sidebar-sticky">
  <div class="list-group shadow-sm">
    <a href="<?= Url::to('/conta') ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?= $act('/conta') ?>">
      <span><i class="bi bi-person-circle me-2"></i> Minha Conta</span>
    </a>
    <a href="<?= Url::to('/conta/pedidos') ?>" class="list-group-item list-group-item-action <?= $act('/conta/pedidos') ?>">
      <i class="bi bi-bag me-2"></i> Meus Pedidos
    </a>
    <a href="<?= Url::to('/conta/dados') ?>" class="list-group-item list-group-item-action <?= $act('/conta/dados') ?>">
      <i class="bi bi-gear me-2"></i> Meus Dados
    </a>
    <a href="<?= Url::to('/conta/enderecos') ?>" class="list-group-item list-group-item-action <?= $act('/conta/enderecos') ?>">
      <i class="bi bi-geo-alt me-2"></i> Endereços
    </a>
  </div>

  <div class="mt-3">
    <a href="<?= Url::to('/logout') ?>" class="btn btn-outline-secondary w-100">
      <i class="bi bi-box-arrow-right me-1"></i> Sair
    </a>
  </div>
</aside>
