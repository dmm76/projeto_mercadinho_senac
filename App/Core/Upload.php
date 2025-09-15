<?php declare(strict_types=1);
namespace App\Core;

final class Upload
{
    /**
     * @param array<string,mixed> $file
     */
    public static function image(array $file, string $destDir, int $maxBytes = 2_000_000): string
    {
        $tmp  = $file['tmp_name'] ?? null;
        $sizeRaw = $file['size'] ?? null;

        if (!is_string($tmp) || $tmp === '' || !is_uploaded_file($tmp)) {
            throw new \RuntimeException('Arquivo inválido.');
        }

        // --- normaliza size para int (sem cast de mixed) ---
        if (is_int($sizeRaw)) {
            $size = $sizeRaw;
        } elseif (is_string($sizeRaw) && preg_match('/^\d+$/', $sizeRaw) === 1) {
            $size = (int) $sizeRaw;
        } else {
            throw new \RuntimeException('Tamanho de arquivo inválido.');
        }
        // ---------------------------------------------------

        if ($size > $maxBytes) {
            throw new \RuntimeException('Arquivo grande demais (máx 2MB).');
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($tmp);
        if ($mime === false) {
            throw new \RuntimeException('Não foi possível detectar o MIME.');
        }

        $ext = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            default      => throw new \RuntimeException('Formato não permitido (JPG/PNG).'),
        };

        if (!is_dir($destDir)) { @mkdir($destDir, 0775, true); }

        $name   = bin2hex(random_bytes(8)) . '-' . time() . '.' . $ext;
        $target = rtrim($destDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $name;

        if (!move_uploaded_file($tmp, $target)) {
            throw new \RuntimeException('Falha ao mover upload.');
        }
        return $name;
    }
}
