<?php
declare(strict_types=1);

use App\Core\Router;

require __DIR__ . '/../vendor/autoload.php';

/** 1) .env */
$root = realpath(__DIR__ . '/..');
$dotenv = Dotenv\Dotenv::createImmutable($root);
$dotenv->load();

/** 2) Erros */
if (($_ENV['APP_ENV'] ?? 'prod') === 'local') {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
}

/** 3) Sessão */
session_start();

/** 4) Router */
$router = new Router();

/** Registra rotas do site e admin */
require $root . '/config/routes/web.php';
require $root . '/config/routes/admin.php';

/** 5) Normaliza a URI antes do dispatch (remove /public e /index.php) */
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri    = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';

$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$scriptDir  = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');

$path = $uri;

// Ex.: /projeto_mercadinho_web/public/index.php/...
if ($scriptName && strpos($path, $scriptName) === 0) {
    $path = substr($path, strlen($scriptName));
}
// Ex.: /projeto_mercadinho_web/public/...
elseif ($scriptDir && $scriptDir !== '' && $scriptDir !== '/' && strpos($path, $scriptDir) === 0) {
    $path = substr($path, strlen($scriptDir));
}

// Se sobrar um /index.php no início, remove
if (strpos($path, '/index.php') === 0) {
    $path = substr($path, strlen('/index.php'));
}

// Garante formato /...
$path = '/' . ltrim($path, '/');
if ($path === '') $path = '/';

/** 6) Despacha a rota limpa */
$router->dispatch($method, $path);
