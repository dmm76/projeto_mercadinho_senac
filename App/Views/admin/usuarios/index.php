<?php
use App\Core\Url;
use App\Core\Flash;

/** @var array<int,array{id:int,nome:string,email:string,perfil:string,status:string,criado_em:string}> $usuarios */
$usuarios = $usuarios ?? [];
$filters = $filters ?? ['q' => '', 'perfil' => '', 'status' => ''];
$h = static fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title><?= $h($title ?? 'Usuários') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <link rel="stylesheet" href="<?= Url::to('/assets/site/css/style.css') ?>" />
  <style>
    .sidebar-sticky { position: sticky; top: 1rem; }
  </style>
</head>
<body>
<div class="d-flex flex-column wrapper">
  <?php require dirname(__DIR__, 2) . '/partials/navbar.php'; ?>

  <main class="flex-fill">
    <div class="container py-3">
      <div class="row g-3">
        <div class="col-12 col-lg-3">
          <?php require dirname(__DIR__, 2) . '/partials/admin-sidebar.php'; ?>
        </div>

        <div class="col-12 col-lg-9">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <h1 class="h4 mb-0">Usuários</h1>
            <a href="<?= Url::to('/admin/usuarios/novo') ?>" class="btn btn-danger">
              <i class="bi bi-plus-lg me-1"></i> Novo usuário
            </a>
          </div>

          <?php if ($msg = Flash::get('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <?= $h($msg) ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
          <?php endif; ?>
          <?php if ($msg = Flash::get('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <?= $h($msg) ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
          <?php endif; ?>

          <form class="row gy-2 gx-2 mb-3" method="get" action="<?= Url::to('/admin/usuarios') ?>">
            <div class="col-sm-4 col-md-5 col-lg-4">
              <input type="text" name="q" value="<?= $h($filters['q'] ?? '') ?>" class="form-control" placeholder="Buscar por nome ou e-mail">
            </div>
            <div class="col-sm-4 col-md-3 col-lg-3">
              <select name="perfil" class="form-select">
                <?php $perfil = $filters['perfil'] ?? ''; ?>
                <option value=""<?= $perfil === '' ? ' selected' : '' ?>>Todos os perfis</option>
                <option value="admin"<?= $perfil === 'admin' ? ' selected' : '' ?>>Admin</option>
                <option value="gerente"<?= $perfil === 'gerente' ? ' selected' : '' ?>>Gerente</option>
                <option value="atendente"<?= $perfil === 'atendente' ? ' selected' : '' ?>>Atendente</option>
              </select>
            </div>
            <div class="col-sm-4 col-md-3 col-lg-3">
              <?php $status = $filters['status'] ?? ''; ?>
              <select name="status" class="form-select">
                <option value=""<?= $status === '' ? ' selected' : '' ?>>Todos os status</option>
                <option value="ativo"<?= $status === 'ativo' ? ' selected' : '' ?>>Ativo</option>
                <option value="inativo"<?= $status === 'inativo' ? ' selected' : '' ?>>Inativo</option>
              </select>
            </div>
            <div class="col-sm-12 col-md-3 col-lg-2 d-grid">
              <button class="btn btn-outline-secondary"><i class="bi bi-search me-1"></i> Filtrar</button>
            </div>
          </form>

          <div class="card shadow-sm">
            <div class="table-responsive">
              <table class="table align-middle table-hover mb-0">
                <thead class="table-light">
                <tr>
                  <th>ID</th>
                  <th>Nome</th>
                  <th>E-mail</th>
                  <th>Perfil</th>
                  <th>Status</th>
                  <th class="text-end">Criado em</th>
                  <th class="text-end">Ações</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                  <?php
                    $perfil = $usuario['perfil'] ?? '-';
                    $statusUsuario = $usuario['status'] ?? 'inativo';
                    $ativo = strtolower($statusUsuario) === 'ativo';
                  ?>
                  <tr>
                    <td><?= (int)($usuario['id'] ?? 0) ?></td>
                    <td><?= $h($usuario['nome'] ?? '') ?></td>
                    <td><?= $h($usuario['email'] ?? '') ?></td>
                    <td><span class="badge text-bg-secondary text-uppercase"><?= $h($perfil) ?></span></td>
                    <td>
                      <span class="badge <?= $ativo ? 'text-bg-success' : 'text-bg-secondary' ?>">
                        <?= $ativo ? 'Ativo' : 'Inativo' ?>
                      </span>
                    </td>
                    <td class="text-end"><?= $h($usuario['criado_em'] ?? '-') ?></td>
                    <td class="text-end">
                      <div class="btn-group btn-group-sm">
                        <a class="btn btn-outline-secondary" href="<?= Url::to('/admin/usuarios/' . (int)($usuario['id'] ?? 0)) ?>">Ver</a>
                        <a class="btn btn-outline-primary" href="<?= Url::to('/admin/usuarios/' . (int)($usuario['id'] ?? 0) . '/editar') ?>">Editar</a>
                        <?php if ($ativo): ?>
                          <a class="btn btn-outline-warning" href="<?= Url::to('/admin/usuarios/' . (int)$usuario['id'] . '/desativar') ?>">Desativar</a>
                        <?php else: ?>
                          <a class="btn btn-outline-success" href="<?= Url::to('/admin/usuarios/' . (int)$usuario['id'] . '/ativar') ?>">Ativar</a>
                        <?php endif; ?>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
                <?php if (empty($usuarios)): ?>
                  <tr>
                    <td colspan="7" class="text-center text-muted py-4">Nenhum usuário encontrado.</td>
                  </tr>
                <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <?php require dirname(__DIR__, 2) . '/partials/footer.php'; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


