<?php declare(strict_types=1);

namespace App\DAO;

use PDO;

final class UsuarioDAO
{
    public function __construct(private PDO $pdo) {}

    /**
     * @param array{q?:string,perfil?:string,status?:string} $filters
     * @return array<int,array<string,mixed>>
     */
    public function search(array $filters = []): array
    {
        $sql = 'SELECT id, nome, email, perfil, ativo, criado_em FROM usuario WHERE 1=1';
        $params = [];

        if (($filters['q'] ?? '') !== '') {
            $params[':q'] = '%' . $filters['q'] . '%';
            $sql .= ' AND (nome LIKE :q OR email LIKE :q)';
        }

        if (($filters['perfil'] ?? '') !== '') {
            $params[':perfil'] = $filters['perfil'];
            $sql .= ' AND perfil = :perfil';
        }

        if (($filters['status'] ?? '') === 'ativo') {
            $sql .= ' AND ativo = 1';
        } elseif (($filters['status'] ?? '') === 'inativo') {
            $sql .= ' AND ativo = 0';
        }

        $sql .= ' ORDER BY criado_em DESC, id DESC LIMIT 200';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /** @return array{id:int,nome:string,email:string,perfil:string,ativo:int,criado_em:string}|null */
    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, nome, email, perfil, ativo, criado_em FROM usuario WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function create(string $nome, string $email, string $hash, string $perfil = 'cliente'): int
    {
        $this->assertEmailDisponivel($email);

        $ins = $this->pdo->prepare(
            'INSERT INTO usuario (nome, email, senha_hash, perfil, ativo) VALUES (:nome, :email, :hash, :perfil, 1)'
        );
        $ins->execute([
            ':nome'   => $nome,
            ':email'  => $email,
            ':hash'   => $hash,
            ':perfil' => $perfil,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function updateBasico(int $id, string $nome, string $email, string $perfil, bool $ativo): void
    {
        $this->assertEmailDisponivel($email, $id);

        $stmt = $this->pdo->prepare(
            'UPDATE usuario SET nome = :nome, email = :email, perfil = :perfil, ativo = :ativo WHERE id = :id'
        );
        $stmt->execute([
            ':nome'   => $nome,
            ':email'  => $email,
            ':perfil' => $perfil,
            ':ativo'  => $ativo ? 1 : 0,
            ':id'     => $id,
        ]);
    }

    public function setAtivo(int $id, bool $ativo): void
    {
        $stmt = $this->pdo->prepare('UPDATE usuario SET ativo = :ativo WHERE id = :id');
        $stmt->execute([
            ':ativo' => $ativo ? 1 : 0,
            ':id'    => $id,
        ]);
    }

    private function assertEmailDisponivel(string $email, int $ignoreId = 0): void
    {
        $sql = 'SELECT id FROM usuario WHERE email = :email';
        $params = [':email' => $email];
        if ($ignoreId > 0) {
            $sql .= ' AND id <> :id';
            $params[':id'] = $ignoreId;
        }
        $sql .= ' LIMIT 1';

        $check = $this->pdo->prepare($sql);
        $check->execute($params);
        if ($check->fetch()) {
            throw new \RuntimeException('E-mail já cadastrado por outro usuário.');
        }
    }
}