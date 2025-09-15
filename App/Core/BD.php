<?php
namespace App\Core;

use PDO;

class BD {
    private static ?PDO $pdo = null;

    public static function conn(): PDO {
        if (!self::$pdo) {
            self::$pdo = new PDO('mysql:host=127.0.0.1;dbname=mercadinho;charset=utf8mb4','root','',[
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        }
        return self::$pdo;
    }

    public static function agoraSql(): string {
        // útil para comparar promoções (inicio/fim)
        return date('Y-m-d H:i:s');
    }
}
