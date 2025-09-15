<?php declare(strict_types=1);
namespace App\Model;

final class Unidade {
    public function __construct(
        public ?int $id,
        public string $sigla,
        public ?string $descricao = null
    ) {}
}
