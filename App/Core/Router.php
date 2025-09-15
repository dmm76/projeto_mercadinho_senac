<?php declare(strict_types=1);

namespace App\Core;

/**
 * @phpstan-type RouteHandler callable|array{0: class-string, 1: string}
 */
final class Router
{
    /** @var array<string, array<string, callable|array{0:class-string,1:string}>> */
    private array $routes = [
        'GET' => [],
        'POST' => [],
    ];

    /** @param callable|array{0:class-string,1:string} $handler */
    public function get(string $path, $handler): void
    {
        $this->map('GET', $path, $handler);
    }

    /** @param callable|array{0:class-string,1:string} $handler */
    public function post(string $path, $handler): void
    {
        $this->map('POST', $path, $handler);
    }

    /** @param callable|array{0:class-string,1:string} $handler */
    private function map(string $method, string $path, $handler): void
    {
        $this->routes[strtoupper($method)][$this->normalize($path)] = $handler;
    }

    public function dispatch(string $method, string $uri): void
    {
        $method = strtoupper($method);

        // caminho solicitado
        $reqPath = parse_url($uri, PHP_URL_PATH) ?: '/';

        // base path (ex.: /projeto_mercadinho_web/public)
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $base = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');

        // remove o base path do request
        if ($base !== '' && $base !== '/' && str_starts_with($reqPath, $base)) {
            $reqPath = substr($reqPath, strlen($base));
        }

        $path = $this->normalize($reqPath);

        $handler = $this->routes[$method][$path] ?? null;

        if ($handler === null) {
            http_response_code(404);
            require __DIR__ . '/../Views/errors/404.php';
            return;
        }

        if (is_array($handler)) {
            /** @var class-string $class */
            $class = $handler[0];
            $action = $handler[1];
            $controller = new $class();
            $controller->{$action}();
            return;
        }

        // callable
        $handler();
    }

    private function normalize(string $path): string
    {
        $n = rtrim($path, '/');
        return $n === '' ? '/' : $n;
    }
}
