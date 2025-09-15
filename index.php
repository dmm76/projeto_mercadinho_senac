<?php
declare(strict_types=1);

ini_set('display_errors','1');
error_reporting(E_ALL);

session_start();
require __DIR__ . '/vendor/autoload.php';

// Como você está em /projeto_mercadinho_web:
define('BASE_PATH', '/projeto_mercadinho_web');

use App\Core\Router;

$router = new Router();

/**
 * Carrega as rotas (esses arquivos devem usar o MESMO $router)
 * e registrar coisas do tipo: $router->get('/...', [Controller::class, 'metodo']);
 */
require __DIR__ . '/config/routes/web.php';
require __DIR__ . '/config/routes/admin.php';

// (Opcional) rotas de health/ping, caso não estejam nos arquivos acima:
$router->get('/ping', fn() => print('OK'));
// $router->get('/health/db', function () { ... });

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri    = $_SERVER['REQUEST_URI'] ?? '/';
$router->dispatch($method, $uri);
