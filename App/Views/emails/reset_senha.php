<?php
$nome  = isset($nome) && $nome !== null && $nome !== '' ? htmlspecialchars((string) $nome, ENT_QUOTES, 'UTF-8') : 'cliente';
$email = isset($email) ? htmlspecialchars((string) $email, ENT_QUOTES, 'UTF-8') : '';
$link  = isset($link) ? htmlspecialchars((string) $link, ENT_QUOTES, 'UTF-8') : '#';
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8" />
    <title>Recuperação de senha</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.5;">
    <div style="max-width: 480px; margin: 0 auto; padding: 24px;">
        <h1 style="font-size: 20px; color: #c00;">Recuperação de senha</h1>
        <p>Olá <?= $nome ?>,</p>
        <p>Recebemos uma solicitação para redefinir a senha da conta associada a <strong><?= $email ?></strong>.</p>
        <p>Para criar uma nova senha, clique no botão abaixo (o link expira em 1 hora):</p>
        <p style="text-align: center; margin: 24px 0;">
            <a href="<?= $link ?>" style="background:#c00; color:#fff; padding:12px 20px; text-decoration:none; border-radius:4px; display:inline-block;">Redefinir senha</a>
        </p>
        <p>Se você não solicitou essa alteração, ignore este e-mail. Sua senha atual continuará válida.</p>
        <p style="margin-top:32px;">Atenciosamente,<br><strong>Mercadinho Borba Gato</strong></p>
    </div>
</body>
</html>