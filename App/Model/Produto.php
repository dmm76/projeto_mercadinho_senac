<?php declare(strict_types=1);
namespace App\Model;

final class Produto
{
    public function __construct(
        public ?int   $id,
        public string $nome,
        public string $sku,
        public ?string $ean,
        public int    $categoriaId,
        public ?int   $marcaId,
        public int    $unidadeId,
        public ?string $descricao,
        public ?string $imagem,
        public int    $ativo = 1,
        public int    $pesoVariavel = 0
    ) {}
}
