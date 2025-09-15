<?php

use App\Core\Url;
use App\Core\Csrf;

/** @var array<int,array{id:int,rotulo:string,nome:string,cep:string,logradouro:string,numero:string,complemento:?string,bairro:string,cidade:string,uf:string,principal:int}> $enderecos */
$enderecos = $enderecos ?? [];
?>
<!doctype html>
<html lang="pt-br">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title><?= htmlspecialchars($title ?? 'Endereços') ?></title>
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
              <h1 class="h4 mb-0">Endereços</h1>
              <a href="<?= Url::to('/conta/enderecos/novo') ?>" class="btn btn-danger">
                <i class="bi bi-plus-lg me-1"></i>Novo endereço
              </a>
            </div>

            <?php if (!empty($enderecos)): ?>
              <div class="row g-3">
                <?php foreach ($enderecos as $e): ?>
                  <div class="col-12 col-md-6">
                    <div class="card shadow-sm h-100">
                      <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                          <div>
                            <div class="fw-semibold">
                              <?= htmlspecialchars($e['rotulo'] ?: 'Endereço') ?>
                              <?php if ((int)$e['principal'] === 1): ?>
                                <span class="badge bg-success ms-2">Principal</span>
                              <?php endif; ?>
                            </div>
                            <div class="text-muted small mb-2"><?= htmlspecialchars($e['nome']) ?>
                            </div>
                          </div>
                          <div class="text-muted small">#<?= (int)$e['id'] ?></div>
                        </div>

                        <div class="small">
                          <div><?= htmlspecialchars($e['logradouro']) ?>,
                            <?= htmlspecialchars($e['numero']) ?><?= $e['complemento'] ? ' - ' . htmlspecialchars($e['complemento']) : '' ?>
                          </div>
                          <div><?= htmlspecialchars($e['bairro']) ?> -
                            <?= htmlspecialchars($e['cidade']) ?>/<?= htmlspecialchars($e['uf']) ?>
                          </div>
                          <div>CEP: <?= htmlspecialchars($e['cep']) ?></div>
                        </div>
                      </div>
                      <div class="card-footer bg-white d-flex flex-wrap gap-2">
                        <!-- EDITAR: GET fixo + id na query -->
                        <a href="<?= Url::to('/conta/enderecos/editar') . '?id=' . (int)$e['id'] ?>"
                          class="btn btn-sm btn-outline-primary">Editar</a>

                        <!-- EXCLUIR: POST fixo + id hidden -->
                        <form method="post" action="<?= Url::to('/conta/enderecos/excluir') ?>"
                          onsubmit="return confirm('Excluir este endereço?')">
                          <?= Csrf::input() ?>
                          <input type="hidden" name="id" value="<?= (int)$e['id'] ?>">
                          <button class="btn btn-sm btn-outline-danger" type="submit">Excluir</button>
                        </form>

                        <?php if ((int)$e['principal'] !== 1): ?>
                          <!-- DEFINIR PRINCIPAL: POST fixo + id hidden -->
                          <form method="post" action="<?= Url::to('/conta/enderecos/principal') ?>">
                            <?= Csrf::input() ?>
                            <input type="hidden" name="id" value="<?= (int)$e['id'] ?>">
                            <button class="btn btn-sm btn-outline-secondary" type="submit">Definir como
                              principal</button>
                          </form>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php else: ?>
              <div class="card shadow-sm">
                <div class="card-body">
                  <p class="mb-2">Você ainda não cadastrou endereços.</p>
                  <a href="<?= Url::to('/conta/enderecos/novo') ?>"
                    class="btn btn-primary btn-sm">Cadastrar endereço</a>
                </div>
              </div>
            <?php endif; ?>

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