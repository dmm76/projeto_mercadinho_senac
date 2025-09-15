<?php
declare(strict_types=1);


use App\Core\Router;


require __DIR__ . '/../vendor/autoload.php';


// 1) .env
$root = realpath(__DIR__ . '/..');
$dotenv = Dotenv\Dotenv::createImmutable($root);
$dotenv->load();


// 2) Config básica de erros
if (($_ENV['APP_ENV'] ?? 'prod') === 'local') {
ini_set('display_errors', '1');
error_reporting(E_ALL);
}


// 3) Sessão
session_start();


// 4) Router
$router = new Router();


// Registra rotas do site e admin
require $root . '/config/routes/web.php';
require $root . '/config/routes/admin.php';


// 5) Despacho
$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');