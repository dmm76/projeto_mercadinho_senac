<?php

declare(strict_types=1);

namespace App\Core;

/**
 * @phpstan-type RouteHandler callable|array{0: class-string, 1: string}
 */
final class Router
{
    /**
     * @var array<string, array{
     *     static: array<string, RouteHandler>,
     *     dynamic: array<int, array{pattern:string, regex:string, handler:RouteHandler}>
     * }>
     */
    private array $routes = [
        'GET' => ['static' => [], 'dynamic' => []],
        'POST' => ['static' => [], 'dynamic' => []],
    ];

    /** @param RouteHandler $handler */
    public function get(string $path, $handler): void
    {
        $this->map('GET', $path, $handler);
    }

    /** @param RouteHandler $handler */
    public function post(string $path, $handler): void
    {
        $this->map('POST', $path, $handler);
    }

    /** @param RouteHandler $handler */
    private function map(string $method, string $path, $handler): void
    {
        $method = strtoupper($method);
        if (!isset($this->routes[$method])) {
            $this->routes[$method] = ['static' => [], 'dynamic' => []];
        }

        $normalized = $this->normalize($path);
        if ($this->isPattern($normalized)) {
            $pattern = $this->normalizePattern($normalized);
            $this->routes[$method]['dynamic'][] = [
                'pattern' => $pattern,
                'regex' => $this->compileRegex($pattern),
                'handler' => $handler,
            ];
            return;
        }

        $this->routes[$method]['static'][$normalized] = $handler;
    }

    public function dispatch(string $method, string $uri): void
    {
        $method = strtoupper($method);
        $routes = $this->routes[$method] ?? ['static' => [], 'dynamic' => []];

        $reqPath = parse_url($uri, PHP_URL_PATH) ?: '/';

        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $base = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
        if ($base !== '' && $base !== '/' && str_starts_with($reqPath, $base)) {
            $reqPath = substr($reqPath, strlen($base));
        }

        $path = $this->normalize($reqPath);

        $handler = $routes['static'][$path] ?? null;
        if ($handler !== null) {
            $this->invoke($handler, []);
            return;
        }

        foreach ($routes['dynamic'] as $route) {
            if (preg_match($route['regex'], $path, $matches) === 1) {
                $params = $this->extractParams($matches);
                $this->invoke($route['handler'], $params);
                return;
            }
        }

        http_response_code(404);
        require __DIR__ . '/../Views/errors/404.php';
    }

    /**
     * @return array<int, mixed>
     */
    private function extractParams(array $matches): array
    {
        $params = [];
        foreach ($matches as $key => $value) {
            if (!is_int($key) || $key === 0) {
                continue;
            }
            $params[] = $this->castParam($value);
        }
        return $params;
    }

    private function castParam(string $value): mixed
    {
        if ($value === '') {
            return $value;
        }
        if (ctype_digit($value)) {
            return (int) $value;
        }
        if (is_numeric($value)) {
            return (float) $value;
        }
        return $value;
    }

    /** @param RouteHandler $handler */
    private function invoke($handler, array $params): void
    {
        if (is_array($handler)) {
            /** @var class-string $class */
            $class = $handler[0];
            $action = $handler[1];
            $controller = new $class();
            $controller->{$action}(...$params);
            return;
        }

        $handler(...$params);
    }

    private function normalize(string $path): string
    {
        $n = rtrim($path, '/');
        if ($n === '') {
            return '/';
        }
        return str_starts_with($n, '/') ? $n : '/' . $n;
    }

    private function normalizePattern(string $pattern): string
    {
        $n = rtrim($pattern, '/');
        if ($n === '') {
            return '/';
        }
        return str_starts_with($n, '/') ? $n : '/' . $n;
    }

    private function isPattern(string $path): bool
    {
        return strpbrk($path, '()*?[]{}:') !== false;
    }

    private function compileRegex(string $pattern): string
    {
        $escaped = str_replace('~', '\\~', $pattern);
        return '~^' . $escaped . '$~';
    }
}
