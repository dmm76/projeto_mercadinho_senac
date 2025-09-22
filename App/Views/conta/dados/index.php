<?php

use App\Core\Url;
use App\Core\Auth;
use App\Core\Flash;
use App\Core\Csrf;

/** @var array{id:int,nome:string,email:string,perfil:string,ativo:int}|null $user */
/** @var array{telefone?:?string,cpf?:?string,nascimento?:?string} $cliente */
/** @var string $perfilAction */
/** @var string $senhaAction */

$user        = $user ?? Auth::user();
$nome        = htmlspecialchars($user['nome'] ?? '');
$email       = htmlspecialchars($user['email'] ?? '');
$tel         = isset($cliente['telefone']) ? htmlspecialchars((string)$cliente['telefone']) : '';
$perfilAction = $perfilAction ?? Url::to('/conta/dados/perfil');
$senhaAction  = $senhaAction  ?? Url::to('/conta/dados/senha');
$cpf         = isset($cliente['cpf']) ? htmlspecialchars((string)$cliente['cpf']) : '';
$nascimento  = isset($cliente['nascimento']) ? htmlspecialchars((string)$cliente['nascimento']) : '';

?>
<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title><?= htmlspecialchars($title ?? 'Meus Dados') ?></title>
    <link rel="stylesheet" href="<?= \App\Core\Url::to('/assets/css/bootstrap.min.css') ?>" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?= Url::to('/assets/site/css/style.css') ?>" />
    <style>
        .sidebar-sticky {
            position: sticky;
            top: 1rem
        }
    </style>
</head>

<body>
    <div class="d-flex flex-column wrapper">

        <?php require __DIR__ . '/../../partials/navbar.php'; ?>

        <main class="flex-fill">
            <div class="container py-3">
                <div class="row g-3">
                    <div class="col-12 col-lg-3">
                        <?php require __DIR__ . '/../../partials/conta-sidebar.php'; ?>
                    </div>

                    <div class="col-12 col-lg-9">
                        <h1 class="h4 mb-3">Meus Dados</h1>

                        <?php if ($m = Flash::get('success')): ?>
                            <div class="alert alert-success"><?= htmlspecialchars($m) ?></div>
                        <?php endif; ?>
                        <?php if ($m = Flash::get('error')): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($m) ?></div>
                        <?php endif; ?>

                        <div class="row g-3">
                            <!-- Informações de Perfil -->
                            <div class="col-12 col-lg-6">
                                <div class="card shadow-sm">
                                    <div class="card-header bg-white"><strong>Informações de Perfil</strong></div>
                                    <div class="card-body">
                                        <form method="post" action="<?= $perfilAction ?>">
                                            <?= Csrf::input() ?>
                                            <div class="mb-3">
                                                <label class="form-label" for="conta-nome">Nome</label>
                                                <input id="conta-nome" type="text" name="nome" class="form-control" value="<?= $nome ?>"
                                                    required maxlength="120">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label" for="conta-email">E-mail</label>
                                                <input id="conta-email" type="email" class="form-control" value="<?= $email ?>" disabled>
                                                <div class="form-text">Para alterar o e-mail, contate o suporte.</div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label" for="conta-telefone">Telefone (opcional)</label>
                                                <input id="conta-telefone" type="text" name="telefone" class="form-control"
                                                    value="<?= $tel ?>" placeholder="(11) 91234-5678" maxlength="20"
                                                    pattern="^\(?\d{2}\)?\s?\d{4,5}-?\d{4}$">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label" for="conta-cpf">CPF (opcional)</label>
                                                <input id="conta-cpf" type="text" name="cpf" class="form-control"
                                                    value="<?= $cpf ?>" placeholder="000.000.000-00" maxlength="14" inputmode="numeric">
                                                <div class="form-text">Preencha se quiser nota fiscal no CPF ou para agilizar estornos.</div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label" for="conta-nascimento">Data de nascimento (opcional)</label>
                                                <input id="conta-nascimento" type="date" name="nascimento" class="form-control"
                                                    value="<?= $nascimento ?>">
                                            </div>

                                            <div class="d-flex gap-2">
                                                <button class="btn btn-danger" type="submit">Salvar alterações</button>
                                                <a class="btn btn-outline-secondary"
                                                    href="<?= Url::to('/conta') ?>">Cancelar</a>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Alterar Senha -->
                            <div class="col-12 col-lg-6">
                                <div class="card shadow-sm">
                                    <div class="card-header bg-white"><strong>Alterar Senha</strong></div>
                                    <div class="card-body">
                                        <form method="post" action="<?= $senhaAction ?>">
                                            <?= Csrf::input() ?>
                                            <div class="mb-3">
                                                <label class="form-label" for="conta-senha-atual">Senha atual</label>
                                                <input id="conta-senha-atual" type="password" name="senha_atual" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label" for="conta-senha-nova">Nova senha</label>
                                                <input id="conta-senha-nova" type="password" name="senha" class="form-control" required
                                                    minlength="6">
                                                <div class="form-text">Mínimo de 6 caracteres.</div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label" for="conta-senha-confirma">Confirmar nova senha</label>
                                                <input id="conta-senha-confirma" type="password" name="senha2" class="form-control" required
                                                    minlength="6">
                                            </div>
                                            <button class="btn btn-outline-primary" type="submit">Atualizar
                                                senha</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </main>

        <?php require __DIR__ . '/../../partials/footer.php'; ?>

    </div>
    <script src="<?= \App\Core\Url::to('/assets/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= Url::to('/assets/site/js/script.js') ?>"></script>
</body>

</html>