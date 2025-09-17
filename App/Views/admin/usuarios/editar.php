<?php
use App\Core\Url;
use App\Core\Csrf;

/** @var array{id:int,nome:string,email:string,perfil:string,ativo:int,criado_em:string} $usuario */
$usuario = $usuario ?? [];
$h = static fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$perfis = ['admin' => 'Admin', 'gerente' => 'Gerente', 'operador' => 'Operador', 'cliente' => 'Cliente'];
$perfilAtual = $usuario['perfil'] ?? 'cliente';
$ativo = (int)($usuario['ativo'] ?? 0) === 1;
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title><?= $h($title ?? 'Editar usuário') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <link rel="stylesheet" href="<?= Url::to('/assets/site/css/style.css') ?>" />
  <style>.sidebar-sticky{position:sticky;top:1rem;}</style>
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
            <h1 class="h4 mb-0">Editar usuário</h1>
            <a href="<?= Url::to('/admin/usuarios') ?>" class="btn btn-outline-secondary">Voltar</a>
          </div>

          <div class="card shadow-sm">
            <div class="card-body">
              <form method="post" action="<?= Url::to('/admin/usuarios/editar') ?>" class="row g-3">
                <?= Csrf::input() ?>
                <input type="hidden" name="id" value="<?= (int)($usuario['id'] ?? 0) ?>">

                <div class="col-12 col-md-6">
                  <label for="u-nome" class="form-label">Nome</label>
                  <input id="u-nome" name="nome" type="text" class="form-control" value="<?= $h($usuario['nome'] ?? '') ?>" required>
                </div>

                <div class="col-12 col-md-6">
                  <label for="u-email" class="form-label">E-mail</label>
                  <input id="u-email" name="email" type="email" class="form-control" value="<?= $h($usuario['email'] ?? '') ?>" required>
                </div>

                <div class="col-12 col-md-6">
                  <label for="u-perfil" class="form-label">Perfil</label>
                  <select id="u-perfil" name="perfil" class="form-select" required>
                    <?php foreach ($perfis as $valor => $label): ?>
                      <option value="<?= $valor ?>"<?= $valor === $perfilAtual ? ' selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div class="col-12 col-md-6">
                  <label class="form-label">Status</label>
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="u-ativo" name="ativo" <?= $ativo ? 'checked' : '' ?>>
                    <label class="form-check-label" for="u-ativo">Ativo</label>
                  </div>
                </div>

                <div class="col-12 text-end">
                  <button type="submit" class="btn btn-danger">Salvar</button>
                </div>
              </form>
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