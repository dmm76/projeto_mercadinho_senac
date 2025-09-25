<?php declare(strict_types=1);

namespace App\DAO;

use PDO;

final class ClienteDAO
{
    public function __construct(private PDO $pdo) {}

    /**
     * @return array{id:int,usuario_id:int,email:string,nome:?string}|null
     */
    public function buscarPorEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT c.id, c.usuario_id, u.email, u.nome
               FROM cliente c
               JOIN usuario u ON u.id = c.usuario_id
              WHERE u.email = :email
              LIMIT 1'
        );
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            return null;
        }

        $usuarioId = isset($row['usuario_id']) ? (int) $row['usuario_id'] : 0;
        if ($usuarioId <= 0) {
            return null;
        }

        return [
            'id'         => (int) $row['id'],
            'usuario_id' => $usuarioId,
            'email'      => (string) $row['email'],
            'nome'       => isset($row['nome']) ? (string) $row['nome'] : null,
        ];
    }

    public function atualizarSenha(int $clienteId, string $hash): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE usuario u
              JOIN cliente c ON c.usuario_id = u.id
               SET u.senha_hash = :hash
             WHERE c.id = :cliente_id'
        );
        $stmt->execute([
            ':hash'        => $hash,
            ':cliente_id'  => $clienteId,
        ]);
    }

    public function usuarioIdPorCliente(int $clienteId): ?int
    {
        $stmt = $this->pdo->prepare('SELECT usuario_id FROM cliente WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $clienteId]);
        $usuarioId = $stmt->fetchColumn();
        return $usuarioId === false || $usuarioId === null ? null : (int) $usuarioId;
    }
}
