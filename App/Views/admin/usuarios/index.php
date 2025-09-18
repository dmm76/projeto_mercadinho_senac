<?php
use App\Core\Url;
use App\Core\Flash;
use App\Core\Csrf;

/**
 * @var array<int,array{id:int,nome:string,email:string,perfil:string,ativo:int,criado_em:string}> $usuarios
 * @var array{q?:string,perfil?:string,status?:string} $filters
 */
$usuarios = $usuarios ?? [];
$filters  = $filters ?? ['q' => '', 'perfil' => '', 'status' => ''];
$h = static fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title><?= $h($title ?? 'Usuários') ?></title>
  <link rel="stylesheet" href="<?= \App\Core\Url::to('/assets/css/bootstrap.min.css') ?>" />
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
          <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
            <h1 class="h4 mb-0">Usuários</h1>
            <a href="<?= Url::to('/admin/usuarios/novo') ?>" class="btn btn-danger disabled" title="Em breve">
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
            <div class="col-sm-12 col-md-5">
              <input type="text" name="q" value="<?= $h($filters['q'] ?? '') ?>" class="form-control" placeholder="Buscar por nome ou e-mail">
            </div>
            <div class="col-sm-6 col-md-3 col-lg-2">
              <?php $perfilAtual = $filters['perfil'] ?? ''; ?>
              <select name="perfil" class="form-select">
                <option value=""<?= $perfilAtual === '' ? ' selected' : '' ?>>Todos os perfis</option>
                <option value="admin"<?= $perfilAtual === 'admin' ? ' selected' : '' ?>>Admin</option>
                <option value="gerente"<?= $perfilAtual === 'gerente' ? ' selected' : '' ?>>Gerente</option>
                <option value="operador"<?= $perfilAtual === 'operador' ? ' selected' : '' ?>>Operador</option>
                <option value="cliente"<?= $perfilAtual === 'cliente' ? ' selected' : '' ?>>Cliente</option>
              </select>
            </div>
            <div class="col-sm-6 col-md-3 col-lg-2">
              <?php $statusAtual = $filters['status'] ?? ''; ?>
              <select name="status" class="form-select">
                <option value=""<?= $statusAtual === '' ? ' selected' : '' ?>>Todos</option>
                <option value="ativo"<?= $statusAtual === 'ativo' ? ' selected' : '' ?>>Ativos</option>
                <option value="inativo"<?= $statusAtual === 'inativo' ? ' selected' : '' ?>>Inativos</option>
              </select>
            </div>
            <div class="col-sm-12 col-md-3 col-lg-3 d-grid">
              <button class="btn btn-outline-secondary"><i class="bi bi-search me-1"></i> Filtrar</button>
            </div>
          </form>

          <div class="card shadow-sm">
            <div class="table-responsive">
              <table class="table align-middle table-hover mb-0">
                <thead class="table-light">
                  <tr>
                    <th>#</th>
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
                    <?php $ativo = (int)($usuario['ativo'] ?? 0) === 1; ?>
                    <tr>
                      <td><?= (int)($usuario['id'] ?? 0) ?></td>
                      <td><?= $h($usuario['nome'] ?? '') ?></td>
                      <td><?= $h($usuario['email'] ?? '') ?></td>
                      <td><span class="badge text-bg-secondary text-uppercase"><?= $h($usuario['perfil'] ?? '-') ?></span></td>
                      <td>
                        <span class="badge <?= $ativo ? 'text-bg-success' : 'text-bg-secondary' ?>">
                          <?= $ativo ? 'Ativo' : 'Inativo' ?>
                        </span>
                      </td>
                      <td class="text-end"><?= $h($usuario['criado_em'] ?? '-') ?></td>
                      <td class="text-end">
                        <div class="d-flex justify-content-end gap-1 flex-wrap">
                          <a class="btn btn-outline-secondary btn-sm" href="<?= Url::to('/admin/usuarios/ver') . '?id=' . (int)($usuario['id'] ?? 0) ?>">Ver</a>
                          <a class="btn btn-outline-primary btn-sm" href="<?= Url::to('/admin/usuarios/editar') . '?id=' . (int)($usuario['id'] ?? 0) ?>">Editar</a>
                          <form method="post" action="<?= Url::to('/admin/usuarios/status') ?>" class="d-inline">
                            <?= Csrf::input() ?>
                            <input type="hidden" name="id" value="<?= (int)($usuario['id'] ?? 0) ?>">
                            <input type="hidden" name="acao" value="<?= $ativo ? 'desativar' : 'ativar' ?>">
                            <button type="submit" class="btn btn-sm btn-outline-<?= $ativo ? 'warning' : 'success' ?>">
                              <?= $ativo ? 'Desativar' : 'Ativar' ?>
                            </button>
                          </form>
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

<script src="<?= \App\Core\Url::to('/assets/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
