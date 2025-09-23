<?php declare(strict_types=1);

namespace App\Model;

final class Categoria
{
    public function __construct(
        public ?int $id,
        public string $nome,
        public string $slug,
        public int $ativa = 1,
        public ?int $ordem = null
    ) {}
}
