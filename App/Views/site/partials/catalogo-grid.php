<?php

use App\Core\Csrf;
use App\Core\Flash;
use App\Core\Url;

$produtos = $produtos ?? [];
$filters = $filters ?? ['q' => '', 'ordem' => 'novidades', 'favoritos' => false];
$pagination = $pagination ?? ['total' => count($produtos), 'page' => 1, 'pages' => 1, 'per_page' => max(1, count($produtos)), 'has_previous' => false, 'has_next' => false];
$clienteId = $clienteId ?? null;
$catalogBasePath = $catalogBasePath ?? '/';

$h = static fn($value): string => htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');

$ordemAtual = $filters['ordem'] ?? 'novidades';
$qAtual = $filters['q'] ?? '';
$favoritosOnly = !empty($filters['favoritos']);
$baseUrl = Url::to($catalogBasePath);

$baseQuery = [
  'q' => $qAtual,
  'ordem' => $ordemAtual,
];
if ($favoritosOnly) {
  $baseQuery['favoritos'] = '1';
}

$buildLink = function (array $overrides = []) use ($baseUrl, $baseQuery): string {
  $query = array_merge($baseQuery, $overrides);
  if (isset($query['q']) && $query['q'] === '') {
    unset($query['q']);
  }
  if (isset($query['favoritos']) && (!$query['favoritos'] || $query['favoritos'] === '0')) {
    unset($query['favoritos']);
  }
  if (isset($query['page']) && ((int)$query['page']) <= 1) {
    unset($query['page']);
  }
  $qs = http_build_query($query);
  return $baseUrl . ($qs !== '' ? ('?' . $qs) : '');
};

$orderOptions = [
  'novidades' => 'Novidades',
  'nome' => 'Nome (A-Z)',
  'preco_asc' => 'Menor preco',
  'preco_desc' => 'Maior preco',
];

$currentUrl = $_SERVER['REQUEST_URI'] ?? $baseUrl;
?>

<?php if ($msg = Flash::get('success')): ?>
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <?= $h($msg) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
  </div>
<?php endif; ?>
<?php if ($msg = Flash::get('error')): ?>
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <?= $h($msg) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
  </div>
<?php endif; ?>

<form class="row gy-2 gx-2 align-items-end mb-3" method="get" action="<?= $baseUrl ?>">
  <div class="col-12 col-md-5 col-lg-4">
    <label class="form-label mb-1" for="catalogo-busca">Buscar produtos</label>
    <input id="catalogo-busca" type="text" name="q" value="<?= $h($qAtual) ?>" class="form-control" placeholder="Digite o nome, SKU ou descricao">
  </div>
  <div class="col-12 col-sm-6 col-md-3 col-lg-3">
    <label class="form-label mb-1" for="catalogo-ordem">Ordenar por</label>
    <select class="form-select" name="ordem" id="catalogo-ordem">
      <?php foreach ($orderOptions as $value => $label): ?>
        <option value="<?= $value ?>" <?= $ordemAtual === $value ? 'selected' : '' ?>><?= $label ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <?php if ($clienteId): ?>
    <div class="col-12 col-sm-6 col-md-3 col-lg-3">
      <div class="form-check mt-4">
        <input class="form-check-input" type="checkbox" value="1" id="favoritosCheck" name="favoritos"
          <?= $favoritosOnly ? 'checked' : '' ?>>
        <label class="form-check-label" for="favoritosCheck">Somente favoritos</label>
      </div>
    </div>
  <?php endif; ?>
  <div class="col-12 col-md-2 col-lg-2 d-grid">
    <button class="btn btn-danger" type="submit"><i class="bi bi-filter me-1"></i>Filtrar</button>
  </div>
</form>

<?php if (empty($produtos)): ?>
  <div class="alert alert-warning">
    Nenhum produto encontrado.
    <a class="alert-link" href="<?= Url::to('/') ?>">Voltar para a pagina inicial</a>.
  </div>
<?php else: ?>
  <div class="row g-3">
    <?php foreach ($produtos as $produto):
      $id = (int)($produto['id'] ?? 0);
      $nome = $produto['nome'] ?? 'Produto';
      $descricao = $produto['descricao'] ?? '';
      $imagemRel = $produto['imagem'] ?? null;
      $imagem = $imagemRel ? Url::to('/' . ltrim((string)$imagemRel, '/')) : Url::to('/assets/site/img/produtos.jpg');
      $precoVenda = (float)($produto['preco_venda'] ?? 0);
      $precoAtual = (float)($produto['preco_atual'] ?? $precoVenda);
      $pesoVariavel = (int)($produto['peso_variavel'] ?? 0) === 1;
      $unidadeSigla = strtoupper((string)($produto['unidade_sigla'] ?? ''));
      $isKg = $pesoVariavel || $unidadeSigla === 'KG';
      $step = $isKg ? 0.001 : 1;
      $valorInicial = $isKg ? 0.250 : 1;
      $estoque = $produto['estoque_qtd'] ?? null;
      $isFavorito = !empty($produto['is_favorito']);
      $heartIcon = $isFavorito ? 'bi-heart-fill' : 'bi-heart';
      $heartTitle = $isFavorito ? 'Remover dos favoritos' : 'Adicionar aos favoritos';
      $heartClass = $isFavorito ? 'text-danger' : 'text-muted';
    ?>
      <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-3">
        <div class="card h-100 shadow-sm position-relative">
          <form method="post" action="<?= Url::to('/favoritos/toggle') ?>" class="position-absolute top-0 end-0 p-2">
            <?= Csrf::input() ?>
            <input type="hidden" name="produto_id" value="<?= $id ?>">
            <input type="hidden" name="redirect" value="<?= $h($currentUrl) ?>">
            <button type="submit" class="btn btn-link p-0" title="<?= $h($heartTitle) ?>">
              <i class="bi <?= $heartIcon ?> <?= $heartClass ?>" style="font-size:20px"></i>
            </button>
          </form>

          <a href="<?= Url::to('/produtos/' . $id) ?>" class="text-decoration-none">
            <img src="<?= $h($imagem) ?>" class="card-img-top" alt="<?= $h($nome) ?>"
              style="object-fit:cover;height:190px;">
          </a>

          <div class="card-body d-flex flex-column">
            <div class="mb-2">
              <span class="fs-5 text-danger fw-semibold">R$ <?= number_format($precoAtual, 2, ',', '.') ?></span>
              <?php if ($precoVenda > 0 && $precoAtual < $precoVenda): ?>
                <small class="text-muted text-decoration-line-through ms-1">R$
                  <?= number_format($precoVenda, 2, ',', '.') ?></small>
              <?php endif; ?>
              <?php if ($isKg): ?>
                <small class="text-muted"> / kg</small>
              <?php endif; ?>
            </div>
            <h5 class="card-title fw-semibold"><?= $h($nome) ?></h5>
            <p class="card-text text-muted flex-grow-1"
              style="display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;">
              <?= $descricao !== '' ? $h($descricao) : '&nbsp;' ?>
            </p>
            <div class="mt-auto">
              <form method="post" action="<?= Url::to('/carrinho/adicionar/' . $id) ?>" class="d-grid gap-2">
                <?= Csrf::input() ?>
                <div class="input-group input-group-sm">
                  <span class="input-group-text">Qtd</span>
                  <input type="number" name="quantidade" class="form-control" aria-label="Quantidade para <?= $h($nome) ?>"
                    value="<?= number_format($valorInicial, $step === 1 ? 0 : 3, '.', '') ?>"
                    min="<?= $step ?>" step="<?= $step ?>">
                  <?php if ($isKg): ?>
                    <span class="input-group-text">kg</span>
                  <?php endif; ?>
                </div>
                <button class="btn btn-danger" type="submit">
                  <i class="bi bi-cart-plus me-1"></i>Adicionar ao carrinho
                </button>
              </form>
              <?php if ($estoque !== null): ?>
                <small class="text-muted d-block mt-2">Estoque: <?= $h((string)$estoque) ?></small>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <?php
  $totalPages = (int)($pagination['pages'] ?? 0);
  $currentPage = (int)($pagination['page'] ?? 1);
  if ($totalPages > 1):
    $start = max(1, $currentPage - 2);
    $end = min($totalPages, $currentPage + 2);
    if ($start > 1) {
      $start = max(1, $end - 4);
    }
    if ($end - $start < 4) {
      $end = min($totalPages, $start + 4);
    }
  ?>
    <nav class="mt-4" aria-label="Paginacao de produtos">
      <ul class="pagination pagination-sm justify-content-center">
        <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
          <a class="page-link" href="<?= $currentPage <= 1 ? '#' : $buildLink(['page' => $currentPage - 1]) ?>"
            tabindex="<?= $currentPage <= 1 ? '-1' : '0' ?>">Anterior</a>
        </li>
        <?php if ($start > 1): ?>
          <li class="page-item"><a class="page-link" href="<?= $buildLink(['page' => 1]) ?>">1</a></li>
          <?php if ($start > 2): ?><li class="page-item disabled"><span class="page-link">&hellip;</span></li>
          <?php endif; ?>
        <?php endif; ?>
        <?php for ($i = $start; $i <= $end; $i++): ?>
          <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
            <a class="page-link" href="<?= $i === $currentPage ? '#' : $buildLink(['page' => $i]) ?>"><?= $i ?></a>
          </li>
        <?php endfor; ?>
        <?php if ($end < $totalPages): ?>
          <?php if ($end < $totalPages - 1): ?><li class="page-item disabled"><span class="page-link">&hellip;</span></li>
          <?php endif; ?>
          <li class="page-item"><a class="page-link"
              href="<?= $buildLink(['page' => $totalPages]) ?>"><?= $totalPages ?></a></li>
        <?php endif; ?>
        <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
          <a class="page-link"
            href="<?= $currentPage >= $totalPages ? '#' : $buildLink(['page' => $currentPage + 1]) ?>"
            tabindex="<?= $currentPage >= $totalPages ? '-1' : '0' ?>">Próxima</a>
        </li>
      </ul>
    </nav>
  <?php endif; ?>
<?php endif; ?>

