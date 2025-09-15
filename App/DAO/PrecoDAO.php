<?php declare(strict_types=1);
namespace App\DAO;

use PDO;

final class PrecoDAO
{
    public function __construct(private PDO $pdo) {}

    public function create(int $produtoId, float $precoVenda, ?float $promocional, ?string $ini, ?string $fim): int
    {
        $st = $this->pdo->prepare('INSERT INTO preco (produto_id, preco_venda, preco_promocional, inicio_promo, fim_promo, criado_em)
                                   VALUES (:p,:v,:pr,:i,:f,NOW())');
        $st->execute([
            ':p'=>$produtoId, ':v'=>$precoVenda,
            ':pr'=>$promocional, ':i'=>$ini, ':f'=>$fim
        ]);
        return (int)$this->pdo->lastInsertId();
    }
}
