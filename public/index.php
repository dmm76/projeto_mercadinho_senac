<?php

declare(strict_types=1);

use App\Core\Router;
use Dotenv\Dotenv; // <<< garante o use

require __DIR__ . '/../vendor/autoload.php';

/** 1) .env */
$root = realpath(__DIR__ . '/..');

// Carrega .env se existir; não explode se não existir (produção)
$dotenv = Dotenv::createImmutable($root);
$dotenv->safeLoad();  // <<< troque load() por safeLoad()

// Helper opcional: lê variável do ambiente com default
$env = static function (string $key, $default = null) {
    return $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key) ?: $default;
};

/** 2) Erros (mostra só em dev/local) */
if ($env('APP_ENV', 'prod') === 'local') {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

/** 3) Sessão */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/** 4) Router */
$router = new Router();

/** Registra rotas do site e admin */
require $root . '/config/routes/web.php';
require $root . '/config/routes/admin.php';

/** 5) Normaliza a URI antes do dispatch (remove /index.php) */
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri    = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';

$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$scriptDir  = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');

$path = $uri;

// Quando DocumentRoot é a pasta "public", normalmente $scriptName = "/index.php"
if ($scriptName && strpos($path, $scriptName) === 0) {
    $path = substr($path, strlen($scriptName));
} elseif ($scriptDir && $scriptDir !== '' && $scriptDir !== '/' && strpos($path, $scriptDir) === 0) {
    $path = substr($path, strlen($scriptDir));
}
if (strpos($path, '/index.php') === 0) {
    $path = substr($path, strlen('/index.php'));
}
$path = '/' . ltrim($path, '/');
if ($path === '') $path = '/';

/** 6) Despacha a rota limpa */
$router->dispatch($method, $path);
