<?php

declare(strict_types=1);

namespace App\Controllers\Site;

use App\Core\Url; // ADICIONE este use


use App\DAO\Database;

class ContatoController
{
    private const CSRF_KEY  = 'csrf_contact';
    private const FLASH_KEY = 'flash_contact';
    private const OLD_KEY   = 'old_contact';

    public function show(): void
    {
        $this->ensureSession();

        if (empty($_SESSION[self::CSRF_KEY])) {
            $_SESSION[self::CSRF_KEY] = bin2hex(random_bytes(32));
        }

        // flash + old
        $flash = $_SESSION[self::FLASH_KEY] ?? null;
        unset($_SESSION[self::FLASH_KEY]);

        $old = $_SESSION[self::OLD_KEY] ?? [];
        unset($_SESSION[self::OLD_KEY]);

        $csrfToken = $_SESSION[self::CSRF_KEY];

        // Ajuste este caminho conforme sua estrutura de pastas
        $view = dirname(__DIR__, 2) . '/Views/site/home/contato.php';
        if (!is_file($view)) {
            http_response_code(500);
            echo 'View não encontrada: ' . $view;
            return;
        }

        /** @var array|null $flash */
        /** @var array $old */
        /** @var string $csrfToken */
        include $view;
    }

    public function send(): void
    {
        $this->ensureSession();

        $nome     = trim($_POST['nome']     ?? '');
        $email    = trim($_POST['email']    ?? '');
        $mensagem = trim($_POST['mensagem'] ?? '');
        $csrf     = $_POST['csrf']          ?? '';
        $robot    = trim($_POST['website']  ?? ''); // honeypot: deve ficar vazio

        $_SESSION[self::OLD_KEY] = compact('nome', 'email', 'mensagem');

        $errors = [];

        if (!$csrf || !hash_equals($_SESSION[self::CSRF_KEY] ?? '', $csrf)) {
            $errors[] = 'Falha de segurança. Atualize a página e tente novamente.';
        }
        if ($robot !== '') {
            $errors[] = 'Verificação anti-spam falhou.';
        }
        if (mb_strlen($nome) < 3) {
            $errors[] = 'Informe seu nome completo (mínimo 3 caracteres).';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Informe um e-mail válido.';
        }
        if (mb_strlen($mensagem) < 10) {
            $errors[] = 'Escreva uma mensagem (mínimo 10 caracteres).';
        }

        if ($errors) {
            $_SESSION[self::FLASH_KEY] = ['type' => 'danger', 'messages' => $errors];
            header('Location: ' . Url::to('/contato'), true, 303);
            exit;
        }

        // Persistência
        $pdo = Database::getConnection(); // deve retornar PDO
        $sql = 'INSERT INTO contato_mensagens (nome, email, mensagem, ip, user_agent) VALUES (?, ?, ?, ?, ?)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $nome,
            $email,
            $mensagem,
            $_SERVER['REMOTE_ADDR'] ?? null,
            substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
        ]);

        unset($_SESSION[self::OLD_KEY]);
        $_SESSION[self::FLASH_KEY] = [
            'type'     => 'success',
            'messages' => ['Mensagem enviada com sucesso! Em breve retornaremos.'],
        ];

        header('Location: ' . Url::to('/contato'), true, 303);
        exit;
    }

    private function ensureSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }
}
