<?php /** app/Views/site/home/contato.php */ ?>
<?php
/**
 * @var array{type?: 'danger'|'success'|'info'|'warning', messages?: array<int,string>}|null $flash
 * @var array{nome?:string,email?:string,mensagem?:string} $old
 */
$csrfToken = isset($csrfToken) ? (string)$csrfToken : '';
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title><?= htmlspecialchars($title ?? 'Contato | Mercadinho Borba Gato') ?></title>

  <!-- Bootstrap (CDN) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"/>
  <!-- Seu CSS -->
  <link rel="stylesheet" href="<?= \App\Core\Url::to('/assets/site/css/style.css') ?>"/>

  <style>
    .hp{position:absolute;left:-9999px;top:auto;width:1px;height:1px;overflow:hidden;}
  </style>
</head>
<body>
<div class="d-flex flex-column wrapper">

  <?php require __DIR__ . '/../../partials/navbar.php'; ?>

  <main class="flex-fill">
    <div class="container py-3">
      <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8 col-xl-6">

          <?php if (!empty($flash) && !empty($flash['messages'])): ?>
            <div class="alert alert-<?= htmlspecialchars($flash['type'] ?? 'info') ?> alert-dismissible fade show" role="alert">
              <ul class="mb-0">
                <?php foreach ($flash['messages'] as $msg): ?>
                  <li><?= htmlspecialchars($msg) ?></li>
                <?php endforeach; ?>
              </ul>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
          <?php endif; ?>

          <div class="card shadow-sm">
            <div class="card-body p-4">
              <h1 class="h3 mb-3">Entre em contato</h1>

              <form action="<?= \App\Core\Url::to('/contato') ?>" method="post" class="needs-validation" novalidate>
                <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrfToken) ?>" />
                <!-- honeypot -->
                <div class="hp"><label for="website">Website</label><input id="website" type="text" name="website" autocomplete="off"/></div>

                <div class="form-floating mb-3">
                  <input
                    type="text"
                    name="nome"
                    id="txtNomeCompleto"
                    class="form-control"
                    placeholder="Seu nome"
                    minlength="3"
                    required
                    value="<?= htmlspecialchars($old['nome'] ?? '') ?>"
                  />
                  <label for="txtNomeCompleto">Nome Completo</label>
                  <div class="invalid-feedback">Informe seu nome (mínimo 3 caracteres).</div>
                </div>

                <div class="form-floating mb-3">
                  <input
                    type="email"
                    name="email"
                    id="txtEmail"
                    class="form-control"
                    placeholder="seu@email.com"
                    required
                    value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                  />
                  <label for="txtEmail">E-mail</label>
                  <div class="invalid-feedback">Informe um e-mail válido.</div>
                </div>

                <div class="form-floating mb-3">
                  <textarea
                    name="mensagem"
                    id="txtMensagem"
                    class="form-control"
                    placeholder="Sua mensagem"
                    style="height: 200px"
                    minlength="10"
                    required
                  ><?= htmlspecialchars($old['mensagem'] ?? '') ?></textarea>
                  <label for="txtMensagem">Mensagem</label>
                  <div class="invalid-feedback">Escreva uma mensagem (mínimo 10 caracteres).</div>
                </div>

                <button type="submit" class="btn btn-danger btn-lg">Enviar Mensagem</button>

                <p class="mt-3 mb-0">Faremos o nosso melhor para responder sua mensagem em até 2 dias úteis.</p>
                <p class="mt-3">Atenciosamente,<br>Central de Relacionamento Mercadinho Borba Gato</p>
              </form>
            </div>
          </div>

        </div>
      </div>
    </div>
  </main>

  <?php require __DIR__ . '/../../partials/footer.php'; ?>

</div>

<!-- Bootstrap JS (CDN) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Seu JS -->
<script src="<?= \App\Core\Url::to('/assets/site/js/script.js') ?>"></script>
<script>
  (function () {
    'use strict';
    const forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function (form) {
      form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    });
  })();
</script>
</body>
</html>
