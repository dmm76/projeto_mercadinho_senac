<?php declare(strict_types=1);
namespace App\DAO;

use PDO;

final class EstoqueDAO
{
    public function __construct(private PDO $pdo) {}

    public function createInit(int $produtoId, float $quantidade = 0.0, float $minimo = 0.0): void
    {
        $st = $this->pdo->prepare('INSERT INTO estoque (produto_id, quantidade, minimo, atualizado_em) VALUES (:p,:q,:m,NOW())');
        $st->execute([':p'=>$produtoId, ':q'=>$quantidade, ':m'=>$minimo]);
    }

    /** @return array{quantidade:float,minimo:float}|null */
    public function getByProduto(int $produtoId): ?array
    {
        $st = $this->pdo->prepare('SELECT quantidade,minimo FROM estoque WHERE produto_id=:p');
        $st->execute([':p'=>$produtoId]);
        /** @var array{quantidade:int|float|string,minimo:int|float|string}|false $r */
        $r = $st->fetch(PDO::FETCH_ASSOC);
        if ($r === false) { return null; }
        return ['quantidade'=>(float)$r['quantidade'], 'minimo'=>(float)$r['minimo']];
    }

    public function update(int $produtoId, float $quantidade, float $minimo): void
    {
        $st = $this->pdo->prepare('UPDATE estoque SET quantidade=:q, minimo=:m WHERE produto_id=:p');
        $st->execute([':q'=>$quantidade, ':m'=>$minimo, ':p'=>$produtoId]);
    }
}
