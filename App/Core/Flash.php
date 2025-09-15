<?php declare(strict_types=1);

namespace App\Core;

final class Flash
{
    public static function set(string $key, string $msg): void
    {
        $_SESSION['flash'][$key] = $msg;
    }
    public static function get(string $key): ?string
    {
        if (!isset($_SESSION['flash'][$key])) return null;
        $m = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $m;
    }
}
