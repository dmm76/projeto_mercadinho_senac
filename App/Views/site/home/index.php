<?php /** app/Views/site/home/index.php */ ?>

<!doctype html>

<html lang="pt-br">

<head>

  <meta charset="utf-8"/>

  <meta name="viewport" content="width=device-width,initial-scale=1"/>

  <title><?= htmlspecialchars($title ?? 'Mercadinho Borba Gato') ?></title>



  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"/>

  <link rel="stylesheet" href="<?= \App\Core\Url::to('/assets/site/css/style.css') ?>"/>



  <style>

    .carousel-img { object-fit: cover; max-height: 380px; }

  </style>

</head>

<body>

<div class="d-flex flex-column wrapper">



  <?php require __DIR__ . '/../../partials/navbar.php'; ?>



  <main class="flex-fill">

    <div class="container py-3">



      <?php $ASSETS = \App\Core\Url::to('/assets/site/img'); ?>

      <div id="carouselHero" class="carousel slide mb-4" data-bs-ride="carousel" data-bs-interval="5000">

        <div class="carousel-indicators">

          <button type="button" data-bs-target="#carouselHero" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>

          <button type="button" data-bs-target="#carouselHero" data-bs-slide-to="1" aria-label="Slide 2"></button>

          <button type="button" data-bs-target="#carouselHero" data-bs-slide-to="2" aria-label="Slide 3"></button>

        </div>

        <div class="carousel-inner">

          <div class="carousel-item active">

            <img src="<?= $ASSETS ?>/banner.jpg" class="d-block w-100 carousel-img" alt="Promocoes">

          </div>

          <div class="carousel-item">

            <img src="<?= $ASSETS ?>/banca.jpg" class="d-block w-100 carousel-img" alt="Frutas frescas">

          </div>

          <div class="carousel-item">

            <img src="<?= $ASSETS ?>/compras01.jpg" class="d-block w-100 carousel-img" alt="Clientes satisfeitos">

          </div>

        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#carouselHero" data-bs-slide="prev">

          <span class="carousel-control-prev-icon" aria-hidden="true"></span>

          <span class="visually-hidden">Anterior</span>

        </button>

        <button class="carousel-control-next" type="button" data-bs-target="#carouselHero" data-bs-slide="next">

          <span class="carousel-control-next-icon" aria-hidden="true"></span>

          <span class="visually-hidden">Proximo</span>

        </button>

      </div>



      <?php

        $catalogBasePath = $catalogBasePath ?? '/';

        require dirname(__DIR__) . '/partials/catalogo-grid.php';

      ?>



    </div>

  </main>



  <?php require __DIR__ . '/../../partials/footer.php'; ?>

</div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

  (function () {

    var el = document.getElementById('carouselHero');

    if (!el) return;

    function init() {

      if (window.bootstrap && bootstrap.Carousel) {

        new bootstrap.Carousel(el, {interval: 5000, ride: true});

      } else {

        setTimeout(init, 50);

      }

    }

    init();

  })();

</script>

</body>

</html>

