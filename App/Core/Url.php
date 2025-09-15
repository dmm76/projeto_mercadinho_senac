<?php declare(strict_types=1);

namespace App\Core;

final class Url
{
    private static function base(): string
    {
        $script = $_SERVER['SCRIPT_NAME'] ?? '';
        $base = rtrim(str_replace('\\','/', dirname($script)), '/');
        if ($base === '/' || $base === '\\') return '';
        return $base;
    }

    /** Gera URL respeitando a subpasta (ex.: /projeto_mercadinho_web) */
    public static function to(string $path): string
    {
        $p = str_starts_with($path, '/') ? $path : '/'.$path;
        return self::base().$p;
    }

    /** Caminho atual normalizado (sem a base) */
    public static function path(): string
    {
        $uri  = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $base = self::base();
        if ($base !== '' && str_starts_with($uri, $base)) {
            $uri = substr($uri, strlen($base));
        }
        $n = rtrim($uri, '/');
        return $n === '' ? '/' : $n;
    }

    /** Verifica se o link é a rota atual (pra “active”) */
    public static function is(string $path): bool
    {
        $n = rtrim($path, '/');
        $n = $n === '' ? '/' : $n;
        return self::path() === $n;
    }
}

