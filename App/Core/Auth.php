<?php

declare(strict_types=1);

namespace App\Core;

use App\DAO\Database;
use PDO;

final class Auth
{
    /**
     * Retorna o usuário logado.
     * @return array{id:int,nome:string,email:string,perfil:string,ativo:int}|null
     */
    public static function user(): ?array
    {
        self::ensureSession();

        $raw = $_SESSION['user'] ?? null;
        if (!is_array($raw)) {
            return null;
        }
        foreach (['id', 'nome', 'email', 'perfil', 'ativo'] as $k) {
            if (!array_key_exists($k, $raw)) {
                return null;
            }
        }

        $id     = filter_var($raw['id'], FILTER_VALIDATE_INT);
        $ativo  = filter_var($raw['ativo'], FILTER_VALIDATE_INT);
        $nome   = is_string($raw['nome'])   ? $raw['nome']   : null;
        $email  = is_string($raw['email'])  ? $raw['email']  : null;
        $perfil = is_string($raw['perfil']) ? $raw['perfil'] : null;

        if ($id === false || $ativo === false || $nome === null || $email === null || $perfil === null) {
            return null;
        }

        return [
            'id'     => $id,
            'nome'   => $nome,
            'email'  => $email,
            'perfil' => $perfil,
            'ativo'  => $ativo,
        ];
    }

    public static function isLoggedIn(): bool
    {
        return self::user() !== null;
    }

    /** Conveniência (opcional) */
    public static function isAdmin(): bool
    {
        $u = self::user();
        return $u !== null && $u['perfil'] === 'admin';
    }

    public static function requireAdmin(): void
    {
        $u = self::user();
        if (!$u || $u['perfil'] !== 'admin') {
            header('Location: /login');
            exit;
        }
    }

    public static function logout(): void
    {
        self::ensureSession();
        unset($_SESSION['user'], $_SESSION['user_id'], $_SESSION['nome'], $_SESSION['cliente_id']); // + limpa cliente_id
    }

    /**
     * Autentica e carrega o usuário na sessão.
     */
    public static function login(string $email, string $senha): bool
    {
        self::ensureSession();

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            'SELECT id, nome, email, perfil, ativo, senha_hash
             FROM usuario
             WHERE email = ?
             LIMIT 1'
        );
        $stmt->execute([$email]);

        /** @var array{id:int|string,nome:string,email:string,perfil:string,ativo:int|string,senha_hash:string}|false $row */
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            return false;
        }

        $id     = (int) $row['id'];
        $nome   = (string) $row['nome'];
        $mail   = (string) $row['email'];
        $perfil = (string) $row['perfil'];
        $ativo  = (int) $row['ativo'];
        $hash   = (string) $row['senha_hash'];

        if (!password_verify($senha, $hash)) {
            return false;
        }
        if ($ativo !== 1) {
            return false;
        }

        $_SESSION['user'] = [
            'id'     => $id,
            'nome'   => $nome,
            'email'  => $mail,
            'perfil' => $perfil,
            'ativo'  => $ativo,
        ];

        // 🔗 já resolve e cacheia o cliente_id (se existir)
        $stmt = $pdo->prepare('SELECT id FROM cliente WHERE usuario_id = ? LIMIT 1');
        $stmt->execute([$id]);
        $cliId = $stmt->fetchColumn();
        if ($cliId) {
            $_SESSION['cliente_id'] = (int) $cliId;
        } else {
            unset($_SESSION['cliente_id']); // usuário pode não ter cliente
        }

        return true;
    }

    /**
     * Retorna o cliente_id associado ao usuário logado (ou null se não existir).
     * Cacheado em $_SESSION['cliente_id'] para evitar SELECT em cada request.
     */
    public static function clienteId(): ?int
    {
        self::ensureSession();
        $u = self::user();
        if (!$u) {
            return null;
        }

        // cache
        if (isset($_SESSION['cliente_id']) && is_numeric($_SESSION['cliente_id'])) {
            return (int) $_SESSION['cliente_id'];
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT id FROM cliente WHERE usuario_id = ? LIMIT 1');
        $stmt->execute([$u['id']]);
        $id = $stmt->fetchColumn();

        if ($id) {
            $_SESSION['cliente_id'] = (int) $id;
            return (int) $id;
        }

        return null; // usuário sem registro em cliente
    }

    private static function ensureSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }
}
