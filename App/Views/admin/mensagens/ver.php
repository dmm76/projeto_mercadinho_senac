<?php
use App\Core\Url;
use App\Core\Csrf;

/** @var array{id:int,nome:string,email:string,mensagem:string,status:string,criada_em:string,ip:?string,user_agent:?string,resposta:?string,respondida_em:?string} $mensagem */
$mensagem = $mensagem ?? [];
$h = static fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$statusAtual = $mensagem['status'] ?? 'aberta';
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title><?= $h($title ?? 'Mensagem') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <link rel="stylesheet" href="<?= Url::to('/assets/site/css/style.css') ?>" />
  <style>.sidebar-sticky{position:sticky;top:1rem;}</style>
</head>
<body>
<div class="d-flex flex-column wrapper">
  <?php require dirname(__DIR__, 2) . '/partials/navbar.php'; ?>
  <main class="flex-fill">
    <div class="container py-3">
      <div class="row g-3">
        <div class="col-12 col-lg-3">
          <?php require dirname(__DIR__, 2) . '/partials/admin-sidebar.php'; ?>
        </div>
        <div class="col-12 col-lg-9">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <h1 class="h4 mb-0">Mensagem #<?= (int)($mensagem['id'] ?? 0) ?></h1>
            <a href="<?= Url::to('/admin/mensagens') ?>" class="btn btn-outline-secondary">Voltar</a>
          </div>

          <div class="card shadow-sm mb-3">
            <div class="card-body">
              <dl class="row mb-0">
                <dt class="col-sm-3">Nome</dt>
                <dd class="col-sm-9"><?= $h($mensagem['nome'] ?? '') ?></dd>
                <dt class="col-sm-3">E-mail</dt>
                <dd class="col-sm-9"><a href="mailto:<?= $h($mensagem['email'] ?? '') ?>"><?= $h($mensagem['email'] ?? '') ?></a></dd>
                <dt class="col-sm-3">Recebida em</dt>
                <dd class="col-sm-9"><?= $h($mensagem['criada_em'] ?? '-') ?></dd>
                <dt class="col-sm-3">IP</dt>
                <dd class="col-sm-9"><?= $h($mensagem['ip'] ?? '-') ?></dd>
                <dt class="col-sm-3">User agent</dt>
                <dd class="col-sm-9 text-break"><?= $h($mensagem['user_agent'] ?? '-') ?></dd>
                <dt class="col-sm-3">Status</dt>
                <dd class="col-sm-9"><span class="badge text-bg-secondary text-uppercase"><?= $h($statusAtual) ?></span></dd>
              </dl>
            </div>
          </div>

          <div class="card shadow-sm mb-3">
            <div class="card-header bg-white fw-semibold">Mensagem</div>
            <div class="card-body">
              <p class="mb-0 text-prewrap"><?= nl2br($h($mensagem['mensagem'] ?? '')) ?></p>
            </div>
          </div>

          <div class="card shadow-sm mb-3">
            <div class="card-header bg-white fw-semibold">Responder</div>
            <div class="card-body">
              <?php if (($mensagem['resposta'] ?? '') !== ''): ?>
                <div class="mb-3">
                  <label class="form-label">Resposta registrada</label>
                  <textarea class="form-control" rows="3" disabled><?= $h($mensagem['resposta']) ?></textarea>
                  <div class="form-text">Respondida em <?= $h($mensagem['respondida_em'] ?? '-') ?></div>
                </div>
              <?php endif; ?>

              <form method="post" action="<?= Url::to('/admin/mensagens/responder') ?>" class="row g-3">
                <?= Csrf::input() ?>
                <input type="hidden" name="id" value="<?= (int)($mensagem['id'] ?? 0) ?>">
                <div class="col-12">
                  <label for="resposta" class="form-label">Resposta</label>
                  <textarea id="resposta" name="resposta" class="form-control" rows="4" required></textarea>
                </div>
                <div class="col-12">
                  <button type="submit" class="btn btn-danger">Registrar resposta</button>
                </div>
              </form>

              <div class="mt-3 d-flex gap-2">
                <form method="post" action="<?= Url::to('/admin/mensagens/status') ?>" class="d-inline">
                  <?= Csrf::input() ?>
                  <input type="hidden" name="id" value="<?= (int)($mensagem['id'] ?? 0) ?>">
                  <input type="hidden" name="status" value="arquivada">
                  <button type="submit" class="btn btn-outline-dark">Arquivar</button>
                </form>
                <?php if ($statusAtual !== 'aberta'): ?>
                  <form method="post" action="<?= Url::to('/admin/mensagens/status') ?>" class="d-inline">
                    <?= Csrf::input() ?>
                    <input type="hidden" name="id" value="<?= (int)($mensagem['id'] ?? 0) ?>">
                    <input type="hidden" name="status" value="aberta">
                    <button type="submit" class="btn btn-outline-success">Reabrir</button>
                  </form>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
  <?php require dirname(__DIR__, 2) . '/partials/footer.php'; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>