<?php
use App\Core\Url;

/** @var array{id:int,nome:string,email:string,perfil:string,ativo:int,criado_em:string} $usuario */
$usuario = $usuario ?? [];
$h = static fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$ativo = (int)($usuario['ativo'] ?? 0) === 1;
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title><?= $h($title ?? 'UsuÃ¡rio') ?></title>
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
            <h1 class="h4 mb-0">UsuÃ¡rio #<?= (int)($usuario['id'] ?? 0) ?></h1>
            <div class="d-flex gap-2">
              <a href="<?= Url::to('/admin/usuarios/editar') . '?id=' . (int)($usuario['id'] ?? 0) ?>" class="btn btn-outline-primary">
                <i class="bi bi-pencil-square me-1"></i>Editar
              </a>
              <a href="<?= Url::to('/admin/usuarios') ?>" class="btn btn-outline-secondary">Voltar</a>
            </div>
          </div>

          <div class="card shadow-sm">
            <div class="card-body">
              <dl class="row mb-0">
                <dt class="col-sm-3">Nome</dt>
                <dd class="col-sm-9"><?= $h($usuario['nome'] ?? '') ?></dd>

                <dt class="col-sm-3">E-mail</dt>
                <dd class="col-sm-9"><?= $h($usuario['email'] ?? '') ?></dd>

                <dt class="col-sm-3">Perfil</dt>
                <dd class="col-sm-9"><span class="badge text-bg-secondary text-uppercase"><?= $h($usuario['perfil'] ?? '-') ?></span></dd>

                <dt class="col-sm-3">Status</dt>
                <dd class="col-sm-9">
                  <span class="badge <?= $ativo ? 'text-bg-success' : 'text-bg-secondary' ?>">
                    <?= $ativo ? 'Ativo' : 'Inativo' ?>
                  </span>
                </dd>

                <dt class="col-sm-3">Criado em</dt>
                <dd class="col-sm-9"><?= $h($usuario['criado_em'] ?? '-') ?></dd>
              </dl>
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