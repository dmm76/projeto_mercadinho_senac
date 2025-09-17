<?php declare(strict_types=1);

namespace App\DAO;

use PDO;

final class MensagemDAO
{
    public function __construct(private PDO $pdo) {}

    /**
     * @param array{q?string,status?string} $filters
     * @return array<int,array<string,mixed>>
     */
    public function list(array $filters = []): array
    {
        $sql = 'SELECT id, nome, email, mensagem, status, criada_em, LEFT(mensagem, 80) AS assunto_preview FROM contato_mensagens';
        $where = [];
        $params = [];

        if (($filters['q'] ?? '') !== '') {
            $where[] = '(nome LIKE :q OR email LIKE :q OR mensagem LIKE :q)';
            $params[':q'] = '%' . $filters['q'] . '%';
        }

        if (($filters['status'] ?? '') !== '') {
            $where[] = 'status = status';
            $params['status'] = $filters['status'];
        }

        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $sql .= ' ORDER BY criada_em DESC LIMIT 100';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /** @return array{id:int,nomestring,emailstring,mensagemstring,statusstring,criada_emstring,ip:?string,user_agent:?string,resposta:?string,respondida_em:?string}|null */
    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, nome, email, mensagem, status, criada_em, ip, user_agent, resposta, respondida_em FROM contato_mensagens WHERE id = id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function atualizarStatus(int $id, string $status): void
    {
        $stmt = $this->pdo->prepare('UPDATE contato_mensagens SET status = st, respondida_em = (CASE WHEN st = "respondida" THEN NOW() ELSE NULL END) WHERE id = id');
        $stmt->execute([':st' => $status, ':id' => $id]);
    }

    public function registrarResposta(int $id, string $resposta): void
    {
        $stmt = $this->pdo->prepare('UPDATE contato_mensagens SET status = "respondida", respondida_em = NOW(), resposta = :resposta WHERE id = id');
        $stmt->execute([':resposta' => $resposta, ':id' => $id]);
    }
}