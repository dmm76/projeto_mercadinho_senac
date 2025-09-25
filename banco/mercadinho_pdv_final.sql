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
CREATE DATABASE IF NOT EXISTS `mercadinho` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
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
  `terminal_id` int(11) DEFAULT NULL,
  `turno_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_caixa_operador_abertura` (`operador_id`,`abertura`),
  KEY `fk_caixa_terminal` (`terminal_id`),
  KEY `fk_caixa_turno` (`turno_id`),
  CONSTRAINT `fk_caixa_operador` FOREIGN KEY (`operador_id`) REFERENCES `usuario` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_caixa_terminal` FOREIGN KEY (`terminal_id`) REFERENCES `pdv_terminal` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_caixa_turno` FOREIGN KEY (`turno_id`) REFERENCES `pdv_turno` (`id`) ON UPDATE CASCADE
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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Categorias do catálogo';

-- Copiando dados para a tabela mercadinho.categoria: ~10 rows (aproximadamente)
INSERT INTO `categoria` (`id`, `nome`, `slug`, `ativa`, `ordem`) VALUES
	(1, 'Mercearia', 'mercearia', 1, 1),
	(3, 'Padaria', 'padaria', 1, 2),
	(4, 'Bebidas', 'bebidas', 1, 4),
	(5, 'Laticinios', 'laticinios', 1, 1),
	(6, 'Congelados', 'congelados', 1, 3),
	(7, 'Hortifruti', 'hortifruti', 1, 6),
	(8, 'Acougue', 'acougue', 1, 7),
	(9, 'Limpeza', 'limpeza', 1, 8),
	(10, 'Higiene', 'higiene', 1, 9),
	(11, 'Petshop', 'petshop', 1, 10);

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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Cadastro de clientes (pode vincular a um usuário)';

-- Copiando dados para a tabela mercadinho.cliente: ~6 rows (aproximadamente)
INSERT INTO `cliente` (`id`, `usuario_id`, `cpf`, `telefone`, `nascimento`, `criado_em`) VALUES
	(1, 2, NULL, NULL, NULL, '2025-09-13 00:41:59'),
	(2, 3, '02168710937', '(44) 99901-3434', '1976-02-21', '2025-09-13 00:41:59'),
	(3, 5, NULL, NULL, NULL, '2025-09-13 00:41:59'),
	(4, 1, NULL, NULL, NULL, '2025-09-13 00:41:59'),
	(5, 4, NULL, '(44) 99999-1234', NULL, '2025-09-13 00:41:59'),
	(6, 6, NULL, NULL, NULL, '2025-09-18 17:56:45');

-- Copiando estrutura para tabela mercadinho.cliente_favorito
CREATE TABLE IF NOT EXISTS `cliente_favorito` (
  `cliente_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`cliente_id`,`produto_id`),
  KEY `fk_cliente_favorito_produto` (`produto_id`),
  CONSTRAINT `fk_cliente_favorito_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `cliente` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cliente_favorito_produto` FOREIGN KEY (`produto_id`) REFERENCES `produto` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela mercadinho.cliente_favorito: ~0 rows (aproximadamente)
INSERT INTO `cliente_favorito` (`cliente_id`, `produto_id`, `criado_em`) VALUES
	(2, 35, '2025-09-24 19:42:07');

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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(120) NOT NULL,
  `email` varchar(160) NOT NULL,
  `mensagem` text NOT NULL,
  `resposta` text DEFAULT NULL,
  `status` enum('aberta','respondida','arquivada') NOT NULL DEFAULT 'aberta',
  `ip` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `criada_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `respondida_em` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela mercadinho.contato_mensagens: ~3 rows (aproximadamente)
INSERT INTO `contato_mensagens` (`id`, `nome`, `email`, `mensagem`, `resposta`, `status`, `ip`, `user_agent`, `criada_em`, `respondida_em`) VALUES
	(1, 'Douglas', 'douglas@email.com', 'teste agora', 'estamos em teste', 'respondida', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-17 00:37:03', '2025-09-17 02:27:39'),
	(2, 'Douglas Marcelo Monquero', 'douglas@email.com', 'teste de msg dia 17', 'resposta respondida', 'arquivada', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-17 15:56:38', NULL),
	(3, 'Valdir Mendonça', 'valdir@email.com', 'Teste de envio 17 as 18horas', 'teste continua ok', 'respondida', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:142.0) Gecko/20100101 Firefox/142.0', '2025-09-17 21:21:13', '2025-09-23 18:24:49');

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
  `regras_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cupom_codigo` (`codigo`),
  CONSTRAINT `cupom_chk_1` CHECK (json_valid(`regras_json`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Cupons de desconto';

-- Copiando dados para a tabela mercadinho.cupom: ~0 rows (aproximadamente)

-- Copiando estrutura para tabela mercadinho.endereco
CREATE TABLE IF NOT EXISTS `endereco` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_id` int(11) NOT NULL,
  `rotulo` varchar(50) DEFAULT NULL,
  `nome` varchar(80) NOT NULL,
  `cep` varchar(15) NOT NULL,
  `logradouro` varchar(200) NOT NULL,
  `numero` varchar(20) NOT NULL,
  `complemento` varchar(120) DEFAULT NULL,
  `bairro` varchar(120) NOT NULL,
  `cidade` varchar(120) NOT NULL,
  `uf` char(2) NOT NULL,
  `principal` tinyint(1) NOT NULL DEFAULT 0,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_endereco_cliente` (`cliente_id`,`principal`),
  CONSTRAINT `fk_endereco_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `cliente` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Endereços de clientes (um pode ser principal)';

-- Copiando dados para a tabela mercadinho.endereco: ~4 rows (aproximadamente)
INSERT INTO `endereco` (`id`, `cliente_id`, `rotulo`, `nome`, `cep`, `logradouro`, `numero`, `complemento`, `bairro`, `cidade`, `uf`, `principal`, `criado_em`) VALUES
	(1, 2, 'Casa', 'Douglas', '87060-110', 'Rua dos Ipês', '312', 'Casa', 'Conjunto Habitacional Inocente Vila Nova Júnior', 'Maringá', 'PR', 1, '2025-09-13 00:44:35'),
	(2, 5, 'Apartamento', 'Patricia Alves de Oliveira', '87010-255', 'Rua Tanaka', '50', 'bloco 3 apto 21', 'Vila Emilia', 'Maringá', 'PR', 1, '2025-09-13 00:46:52'),
	(3, 2, 'Estudo', 'Douglas Marcelo Monquero', '87020-000', 'Avenida Colombo', '6225', 'Senac', 'Zona 7', 'Maringá', 'PR', 0, '2025-09-13 01:16:27'),
	(4, 2, 'Trabalho', 'Douglas', '87010-100', 'Rua Antônio Valdir Zanutto', '100', 'Sala 01', 'Jardim Novo Horizonte', 'Maringá', 'PR', 0, '2025-09-13 01:37:52');

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
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Estoque atual por produto (1:1)';

-- Copiando dados para a tabela mercadinho.estoque: ~47 rows (aproximadamente)
INSERT INTO `estoque` (`id`, `produto_id`, `quantidade`, `minimo`, `atualizado_em`) VALUES
	(1, 1, 44.000, 5.000, '2025-09-17 00:08:49'),
	(2, 2, 48.000, 5.000, '2025-09-16 23:45:10'),
	(3, 3, 95.450, 10.000, '2025-09-17 01:11:07'),
	(4, 4, 95.000, 12.000, '2025-09-17 18:30:33'),
	(5, 5, 30.000, 5.000, '2025-09-17 18:30:33'),
	(6, 6, 58.000, 10.000, '2025-09-17 21:50:17'),
	(7, 7, 40.000, 8.000, '2025-09-17 18:30:33'),
	(8, 8, 80.000, 10.000, '2025-09-17 18:30:33'),
	(9, 9, 70.000, 10.000, '2025-09-17 18:30:33'),
	(10, 10, 55.000, 8.000, '2025-09-17 18:30:33'),
	(11, 11, 120.000, 15.000, '2025-09-17 18:36:25'),
	(12, 12, 45.000, 8.000, '2025-09-17 18:36:25'),
	(13, 13, 150.000, 20.000, '2025-09-17 18:36:25'),
	(14, 14, 65.000, 10.000, '2025-09-17 18:36:25'),
	(15, 15, 120.000, 25.000, '2025-09-17 18:36:25'),
	(16, 16, 80.000, 12.000, '2025-09-17 18:36:25'),
	(17, 17, 70.000, 10.000, '2025-09-17 18:36:25'),
	(18, 18, 40.000, 6.000, '2025-09-17 18:36:25'),
	(19, 19, 55.000, 10.000, '2025-09-17 18:36:25'),
	(20, 20, 45.000, 8.000, '2025-09-17 18:36:25'),
	(21, 21, 60.000, 10.000, '2025-09-17 18:36:25'),
	(22, 22, 80.000, 15.000, '2025-09-17 18:36:25'),
	(23, 23, 65.000, 12.000, '2025-09-17 18:36:25'),
	(24, 24, 70.000, 10.000, '2025-09-17 18:36:25'),
	(25, 25, 50.000, 8.000, '2025-09-17 18:36:25'),
	(26, 26, 90.000, 10.000, '2025-09-23 18:27:40'),
	(27, 27, 120.000, 18.000, '2025-09-17 18:36:25'),
	(28, 28, 110.000, 15.000, '2025-09-17 18:36:25'),
	(29, 29, 130.000, 20.000, '2025-09-17 18:36:25'),
	(30, 30, 115.000, 18.000, '2025-09-17 18:36:25'),
	(31, 31, 70.000, 10.000, '2025-09-17 18:36:25'),
	(32, 32, 90.000, 15.000, '2025-09-17 18:36:25'),
	(33, 33, 140.000, 25.000, '2025-09-17 18:36:25'),
	(34, 34, 200.000, 30.000, '2025-09-17 18:36:25'),
	(35, 35, 178.000, 25.000, '2025-09-24 19:42:16'),
	(36, 36, 58.000, 8.000, '2025-09-23 18:12:53'),
	(37, 37, 89.500, 20.000, '2025-09-18 17:58:11'),
	(38, 38, 85.000, 18.000, '2025-09-17 18:36:25'),
	(39, 39, 117.000, 25.000, '2025-09-17 23:00:22'),
	(40, 40, 60.000, 10.000, '2025-09-17 18:36:25'),
	(41, 41, 70.000, 12.000, '2025-09-17 18:36:25'),
	(42, 42, 107.000, 15.000, '2025-09-17 23:00:22'),
	(43, 43, 80.000, 12.000, '2025-09-17 18:37:33'),
	(44, 44, 116.000, 20.000, '2025-09-18 18:59:18'),
	(45, 45, 74.000, 10.000, '2025-09-24 18:31:09'),
	(46, 46, 88.000, 12.000, '2025-09-17 23:00:22'),
	(47, 47, 72.000, 12.000, '2025-09-23 18:16:20');

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
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Itens de pedido';

-- Copiando dados para a tabela mercadinho.item_pedido: ~22 rows (aproximadamente)
INSERT INTO `item_pedido` (`id`, `pedido_id`, `produto_id`, `quantidade`, `peso_kg`, `preco_unit`, `desconto_unit`) VALUES
	(3, 1, 1, 2.000, NULL, 50.00, 0.00),
	(4, 1, 2, 1.000, NULL, 20.00, 0.00),
	(5, 2, 1, 2.000, NULL, 50.00, NULL),
	(6, 2, 2, 1.000, NULL, 20.00, NULL),
	(7, 2, 3, 1.000, NULL, 3.99, NULL),
	(8, 3, 1, 1.000, NULL, 50.00, NULL),
	(9, 4, 1, 2.000, NULL, 50.00, NULL),
	(10, 4, 2, 1.000, NULL, 20.00, NULL),
	(11, 5, 1, 1.000, NULL, 50.00, NULL),
	(12, 6, 3, 3.550, NULL, 3.99, NULL),
	(13, 7, 47, 1.000, NULL, 19.90, NULL),
	(14, 7, 46, 1.000, NULL, 16.90, NULL),
	(15, 7, 6, 2.000, NULL, 18.50, NULL),
	(16, 8, 39, 3.000, NULL, 2.99, NULL),
	(17, 8, 46, 1.000, NULL, 16.90, NULL),
	(18, 8, 44, 3.000, NULL, 8.49, NULL),
	(19, 8, 42, 3.000, NULL, 5.49, NULL),
	(20, 9, 47, 3.000, NULL, 19.90, NULL),
	(21, 9, 37, 0.500, NULL, 7.80, NULL),
	(22, 10, 26, 2.000, NULL, 5.49, NULL),
	(23, 11, 44, 1.000, NULL, 8.49, NULL),
	(24, 12, 47, 3.000, NULL, 19.90, NULL),
	(25, 13, 36, 2.000, NULL, 18.90, NULL),
	(26, 14, 47, 1.000, NULL, 19.90, NULL),
	(27, 15, 45, 1.000, NULL, 18.90, NULL),
	(28, 16, 35, 2.000, NULL, 2.99, NULL),
	(29, 17, 1, 1.000, NULL, 50.00, 0.00);

-- Copiando estrutura para tabela mercadinho.marca
CREATE TABLE IF NOT EXISTS `marca` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(120) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_marca_nome` (`nome`)
) ENGINE=InnoDB AUTO_INCREMENT=95 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Marcas dos produtos';

-- Copiando dados para a tabela mercadinho.marca: ~35 rows (aproximadamente)
INSERT INTO `marca` (`id`, `nome`) VALUES
	(1, 'Genérica'),
	(2, 'Tio João'),
	(3, 'Aurora'),
	(4, 'Perdigao'),
	(5, 'Forno de Minas'),
	(6, 'Pilao'),
	(7, 'Do Bem'),
	(8, 'Barrao'),
	(9, 'Bela Vista'),
	(17, 'Nestle'),
	(18, 'Aviacao'),
	(19, 'Seara'),
	(20, 'Sadia'),
	(21, 'McCain'),
	(22, 'Galo'),
	(23, 'Renata'),
	(24, 'Uniao'),
	(25, 'Gallo'),
	(26, 'Castelo'),
	(27, 'Antarctica'),
	(28, 'Heineken'),
	(29, 'Crystal'),
	(30, 'Amoara'),
	(31, 'Comfort'),
	(32, 'Veja'),
	(33, 'OMO'),
	(34, 'Neve'),
	(35, 'Colgate'),
	(36, 'Pantene'),
	(37, 'Dog Chow'),
	(38, 'Whiskas'),
	(39, 'Pullman'),
	(40, 'Tirolez'),
	(41, 'Wickbold'),
	(42, 'Aurora Alimentos');

-- Copiando estrutura para tabela mercadinho.mov_caixa
CREATE TABLE IF NOT EXISTS `mov_caixa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `caixa_id` int(11) NOT NULL,
  `tipo` enum('entrada','saida') NOT NULL,
  `valor` decimal(12,2) NOT NULL,
  `descricao` varchar(200) DEFAULT NULL,
  `pedido_id` int(11) DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `terminal_id` int(11) DEFAULT NULL,
  `turno_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_movcaixa_caixa` (`caixa_id`,`criado_em`),
  KEY `idx_movcaixa_pedido` (`pedido_id`),
  KEY `fk_movcaixa_terminal` (`terminal_id`),
  KEY `fk_movcaixa_turno` (`turno_id`),
  CONSTRAINT `fk_movcaixa_caixa` FOREIGN KEY (`caixa_id`) REFERENCES `caixa` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_movcaixa_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedido` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_movcaixa_terminal` FOREIGN KEY (`terminal_id`) REFERENCES `pdv_terminal` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_movcaixa_turno` FOREIGN KEY (`turno_id`) REFERENCES `pdv_turno` (`id`) ON UPDATE CASCADE
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
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Movimentações de estoque';

-- Copiando dados para a tabela mercadinho.mov_estoque: ~18 rows (aproximadamente)
INSERT INTO `mov_estoque` (`id`, `produto_id`, `tipo`, `quantidade`, `origem`, `referencia_id`, `observacao`, `criado_em`) VALUES
	(1, 1, 'saida', 2.000, 'pedido', 2, 'Saida por venda', '2025-09-16 23:33:56'),
	(2, 2, 'saida', 1.000, 'pedido', 2, 'Saida por venda', '2025-09-16 23:33:56'),
	(3, 3, 'saida', 1.000, 'pedido', 2, 'Saida por venda', '2025-09-16 23:33:56'),
	(4, 1, 'saida', 1.000, 'pedido', 3, 'Saida por venda', '2025-09-16 23:35:06'),
	(5, 1, 'saida', 2.000, 'pedido', 4, 'Saida por venda', '2025-09-16 23:45:10'),
	(6, 2, 'saida', 1.000, 'pedido', 4, 'Saida por venda', '2025-09-16 23:45:10'),
	(7, 1, 'saida', 1.000, 'pedido', 5, 'Saida por venda', '2025-09-17 00:08:49'),
	(8, 3, 'saida', 3.550, 'pedido', 6, 'Saida por venda', '2025-09-17 01:11:07'),
	(9, 47, 'saida', 1.000, 'pedido', 7, 'Saida por venda', '2025-09-17 21:50:17'),
	(10, 46, 'saida', 1.000, 'pedido', 7, 'Saida por venda', '2025-09-17 21:50:17'),
	(11, 6, 'saida', 2.000, 'pedido', 7, 'Saida por venda', '2025-09-17 21:50:17'),
	(12, 39, 'saida', 3.000, 'pedido', 8, 'Saida por venda', '2025-09-17 23:00:22'),
	(13, 46, 'saida', 1.000, 'pedido', 8, 'Saida por venda', '2025-09-17 23:00:22'),
	(14, 44, 'saida', 3.000, 'pedido', 8, 'Saida por venda', '2025-09-17 23:00:22'),
	(15, 42, 'saida', 3.000, 'pedido', 8, 'Saida por venda', '2025-09-17 23:00:22'),
	(16, 47, 'saida', 3.000, 'pedido', 9, 'Saida por venda', '2025-09-18 17:58:11'),
	(17, 37, 'saida', 0.500, 'pedido', 9, 'Saida por venda', '2025-09-18 17:58:11'),
	(18, 26, 'saida', 2.000, 'pedido', 10, 'Saida por venda', '2025-09-18 18:05:00'),
	(19, 44, 'saida', 1.000, 'pedido', 11, 'Saida por venda', '2025-09-18 18:59:18'),
	(20, 47, 'saida', 3.000, 'pedido', 12, 'Saida por venda', '2025-09-23 18:10:44'),
	(21, 36, 'saida', 2.000, 'pedido', 13, 'Saida por venda', '2025-09-23 18:12:53'),
	(22, 47, 'saida', 1.000, 'pedido', 14, 'Saida por venda', '2025-09-23 18:16:20'),
	(23, 45, 'saida', 1.000, 'pedido', 15, 'Saida por venda', '2025-09-24 18:31:09'),
	(24, 35, 'saida', 2.000, 'pedido', 16, 'Saida por venda', '2025-09-24 19:42:16');

-- Copiando estrutura para tabela mercadinho.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `cliente_id` int(11) NOT NULL,
  `token_hash` char(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used_at` datetime DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `token_hash` (`token_hash`),
  KEY `cliente_id` (`cliente_id`),
  CONSTRAINT `fk_prt_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `cliente` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela mercadinho.password_reset_tokens: ~1 rows (aproximadamente)
INSERT INTO `password_reset_tokens` (`id`, `cliente_id`, `token_hash`, `expires_at`, `used_at`, `ip`, `user_agent`, `created_at`) VALUES
	(3, 2, '180a122543344128da783676e381588522b8c02b01744ccf0dd73786f08194f3', '2025-09-25 20:17:40', '2025-09-25 14:20:29', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-25 14:17:40');

-- Copiando estrutura para view mercadinho.pdv_caixa_resumo
-- Criando tabela temporária para evitar erros de dependência de VIEW
CREATE TABLE `pdv_caixa_resumo` (
	`pedido_id` INT(11) NOT NULL,
	`tipo` ENUM('dinheiro','credito','debito','cheque','pix') NULL COLLATE 'utf8mb4_general_ci',
	`valor` DECIMAL(12,2) NULL,
	`mov_tipo` ENUM('entrada','saida') NULL COLLATE 'utf8mb4_unicode_ci',
	`terminal_id` INT(11) NOT NULL,
	`turno_id` INT(11) NOT NULL,
	`troco` DECIMAL(12,2) NOT NULL,
	`total` DECIMAL(12,2) NOT NULL,
	`criado_em` TIMESTAMP NOT NULL
);

-- Copiando estrutura para tabela mercadinho.pdv_evento
CREATE TABLE IF NOT EXISTS `pdv_evento` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `turno_id` bigint(20) NOT NULL,
  `terminal_id` bigint(20) NOT NULL,
  `operador_id` bigint(20) NOT NULL,
  `tipo` varchar(40) NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload`)),
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `turno_id` (`turno_id`,`tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela mercadinho.pdv_evento: ~0 rows (aproximadamente)

-- Copiando estrutura para tabela mercadinho.pdv_fiscal_nfce
CREATE TABLE IF NOT EXISTS `pdv_fiscal_nfce` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `pedido_id` int(11) NOT NULL,
  `numero` int(11) DEFAULT NULL,
  `serie` int(11) DEFAULT NULL,
  `chave` varchar(60) DEFAULT NULL,
  `protocolo` varchar(60) DEFAULT NULL,
  `xml_path` varchar(255) DEFAULT NULL,
  `status` enum('autorizada','denegada','cancelada','em_processamento','erro') NOT NULL DEFAULT 'em_processamento',
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_nfce_pedido` (`pedido_id`),
  CONSTRAINT `fk_nfce_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedido` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela mercadinho.pdv_fiscal_nfce: ~0 rows (aproximadamente)

-- Copiando estrutura para tabela mercadinho.pdv_pag_cartao
CREATE TABLE IF NOT EXISTS `pdv_pag_cartao` (
  `pagamento_id` bigint(20) NOT NULL,
  `bandeira` varchar(30) DEFAULT NULL,
  `parcelas` int(11) DEFAULT NULL,
  `nsu` varchar(50) DEFAULT NULL,
  `autorizacao` varchar(50) DEFAULT NULL,
  `adquirente` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`pagamento_id`),
  CONSTRAINT `fk_pagcartao_pagamento` FOREIGN KEY (`pagamento_id`) REFERENCES `pedido_pagamento` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela mercadinho.pdv_pag_cartao: ~1 rows (aproximadamente)
INSERT INTO `pdv_pag_cartao` (`pagamento_id`, `bandeira`, `parcelas`, `nsu`, `autorizacao`, `adquirente`) VALUES
	(5, 'VISA', 1, 'NSU123', 'AUTH456', 'Demo');

-- Copiando estrutura para tabela mercadinho.pdv_pag_cheque
CREATE TABLE IF NOT EXISTS `pdv_pag_cheque` (
  `pagamento_id` bigint(20) NOT NULL,
  `banco_codigo` varchar(10) DEFAULT NULL,
  `banco_nome` varchar(60) DEFAULT NULL,
  `agencia` varchar(20) DEFAULT NULL,
  `conta` varchar(20) DEFAULT NULL,
  `numero_cheque` varchar(30) DEFAULT NULL,
  `bom_para` date DEFAULT NULL,
  PRIMARY KEY (`pagamento_id`),
  CONSTRAINT `fk_pagcheque_pagamento` FOREIGN KEY (`pagamento_id`) REFERENCES `pedido_pagamento` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela mercadinho.pdv_pag_cheque: ~0 rows (aproximadamente)

-- Copiando estrutura para tabela mercadinho.pdv_pag_pix
CREATE TABLE IF NOT EXISTS `pdv_pag_pix` (
  `pagamento_id` bigint(20) NOT NULL,
  `txid` varchar(60) DEFAULT NULL,
  `qr_code` text DEFAULT NULL,
  `status` enum('pendente','pago','expirado','cancelado') NOT NULL DEFAULT 'pendente',
  PRIMARY KEY (`pagamento_id`),
  CONSTRAINT `fk_pagpix_pagamento` FOREIGN KEY (`pagamento_id`) REFERENCES `pedido_pagamento` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela mercadinho.pdv_pag_pix: ~0 rows (aproximadamente)

-- Copiando estrutura para tabela mercadinho.pdv_pedido_meta
CREATE TABLE IF NOT EXISTS `pdv_pedido_meta` (
  `pedido_id` int(11) NOT NULL,
  `terminal_id` int(11) NOT NULL,
  `turno_id` int(11) NOT NULL,
  `operador_id` int(11) NOT NULL,
  `cpf_na_nota` varchar(14) DEFAULT NULL,
  `observacao` varchar(255) DEFAULT NULL,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`pedido_id`),
  KEY `terminal_id` (`terminal_id`),
  KEY `turno_id` (`turno_id`),
  KEY `idx_pdvmeta_pedido` (`pedido_id`),
  KEY `idx_pdvmeta_terminal` (`terminal_id`),
  KEY `idx_pdvmeta_turno` (`turno_id`),
  KEY `idx_pdvmeta_operador` (`operador_id`),
  CONSTRAINT `fk_pdvmeta_operador` FOREIGN KEY (`operador_id`) REFERENCES `usuario` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_pdvmeta_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedido` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_pdvmeta_terminal` FOREIGN KEY (`terminal_id`) REFERENCES `pdv_terminal` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_pdvmeta_turno` FOREIGN KEY (`turno_id`) REFERENCES `pdv_turno` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela mercadinho.pdv_pedido_meta: ~1 rows (aproximadamente)
INSERT INTO `pdv_pedido_meta` (`pedido_id`, `terminal_id`, `turno_id`, `operador_id`, `cpf_na_nota`, `observacao`, `criado_em`) VALUES
	(17, 1, 1, 1, NULL, NULL, '2025-09-25 15:34:26');

-- Copiando estrutura para tabela mercadinho.pdv_tef_transacao
CREATE TABLE IF NOT EXISTS `pdv_tef_transacao` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `pagamento_id` bigint(20) NOT NULL,
  `provedor` varchar(40) NOT NULL,
  `nsu` varchar(50) DEFAULT NULL,
  `codigo_host` varchar(20) DEFAULT NULL,
  `comprovante` text DEFAULT NULL,
  `status` enum('aprovado','negado','cancelado','pendente') NOT NULL,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `pagamento_id` (`pagamento_id`),
  CONSTRAINT `fk_tef_pagamento` FOREIGN KEY (`pagamento_id`) REFERENCES `pedido_pagamento` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela mercadinho.pdv_tef_transacao: ~0 rows (aproximadamente)

-- Copiando estrutura para tabela mercadinho.pdv_terminal
CREATE TABLE IF NOT EXISTS `pdv_terminal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `loja_id` bigint(20) NOT NULL,
  `nome` varchar(60) NOT NULL,
  `identificador` varchar(60) DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`config`)),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `identificador` (`identificador`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela mercadinho.pdv_terminal: ~1 rows (aproximadamente)
INSERT INTO `pdv_terminal` (`id`, `loja_id`, `nome`, `identificador`, `ativo`, `config`, `created_at`) VALUES
	(1, 1, 'Caixa 01', 'HOST-CAIXA01', 1, NULL, '2025-09-25 15:33:50');

-- Copiando estrutura para tabela mercadinho.pdv_turno
CREATE TABLE IF NOT EXISTS `pdv_turno` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `terminal_id` int(11) NOT NULL,
  `operador_id` int(11) NOT NULL,
  `caixa_id` int(11) DEFAULT NULL,
  `aberto_em` datetime NOT NULL,
  `valor_inicial` decimal(12,2) NOT NULL DEFAULT 0.00,
  `fechado_em` datetime DEFAULT NULL,
  `valor_fechamento` decimal(12,2) DEFAULT NULL,
  `status` enum('aberto','fechado','cancelado') NOT NULL DEFAULT 'aberto',
  PRIMARY KEY (`id`),
  KEY `terminal_id` (`terminal_id`,`status`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela mercadinho.pdv_turno: ~1 rows (aproximadamente)
INSERT INTO `pdv_turno` (`id`, `terminal_id`, `operador_id`, `caixa_id`, `aberto_em`, `valor_inicial`, `fechado_em`, `valor_fechamento`, `status`) VALUES
	(1, 1, 1, NULL, '2025-09-25 15:34:11', 100.00, NULL, NULL, 'aberto');

-- Copiando estrutura para view mercadinho.pdv_turnos_abertos
-- Criando tabela temporária para evitar erros de dependência de VIEW
CREATE TABLE `pdv_turnos_abertos` (
	`id` INT(11) NOT NULL,
	`terminal_id` INT(11) NOT NULL,
	`operador_id` INT(11) NOT NULL,
	`caixa_id` INT(11) NULL,
	`aberto_em` DATETIME NOT NULL,
	`valor_inicial` DECIMAL(12,2) NOT NULL,
	`fechado_em` DATETIME NULL,
	`valor_fechamento` DECIMAL(12,2) NULL,
	`status` ENUM('aberto','fechado','cancelado') NOT NULL COLLATE 'utf8mb4_general_ci',
	`terminal` VARCHAR(1) NOT NULL COLLATE 'utf8mb4_general_ci'
);

-- Copiando estrutura para tabela mercadinho.pedido
CREATE TABLE IF NOT EXISTS `pedido` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_id` int(11) NOT NULL,
  `endereco_id` int(11) DEFAULT NULL,
  `status` enum('novo','em_separacao','em_transporte','pronto','finalizado','cancelado') NOT NULL DEFAULT 'novo',
  `canal` enum('online','pdv') NOT NULL DEFAULT 'online',
  `entrega` enum('retirada','entrega') NOT NULL,
  `pagamento` enum('na_entrega','pix','cartao','gateway') NOT NULL DEFAULT 'na_entrega',
  `subtotal` decimal(12,2) NOT NULL,
  `frete` decimal(12,2) NOT NULL DEFAULT 0.00,
  `desconto` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total` decimal(12,2) NOT NULL,
  `troco` decimal(12,2) NOT NULL DEFAULT 0.00,
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
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Pedidos da loja (online/retirada)';

-- Copiando dados para a tabela mercadinho.pedido: ~16 rows (aproximadamente)
INSERT INTO `pedido` (`id`, `cliente_id`, `endereco_id`, `status`, `canal`, `entrega`, `pagamento`, `subtotal`, `frete`, `desconto`, `total`, `troco`, `codigo_externo`, `criado_em`, `atualizado_em`) VALUES
	(1, 5, 1, 'cancelado', 'online', 'entrega', 'pix', 120.00, 10.00, 0.00, 130.00, 0.00, 'PED-0001', '2025-09-13 02:38:18', '2025-09-25 19:13:55'),
	(2, 2, NULL, 'cancelado', 'online', 'retirada', 'pix', 123.99, 0.00, 0.00, 123.99, 0.00, NULL, '2025-09-16 23:33:56', '2025-09-25 19:13:55'),
	(3, 2, NULL, 'pronto', 'online', 'retirada', 'na_entrega', 50.00, 0.00, 0.00, 50.00, 0.00, NULL, '2025-09-16 23:35:06', '2025-09-25 19:13:55'),
	(4, 2, 1, 'em_transporte', 'online', 'entrega', 'na_entrega', 120.00, 0.00, 0.00, 120.00, 0.00, NULL, '2025-09-16 23:45:10', '2025-09-25 19:13:55'),
	(5, 2, NULL, 'cancelado', 'online', 'retirada', 'na_entrega', 50.00, 0.00, 0.00, 50.00, 0.00, NULL, '2025-09-17 00:08:49', '2025-09-25 19:13:55'),
	(6, 5, NULL, 'em_transporte', 'online', 'retirada', 'na_entrega', 14.16, 0.00, 0.00, 14.16, 0.00, NULL, '2025-09-17 01:11:07', '2025-09-25 19:13:55'),
	(7, 2, 1, 'em_transporte', 'online', 'entrega', 'na_entrega', 73.80, 0.00, 0.00, 73.80, 0.00, NULL, '2025-09-17 21:50:17', '2025-09-25 19:13:55'),
	(8, 2, 3, 'em_transporte', 'online', 'entrega', 'gateway', 67.81, 0.00, 0.00, 67.81, 0.00, NULL, '2025-09-17 23:00:22', '2025-09-25 19:13:55'),
	(9, 6, NULL, 'pronto', 'online', 'retirada', 'pix', 63.60, 0.00, 0.00, 63.60, 0.00, NULL, '2025-09-18 17:58:11', '2025-09-25 19:13:55'),
	(10, 2, NULL, 'em_transporte', 'online', 'retirada', 'gateway', 10.98, 0.00, 0.00, 10.98, 0.00, NULL, '2025-09-18 18:05:00', '2025-09-25 19:13:55'),
	(11, 2, NULL, 'em_transporte', 'online', 'retirada', 'pix', 8.49, 0.00, 0.00, 8.49, 0.00, NULL, '2025-09-18 18:59:18', '2025-09-25 19:13:55'),
	(12, 2, 1, 'em_transporte', 'online', 'entrega', 'pix', 59.70, 0.00, 0.00, 59.70, 0.00, NULL, '2025-09-23 18:10:44', '2025-09-25 19:13:55'),
	(13, 2, NULL, 'cancelado', 'online', 'retirada', 'pix', 37.80, 0.00, 0.00, 37.80, 0.00, NULL, '2025-09-23 18:12:53', '2025-09-25 19:13:55'),
	(14, 2, NULL, 'em_transporte', 'online', 'retirada', 'na_entrega', 19.90, 0.00, 0.00, 19.90, 0.00, NULL, '2025-09-23 18:16:20', '2025-09-25 19:13:55'),
	(15, 2, NULL, 'novo', 'online', 'retirada', 'pix', 18.90, 0.00, 0.00, 18.90, 0.00, 'PED-000015', '2025-09-24 18:31:09', '2025-09-25 19:13:55'),
	(16, 2, NULL, 'novo', 'online', 'retirada', 'pix', 5.98, 0.00, 0.00, 5.98, 0.00, 'PED-000016', '2025-09-24 19:42:16', '2025-09-25 19:13:55'),
	(17, 1, NULL, 'novo', 'pdv', 'retirada', 'na_entrega', 50.00, 0.00, 0.00, 50.00, 0.00, NULL, '2025-09-25 18:34:19', '2025-09-25 18:34:19');

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

-- Copiando estrutura para tabela mercadinho.pedido_pagamento
CREATE TABLE IF NOT EXISTS `pedido_pagamento` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `pedido_id` int(11) NOT NULL,
  `tipo` enum('dinheiro','credito','debito','cheque','pix') NOT NULL,
  `valor` decimal(12,2) NOT NULL,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `pedido_id` (`pedido_id`),
  CONSTRAINT `fk_pedidopag_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedido` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Copiando dados para a tabela mercadinho.pedido_pagamento: ~2 rows (aproximadamente)
INSERT INTO `pedido_pagamento` (`id`, `pedido_id`, `tipo`, `valor`, `criado_em`) VALUES
	(5, 17, 'credito', 30.00, '2025-09-25 15:38:31'),
	(6, 17, 'dinheiro', 20.00, '2025-09-25 15:38:45');

-- Copiando estrutura para view mercadinho.pedido_pdv
-- Criando tabela temporária para evitar erros de dependência de VIEW
CREATE TABLE `pedido_pdv` (
	`id` INT(11) NOT NULL,
	`cliente_id` INT(11) NOT NULL,
	`endereco_id` INT(11) NULL,
	`status` ENUM('novo','em_separacao','em_transporte','pronto','finalizado','cancelado') NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`canal` ENUM('online','pdv') NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`entrega` ENUM('retirada','entrega') NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`pagamento` ENUM('na_entrega','pix','cartao','gateway') NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`subtotal` DECIMAL(12,2) NOT NULL,
	`frete` DECIMAL(12,2) NOT NULL,
	`desconto` DECIMAL(12,2) NOT NULL,
	`total` DECIMAL(12,2) NOT NULL,
	`troco` DECIMAL(12,2) NOT NULL,
	`codigo_externo` VARCHAR(1) NULL COLLATE 'utf8mb4_unicode_ci',
	`criado_em` TIMESTAMP NOT NULL,
	`atualizado_em` TIMESTAMP NOT NULL
);

-- Copiando estrutura para tabela mercadinho.pedido_pix
CREATE TABLE IF NOT EXISTS `pedido_pix` (
  `pedido_id` int(11) NOT NULL,
  `mp_payment_id` bigint(20) NOT NULL,
  `status` varchar(32) NOT NULL,
  `status_detail` varchar(64) DEFAULT NULL,
  `qr_code` text DEFAULT NULL,
  `qr_code_base64` mediumtext DEFAULT NULL,
  `ticket_url` varchar(255) DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`pedido_id`),
  CONSTRAINT `fk_pedido_pix_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedido` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Copiando dados para a tabela mercadinho.pedido_pix: ~2 rows (aproximadamente)
INSERT INTO `pedido_pix` (`pedido_id`, `mp_payment_id`, `status`, `status_detail`, `qr_code`, `qr_code_base64`, `ticket_url`, `expires_at`, `created_at`, `updated_at`) VALUES
	(15, 1341342135, 'pending', 'pending_waiting_transfer', '00020126580014br.gov.bcb.pix0136b76aa9c2-2ec4-4110-954e-ebfe34f05b61520400005303986540518.905802BR5916DOYGlyn.ezwRlORO6006MaZWMH62230519mpqrinter13413421356304A645', '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', 'https://www.mercadopago.com.br/sandbox/payments/1341342135/ticket?caller_id=1406372264&hash=bdbce810-8ff9-4d46-a3c8-bcb5879fee4d', '2025-09-25 19:38:43', '2025-09-24 19:38:43', '2025-09-24 19:41:44'),
	(16, 1341340273, 'pending', 'pending_waiting_transfer', '00020126580014br.gov.bcb.pix0136b76aa9c2-2ec4-4110-954e-ebfe34f05b6152040000530398654045.985802BR5916DOYGlyn.ezwRlORO6006MaZWMH62230519mpqrinter134134027363044099', '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', 'https://www.mercadopago.com.br/sandbox/payments/1341340273/ticket?caller_id=1406372264&hash=bed8b231-ee63-4157-b7e7-eae082b94e33', '2025-09-25 19:42:18', '2025-09-24 19:42:18', '2025-09-24 19:42:18');

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
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Histórico de preços por produto';

-- Copiando dados para a tabela mercadinho.preco: ~47 rows (aproximadamente)
INSERT INTO `preco` (`id`, `produto_id`, `preco_venda`, `preco_promocional`, `inicio_promo`, `fim_promo`, `criado_em`) VALUES
	(1, 1, 50.00, NULL, NULL, NULL, '2025-09-13 03:27:42'),
	(2, 2, 20.00, NULL, NULL, NULL, '2025-09-13 03:27:42'),
	(3, 3, 5.99, 3.99, NULL, NULL, '2025-09-16 23:32:39'),
	(4, 4, 4.99, NULL, NULL, NULL, '2025-09-17 18:30:33'),
	(5, 5, 24.90, NULL, NULL, NULL, '2025-09-17 18:30:33'),
	(6, 6, 18.50, NULL, NULL, NULL, '2025-09-17 18:30:33'),
	(7, 7, 32.90, 29.90, '2025-09-17 15:30:33', '2025-09-24 15:30:33', '2025-09-17 18:30:33'),
	(8, 8, 17.99, NULL, NULL, NULL, '2025-09-17 18:30:33'),
	(9, 9, 9.49, NULL, NULL, NULL, '2025-09-17 18:30:33'),
	(10, 10, 22.50, NULL, NULL, NULL, '2025-09-17 18:30:33'),
	(11, 11, 6.99, NULL, NULL, NULL, '2025-09-17 18:36:25'),
	(12, 12, 19.90, NULL, NULL, NULL, '2025-09-17 18:36:25'),
	(13, 13, 2.89, NULL, NULL, NULL, '2025-09-17 18:36:25'),
	(14, 14, 8.49, NULL, NULL, NULL, '2025-09-17 18:36:25'),
	(15, 15, 16.90, NULL, NULL, NULL, '2025-09-17 18:36:25'),
	(16, 16, 11.90, NULL, NULL, NULL, '2025-09-17 18:36:25'),
	(17, 17, 9.50, NULL, NULL, NULL, '2025-09-17 18:36:25'),
	(18, 18, 14.90, NULL, NULL, NULL, '2025-09-17 18:36:25'),
	(19, 19, 21.90, NULL, NULL, NULL, '2025-09-17 18:36:25'),
	(20, 20, 29.90, NULL, NULL, NULL, '2025-09-17 18:36:25'),
	(21, 21, 19.90, NULL, NULL, NULL, '2025-09-17 18:36:25'),
	(22, 22, 13.90, NULL, NULL, NULL, '2025-09-17 18:36:25'),
	(23, 23, 44.90, NULL, NULL, NULL, '2025-09-17 18:36:25'),
	(24, 24, 6.50, NULL, NULL, NULL, '2025-09-17 18:36:25'),
	(25, 25, 27.90, NULL, NULL, NULL, '2025-09-17 18:36:25'),
	(26, 26, 5.49, NULL, NULL, NULL, '2025-09-17 18:36:25'),
	(27, 27, 7.99, NULL, NULL, NULL, '2025-09-17 18:36:25'),
	(28, 28, 6.90, NULL, NULL, NULL, '2025-09-17 18:36:25'),
	(29, 29, 5.60, NULL, NULL, NULL, '2025-09-17 18:36:25'),
	(30, 30, 9.90, NULL, NULL, NULL, '2025-09-17 18:36:25'),
	(31, 31, 32.90, NULL, NULL, NULL, '2025-09-17 18:36:25'),
	(32, 32, 3.99, NULL, NULL, NULL, '2025-09-17 18:36:25'),
	(33, 33, 8.99, NULL, NULL, NULL, '2025-09-17 18:36:25'),
	(34, 34, 5.99, NULL, NULL, NULL, '2025-09-17 18:36:25'),
	(35, 35, 2.99, NULL, NULL, NULL, '2025-09-17 18:36:25'),
	(36, 36, 18.90, NULL, NULL, NULL, '2025-09-17 18:36:25'),
	(37, 37, 7.80, NULL, NULL, NULL, '2025-09-17 18:36:25'),
	(38, 38, 9.50, NULL, NULL, NULL, '2025-09-17 18:36:25'),
	(39, 39, 2.99, NULL, NULL, NULL, '2025-09-17 18:36:25'),
	(40, 40, 27.90, NULL, NULL, NULL, '2025-09-17 18:36:25'),
	(41, 41, 19.90, NULL, NULL, NULL, '2025-09-17 18:36:25'),
	(42, 42, 5.49, NULL, NULL, NULL, '2025-09-17 18:36:25'),
	(43, 43, 24.90, NULL, NULL, NULL, '2025-09-17 18:37:33'),
	(44, 44, 8.49, NULL, NULL, NULL, '2025-09-17 18:38:08'),
	(45, 45, 18.90, NULL, NULL, NULL, '2025-09-17 18:38:08'),
	(46, 46, 16.90, NULL, NULL, NULL, '2025-09-17 18:38:08'),
	(47, 47, 19.90, NULL, NULL, NULL, '2025-09-17 18:38:08'),
	(48, 26, 9.90, 4.99, NULL, NULL, '2025-09-18 18:03:13');

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
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Produtos do catálogo';

-- Copiando dados para a tabela mercadinho.produto: ~47 rows (aproximadamente)
INSERT INTO `produto` (`id`, `nome`, `sku`, `ean`, `categoria_id`, `marca_id`, `unidade_id`, `descricao`, `imagem`, `ativo`, `peso_variavel`, `criado_em`) VALUES
	(1, 'Arroz Tio João 5kg Tipo 1', 'ARROZ5', '789000000001', 1, 2, 1, 'Pacote 5kg', 'uploads/produtos/19bba88ad394ab08-1758144441.jpg', 1, 0, '2025-09-13 03:26:58'),
	(2, 'Feijão Carioca 1kg', 'FEIJAO1', '789000000002', 1, 1, 1, 'Pacote 1kg', 'uploads/produtos/4a708d9feef7daea-1758144868.jpg', 1, 0, '2025-09-13 03:26:58'),
	(3, 'Banana Prata', 'bananaprta', NULL, 1, 1, 2, 'banana prata', 'uploads/produtos/f0a37618390f6d0e-1758144487.jpg', 1, 0, '2025-09-16 23:32:39'),
	(4, 'Leite Integral Aurora 1L', 'LEITEAUR1L', '7891150043210', 5, 3, 4, 'Leite UHT integral catarinense.', 'uploads/produtos/73bf2bce587591d5-1758145111.jpg', 1, 0, '2025-09-17 18:30:33'),
	(5, 'Queijo Minas Frescal 500g', 'QUEIJOMIN500', '7892222000456', 5, 9, 3, 'Queijo minas frescal tradicional.', 'uploads/produtos/7bcd1cffea6f2b89-1758145424.jpg', 1, 0, '2025-09-17 18:30:33'),
	(6, 'Pao de Queijo Forno de Minas 400g', 'PAOQFMIN400', '7896004001234', 6, 5, 3, 'Pao de queijo congelado pronto para assar.', 'uploads/produtos/0ddb113ff8420180-1758145249.jpg', 1, 0, '2025-09-17 18:30:33'),
	(7, 'Coxinha Congelada Perdigao 1kg', 'COXPERD1KG', '7892300005678', 6, 4, 2, 'Coxinha de frango congelada, pacote 1kg.', 'uploads/produtos/23ee276665706384-1758144733.jpg', 1, 0, '2025-09-17 18:30:33'),
	(8, 'Cafe Pilao Tradicional 500g', 'CAFEPIL500', '7894900012346', 3, 6, 3, 'Cafe torrado e moido Pilao tradicional.', 'uploads/produtos/2245b3868c65dcdb-1758144622.jpg', 1, 0, '2025-09-17 18:30:33'),
	(9, 'Suco de Laranja Do Bem 1L', 'SUCLDMB1L', '7891991009871', 4, 7, 4, 'Suco integral de laranja, sem acucar.', 'uploads/produtos/be237b0ce735aead-1758145631.jpg', 1, 0, '2025-09-17 18:30:33'),
	(10, 'Erva Mate Barrao 1kg', 'ERVABARR1K', '7897151700150', 4, 8, 2, 'Erva mate para chimarrao, moagem grossa.', 'uploads/produtos/415305279c25b91c-1758144807.jpg', 1, 0, '2025-09-17 18:30:33'),
	(11, 'Leite Condensado Mooca 395g', 'LEITECOND395', '7891000054321', 1, 17, 3, 'Leite condensado tradicional para sobremesas.', 'uploads/produtos/5f02ceaf8e012dce-1758145062.jpg', 1, 0, '2025-09-17 18:36:25'),
	(12, 'Manteiga Aviacao 200g', 'MANTAVI200', '7896034501234', 5, 18, 3, 'Manteiga extra com sal, pote 200g.', 'uploads/produtos/d77a74fe15e26e60-1758145198.jpg', 1, 0, '2025-09-17 18:36:25'),
	(13, 'Iogurte Natural Nestle 170g', 'IOGUNEST170', '7891000256780', 5, 17, 3, 'Iogurte natural integral copo 170g.', 'uploads/produtos/fb375537b706afbf-1758144960.jpg', 1, 0, '2025-09-17 18:36:25'),
	(14, 'Requeijao Cremoso Tirolez 200g', 'REQTIROL200', '7896036001236', 5, 40, 3, 'Requeijao cremoso tradicional.', 'uploads/produtos/f1de563137b70226-1758145538.jpg', 1, 0, '2025-09-17 18:36:25'),
	(15, 'Pao Frances kg', 'PAOFRANCESKG', NULL, 3, 41, 2, 'Pao frances assado diariamente.', 'uploads/produtos/9e2ff9ad86c15929-1758145278.jpg', 1, 1, '2025-09-17 18:36:25'),
	(16, 'Pao Integral Wickbold 500g', 'PAOWICK500', '7896004005677', 3, 41, 3, 'Pao integral fatiado com graos.', 'uploads/produtos/5c5119a1626f3775-1758145315.jpg', 1, 0, '2025-09-17 18:36:25'),
	(17, 'Bisnaguinha Pullman 300g', 'BISNPULL300', '7891910007654', 3, 39, 3, 'Pao tipo bisnaguinha fofinho.', 'uploads/produtos/7ffb5e10610578df-1758144540.jpg', 1, 0, '2025-09-17 18:36:25'),
	(18, 'Bolo de Milho Congelado Seara 400g', 'BOLOSMIL400', '7894904003456', 6, 19, 3, 'Bolo de milho congelado pronto.', 'uploads/produtos/f29f2dbd589c654d-1758144598.jpg', 1, 0, '2025-09-17 18:36:25'),
	(19, 'Lasanha Sadia Bolonhesa 600g', 'LASASAD600', '7891810009870', 6, 20, 3, 'Lasanha congelada sabor bolonhesa.', 'uploads/produtos/64043b572cf427fa-1758145029.jpg', 1, 0, '2025-09-17 18:36:25'),
	(20, 'Batata Palito McCain 2kg', 'BATAMCC2KG', '7894904500123', 6, 21, 2, 'Batata palito congelada embalagens 2kg.', 'uploads/produtos/f9776ff2932f154a-1758144515.jpg', 1, 0, '2025-09-17 18:36:25'),
	(21, 'Pizza Calabresa Perdigao 460g', 'PIZZAPER460', '7892300056784', 6, 4, 3, 'Pizza congelada sabor calabresa.', 'uploads/produtos/2f8c5b6a7af26ea7-1758145381.jpg', 1, 0, '2025-09-17 18:36:25'),
	(22, 'Frango Inteiro Congelado Seara 2kg', 'FRANGSEAR2K', '7894900303256', 8, 19, 2, 'Frango inteiro congelado.', 'uploads/produtos/31b37116840c5140-1758144924.jpg', 1, 0, '2025-09-17 18:36:25'),
	(23, 'Contra File Bovino kg', 'CONTRAFILKG', NULL, 8, 20, 2, 'Corte bovino contra file fresco.', 'uploads/produtos/22b3a7446fafd1ed-1758144704.jpg', 1, 1, '2025-09-17 18:36:25'),
	(24, 'Mortadela Seara Fatiada 200g', 'MORTSEAR200', '7894900001235', 3, 19, 3, 'Mortadela fatiada classica 200g.', 'uploads/produtos/c68b8ad928fc48a9-1758145221.jpg', 1, 0, '2025-09-17 18:36:25'),
	(25, 'Cafe Soluvel Nescafe 200g', 'CAFESOL200', '7891000103567', 1, 17, 3, 'Cafe soluvel tradicional 200g.', 'uploads/produtos/b9eabed72a0ed825-1758144647.jpg', 1, 0, '2025-09-17 18:36:25'),
	(26, 'Acucar Refinado Uniao 1kg', 'ACUCUNIA1K', '7891021000017', 1, 24, 6, 'Acucar refinado cristal fino 1kg.', 'uploads/produtos/1cbb6b33fa6d758f-1758144171.jpg', 1, 0, '2025-09-17 18:36:25'),
	(27, 'Feijao Preto Sao Joao 1kg', 'FEIJSAO1KG', '7896028701234', 1, 8, 2, 'Feijao preto tipo 1.', 'uploads/produtos/a07c720ba554409f-1758144903.jpg', 1, 0, '2025-09-17 18:36:25'),
	(28, 'Farinha de Trigo Renata 1kg', 'FARINREN1K', '7896102502345', 1, 23, 2, 'Farinha de trigo especial.', 'uploads/produtos/171a00a459bce0d6-1758144835.jpg', 1, 0, '2025-09-17 18:36:25'),
	(29, 'Macarrao Espaguete Galo 500g', 'MACGAL500', '7891234009876', 1, 22, 3, 'Macarrao espaguete n10.', 'uploads/produtos/0db02103febae097-1758145164.jpg', 1, 0, '2025-09-17 18:36:25'),
	(30, 'Arroz Integral Tio Joao 1kg', 'ARROZINT1K', '7896079901233', 1, 8, 2, 'Arroz integral grao longo.', 'uploads/produtos/43cd816d30f8a223-1758144389.jpg', 1, 0, '2025-09-17 18:36:25'),
	(31, 'Azeite Extra Virgem Gallo 500ml', 'AZEIGAL500', '7891107004567', 1, 25, 5, 'Azeite portugues extra virgem 0.5L.', 'uploads/produtos/750e131763fc9979-1758144467.jpg', 1, 0, '2025-09-17 18:36:25'),
	(32, 'Vinagre de Alcool Castelo 750ml', 'VINACAST750', '7891040003456', 1, 26, 5, 'Vinagre de alcool culinario.', 'uploads/produtos/290b4c439da511f5-1758145730.jpg', 1, 0, '2025-09-17 18:36:25'),
	(33, 'Refrigerante Guarana Antarctica 2L', 'REFRGAU2L', '7891991012345', 4, 27, 4, 'Refrigerante guarana garrafa 2 litros.', 'uploads/produtos/a43734bcee9d7119-1758145504.jpg', 1, 0, '2025-09-17 18:36:25'),
	(34, 'Cerveja Heineken Long Neck 330ml', 'CERVHEI330', '7894321654321', 4, 28, 5, 'Cerveja premium long neck 330ml.', 'uploads/produtos/6f5fb3cd68805d63-1758144677.jpg', 1, 0, '2025-09-17 18:36:25'),
	(35, 'Agua Mineral Crystal 1.5L', 'AGCRYS15L', '7894900223456', 4, 29, 4, 'Agua mineral sem gas 1.5L.', 'uploads/produtos/618fcdb05930e5da-1758144218.jpg', 1, 0, '2025-09-17 18:36:25'),
	(36, 'Suco de Uva Aurora Integral 1.5L', 'SUCUAUR15L', '7891149101234', 4, 3, 4, 'Suco de uva integral 1.5L.', 'uploads/produtos/374bf8811bebfb52-1758145671.jpg', 1, 0, '2025-09-17 18:36:25'),
	(37, 'Maca Gala kg', 'MACAGALAKG', NULL, 7, 3, 2, 'Maca gala selecionada.', 'uploads/produtos/539441599911698d-1758145137.jpg', 1, 1, '2025-09-17 18:36:25'),
	(38, 'Tomate Italiano kg', 'TOMATITALKG', NULL, 7, 3, 2, 'Tomate italiano fresco.', 'uploads/produtos/4ffaeaa623b81c0e-1758145704.jpg', 1, 1, '2025-09-17 18:36:25'),
	(39, 'Alface Crespa unidade', 'ALFACECRESP', NULL, 7, 3, 1, 'Alface crespa colhida no dia.', 'uploads/produtos/555e5020b133c69c-1758144307.jpg', 1, 0, '2025-09-17 18:36:25'),
	(40, 'Sabao em Po OMO Lavagem Perfeita 1.6kg', 'SABOMO16KG', '7891150067890', 9, 33, 1, 'Sabao em po lavagem perfeita 1.6kg.', 'uploads/produtos/0833a3ad4ef5bd7c-1758145567.jpg', 1, 0, '2025-09-17 18:36:25'),
	(41, 'Amaciante Comfort Concentrado 2L', 'AMACCOMF2L', '7891021006543', 9, 31, 4, 'Amaciante concentrado fragrancia original.', 'uploads/produtos/27dce4b8e089bb38-1758144361.jpg', 1, 0, '2025-09-17 18:36:25'),
	(42, 'Desinfetante Veja Multiuso 500ml', 'DESINFVEJA500', '7891035009876', 9, 32, 5, 'Desinfetante multiuso perfumado.', 'uploads/produtos/99e4e24d1ae75b2d-1758144781.jpg', 1, 0, '2025-09-17 18:36:25'),
	(43, 'Papel Higienico Neve Folha Dupla 12x30m', 'PAPNEVE12', '7896079904567', 10, 34, 6, 'Papel higienico folha dupla pacote 12 rolos.', 'uploads/produtos/1e3ca05300b80d91-1758145347.jpg', 1, 0, '2025-09-17 18:37:33'),
	(44, 'Creme Dental Colgate Total 90g', 'CREMCOLG90', '7891000098765', 10, 35, 3, 'Creme dental protecao total 12h.', 'uploads/produtos/d3d437910e1596f5-1758144757.jpg', 1, 0, '2025-09-17 18:38:08'),
	(45, 'Shampoo Pantene Liso Extremo 400ml', 'SHAMPANT400', '7891021007890', 10, 36, 5, 'Shampoo pantene liso extremo 400ml.', 'uploads/produtos/282e2010f944bc91-1758145596.jpg', 1, 0, '2025-09-17 18:38:08'),
	(46, 'Racao Dog Chow Adulto 1kg', 'RACDOGCH1K', '7896044023456', 11, 37, 6, 'Racao seca premium para cães adultos.', 'uploads/produtos/50514bce6ca260ff-1758145454.jpg', 1, 0, '2025-09-17 18:38:08'),
	(47, 'Racao Whiskas Carne 1kg', 'RACWHISK1K', '7896021109876', 11, 38, 12, 'Racao seca para gatos sabor carne.', 'uploads/produtos/e2ce2c04b5eaae15-1758145479.jpg', 1, 0, '2025-09-17 18:38:08');

-- Copiando estrutura para tabela mercadinho.unidade
CREATE TABLE IF NOT EXISTS `unidade` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sigla` varchar(10) NOT NULL,
  `descricao` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_unidade_sigla` (`sigla`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Unidades de medida';

-- Copiando dados para a tabela mercadinho.unidade: ~9 rows (aproximadamente)
INSERT INTO `unidade` (`id`, `sigla`, `descricao`) VALUES
	(1, 'UN', 'Unidade'),
	(2, 'KG', 'Quilo'),
	(3, 'G', 'Gramas'),
	(4, 'L', 'Litro'),
	(5, 'ML', 'Mililitro'),
	(6, 'PCT', 'Pacote'),
	(7, 'CX', 'Caixa'),
	(12, 'PC', 'Peca'),
	(13, 'BL', 'Blister');

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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Usuários do sistema (admin/gerente/operador/cliente)';

-- Copiando dados para a tabela mercadinho.usuario: ~5 rows (aproximadamente)
INSERT INTO `usuario` (`id`, `nome`, `email`, `senha_hash`, `perfil`, `ativo`, `criado_em`) VALUES
	(1, 'João Ferlini', 'operador@mercado.local', '18c897bfc3cfce2b6d13cb154176b52b45253de6d82abe848a79d90f81e4f441', 'operador', 1, '2025-09-10 22:40:01'),
	(2, 'Administrador', 'admin@mercado.local', 'a14b819a2867d23c914df366addc22a5567d341b61b393878e476846d1b39e2c', 'admin', 1, '2025-09-10 22:40:44'),
	(3, 'Douglas Marcelo Monquero', 'douglas.monquero@gmail.com', '$2y$10$Mz7UePUj/MJaz67HWKbqUuDQpxv7.JLZJtMckFe0/7OOOdjpuALBm', 'admin', 1, '2025-09-10 22:45:37'),
	(4, 'Patricia Alves de Oliveira', 'paty@gatinha.com.br', '$2y$10$I5Oyd5DlUqmml0JsV/5c3uGBi8UCv.ScW73U.LE5ukslxtUkQt4X.', 'cliente', 1, '2025-09-10 22:46:41'),
	(5, 'Lucas Vinicius', 'lucas@email.com', '$2y$10$INlbktG5YUVvhJbEyyVSwO5f9inu1C6L.kKRQi3ocVHyEzzgUUU62', 'gerente', 1, '2025-09-12 22:52:31'),
	(6, 'Douglas Cliente', 'cliente@gmail.com', '$2y$10$./OhFnHDYC504U1Oe9.wku8B9V/WHzkDnEwrEMqRFsobkSuIxRivy', 'cliente', 1, '2025-09-18 17:56:35');

-- Removendo tabela temporária e criando a estrutura VIEW final
DROP TABLE IF EXISTS `pdv_caixa_resumo`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `pdv_caixa_resumo` AS SELECT
    p.id AS pedido_id, pm.tipo, pm.valor, mca.tipo AS mov_tipo,
    meta.terminal_id, meta.turno_id, p.troco, p.total, p.criado_em
  FROM pedido p
  JOIN pdv_pedido_meta meta ON meta.pedido_id=p.id
  LEFT JOIN pedido_pagamento pm ON pm.pedido_id=p.id
  LEFT JOIN mov_caixa mca ON mca.pedido_id=p.id
  WHERE p.canal='pdv' 
;

-- Removendo tabela temporária e criando a estrutura VIEW final
DROP TABLE IF EXISTS `pdv_turnos_abertos`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `pdv_turnos_abertos` AS SELECT t.*, term.nome AS terminal
  FROM pdv_turno t
  JOIN pdv_terminal term ON term.id=t.terminal_id
  WHERE t.status='aberto' 
;

-- Removendo tabela temporária e criando a estrutura VIEW final
DROP TABLE IF EXISTS `pedido_pdv`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `pedido_pdv` AS SELECT * FROM pedido WHERE canal='pdv' 
;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
