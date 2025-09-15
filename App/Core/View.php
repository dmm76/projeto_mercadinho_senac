<?php

declare(strict_types=1);

namespace App\Core;

final class View
{
    private string $basePath;

    public function __construct()
    {
        $bp = realpath(__DIR__ . '/../Views');
        $this->basePath = $bp !== false ? $bp : (__DIR__ . '/../Views');
    }

    /** @param array<string,mixed> $data */
    public function render(string $template, array $data = []): void
    {
        $rel = str_replace(['.', ':'], DIRECTORY_SEPARATOR, $template) . '.php';
        $file = $this->basePath . DIRECTORY_SEPARATOR . $rel;

        if (!is_file($file)) {
            http_response_code(500);
            echo 'View não encontrada: ' . htmlspecialchars($file, ENT_QUOTES, 'UTF-8');
            return;
        }

        /** @var array<string,mixed> $data */
        extract($data, EXTR_SKIP);
        require $file;
    }

    /** -------- Helpers de parciais -------- */

    /** Inclui uma parcial relativa a App/Views (uso estático) */
    public static function partial(string $path): void
    {
        $root = realpath(__DIR__ . '/../Views') ?: (__DIR__ . '/../Views');
        // normaliza separadores e remove barra inicial
        $norm  = ltrim(str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $path), DIRECTORY_SEPARATOR);
        $full  = $root . DIRECTORY_SEPARATOR . $norm;

        if (!is_file($full)) {
            http_response_code(500);
            echo 'Parcial não encontrada: ' . htmlspecialchars($full, ENT_QUOTES, 'UTF-8');
            return;
        }
        require $full;
    }

    /** Inclui uma parcial relativa a App/Views (uso via $this) */
    public function includePartial(string $path): void
    {
        self::partial($path);
    }
}
