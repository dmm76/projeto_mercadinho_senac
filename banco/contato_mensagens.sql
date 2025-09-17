DELIMITER $$
CREATE TABLE IF NOT EXISTS contato_mensagens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(120) NOT NULL,
    email VARCHAR(160) NOT NULL,
    mensagem TEXT NOT NULL,
    resposta TEXT NULL,
    status ENUM(''aberta'',''respondida'',''arquivada'') NOT NULL DEFAULT ''aberta'',
    ip VARCHAR(45) DEFAULT NULL,
    user_agent VARCHAR(255) DEFAULT NULL,
    criada_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    respondida_em TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
DELIMITER ;