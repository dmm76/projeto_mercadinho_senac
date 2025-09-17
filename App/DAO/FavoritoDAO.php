<?php

declare(strict_types=1);

namespace App\DAO;

use PDO;

final class FavoritoDAO
{
    public function __construct(private PDO $pdo) {}

    public function toggle(int $clienteId, int $produtoId): bool
    {
        if ($this->exists($clienteId, $produtoId)) {
            $this->remove($clienteId, $produtoId);
            return false;
        }
        $this->add($clienteId, $produtoId);
        return true;
    }

    public function add(int $clienteId, int $produtoId): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT IGNORE INTO cliente_favorito (cliente_id, produto_id, criado_em)
             VALUES (:cliente, :produto, CURRENT_TIMESTAMP)'
        );
        $stmt->execute([
            ':cliente' => $clienteId,
            ':produto' => $produtoId,
        ]);
    }

    public function remove(int $clienteId, int $produtoId): void
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM cliente_favorito WHERE cliente_id = :cliente AND produto_id = :produto'
        );
        $stmt->execute([
            ':cliente' => $clienteId,
            ':produto' => $produtoId,
        ]);
    }

    public function exists(int $clienteId, int $produtoId): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT 1 FROM cliente_favorito WHERE cliente_id = :cliente AND produto_id = :produto LIMIT 1'
        );
        $stmt->execute([
            ':cliente' => $clienteId,
            ':produto' => $produtoId,
        ]);
        return $stmt->fetchColumn() !== false;
    }

    /**
     * @return int[]
     */
    public function listIds(int $clienteId): array
    {
        $stmt = $this->pdo->prepare('SELECT produto_id FROM cliente_favorito WHERE cliente_id = :cliente');
        $stmt->execute([':cliente' => $clienteId]);
        $rows = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        return array_map('intval', $rows ?: []);
    }
}
