<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title><?= htmlspecialchars($title ?? 'Entrar') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"/>
</head>
<body class="bg-light">
<div class="d-flex flex-column wrapper">

  <?php require __DIR__ . '/../../partials/navbar.php'; ?>

  <main class="flex-fill">
    <div class="container py-4" style="max-width: 720px;">
      <h1 class="h3 mb-4">Identifique-se, por favor</h1>

      <?php if ($msg = \App\Core\Flash::get('error')): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($msg) ?></div>
      <?php endif; ?>
      <?php if ($msg = \App\Core\Flash::get('success')): ?>
        <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
      <?php endif; ?>

      <form method="post" action="<?= \App\Core\Url::to('/login') ?>" class="mb-3">
        <input type="hidden" name="csrf" value="<?= \App\Core\Csrf::token() ?>">
        <div class="mb-3">
          <label class="form-label">E-mail</label>
          <input type="email" name="email" class="form-control" placeholder="voce@exemplo.com" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Senha</label>
          <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-danger px-4">Entrar</button>
        <a href="<?= \App\Core\Url::to('/registrar') ?>" class="btn btn-link">Criar conta</a>
      </form>
    </div>
  </main>

  <?php require __DIR__ . '/../../partials/footer.php'; ?>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
