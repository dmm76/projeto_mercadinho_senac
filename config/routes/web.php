<?php

declare(strict_types=1);

use App\Controllers\Site\HomeController;
use App\Controllers\Site\AuthController;
use App\Controllers\Site\ContatoController;
use App\Controllers\Site\CarrinhoController;
use App\Controllers\Conta\ContaController;

use App\Controllers\Site\ProdutoController as SiteProdutoController;
use App\Controllers\Site\PedidoController  as SitePedidoController;

// HOME
$router->get('/', [HomeController::class, 'index']);

// AUTH
$router->get('/login',       [AuthController::class, 'showLogin']);
$router->post('/login',      [AuthController::class, 'login']);
$router->get('/registrar',   [AuthController::class, 'showRegister']);
$router->post('/registrar',  [AuthController::class, 'register']);
$router->get('/logout',      [AuthController::class, 'logout']);

// SITE
$router->get('/contato',  [ContatoController::class, 'show']);
$router->post('/contato', [ContatoController::class, 'send']);
$router->get('/carrinho', [CarrinhoController::class, 'index']);

// LEGADO: /preview/home -> /
$router->get('/preview/home', function (): void {
    header('Location: ' . \App\Core\Url::to('/'), true, 302);
    exit;
});

// HEALTH (opcionais)
$router->get('/health/db', function (): void {
    try {
        \App\DAO\Database::getConnection();
        echo 'DB OK';
    } catch (\Throwable $e) {
        http_response_code(500);
        echo 'DB FAIL: ' . $e->getMessage();
    }
});
$router->get('/health/autoload', function () {
    $ok = class_exists(\App\Model\Produto::class)
        && class_exists(\App\Model\Categoria::class)
        && class_exists(\App\Model\Marca::class)
        && class_exists(\App\Model\Unidade::class)
        && class_exists(\App\Model\Usuario::class);
    echo $ok ? 'AUTOLOAD OK' : 'AUTOLOAD FAIL';
});

// PAINEL DO CLIENTE
$router->get('/conta',                        [ContaController::class, 'dashboard']);

$router->get('/conta/pedidos',           [ContaController::class, 'pedidos']);
$router->get('/conta/pedidos/(\d+)',     [ContaController::class, 'verPedido']);
$router->get('/conta/pedidos/ver',       [ContaController::class, 'verPedidoQuery']);

$router->get('/conta/dados',             [ContaController::class, 'dados']);

$router->get('/conta/enderecos',         [ContaController::class, 'enderecos']);
$router->get('/conta/enderecos/novo',    [ContaController::class, 'novoEndereco']);
$router->get('/conta/enderecos/editar',  [ContaController::class, 'editarEnderecoQuery']);

$router->post('/conta/enderecos/novo',        [ContaController::class, 'criarEndereco']);
$router->post('/conta/enderecos/editar',      [ContaController::class, 'atualizarEnderecoQuery']);
$router->post('/conta/enderecos/excluir',     [ContaController::class, 'excluirEnderecoQuery']);
$router->post('/conta/enderecos/principal',   [ContaController::class, 'definirPrincipalQuery']);

$router->post('/conta/dados/perfil', [ContaController::class, 'salvarPerfil']);
$router->post('/conta/dados/senha',  [ContaController::class, 'atualizarSenha']);

// LOJA — Produtos  (reposto)
$router->get('/produtos',       [SiteProdutoController::class, 'index']);
$router->get('/produtos/(\d+)', [SiteProdutoController::class, 'ver']);

// LOJA — Carrinho (já há GET /carrinho acima; não duplique)
$router->post('/carrinho/adicionar/(\d+)', [CarrinhoController::class, 'adicionar']);
$router->post('/carrinho/atualizar/(\d+)', [CarrinhoController::class, 'atualizar']);
$router->post('/carrinho/remover/(\d+)',   [CarrinhoController::class, 'remover']);

// LOJA — Checkout
$router->post('/checkout', [SitePedidoController::class, 'finalizar']);

// Alias (opcional): manter URLs antigas /meus-pedidos -> /conta/pedidos
$router->get('/meus-pedidos', function () {
    header('Location: ' . \App\Core\Url::to('/conta/pedidos'), true, 301);
    exit;
});
$router->get('/meus-pedidos/(\d+)', function ($id) {
    header('Location: ' . \App\Core\Url::to('/conta/pedidos/'.$id), true, 301);
    exit;
});
