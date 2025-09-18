<!doctype html>
<html lang="pt-br">

<head>
  <meta charset="utf-8" />
  <meta name="description" content="Mercadinho Borba Gato: supermercado online com ofertas atualizadas, entrega rapida e catalogo completo de produtos para o dia a dia." />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($title ?? 'Criar conta') ?></title>
  <link rel="stylesheet" href="<?= \App\Core\Url::to('/assets/css/bootstrap.min.css') ?>" />
</head>

<body class="bg-light">
  <div class="d-flex flex-column wrapper">

    <?php require dirname(__DIR__, 2) . '/partials/navbar.php'; ?>

    <main class="flex-fill">
      <div class="container py-4" style="max-width: 720px;">
        <h1 class="h3 mb-4">Criar conta</h1>

        <?php if ($msg = \App\Core\Flash::get('error')): ?>
          <div class="alert alert-danger"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>
        <?php if ($msg = \App\Core\Flash::get('success')): ?>
          <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>

        <form method="post" action="<?= \App\Core\Url::to('/registrar') ?>">
          <input type="hidden" name="csrf" value="<?= \App\Core\Csrf::token() ?>">
          <div class="mb-3">
            <label class="form-label" for="register-nome">Nome</label><input id="register-nome" type="text"
              name="nome" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label" for="register-email">E-mail</label>
            <input id="register-email" type="email" name="email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label" for="register-password">Senha</label>
            <input id="register-password" type="password" name="password" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label" for="register-password2">Confirmar Senha</label>
            <input id="register-password2" type="password" name="password2" class="form-control" required>
          </div>
          <button type="submit" class="btn btn-danger px-4">Registrar</button>
          <a href="<?= \App\Core\Url::to('/login') ?>" class="btn btn-link">Já tenho conta</a>
        </form>
      </div>
    </main>

    <?php require dirname(__DIR__, 2) . '/partials/footer.php'; ?>

  </div>
  <script src="<?= \App\Core\Url::to('/assets/js/bootstrap.bundle.min.js') ?>"></script>
</body>

</html>
