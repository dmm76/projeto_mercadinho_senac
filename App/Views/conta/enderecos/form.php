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

$val = static fn(string $key, string $default = ''): string => htmlspecialchars((string)($e[$key] ?? $default));
?>
<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title><?= $isEdit ? 'Editar Endereco' : 'Novo Endereco' ?></title>
    <link rel="stylesheet" href="<?= Url::to('/assets/css/bootstrap.min.css') ?>" />
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
        <?php require __DIR__ . '/../../partials/navbar.php'; ?>

        <main class="flex-fill">
            <div class="container py-3">
                <div class="row g-3">
                    <div class="col-12 col-lg-3">
                        <?php require __DIR__ . '/../../partials/conta-sidebar.php'; ?>
                    </div>

                    <div class="col-12 col-lg-9">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h1 class="h4 mb-0"><?= $isEdit ? 'Editar Endereco' : 'Novo Endereco' ?></h1>
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
                                <form method="post" action="<?= Url::to($actionUrl ?? '/conta/enderecos/novo') ?>">
                                    <?= Csrf::input() ?>
                                    <?php if ($isEdit): ?>
                                        <input type="hidden" name="id" value="<?= (int)($e['id'] ?? 0) ?>">
                                    <?php endif; ?>

                                    <div class="row g-3">
                                        <div class="col-12 col-md-6">
                                            <label class="form-label" for="endereco-rotulo">Rotulo</label>
                                            <input id="endereco-rotulo" type="text" name="rotulo" class="form-control"
                                                value="<?= $val('rotulo') ?>" placeholder="Casa, Trabalho..."
                                                maxlength="50">
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="form-label" for="endereco-nome">Nome do destinatario</label>
                                            <input id="endereco-nome" type="text" name="nome" class="form-control"
                                                value="<?= $val('nome') ?>" required maxlength="80">
                                        </div>

                                        <div class="col-6 col-md-3">
                                            <label class="form-label" for="endereco-cep">CEP</label>
                                            <input id="endereco-cep" type="text" name="cep" class="form-control"
                                                value="<?= $val('cep') ?>" placeholder="00000-000" required
                                                maxlength="9">
                                            <small id="cep-status" class="text-muted" aria-live="polite"></small>
                                        </div>



                                        <div class="col-6 col-md-7">
                                            <label class="form-label" for="endereco-logradouro">Logradouro</label>
                                            <input id="endereco-logradouro" type="text" name="logradouro"
                                                class="form-control" value="<?= $val('logradouro') ?>" required
                                                maxlength="120">
                                        </div>
                                        <div class="col-12 col-md-2">
                                            <label class="form-label" for="endereco-numero">Numero</label>
                                            <input id="endereco-numero" type="text" name="numero" class="form-control"
                                                value="<?= $val('numero') ?>" required maxlength="10">
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <label class="form-label" for="endereco-complemento">Complemento</label>
                                            <input id="endereco-complemento" type="text" name="complemento"
                                                class="form-control" value="<?= $val('complemento') ?>" maxlength="60">
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="form-label" for="endereco-bairro">Bairro</label>
                                            <input id="endereco-bairro" type="text" name="bairro" class="form-control"
                                                value="<?= $val('bairro') ?>" required maxlength="60">
                                        </div>

                                        <div class="col-12 col-md-8">
                                            <label class="form-label" for="endereco-cidade">Cidade</label>
                                            <input id="endereco-cidade" type="text" name="cidade" class="form-control"
                                                value="<?= $val('cidade') ?>" required maxlength="80">
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <label class="form-label" for="endereco-uf">UF</label>
                                            <select id="endereco-uf" name="uf" class="form-select" required>
                                                <option value="" disabled <?= $val('uf') === '' ? 'selected' : '' ?>>
                                                    Selecione</option>
                                                <?php foreach ($ufs as $uf): ?>
                                                    <option value="<?= $uf ?>" <?= $val('uf') === $uf ? 'selected' : '' ?>>
                                                        <?= $uf ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="principal"
                                                    id="endereco-principal" value="1"
                                                    <?= !empty($e['principal']) ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="endereco-principal">Definir como
                                                    endereco principal</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-3 d-flex gap-2">
                                        <button class="btn btn-danger" type="submit">
                                            <?= $isEdit ? 'Salvar alteracoes' : 'Cadastrar endereco' ?>
                                        </button>
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

        <?php require __DIR__ . '/../../partials/footer.php'; ?>
    </div>

    <script src="<?= Url::to('/assets/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= Url::to('/assets/site/js/script.js') ?>"></script>
    <script>
        (function() {
            const $ = (sel) => document.querySelector(sel);
            const cepInput = $('#endereco-cep');
            const statusEl = $('#cep-status');
            const logradouro = $('#endereco-logradouro');
            const bairro = $('#endereco-bairro');
            const cidade = $('#endereco-cidade');
            const uf = $('#endereco-uf');
            const submitBtn = document.querySelector('button[type="submit"]');

            let loading = false;

            function setStatus(msg, type = 'muted') {
                if (!statusEl) return;
                statusEl.textContent = msg || '';
                statusEl.classList.remove('text-muted', 'text-danger', 'text-success');
                statusEl.classList.add(type === 'error' ? 'text-danger' : type === 'success' ? 'text-success' : 'text-muted');
            }

            function toggleLoading(on) {
                loading = !!on;
                [logradouro, bairro, cidade, uf, cepInput, submitBtn].forEach(el => {
                    if (el) el.toggleAttribute('disabled', loading);
                });
            }

            // máscara no digitar
            cepInput.addEventListener('input', () => {
                let v = cepInput.value.replace(/\D/g, '').slice(0, 8);
                if (v.length > 5) v = v.slice(0, 5) + '-' + v.slice(5);
                cepInput.value = v;

                // auto-dispara quando completa 8 dígitos
                if (v.replace(/\D/g, '').length === 8) {
                    buscarCEP();
                }
            });

            cepInput.addEventListener('blur', buscarCEP);

            async function buscarCEP() {
                const raw = (cepInput.value || '').replace(/\D/g, '');
                if (raw.length === 0) {
                    setStatus('');
                    return;
                }
                if (raw.length !== 8) {
                    setStatus('CEP inválido (use 8 dígitos).', 'error');
                    return;
                }

                setStatus('Buscando CEP…');
                toggleLoading(true);
                try {
                    const data = await buscaCEPComFallback(raw);
                    preencherCampos(data);
                    setStatus(`Endereço encontrado (${data.fonte}).`, 'success');
                } catch (e) {
                    setStatus(e?.message || 'Não foi possível buscar o CEP.', 'error');
                    // mantém campos livres pra digitação manual
                } finally {
                    toggleLoading(false);
                }
            }

            async function fetchComTimeout(url, ms = 6000) {
                const ctrl = new AbortController();
                const id = setTimeout(() => ctrl.abort(), ms);
                try {
                    const res = await fetch(url, {
                        signal: ctrl.signal
                    });
                    return res;
                } finally {
                    clearTimeout(id);
                }
            }

            async function buscaCEPComFallback(cep) {
                // 1) BrasilAPI
                try {
                    const r = await fetchComTimeout(`https://brasilapi.com.br/api/cep/v1/${cep}`);
                    if (r.ok) {
                        const j = await r.json();
                        if (j && j.cep) {
                            return {
                                cep: (j.cep || '').replace(/\D/g, ''),
                                logradouro: j.street || '',
                                bairro: j.neighborhood || '',
                                cidade: j.city || '',
                                uf: j.state || '',
                                fonte: 'BrasilAPI'
                            };
                        }
                    }
                } catch (_) {
                    /* continua pro fallback */ }

                // 2) ViaCEP (fallback)
                const r2 = await fetchComTimeout(`https://viacep.com.br/ws/${cep}/json/`);
                if (!r2.ok) throw new Error('Erro ao consultar ViaCEP.');
                const j2 = await r2.json();
                if (j2.erro) throw new Error('CEP não encontrado.');
                return {
                    cep: (j2.cep || '').replace(/\D/g, ''),
                    logradouro: j2.logradouro || '',
                    bairro: j2.bairro || '',
                    cidade: j2.localidade || '',
                    uf: j2.uf || '',
                    fonte: 'ViaCEP'
                };
            }

            function preencherCampos({
                logradouro: lg,
                bairro: br,
                cidade: ci,
                uf: estado
            }) {
                if (logradouro) logradouro.value = lg || '';
                if (bairro) bairro.value = br || '';
                if (cidade) cidade.value = ci || '';
                if (uf) {
                    // tenta selecionar exatamente; se não existir na lista, não força
                    const options = Array.from(uf.options).map(o => o.value);
                    if (estado && options.includes(estado)) uf.value = estado;
                }
            }
        })();
    </script>

</body>

</html>