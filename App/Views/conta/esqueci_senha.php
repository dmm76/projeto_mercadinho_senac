<?php

use App\Core\Csrf;
use App\Core\Flash;
use App\Core\Url;

$title = htmlspecialchars($title ?? 'Recuperar senha', ENT_QUOTES, 'UTF-8');
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= $title ?></title>
    <link rel="stylesheet" href="<?= Url::to('/assets/css/bootstrap.min.css') ?>" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
</head>
<body class="bg-light">
<div class="d-flex flex-column wrapper">
    <?php require __DIR__ . '/../partials/navbar.php'; ?>

    <main class="flex-fill">
        <div class="container py-4" style="max-width: 640px;">
            <h1 class="h3 mb-3">Esqueci minha senha</h1>
            <p class="text-muted">Informe o e-mail cadastrado. Enviaremos um link para criar uma nova senha.</p>

            <?php if ($msg = Flash::get('success')): ?>
                <div class="alert alert-success"><?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
            <?php if ($msg = Flash::get('error')): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <form method="post" action="<?= Url::to('/conta/esqueci-senha') ?>" class="card shadow-sm">
                <div class="card-body">
                    <?= Csrf::input() ?>
                    <div class="mb-3">
                        <label class="form-label" for="reset-email">E-mail</label>
                        <input id="reset-email" type="email" name="email" class="form-control" placeholder="você@exemplo.com" required autocomplete="email" />
                    </div>
                    <button type="submit" class="btn btn-danger">Enviar link</button>
                    <a href="<?= Url::to('/login') ?>" class="btn btn-link">Voltar ao login</a>
                </div>
            </form>
        </div>
    </main>

    <?php require __DIR__ . '/../partials/footer.php'; ?>
</div>
<script src="<?= Url::to('/assets/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>