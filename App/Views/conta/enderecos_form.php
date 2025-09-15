<?php

use App\Core\Url;
use App\Core\Flash;
use App\Core\Csrf;

/** @var array<string,mixed> $endereco */
/** @var bool $isEdit */
/** @var string $actionUrl */
/** @var array<int,string> $ufs */
/** @var array<int,string> $errors */

$e = $endereco ?? [];
$errors = $errors ?? [];
$isEdit = (bool)($isEdit ?? false);
$actionPath = isset($actionUrl) && is_string($actionUrl) ? $actionUrl : '/conta/enderecos/novo';
$actionHref = Url::to($actionPath);
$ufs = $ufs ?? ['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'];

$val = fn($k, $d = '') => htmlspecialchars((string)($e[$k] ?? $d));
?>
<!doctype html>
<html lang="pt-br">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title><?= $isEdit ? 'Editar Endereço' : 'Novo Endereço' ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
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

    <?php require dirname(__DIR__) . '/partials/navbar.php'; ?>

    <main class="flex-fill">
      <div class="container py-3">
        <div class="row g-3">
          <div class="col-12 col-lg-3">
            <?php require dirname(__DIR__) . '/partials/conta-sidebar.php'; ?>
          </div>

          <div class="col-12 col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h1 class="h4 mb-0"><?= $isEdit ? 'Editar Endereço' : 'Novo Endereço' ?></h1>
              <a class="btn btn-outline-secondary" href="<?= Url::to('/conta/enderecos') ?>">Voltar</a>
            </div>

            <?php if ($m = Flash::get('error')): ?>
              <div class="alert alert-danger mb-3"><?= htmlspecialchars($m) ?></div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
              <div class="alert alert-warning">
                <strong>Corrija os campos abaixo:</strong>
                <ul class="mb-0">
                  <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                  <?php endforeach; ?>
                </ul>
              </div>
            <?php endif; ?>

            <div class="card shadow-sm">
              <div class="card-body">
                <form method="post"
                  action="<?= \App\Core\Url::to($actionUrl ?? '/conta/enderecos/novo') ?>">
                  <?= Csrf::input() ?>
                  <?php if ($isEdit): ?>
                    <input type="hidden" name="id" value="<?= (int)($e['id'] ?? 0) ?>">
                  <?php endif; ?>

                  <div class="row g-3">
                    <div class="col-12 col-md-6">
                      <label class="form-label">Rótulo</label>
                      <input type="text" name="rotulo" class="form-control"
                        value="<?= $val('rotulo') ?>" placeholder="Casa, Trabalho..."
                        maxlength="50">
                    </div>
                    <div class="col-12 col-md-6">
                      <label class="form-label">Nome do destinatário</label>
                      <input type="text" name="nome" class="form-control"
                        value="<?= $val('nome') ?>" required maxlength="80">
                    </div>

                    <div class="col-6 col-md-3">
                      <label class="form-label">CEP</label>
                      <input type="text" name="cep" class="form-control"
                        value="<?= $val('cep') ?>" placeholder="00000-000" required
                        maxlength="9">
                    </div>
                    <div class="col-6 col-md-7">
                      <label class="form-label">Logradouro</label>
                      <input type="text" name="logradouro" class="form-control"
                        value="<?= $val('logradouro') ?>" required maxlength="120">
                    </div>
                    <div class="col-12 col-md-2">
                      <label class="form-label">Número</label>
                      <input type="text" name="numero" class="form-control"
                        value="<?= $val('numero') ?>" required maxlength="10">
                    </div>

                    <div class="col-12 col-md-6">
                      <label class="form-label">Complemento</label>
                      <input type="text" name="complemento" class="form-control"
                        value="<?= $val('complemento') ?>" maxlength="60">
                    </div>
                    <div class="col-12 col-md-6">
                      <label class="form-label">Bairro</label>
                      <input type="text" name="bairro" class="form-control"
                        value="<?= $val('bairro') ?>" required maxlength="60">
                    </div>

                    <div class="col-12 col-md-8">
                      <label class="form-label">Cidade</label>
                      <input type="text" name="cidade" class="form-control"
                        value="<?= $val('cidade') ?>" required maxlength="80">
                    </div>
                    <div class="col-12 col-md-4">
                      <label class="form-label">UF</label>
                      <select name="uf" class="form-select" required>
                        <option value="" disabled <?= $val('uf') === '' ? 'selected' : '' ?>>Selecione
                        </option>
                        <?php foreach ($ufs as $uf): ?>
                          <option value="<?= $uf ?>" <?= $val('uf') === $uf ? 'selected' : '' ?>>
                            <?= $uf ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>

                    <div class="col-12">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="principal"
                          id="principal" value="1"
                          <?= !empty($e['principal']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="principal">Definir como endereço
                          principal</label>
                      </div>
                    </div>
                  </div>

                  <div class="mt-3 d-flex gap-2">
                    <button class="btn btn-danger"
                      type="submit"><?= $isEdit ? 'Salvar alterações' : 'Cadastrar endereço' ?></button>
                    <a class="btn btn-outline-secondary"
                      href="<?= Url::to('/conta/enderecos') ?>">Cancelar</a>
                  </div>

                </form>
              </div>
            </div>

          </div>
        </div>
      </div>
    </main>

    <?php require dirname(__DIR__) . '/partials/footer.php'; ?>

  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="<?= Url::to('/assets/site/js/script.js') ?>"></script>
</body>

</html>