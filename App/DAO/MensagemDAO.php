<?php

declare(strict_types=1);

namespace App\DAO;

use PDO;

final class MensagemDAO
{
    public function __construct(private PDO $pdo) {}

    /**
     * @param array{q?:string,status?:string} $filters
     * @return array<int,array<string,mixed>>
     */
    public function list(array $filters = []): array
    {
        $sql = 'SELECT id, nome, email, mensagem, status, criada_em, LEFT(mensagem, 80) AS assunto_preview FROM contato_mensagens';
        $where = [];
        $params = [];

        if (!empty($filters['q'])) {
            $where[] = '(nome LIKE :q OR email LIKE :q OR mensagem LIKE :q)';
            $params[':q'] = '%' . $filters['q'] . '%';
        }

        if (!empty($filters['status'])) {
            $where[] = 'status = :status';
            $params[':status'] = $filters['status'];
        }

        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $sql .= ' ORDER BY criada_em DESC LIMIT 100';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
