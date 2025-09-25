<?php declare(strict_types=1);

namespace App\Controllers\Conta;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Flash;
use App\Core\Url;
use App\DAO\ClienteDAO;
use App\DAO\Database;
use App\DAO\PasswordResetTokenDAO;
use DateInterval;
use DateTimeImmutable;
use PHPMailer\PHPMailer\PHPMailer;
use Throwable;

final class SenhaController extends Controller
{
    private ClienteDAO $clientes;
    private PasswordResetTokenDAO $tokens;

    public function __construct()
    {
        parent::__construct();

        $pdo = Database::getConnection();
        $this->clientes = new ClienteDAO($pdo);
        $this->tokens   = new PasswordResetTokenDAO($pdo);
    }

    public function create(): void
    {
        $this->render('conta/esqueci_senha', ['title' => 'Esqueci minha senha']);
    }

    public function store(): void
    {
        if (!$this->checkCsrf($_POST['csrf'] ?? null, '/conta/esqueci-senha')) {
            return;
        }

        $email   = strtolower(trim((string) ($_POST['email'] ?? '')));
        $generic = 'Se o e-mail existir, enviaremos um link de recuperação.';
        $isEmail = filter_var($email, FILTER_VALIDATE_EMAIL) !== false;

        if ($email === '' || !$isEmail) {
            Flash::set('error', $generic);
            $this->redirect('/conta/esqueci-senha');
            return;
        }

        try {
            $cliente = $this->clientes->buscarPorEmail($email);
            if ($cliente) {
                $agora  = new DateTimeImmutable();
                $ultimo = $this->tokens->ultimoPedidoPorEmail($email);
                if (!$ultimo || $ultimo->add(new DateInterval('PT15M')) <= $agora) {
                    $tokenPlain = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
                    $tokenHash  = hash('sha256', $tokenPlain);
                    $expiresAt  = $agora->add(new DateInterval('PT1H'));

                    $this->tokens->excluirExpiradosParaCliente($cliente['id']);
                    $this->tokens->criar([
                        'cliente_id' => $cliente['id'],
                        'token_hash' => $tokenHash,
                        'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
                        'ip'         => $_SERVER['REMOTE_ADDR'] ?? null,
                        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
                    ]);

                    $baseUrl = rtrim((string) ($_ENV['APP_URL'] ?? ''), '/');
                    if ($baseUrl === '') {
                        $scheme  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                        $host    = $_SERVER['HTTP_HOST'] ?? 'localhost';
                        $baseUrl = $scheme . '://' . $host;
                    }

                    $link = $baseUrl . Url::to('/conta/resetar-senha') . '?token=' . urlencode($tokenPlain);
                    $this->enviarEmailReset($cliente['email'], $cliente['nome'] ?? null, $link);
                }
            }
        } catch (Throwable $e) {
            error_log('Erro ao processar recuperação de senha: ' . $e->getMessage());
        }

        Flash::set('success', $generic);
        $this->redirect('/conta/esqueci-senha');
    }

    public function edit(): void
    {
        $token = (string) ($_GET['token'] ?? '');
        $this->render('conta/resetar_senha', [
            'title' => 'Definir nova senha',
            'token' => $token,
        ]);
    }

    public function update(): void
    {
        if (!$this->checkCsrf($_POST['csrf'] ?? null, '/conta/resetar-senha')) {
            return;
        }

        $token = (string) ($_POST['token'] ?? '');
        $senha = (string) ($_POST['senha'] ?? '');
        $conf  = (string) ($_POST['senha_confirmacao'] ?? '');

        if ($token === '' || $senha === '' || $senha !== $conf || strlen($senha) < 6) {
            Flash::set('error', 'Senha inválida ou não confere.');
            $this->redirect('/conta/resetar-senha?token=' . urlencode($token));
            return;
        }

        $registro = $this->tokens->buscarValidoPorHash(hash('sha256', $token), new DateTimeImmutable());
        if (!$registro) {
            Flash::set('error', 'Link inválido ou expirado.');
            $this->redirect('/conta/esqueci-senha');
            return;
        }

        $hash = password_hash($senha, PASSWORD_DEFAULT);
        $this->clientes->atualizarSenha($registro['cliente_id'], $hash);
        $this->tokens->marcarComoUsado($registro['id']);

        Auth::logoutEmTodosDispositivos($registro['cliente_id']);

        Flash::set('success', 'Senha alterada com sucesso! Faça login.');
        $this->redirect('/login');
    }

    private function enviarEmailReset(string $email, ?string $nome, string $link): void
    {
        $mail = new PHPMailer(true);
        $mail->CharSet = 'UTF-8';
        $mail->isSMTP();
        $mail->Host       = (string) ($_ENV['MAIL_HOST'] ?? '');
        $mail->Port       = (int) ($_ENV['MAIL_PORT'] ?? 587);
        $mail->SMTPAuth   = true;
        $mail->Username   = (string) ($_ENV['MAIL_USERNAME'] ?? '');
        $mail->Password   = (string) ($_ENV['MAIL_PASSWORD'] ?? '');

        $enc = strtolower((string) ($_ENV['MAIL_ENCRYPTION'] ?? 'tls'));
        if ($enc === 'ssl') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } elseif ($enc === 'tls' || $enc === '') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        } else {
            $mail->SMTPSecure = $enc;
        }

        $fromAddress = (string) ($_ENV['MAIL_FROM_ADDRESS'] ?? $mail->Username);
        $fromName    = (string) ($_ENV['MAIL_FROM_NAME'] ?? 'Mercadinho');
        $mail->setFrom($fromAddress, $fromName);
        $mail->addAddress($email, $nome ?? '');

        $mail->isHTML(true);
        $mail->Subject = 'Recuperação de senha';

        $body = $this->renderViewToString('emails/reset_senha', [
            'nome'  => $nome,
            'email' => $email,
            'link'  => $link,
        ]);

        $mail->Body    = $body;
        $mail->AltBody = 'Para redefinir sua senha, acesse: ' . $link;

        $mail->send();
    }

    /**
     * @param array<string,mixed> $data
     */
    private function renderViewToString(string $template, array $data = []): string
    {
        ob_start();
        $this->render($template, $data);
        return (string) ob_get_clean();
    }

    private function checkCsrf(?string $token, string $redirectTo): bool
    {
        if (Csrf::check($token)) {
            return true;
        }
        Flash::set('error', 'Sessão expirada. Tente novamente.');
        $this->redirect($redirectTo);
        return false;
    }

    private function redirect(string $path): void
    {
        header('Location: ' . Url::to($path), true, 302);
        exit;
    }
}