<?php

declare(strict_types=1);

namespace App\DAO;

use PDO;

final class Database
{
    private static ?PDO $pdo = null;

    public static function getConnection(): PDO
    {
        if (self::$pdo === null) {
            $host = $_ENV['DB_HOST'] ?? $_ENV['MYSQLHOST'] ?? '127.0.0.1';
            $port = $_ENV['DB_PORT'] ?? $_ENV['MYSQLPORT'] ?? '3306';
            $database = $_ENV['DB_NAME'] ?? $_ENV['DB_DATABASE'] ?? $_ENV['MYSQLDATABASE'] ?? 'mercadinho';
            $user = $_ENV['DB_USER'] ?? $_ENV['DB_USERNAME'] ?? $_ENV['MYSQLUSER'] ?? 'root';
            $pass = $_ENV['DB_PASS'] ?? $_ENV['DB_PASSWORD'] ?? $_ENV['MYSQLPASSWORD'] ?? '';

            $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $database);

            self::$pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }
        return self::$pdo;
    }

    /**
     * @template T
     * @param callable(PDO):T $fn
     * @return T
     */
    public static function transaction(callable $fn): mixed
    {
        $pdo = self::getConnection();
        try {
            $pdo->beginTransaction();
            $result = $fn($pdo);
            $pdo->commit();
            return $result;
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }
}