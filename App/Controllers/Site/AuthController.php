<?php

declare(strict_types=1);

namespace App\Controllers\Site;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Flash;
use App\Core\Url;
use App\DAO\Database;
use App\DAO\UsuarioDAO;
use PDO;

final class AuthController extends Controller
{
    public function showLogin(): void
    {
        $this->render('site/auth/login', ['title' => 'Entrar']);
    }

    public function login(): void
    {
        if (!Csrf::check($_POST['csrf'] ?? null)) {
            Flash::set('error', 'Token inválido.');
            header('Location: ' . Url::to('/login'), true, 303);
            exit;
        }

        $email = trim($_POST['email'] ?? '');
        $pass  = (string)($_POST['password'] ?? '');

        if ($email === '' || $pass === '') {
            Flash::set('error', 'Informe e-mail e senha.');
            header('Location: ' . Url::to('/login'), true, 303);
            exit;
        }

        if (Auth::login($email, $pass)) {
            // compat com navbar antiga que checa user_id/nome
            $u = \App\Core\Auth::user();
            if ($u) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $u['id'];
                $_SESSION['nome']    = $u['nome'];
                $_SESSION['cliente_id'] = $this->ensureClienteId((int)$u['id']);
            }

            // se for admin vai pro /admin, senão vai pra home
            $dest = ($u && $u['perfil'] === 'admin') ? '/admin' : '/';
            \App\Core\Flash::set('success', 'Bem-vindo, ' . ($u['nome'] ?? ''));
            // PRG
            header('Location: ' . Url::to($dest), true, 303);
            exit;
        }

        Flash::set('error', 'Credenciais inválidas.');
        header('Location: ' . Url::to('/login'), true, 303);
        exit;
    }

    public function showRegister(): void
    {
        $this->render('site/auth/registrar', ['title' => 'Criar conta']);
    }

    public function register(): void
    {
        if (!Csrf::check($_POST['csrf'] ?? null)) {
            Flash::set('error', 'Token inválido.');
            header('Location: ' . Url::to('/registrar'));
            exit;
        }

        $nome  = trim($_POST['nome'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $pass  = (string)($_POST['password'] ?? '');
        $pass2 = (string)($_POST['password2'] ?? '');

        if ($nome === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $pass === '' || $pass !== $pass2) {
            Flash::set('error', 'Dados inválidos.');
            header('Location: ' . Url::to('/registrar'));
            exit;
        }

        $dao = new UsuarioDAO(Database::getConnection());
        $hash = password_hash($pass, PASSWORD_BCRYPT);
        try {
            $dao->create($nome, $email, $hash, 'cliente');
        } catch (\Throwable $e) {
            // se quiser, cheque por código 1062 (duplicate key)
            Flash::set('error', 'Este e-mail já está em uso.');
            header('Location: ' . Url::to('/registrar'));
            exit;
        }

        Flash::set('success', 'Conta criada! Faça login.');
        header('Location: ' . Url::to('/login'));
        exit;
    }

    public function logout(): void
    {
        Auth::logout();
        unset($_SESSION['cliente_id'], $_SESSION['user_id'], $_SESSION['nome']); // + estas 2
        \App\Core\Flash::set('success', 'Você saiu com sucesso.');
        session_regenerate_id(true);
        header('Location: ' . Url::to('/'), true, 303);
        exit;
    }


    /**
     * Garante que exista um cliente vinculado ao usuário informado.
     * Retorna o cliente_id. Cria se não existir.
     */
    private function ensureClienteId(int $usuarioId): int
    {
        $pdo = Database::getConnection();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare("SELECT id FROM cliente WHERE usuario_id = ?");
            $stmt->execute([$usuarioId]);
            $id = $stmt->fetchColumn();

            if ($id) {
                $pdo->commit();
                return (int) $id;
            }

            $ins = $pdo->prepare("INSERT INTO cliente (usuario_id) VALUES (?)");
            $ins->execute([$usuarioId]);
            $novoId = (int) $pdo->lastInsertId();

            $pdo->commit();
            return $novoId;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
