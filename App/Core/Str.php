<?php declare(strict_types=1);

namespace App\Core;

final class Str
{
    public static function slug(string $s): string
    {
        $s = mb_strtolower($s, 'UTF-8');
        $s = iconv('UTF-8', 'ASCII//TRANSLIT', $s) ?: $s;
        $s = preg_replace('~[^a-z0-9]+~', '-', $s) ?? $s;
        return trim($s, '-');
    }
}
