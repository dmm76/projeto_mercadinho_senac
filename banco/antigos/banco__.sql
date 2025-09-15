-- --------------------------------------------------------
-- Servidor:                     127.0.0.1
-- Versão do servidor:           10.4.32-MariaDB - mariadb.org binary distribution
-- OS do Servidor:               Win64
-- HeidiSQL Versão:              12.11.0.7065
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Copiando estrutura do banco de dados para mercadinho
CREATE DATABASE IF NOT EXISTS `mercadinho` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;
USE `mercadinho`;

-- Copiando estrutura para tabela mercadinho.caixa
CREATE TABLE IF NOT EXISTS `caixa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `operador_id` int(11) NOT NULL,
  `abertura` datetime NOT NULL DEFAULT current_timestamp(),
  `saldo_inicial` decimal(12,2) NOT NULL DEFAULT 0.00,
  `fechamento` datetime DEFAULT NULL,
  `saldo_final` decimal(12,2) DEFAULT NULL,
  `observacao` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_caixa_operador_abertura` (`operador_id`,`abertura`),
  CONSTRAINT `fk_caixa_operador` FOREIGN KEY (`operador_id`) REFERENCES `usuario` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Abertura/fechamento de caixa';

-- Copiando dados para a tabela mercadinho.caixa: ~0 rows (aproximadamente)

-- Copiando estrutura para tabela mercadinho.categoria
CREATE TABLE IF NOT EXISTS `categoria` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(120) NOT NULL,
  `slug` varchar(140) NOT NULL,
  `ativa` tinyint(1) NOT NULL DEFAULT 1,
  `ordem` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_categoria_slug` (`slug`),
  KEY `idx_categoria_ativa` (`ativa`,`ordem`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Categorias do catálogo';

-- Copiando dados para a tabela mercadinho.categoria: ~5 rows (aproximadamente)
INSERT INTO `categoria` (`id`, `nome`, `slug`, `ativa`, `ordem`) VALUES
	(1, 'Mercearia', 'mercearia', 1, 1),
	(2, 'Hortifruti', 'hortifruti', 1, 2),
	(3, 'Bebidas', 'bebidas', 1, 3),
	(4, 'Limpeza', 'limpeza', 1, 4),
	(5, 'Padaria', 'produtos de padaria', 1, 5);

-- Copiando estrutura para tabela mercadinho.cliente
CREATE TABLE IF NOT EXISTS `cliente` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) DEFAULT NULL,
  `cpf` varchar(14) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `nascimento` date DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cliente_cpf` (`cpf`),
  UNIQUE KEY `uq_cliente_usuario` (`usuario_id`),
  CONSTRAINT `fk_cliente_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Cadastro de clientes (pode vincular a um usuário)';

-- Copiando dados para a tabela mercadinho.cliente: ~0 rows (aproximadamente)

-- Copiando estrutura para tabela mercadinho.compra
CREATE TABLE IF NOT EXISTS `compra` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fornecedor_id` int(11) NOT NULL,
  `data` datetime NOT NULL DEFAULT current_timestamp(),
  `total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `observacao` text DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_compra_forn_data` (`fornecedor_id`,`data`),
  CONSTRAINT `fk_compra_fornecedor` FOREIGN KEY (`fornecedor_id`) REFERENCES `fornecedor` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Compras de fornecedores';

-- Copiando dados para a tabela mercadinho.compra: ~0 rows (aproximadamente)

-- Copiando estrutura para tabela mercadinho.contato_mensagens
CREATE TABLE IF NOT EXISTS `contato_mensagens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(120) NOT NULL,
  `email` varchar(160) NOT NULL,
  `mensagem` text NOT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `status` enum('nova','lida','respondida','arquivada') NOT NULL DEFAULT 'nova',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_contato_created_at` (`created_at`),
  KEY `idx_contato_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela mercadinho.contato_mensagens: ~6 rows (aproximadamente)
INSERT INTO `contato_mensagens` (`id`, `nome`, `email`, `mensagem`, `ip`, `user_agent`, `status`, `created_at`) VALUES
	(1, 'Douglas Marcelo Monquero', 'douglas.monquero@gmail.com', 'primeiro teste do formulario de contato', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'nova', '2025-09-11 18:30:51'),
	(2, 'Joao Ferlini', 'joao@email.com', 'segundo teste verificacao do botao', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'nova', '2025-09-11 18:36:45'),
	(3, 'Joao Ferlini', 'joao@email.com', 'segundo teste verificacao do botao', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'nova', '2025-09-11 18:37:15'),
	(4, 'Joao Ferlini', 'joao@email.com', 'segundo teste verificacao do botao', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'nova', '2025-09-11 18:38:52'),
	(5, 'Joao Ferlini', 'joao@email.com', 'segundo teste verificacao do botao', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'nova', '2025-09-11 18:40:16'),
	(6, 'Teste', 'testando@123.com', 'Teste do botao', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'nova', '2025-09-11 18:41:11'),
	(7, 'Teste', 'testando@123.com', 'Teste do botao', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'nova', '2025-09-11 18:58:56');

-- Copiando estrutura para tabela mercadinho.cupom
CREATE TABLE IF NOT EXISTS `cupom` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(40) NOT NULL,
  `tipo` enum('percentual','valor') NOT NULL,
  `valor` decimal(12,2) NOT NULL,
  `inicio` datetime DEFAULT NULL,
  `fim` datetime DEFAULT NULL,
  `usos_max` int(11) DEFAULT NULL,
  `usos_ate_agora` int(11) NOT NULL DEFAULT 0,
  `regras_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`regras_json`)),
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cupom_codigo` (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Cupons de desconto';

-- Copiando dados para a tabela mercadinho.cupom: ~0 rows (aproximadamente)

-- Copiando estrutura para tabela mercadinho.endereco
CREATE TABLE IF NOT EXISTS `endereco` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_id` int(11) NOT NULL,
  `rotulo` varchar(50) DEFAULT NULL,
  `nome` varchar(80) NOT NULL,
  `cep` varchar(9) NOT NULL,
  `logradouro` varchar(200) NOT NULL,
  `numero` varchar(20) NOT NULL,
  `complemento` varchar(120) DEFAULT NULL,
  `bairro` varchar(120) NOT NULL,
  `cidade` varchar(120) NOT NULL,
  `uf` char(2) NOT NULL,
  `principal` tinyint(1) NOT NULL DEFAULT 0,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_endereco_cliente` (`cliente_id`,`principal`),
  CONSTRAINT `fk_endereco_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `cliente` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Endereços de clientes (um pode ser principal)';

-- Copiando dados para a tabela mercadinho.endereco: ~0 rows (aproximadamente)

-- Copiando estrutura para tabela mercadinho.estoque
CREATE TABLE IF NOT EXISTS `estoque` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `produto_id` int(11) NOT NULL,
  `quantidade` decimal(10,3) NOT NULL DEFAULT 0.000,
  `minimo` decimal(10,3) NOT NULL DEFAULT 0.000,
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_estoque_produto` (`produto_id`),
  KEY `idx_estoque_minimo` (`minimo`),
  CONSTRAINT `fk_estoque_produto` FOREIGN KEY (`produto_id`) REFERENCES `produto` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Estoque atual por produto (1:1)';

-- Copiando dados para a tabela mercadinho.estoque: ~0 rows (aproximadamente)
INSERT INTO `estoque` (`id`, `produto_id`, `quantidade`, `minimo`, `atualizado_em`) VALUES
	(1, 2, 100.000, 10.000, '2025-09-11 19:48:16'),
	(2, 3, 100.000, 10.000, '2025-09-12 17:16:36');

-- Copiando estrutura para tabela mercadinho.fornecedor
CREATE TABLE IF NOT EXISTS `fornecedor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(160) NOT NULL,
  `cnpj` varchar(18) DEFAULT NULL,
  `contato` varchar(120) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `email` varchar(160) DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_fornecedor_cnpj` (`cnpj`),
  KEY `idx_fornecedor_nome` (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Fornecedores';

-- Copiando dados para a tabela mercadinho.fornecedor: ~0 rows (aproximadamente)

-- Copiando estrutura para tabela mercadinho.item_compra
CREATE TABLE IF NOT EXISTS `item_compra` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `compra_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `quantidade` decimal(10,3) NOT NULL,
  `custo_unit` decimal(12,4) NOT NULL,
  `desconto_unit` decimal(12,4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_itemcompra_compra` (`compra_id`),
  KEY `idx_itemcompra_produto` (`produto_id`),
  CONSTRAINT `fk_itemcompra_compra` FOREIGN KEY (`compra_id`) REFERENCES `compra` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_itemcompra_produto` FOREIGN KEY (`produto_id`) REFERENCES `produto` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Itens das compras (entrada de estoque)';

-- Copiando dados para a tabela mercadinho.item_compra: ~0 rows (aproximadamente)

-- Copiando estrutura para tabela mercadinho.item_pedido
CREATE TABLE IF NOT EXISTS `item_pedido` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pedido_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `quantidade` decimal(10,3) NOT NULL DEFAULT 1.000,
  `peso_kg` decimal(10,3) DEFAULT NULL,
  `preco_unit` decimal(12,2) NOT NULL,
  `desconto_unit` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_itempedido_pedido` (`pedido_id`),
  KEY `idx_itempedido_produto` (`produto_id`),
  CONSTRAINT `fk_itempedido_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedido` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_itempedido_produto` FOREIGN KEY (`produto_id`) REFERENCES `produto` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Itens de pedido';

-- Copiando dados para a tabela mercadinho.item_pedido: ~0 rows (aproximadamente)

-- Copiando estrutura para tabela mercadinho.marca
CREATE TABLE IF NOT EXISTS `marca` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(120) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_marca_nome` (`nome`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Marcas dos produtos';

-- Copiando dados para a tabela mercadinho.marca: ~4 rows (aproximadamente)
INSERT INTO `marca` (`id`, `nome`) VALUES
	(1, 'Camil'),
	(2, 'Italac'),
	(3, 'Ypê'),
	(4, 'Seara');

-- Copiando estrutura para tabela mercadinho.mov_caixa
CREATE TABLE IF NOT EXISTS `mov_caixa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `caixa_id` int(11) NOT NULL,
  `tipo` enum('entrada','saida') NOT NULL,
  `valor` decimal(12,2) NOT NULL,
  `descricao` varchar(200) DEFAULT NULL,
  `pedido_id` int(11) DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_movcaixa_caixa` (`caixa_id`,`criado_em`),
  KEY `idx_movcaixa_pedido` (`pedido_id`),
  CONSTRAINT `fk_movcaixa_caixa` FOREIGN KEY (`caixa_id`) REFERENCES `caixa` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_movcaixa_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedido` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Movimentações de caixa (vendas/sangria/suprimento)';

-- Copiando dados para a tabela mercadinho.mov_caixa: ~0 rows (aproximadamente)

-- Copiando estrutura para tabela mercadinho.mov_estoque
CREATE TABLE IF NOT EXISTS `mov_estoque` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `produto_id` int(11) NOT NULL,
  `tipo` enum('entrada','saida','ajuste') NOT NULL,
  `quantidade` decimal(10,3) NOT NULL,
  `origem` enum('pedido','compra','ajuste') NOT NULL,
  `referencia_id` int(11) DEFAULT NULL,
  `observacao` text DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_movestoque_produto` (`produto_id`,`criado_em`),
  CONSTRAINT `fk_movestoque_produto` FOREIGN KEY (`produto_id`) REFERENCES `produto` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Movimentações de estoque';

-- Copiando dados para a tabela mercadinho.mov_estoque: ~0 rows (aproximadamente)

-- Copiando estrutura para tabela mercadinho.pedido
CREATE TABLE IF NOT EXISTS `pedido` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_id` int(11) NOT NULL,
  `endereco_id` int(11) DEFAULT NULL,
  `status` enum('novo','em_separacao','em_transporte','pronto','finalizado','cancelado') NOT NULL DEFAULT 'novo',
  `entrega` enum('retirada','entrega') NOT NULL,
  `pagamento` enum('na_entrega','pix','cartao','gateway') NOT NULL DEFAULT 'na_entrega',
  `subtotal` decimal(12,2) NOT NULL,
  `frete` decimal(12,2) NOT NULL DEFAULT 0.00,
  `desconto` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total` decimal(12,2) NOT NULL,
  `codigo_externo` varchar(80) DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_pedido_cliente` (`cliente_id`,`criado_em`),
  KEY `idx_pedido_status` (`status`),
  KEY `fk_pedido_endereco` (`endereco_id`),
  KEY `idx_pedido_status_data` (`status`,`criado_em`),
  CONSTRAINT `fk_pedido_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `cliente` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_pedido_endereco` FOREIGN KEY (`endereco_id`) REFERENCES `endereco` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Pedidos da loja (online/retirada)';

-- Copiando dados para a tabela mercadinho.pedido: ~0 rows (aproximadamente)

-- Copiando estrutura para tabela mercadinho.pedido_cupom
CREATE TABLE IF NOT EXISTS `pedido_cupom` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pedido_id` int(11) NOT NULL,
  `cupom_id` int(11) NOT NULL,
  `valor_desconto_aplicado` decimal(12,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_pedidocupom_pedido` (`pedido_id`),
  KEY `idx_pedidocupom_cupom` (`cupom_id`),
  CONSTRAINT `fk_pedidocupom_cupom` FOREIGN KEY (`cupom_id`) REFERENCES `cupom` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_pedidocupom_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedido` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Aplicações de cupons em pedidos';

-- Copiando dados para a tabela mercadinho.pedido_cupom: ~0 rows (aproximadamente)

-- Copiando estrutura para tabela mercadinho.preco
CREATE TABLE IF NOT EXISTS `preco` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `produto_id` int(11) NOT NULL,
  `preco_venda` decimal(12,2) NOT NULL,
  `preco_promocional` decimal(12,2) DEFAULT NULL,
  `inicio_promo` datetime DEFAULT NULL,
  `fim_promo` datetime DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_preco_produto` (`produto_id`,`inicio_promo`,`fim_promo`),
  CONSTRAINT `fk_preco_produto` FOREIGN KEY (`produto_id`) REFERENCES `produto` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Histórico de preços por produto';

-- Copiando dados para a tabela mercadinho.preco: ~0 rows (aproximadamente)
INSERT INTO `preco` (`id`, `produto_id`, `preco_venda`, `preco_promocional`, `inicio_promo`, `fim_promo`, `criado_em`) VALUES
	(1, 2, 8.55, 5.55, '2025-09-11 16:45:00', '2025-09-12 16:45:00', '2025-09-11 19:48:16'),
	(2, 3, 9.25, 6.25, NULL, NULL, '2025-09-12 17:16:36');

-- Copiando estrutura para tabela mercadinho.produto
CREATE TABLE IF NOT EXISTS `produto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(180) NOT NULL,
  `sku` varchar(60) NOT NULL,
  `ean` varchar(14) DEFAULT NULL,
  `categoria_id` int(11) NOT NULL,
  `marca_id` int(11) DEFAULT NULL,
  `unidade_id` int(11) NOT NULL,
  `descricao` text DEFAULT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `peso_variavel` tinyint(1) NOT NULL DEFAULT 0,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_produto_sku` (`sku`),
  UNIQUE KEY `uq_produto_ean` (`ean`),
  KEY `idx_produto_nome` (`nome`),
  KEY `idx_produto_cat` (`categoria_id`),
  KEY `idx_produto_marca` (`marca_id`),
  KEY `idx_produto_unidade` (`unidade_id`),
  KEY `idx_produto_cat_ativo` (`categoria_id`,`ativo`),
  CONSTRAINT `fk_produto_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categoria` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_produto_marca` FOREIGN KEY (`marca_id`) REFERENCES `marca` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_produto_unidade` FOREIGN KEY (`unidade_id`) REFERENCES `unidade` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Produtos do catálogo';

-- Copiando dados para a tabela mercadinho.produto: ~0 rows (aproximadamente)
INSERT INTO `produto` (`id`, `nome`, `sku`, `ean`, `categoria_id`, `marca_id`, `unidade_id`, `descricao`, `imagem`, `ativo`, `peso_variavel`, `criado_em`) VALUES
	(2, 'Banana Nanica', 'BAN-NAN-GR-KG', NULL, 2, 1, 2, 'banana nanica preço cobrado por kilo', 'uploads/produtos/9d9a01a2f31685ee-1757620096.jpg', 1, 0, '2025-09-11 19:48:16'),
	(3, 'Pão Frances', '152966', NULL, 5, 1, 2, 'pao frances fabricação', NULL, 1, 0, '2025-09-12 17:16:36');

-- Copiando estrutura para tabela mercadinho.unidade
CREATE TABLE IF NOT EXISTS `unidade` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sigla` varchar(10) NOT NULL,
  `descricao` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_unidade_sigla` (`sigla`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Unidades de medida';

-- Copiando dados para a tabela mercadinho.unidade: ~3 rows (aproximadamente)
INSERT INTO `unidade` (`id`, `sigla`, `descricao`) VALUES
	(1, 'UN', 'Unidade'),
	(2, 'KG', 'Quilo'),
	(3, 'L', 'Litro');

-- Copiando estrutura para tabela mercadinho.usuario
CREATE TABLE IF NOT EXISTS `usuario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(120) NOT NULL,
  `email` varchar(160) NOT NULL,
  `senha_hash` varchar(255) NOT NULL,
  `perfil` enum('admin','gerente','operador','cliente') NOT NULL DEFAULT 'cliente',
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_usuario_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Usuários do sistema (admin/gerente/operador/cliente)';

-- Copiando dados para a tabela mercadinho.usuario: ~0 rows (aproximadamente)
INSERT INTO `usuario` (`id`, `nome`, `email`, `senha_hash`, `perfil`, `ativo`, `criado_em`) VALUES
	(1, 'Admin', 'admin@mercadinho.local', '$2y$10$DLhFnQ52KkJTimkOCn6RYe0.zC2aNfBETvNGVLbYjkB6kd6K2l9Zm', 'admin', 1, '2025-09-09 18:06:00'),
	(2, 'João Ferlini', 'joao@email.com.br', '$2y$10$AqS90lJSbWYjpYB8WICyVuyZu6qFqWbodBuggXgPNb4K8S9GAx4pS', 'cliente', 1, '2025-09-11 17:07:59'),
	(3, 'Pedro Paulo', 'pedrino@email.com', '$2y$10$nq7pBPlNeVo9V67pr3N5QewovgjKogIZaPi3DYm3jKN6LGDWHBbyi', 'cliente', 1, '2025-09-12 19:54:15');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
