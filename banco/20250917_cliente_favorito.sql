-- Favoritos de produtos por cliente
CREATE TABLE IF NOT EXISTS cliente_favorito (
    cliente_id INT NOT NULL,
    produto_id INT NOT NULL,
    criado_em  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (cliente_id, produto_id),
    CONSTRAINT fk_cliente_favorito_cliente FOREIGN KEY (cliente_id) REFERENCES cliente (id) ON DELETE CASCADE,
    CONSTRAINT fk_cliente_favorito_produto FOREIGN KEY (produto_id) REFERENCES produto (id) ON DELETE CASCADE
);
