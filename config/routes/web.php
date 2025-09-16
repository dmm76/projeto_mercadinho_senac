<?php

declare(strict_types=1);

use App\Controllers\Site\HomeController;
use App\Controllers\Site\AuthController;
use App\Controllers\Site\ContatoController;
use App\Controllers\Site\CarrinhoController;
use App\Controllers\Conta\ContaController;
use App\Controllers\Site\ProdutoController as SiteProdutoController;
use App\Controllers\Site\PedidoController  as SitePedidoController;

/**
 * SITE (público)
 */
$router->get('/', [HomeController::class, 'index']);

// Auth
$router->get ('/login',      [AuthController::class, 'showLogin']);
$router->post('/login',      [AuthController::class, 'login']);
$router->get ('/registrar',  [AuthController::class, 'showRegister']);
$router->post('/registrar',  [AuthController::class, 'register']);
$router->get ('/logout',     [AuthController::class, 'logout']);

// Contato
$router->get ('/contato',  [ContatoController::class, 'show']);
$router->post('/contato',  [ContatoController::class, 'send']);

// Produtos
$router->get('/produtos/(\d+)', [SiteProdutoController::class, 'ver']);   // detalhe
$router->get('/produtos',       [SiteProdutoController::class, 'index']); // lista
$router->get('/buscar',         [SiteProdutoController::class, 'index']); // busca via ?q=

// Carrinho
$router->get ('/carrinho',                    [CarrinhoController::class, 'index']);
$router->get ('/carrinho/adicionar/(\d+)',    [CarrinhoController::class, 'adicionarGet']); // fallback p/ links antigos (GET)
$router->post('/carrinho/adicionar/(\d+)',    [CarrinhoController::class, 'adicionar']);
$router->post('/carrinho/atualizar/(\d+)',    [CarrinhoController::class, 'atualizar']);
$router->post('/carrinho/remover/(\d+)',      [CarrinhoController::class, 'remover']);

// Checkout
$router->get ('/checkout', [SitePedidoController::class, 'checkout']);  // exibe
$router->post('/checkout', [SitePedidoController::class, 'finalizar']); // finaliza

/**
 * CONTA (cliente logado)
 */
$router->get('/conta',                      [ContaController::class, 'dashboard']);

$router->get('/conta/pedidos',              [ContaController::class, 'pedidos']);
$router->get('/conta/pedidos/(\d+)',        [ContaController::class, 'verPedido']);
$router->get('/conta/pedidos/ver',          [ContaController::class, 'verPedidoQuery']); // legado ?id=

$router->get('/conta/dados',                [ContaController::class, 'dados']);
$router->post('/conta/dados/perfil',        [ContaController::class, 'salvarPerfil']);
$router->post('/conta/dados/senha',         [ContaController::class, 'atualizarSenha']);

$router->get ('/conta/enderecos',           [ContaController::class, 'enderecos']);
$router->get ('/conta/enderecos/novo',      [ContaController::class, 'novoEndereco']);
$router->get ('/conta/enderecos/editar',    [ContaController::class, 'editarEnderecoQuery']); // legado ?id=
$router->post('/conta/enderecos/novo',      [ContaController::class, 'criarEndereco']);
$router->post('/conta/enderecos/editar',    [ContaController::class, 'atualizarEnderecoQuery']);
$router->post('/conta/enderecos/excluir',   [ContaController::class, 'excluirEnderecoQuery']);
$router->post('/conta/enderecos/principal', [ContaController::class, 'definirPrincipalQuery']);

/**
 * LEGADO / ALIASES
 */
// /preview/home -> /
$router->get('/preview/home', function (): void {
    header('Location: ' . \App\Core\Url::to('/'), true, 302);
    exit;
});

// /meus-pedidos* -> /conta/pedidos*
$router->get('/meus-pedidos', function (): void {
    header('Location: ' . \App\Core\Url::to('/conta/pedidos'), true, 301);
    exit;
});
$router->get('/meus-pedidos/(\d+)', function ($id): void {
    header('Location: ' . \App\Core\Url::to('/conta/pedidos/' . (int)$id), true, 301);
    exit;
});

// /produto?id=123 -> /produtos/123
$router->get('/produto', function (): void {
    $id = (int)($_GET['id'] ?? 0);
    $dest = $id > 0 ? '/produtos/' . $id : '/produtos';
    header('Location: ' . \App\Core\Url::to($dest), true, 301);
    exit;
});

/**
 * HEALTHCHECKS
 */
$router->get('/health/db', function (): void {
    try {
        \App\DAO\Database::getConnection();
        echo 'DB OK';
    } catch (\Throwable $e) {
        http_response_code(500);
        echo 'DB FAIL: ' . $e->getMessage();
    }
});
$router->get('/health/autoload', function (): void {
    $ok = class_exists(\App\Model\Produto::class)
        && class_exists(\App\Model\Categoria::class)
        && class_exists(\App\Model\Marca::class)
        && class_exists(\App\Model\Unidade::class)
        && class_exists(\App\Model\Usuario::class);
    echo $ok ? 'AUTOLOAD OK' : 'AUTOLOAD FAIL';
});
