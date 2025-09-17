<?php

declare(strict_types=1);

namespace App\Core;

use App\DAO\Database;
use PDO;

final class Auth
{
    private const INTENDED_SESSION_KEY = 'auth_intended';

    /**
     * Retorna o usuario logado.
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

    /** Conveniencia (opcional) */
    public static function isAdmin(): bool
    {
        $u = self::user();
        return $u !== null && $u['perfil'] === 'admin';
    }

    public static function requireAdmin(): void
    {
        $u = self::user();
        if (!$u || $u['perfil'] !== 'admin') {
            self::redirectToLogin('Faca login como administrador para continuar.', '/admin');
        }
    }

    public static function requireLogin(string $message = 'Faca login para continuar.', string $fallback = '/'): void
    {
        if (self::isLoggedIn()) {
            return;
        }

        self::redirectToLogin($message, $fallback);
    }

    public static function logout(): void
    {
        self::ensureSession();
        unset(
            $_SESSION['user'],
            $_SESSION['user_id'],
            $_SESSION['nome'],
            $_SESSION['cliente_id'],
            $_SESSION['carrinho'],
            $_SESSION['cart_count'],
            $_SESSION[self::INTENDED_SESSION_KEY]
        ); // + limpa cliente_id, carrinho e destino
    }

    /**
     * Autentica e carrega o usuario na sessao.
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

        // resolve e cacheia o cliente_id (se existir)
        $stmt = $pdo->prepare('SELECT id FROM cliente WHERE usuario_id = ? LIMIT 1');
        $stmt->execute([$id]);
        $cliId = $stmt->fetchColumn();
        if ($cliId) {
            $_SESSION['cliente_id'] = (int) $cliId;
        } else {
            unset($_SESSION['cliente_id']); // usuario pode nao ter cliente
        }

        return true;
    }

    /**
     * Recupera e consome a rota pretendida pelo usuario.
     */
    public static function popIntended(?string $default = null): ?string
    {
        self::ensureSession();

        if (!isset($_SESSION[self::INTENDED_SESSION_KEY])) {
            return $default;
        }

        $target = $_SESSION[self::INTENDED_SESSION_KEY];
        unset($_SESSION[self::INTENDED_SESSION_KEY]);

        $normalized = self::normalizeTarget(is_string($target) ? $target : null);

        return $normalized ?? $default;
    }

    /**
     * Armazena o destino pretendido e redireciona para o login.
     */
    private static function redirectToLogin(string $message, string $fallback): void
    {
        self::storeIntended($fallback);

        if ($message !== '') {
            Flash::set('error', $message);
        }

        header('Location: ' . Url::to('/login'), true, 302);
        exit;
    }

    /**
     * Guarda a URL requisitada (sem o prefixo da aplicacao) para pos-login.
     */
    private static function storeIntended(?string $fallback = null): void
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $path = Url::path();
        $query = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_QUERY);
        $fallback = self::normalizeTarget($fallback);

        if ($path === '/login') {
            $path = null; // evita ciclo login -> login
        } elseif ($method !== 'GET') {
            $path = $fallback ?? $path;
            $query = null;
        }

        if ($path === null || $path === '') {
            $path = $fallback ?? '/';
        }

        if (is_string($query) && $query !== '') {
            $path .= '?' . $query;
        }

        self::ensureSession();
        $_SESSION[self::INTENDED_SESSION_KEY] = $path;
    }

    private static function normalizeTarget(?string $target): ?string
    {
        if ($target === null) {
            return null;
        }

        $trimmed = trim($target);
        if ($trimmed === '') {
            return null;
        }

        return str_starts_with($trimmed, '/') ? $trimmed : '/' . ltrim($trimmed, '/');
    }

    /**
     * Retorna o cliente_id associado ao usuario logado (ou null se nao existir).
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

        return null; // usuario sem registro em cliente
    }

    private static function ensureSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }
}
