<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title><?= htmlspecialchars($title ?? 'Mercadinho') ?></title>

  <!-- Bootstrap (CDN) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"/>
  <!-- Seu CSS -->
  <link rel="stylesheet" href="<?= \App\Core\Url::to('/assets/site/css/style.css') ?>"/>
  <!-- Seu JS -->
  <script src="<?= \App\Core\Url::to('/assets/site/js/script.js') ?>" defer></script>

  <style>
    .carousel-img{object-fit:cover; max-height:380px;}
    .truncar-3l{
      display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden;
    }
  </style>
</head>
<body>
<div class="d-flex flex-column wrapper">

  <?php require __DIR__ . '/../../partials/site/navbar.php'; ?>

  <main class="flex-fill">
    <div class="container">

      <!-- Carrossel -->
      <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
        <div class="carousel-indicators">
          <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
          <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
          <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>
        <div class="carousel-inner">
          <?php $ASSETS = \App\Core\Url::to('/assets/site/img'); ?>
          <div class="carousel-item active">
            <img src="<?= $ASSETS ?>/banner.jpg" class="d-block w-100 carousel-img" alt="Promoções"/>
          </div>
          <div class="carousel-item">
            <img src="<?= $ASSETS ?>/banca.jpg" class="d-block w-100 carousel-img" alt="Frutas frescas"/>
          </div>
          <div class="carousel-item">
            <img src="<?= $ASSETS ?>/compras01.jpg" class="d-block w-100 carousel-img" alt="Clientes satisfeitos"/>
          </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span><span class="visually-hidden">Anterior</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span><span class="visually-hidden">Próximo</span>
        </button>
      </div>

      <hr class="mt-3"/>

      <!-- Busca + Ordenação + Paginação -->
      <div class="container mt-3">
        <div class="row">
          <div class="col-12 col-md-5">
            <form class="justify-content-center justify-content-md-start mb-3 mb-md-0">
              <div class="input-group input-group-sm">
                <input type="text" class="form-control" placeholder="Digite aqui o que procura"/>
                <button class="btn btn-danger">Buscar</button>
              </div>
            </form>
          </div>
          <div class="col-12 col-md-7">
            <div class="d-flex flex-row-reverse justify-content-center justify-content-md-start">
              <form class="inline-block">
                <select class="form-select form-select-sm">
                  <option>Ordenar pelo nome</option>
                  <option>Ordenar pelo menor preço</option>
                  <option>Ordenar pelo maior preço</option>
                </select>
              </form>
              <nav class="d-inline-block me-3">
                <ul class="pagination pagination-sm my-0">
                  <li class="page-item"><a class="page-link" href="#">1</a></li>
                  <li class="page-item"><a class="page-link" href="#">2</a></li>
                  <li class="page-item disabled"><a class="page-link" href="#">3</a></li>
                  <li class="page-item"><a class="page-link" href="#">4</a></li>
                  <li class="page-item"><a class="page-link" href="#">5</a></li>
                  <li class="page-item"><a class="page-link" href="#">6</a></li>
                </ul>
              </nav>
            </div>
          </div>
        </div>
      </div>

      <hr/>

      <!-- Cards de Produtos (estáticos por enquanto) -->
      <div class="row g-3">
        <?php
        // exemplo de um card (você pode duplicar temporariamente)
        $produtoImg = \App\Core\Url::to('/assets/site/img/banana_orig.webp');
        ?>
        <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2">
          <div class="card text-center bg-light my-3">
            <button class="btn position-absolute end-0 p-2 text-danger" aria-label="Favoritar">
              <i class="bi bi-heart favorito-icon position-absolute top-0 end-0 m-2" style="cursor:pointer;font-size:24px"></i>
            </button>
            <a href="<?= \App\Core\Url::to('/produto') ?>?id=1">
              <img src="<?= $produtoImg ?>" class="card-img-top" alt="Banana Prata">
            </a>
            <div class="card-header">R$ 13,50</div>
            <div class="card-body">
              <h5 class="card-title">Banana Prata</h5>
              <p class="card-text truncar-3l">A banana-prata é uma das variedades mais apreciadas no Brasil...</p>
            </div>
            <div class="card-footer">
              <a href="<?= \App\Core\Url::to('/carrinho') ?>" class="btn btn-danger mt-2 d-block">Adicionar ao Carrinho</a>
              <small class="text-success">320,5 kg em estoque</small>
            </div>
          </div>
        </div>
        <!-- Duplicar cards de exemplo conforme necessidade -->
      </div>
    </div>
  </main>

  <?php require __DIR__ . '/../../partials/site/footer.php'; ?>

</div>

<!-- Bootstrap JS (CDN) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
