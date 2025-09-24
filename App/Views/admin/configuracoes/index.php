<?php

use App\Core\Url;

/** @var array<string,mixed> $config */
$config = $config ?? [];
$h = static fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
?>
<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title><?= $h($title ?? 'Configura��es') ?></title>
    <link rel="stylesheet" href="<?= \App\Core\Url::to('/assets/css/bootstrap.min.css') ?>" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?= Url::to('/assets/site/css/style.css') ?>" />
    <style>
    .sidebar-sticky {
        position: sticky;
        top: 1rem;
    }
    </style>
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
                        <div class="mb-4">
                            <h1 class="h4 mb-1">Configurações do sistema</h1>
                            <p class="text-muted mb-0">Defina preferências gerais da loja e funcionalidades do checkout.
                            </p>
                        </div>

                        <?php if (empty($config)): ?>
                        <div class="alert alert-info">
                            Nenhuma configuração persistida foi encontrada. Você pode usar este formulário como
                            referência para salvar em um arquivo ou tabela (por exemplo, `configuracoes_loja`).
                        </div>
                        <?php endif; ?>

                        <form class="needs-validation" method="post" novalidate>
                            <div class="card shadow-sm mb-3">
                                <div class="card-header bg-white fw-semibold">Informações da loja</div>
                                <div class="card-body row g-3">
                                    <div class="col-md-6">
                                        <label for="cfg-nome" class="form-label">Nome da loja</label>
                                        <input type="text" id="cfg-nome" name="nome_loja" class="form-control"
                                            value="<?= $h($config['nome_loja'] ?? 'Mercadinho Borba Gato') ?>" disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="cfg-email" class="form-label">E-mail de contato</label>
                                        <input type="email" id="cfg-email" name="email_contato" class="form-control"
                                            value="<?= $h($config['email_contato'] ?? 'contato@mercadinho.com') ?>"
                                            disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="cfg-telefone" class="form-label">Telefone de atendimento</label>
                                        <input type="text" id="cfg-telefone" name="telefone" class="form-control"
                                            value="<?= $h($config['telefone'] ?? '(44) 3259-1533') ?>" disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="cfg-horario" class="form-label">Horário de funcionamento</label>
                                        <input type="text" id="cfg-horario" name="horario" class="form-control"
                                            value="<?= $h($config['horario'] ?? 'Seg � S�b � 08h �s 20h') ?>" disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="card shadow-sm mb-3">
                                <div class="card-header bg-white fw-semibold">Experiência do site</div>
                                <div class="card-body row g-3">
                                    <div class="col-12">
                                        <label for="cfg-banner" class="form-label">Mensagem no topo do site</label>
                                        <input type="text" id="cfg-banner" name="banner_msg" class="form-control"
                                            value="<?= $h($config['banner_msg'] ?? 'Pedidos feitos at� as 19h ser�o entregues no mesmo dia!') ?>"
                                            disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="cfg-retirada" class="form-label">Opções de entrega
                                            habilitadas</label>
                                        <select id="cfg-retirada" class="form-select" disabled>
                                            <option selected>Retirada e entrega domiciliar</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="cfg-pagamentos" class="form-label">Formas de pagamento</label>
                                        <select id="cfg-pagamentos" class="form-select" multiple disabled>
                                            <option selected>Dinheiro na entrega</option>
                                            <option selected>PIX</option>
                                            <option selected>Cartão na entrega</option>
                                            <option>Pagamento online</option>
                                        </select>
                                        <div class="form-text">Marque quais opções devem aparecer no checkout.</div>
                                    </div>
                                </div>
                            </div>

                            <div class="card shadow-sm mb-3">
                                <div class="card-header bg-white fw-semibold">Checkout</div>
                                <div class="card-body row g-3">
                                    <div class="col-12">
                                        <label for="cfg-checkout-msg" class="form-label">Mensagem de confirmação</label>
                                        <textarea id="cfg-checkout-msg" name="checkout_msg" rows="3"
                                            class="form-control"
                                            disabled><?= $h($config['checkout_msg'] ?? 'Obrigado pela prefer�ncia! Entraremos em contato para confirmar a entrega.') ?></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="cfg-pix" class="form-label">Chave PIX da loja</label>
                                        <input type="text" id="cfg-pix" name="pix_chave" class="form-control"
                                            value="<?= $h($config['pix_chave'] ?? 'mercadinho@pix.com.br') ?>" disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="cfg-minimo" class="form-label">Pedido mínimo (R$)</label>
                                        <input type="number" id="cfg-minimo" name="pedido_minimo" class="form-control"
                                            value="<?= $h($config['pedido_minimo'] ?? '30.00') ?>" step="0.01" disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-secondary">
                                Este painel é apenas uma referência visual. Para salvar as configurações, implemente a
                                persistência (ex.: tabela `configuracao`) e remova os atributos `disabled` deste
                                formulário.
                            </div>
                            <button type="button" class="btn btn-secondary" disabled>Salvar alterações</button>
                        </form>
                    </div>
                </div>
            </div>
        </main>

        <?php require dirname(__DIR__, 2) . '/partials/footer.php'; ?>
    </div>

    <script src="<?= \App\Core\Url::to('/assets/js/bootstrap.bundle.min.js') ?>"></script>
</body>

</html>