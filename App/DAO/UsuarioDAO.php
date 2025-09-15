<?php declare(strict_types=1);

namespace App\DAO;

use PDO;

final class UsuarioDAO
{
    public function __construct(private PDO $pdo) {}

    public function create(string $nome, string $email, string $hash, string $perfil = 'cliente'): int
    {
        // evita e-mail duplicado
        $check = $this->pdo->prepare("SELECT id FROM usuario WHERE email = :email LIMIT 1");
        $check->execute([':email' => $email]);
        if ($check->fetch()) {
            throw new \RuntimeException('E-mail já cadastrado.');
        }

        $ins = $this->pdo->prepare(
            "INSERT INTO usuario (nome, email, senha_hash, perfil, ativo)
             VALUES (:nome, :email, :hash, :perfil, 1)"
        );
        $ins->execute([
            ':nome'   => $nome,
            ':email'  => $email,
            ':hash'   => $hash,
            ':perfil' => $perfil,
        ]);

        return (int)$this->pdo->lastInsertId();
    }
}
