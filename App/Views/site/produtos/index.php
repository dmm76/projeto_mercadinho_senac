<?php /** app/Views/site/produtos/index.php */ ?>

<!doctype html>

<html lang="pt-br">

<head>

  <meta charset="utf-8"/>
  <meta name="description" content="Mercadinho Borba Gato: supermercado online com ofertas atualizadas, entrega rapida e catalogo completo de produtos para o dia a dia."/>

  <meta name="viewport" content="width=device-width, initial-scale=1"/>

  <title><?= htmlspecialchars($title ?? 'Produtos') ?></title>



  <link rel="stylesheet" href="<?= \App\Core\Url::to('/assets/css/bootstrap.min.css') ?>"/>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"/>

  <link rel="stylesheet" href="<?= \App\Core\Url::to('/assets/site/css/style.css') ?>"/>

</head>

<body>

<div class="d-flex flex-column wrapper">

  <?php require __DIR__ . '/../../partials/navbar.php'; ?>



  <main class="flex-fill">

    <div class="container py-4">

      <div class="d-flex justify-content-between flex-wrap align-items-center mb-3 gap-2">

        <h1 class="h3 mb-0">Produtos</h1>

        <a class="btn btn-outline-secondary btn-sm" href="<?= \App\Core\Url::to('/') ?>">Voltar a vitrine</a>

      </div>



      <?php

        $catalogBasePath = $catalogBasePath ?? '/produtos';

        require dirname(__DIR__) . '/partials/catalogo-grid.php';

      ?>

    </div>

  </main>



  <?php require __DIR__ . '/../../partials/footer.php'; ?>

</div>



<script src="<?= \App\Core\Url::to('/assets/js/bootstrap.bundle.min.js') ?>"></script>

</body>

</html>



