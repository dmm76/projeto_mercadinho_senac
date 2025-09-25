<?php declare(strict_types=1);

namespace App\DAO;

use DateTimeImmutable;
use PDO;

final class PasswordResetTokenDAO
{
    public function __construct(private PDO $pdo) {}

    /**
     * @param array{cliente_id:int,token_hash:string,expires_at:string,ip?:?string,user_agent?:?string} $dados
     */
    public function criar(array $dados): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO password_reset_tokens (cliente_id, token_hash, expires_at, ip, user_agent)
             VALUES (:cliente_id, :token_hash, :expires_at, :ip, :user_agent)'
        );
        $stmt->execute([
            ':cliente_id' => $dados['cliente_id'],
            ':token_hash' => $dados['token_hash'],
            ':expires_at' => $dados['expires_at'],
            ':ip'         => $dados['ip'] ?? null,
            ':user_agent' => $dados['user_agent'] ?? null,
        ]);
    }

    /**
     * @return array{id:int,cliente_id:int,token_hash:string,expires_at:DateTimeImmutable,created_at:DateTimeImmutable}|null
     */
    public function buscarValidoPorHash(string $tokenHash, DateTimeImmutable $agora): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, cliente_id, token_hash, expires_at, created_at
               FROM password_reset_tokens
              WHERE token_hash = :token_hash
                AND used_at IS NULL
                AND expires_at > :agora
              LIMIT 1'
        );
        $stmt->execute([
            ':token_hash' => $tokenHash,
            ':agora'      => $agora->format('Y-m-d H:i:s'),
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            return null;
        }

        $expiresAt = new DateTimeImmutable((string) $row['expires_at']);
        $createdAt = new DateTimeImmutable((string) $row['created_at']);

        return [
            'id'          => (int) $row['id'],
            'cliente_id'  => (int) $row['cliente_id'],
            'token_hash'  => (string) $row['token_hash'],
            'expires_at'  => $expiresAt,
            'created_at'  => $createdAt,
        ];
    }

    public function marcarComoUsado(int $id): void
    {
        $stmt = $this->pdo->prepare('UPDATE password_reset_tokens SET used_at = NOW() WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }

    public function excluirExpiradosParaCliente(int $clienteId): void
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM password_reset_tokens
             WHERE cliente_id = :cliente_id AND (used_at IS NOT NULL OR expires_at <= NOW())'
        );
        $stmt->execute([':cliente_id' => $clienteId]);
    }

    public function ultimoPedidoPorEmail(string $email): ?DateTimeImmutable
    {
        $stmt = $this->pdo->prepare(
            'SELECT t.created_at
               FROM password_reset_tokens t
               JOIN cliente c   ON c.id = t.cliente_id
               JOIN usuario u   ON u.id = c.usuario_id
              WHERE u.email = :email
              ORDER BY t.created_at DESC
              LIMIT 1'
        );
        $stmt->execute([':email' => $email]);
        $value = $stmt->fetchColumn();
        return $value === false ? null : new DateTimeImmutable((string) $value);
    }
}
