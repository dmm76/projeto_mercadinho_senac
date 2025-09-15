<?php declare(strict_types=1);

namespace App\Models;

final class Usuario
{
    public function __construct(
        public ?int $id,
        public string $nome,
        public string $email,
        public string $senhaHash,
        public string $perfil = 'cliente',
        public int $ativo = 1
    ) {}
}
