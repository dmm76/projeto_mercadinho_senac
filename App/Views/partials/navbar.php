<?php

use App\Core\Url;
use App\Core\Auth;

$u = Auth::user();
$isAdmin = $u && ($u['perfil'] === 'admin');
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-danger border-bottom shadow-sm mb-3 py-3">
  <div class="container">
    <a class="navbar-brand text-white" href="<?= Url::to('/') ?>">Mercadinho Borba Gato</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain"
      aria-controls="navbarMain" aria-expanded="false" aria-label="Alternar navegação">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div id="navbarMain" class="collapse navbar-collapse">
      <!-- Esquerda -->
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a href="<?= Url::to('/') ?>" class="nav-link text-white <?= Url::is('/') ? 'active' : '' ?>">Principal</a>
        </li>
        <li class="nav-item">
          <a href="<?= Url::to('/contato') ?>" class="nav-link text-white <?= Url::is('/contato') ? 'active' : '' ?>">Contato</a>
        </li>

        <?php if ($isAdmin): ?>
          <li class="nav-item">
            <a href="<?= Url::to('/admin') ?>" class="nav-link text-white <?= Url::is('/admin') ? 'active' : '' ?>">
              Painel Admin
            </a>
          </li>
        <?php endif; ?>
      </ul>

      <!-- Direita -->
      <ul class="navbar-nav ms-auto align-items-center">
        <li class="nav-item position-relative me-2">
          <a href="<?= Url::to('/carrinho') ?>" class="nav-link text-white position-relative <?= Url::is('/carrinho') ? 'active' : '' ?>">
            <i class="bi bi-cart" style="font-size:22px"></i>
            <span class="badge rounded-pill bg-light text-danger position-absolute top-0 start-100 translate-middle">
              <small><?= (int)($_SESSION['cart_count'] ?? 0) ?></small>
            </span>
            <span class="d-inline d-lg-none ms-1">Carrinho</span>
          </a>
        </li>

        <?php if (Auth::isLoggedIn()): ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle text-white <?= Url::is('/conta') ? 'active' : '' ?>" href="#" id="userMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <?= htmlspecialchars($u['nome']) ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
              <li><a class="dropdown-item" href="<?= Url::to('/conta') ?>">Minha Conta</a></li>
              <li><a class="dropdown-item" href="<?= Url::to('/conta/pedidos') ?>">Meus Pedidos</a></li>
              <li><a class="dropdown-item" href="<?= Url::to('/conta/dados') ?>">Meus Dados</a></li>
              <li><a class="dropdown-item" href="<?= Url::to('/conta/enderecos') ?>">Endereços</a></li>
              <?php if ($isAdmin): ?>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="<?= Url::to('/admin') ?>">Painel Admin</a></li>
              <?php endif; ?>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="<?= Url::to('/logout') ?>">Sair</a></li>
            </ul>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a href="<?= Url::to('/registrar') ?>" class="nav-link text-white">Quero Me Cadastrar</a>
          </li>
          <li class="nav-item">
            <a href="<?= Url::to('/login') ?>" class="nav-link text-white">Entrar</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
