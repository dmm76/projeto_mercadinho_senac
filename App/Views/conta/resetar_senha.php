<?php

use App\Core\Csrf;
use App\Core\Flash;
use App\Core\Url;

$title = htmlspecialchars($title ?? 'Definir nova senha', ENT_QUOTES, 'UTF-8');
$token = isset($token) ? htmlspecialchars((string) $token, ENT_QUOTES, 'UTF-8') : '';
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
            <h1 class="h3 mb-3">Definir nova senha</h1>
            <p class="text-muted">Informe uma nova senha para acessar sua conta.</p>

            <?php if ($msg = Flash::get('success')): ?>
                <div class="alert alert-success"><?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
            <?php if ($msg = Flash::get('error')): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <form method="post" action="<?= Url::to('/conta/resetar-senha') ?>" class="card shadow-sm">
                <div class="card-body">
                    <?= Csrf::input() ?>
                    <input type="hidden" name="token" value="<?= $token ?>" />
                    <div class="mb-3">
                        <label class="form-label" for="nova-senha">Nova senha</label>
                        <input id="nova-senha" type="password" name="senha" class="form-control" required minlength="6" autocomplete="new-password" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="confirmacao-senha">Confirme a nova senha</label>
                        <input id="confirmacao-senha" type="password" name="senha_confirmacao" class="form-control" required minlength="6" autocomplete="new-password" />
                    </div>
                    <button type="submit" class="btn btn-danger">Salvar nova senha</button>
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