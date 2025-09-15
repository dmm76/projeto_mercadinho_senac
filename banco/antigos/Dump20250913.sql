-- MySQL dump 10.13  Distrib 8.0.42, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: mercadinho
-- ------------------------------------------------------
-- Server version	8.0.42

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `caixa`
--

DROP TABLE IF EXISTS `caixa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `caixa` (
  `id` int NOT NULL AUTO_INCREMENT,
  `operador_id` int NOT NULL,
  `abertura` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `saldo_inicial` decimal(12,2) NOT NULL DEFAULT '0.00',
  `fechamento` datetime DEFAULT NULL,
  `saldo_final` decimal(12,2) DEFAULT NULL,
  `observacao` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_caixa_operador_abertura` (`operador_id`,`abertura`),
  CONSTRAINT `fk_caixa_operador` FOREIGN KEY (`operador_id`) REFERENCES `usuario` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Abertura/fechamento de caixa';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `caixa`
--

LOCK TABLES `caixa` WRITE;
/*!40000 ALTER TABLE `caixa` DISABLE KEYS */;
/*!40000 ALTER TABLE `caixa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categoria`
--

DROP TABLE IF EXISTS `categoria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categoria` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(140) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ativa` tinyint(1) NOT NULL DEFAULT '1',
  `ordem` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_categoria_slug` (`slug`),
  KEY `idx_categoria_ativa` (`ativa`,`ordem`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Categorias do catálogo';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categoria`
--

LOCK TABLES `categoria` WRITE;
/*!40000 ALTER TABLE `categoria` DISABLE KEYS */;
/*!40000 ALTER TABLE `categoria` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cliente`
--

DROP TABLE IF EXISTS `cliente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cliente` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int DEFAULT NULL,
  `cpf` varchar(14) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nascimento` date DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cliente_cpf` (`cpf`),
  UNIQUE KEY `uq_cliente_usuario` (`usuario_id`),
  CONSTRAINT `fk_cliente_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Cadastro de clientes (pode vincular a um usuário)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cliente`
--

LOCK TABLES `cliente` WRITE;
/*!40000 ALTER TABLE `cliente` DISABLE KEYS */;
INSERT INTO `cliente` VALUES (1,2,NULL,NULL,NULL,'2025-09-13 00:41:59'),(2,3,NULL,NULL,NULL,'2025-09-13 00:41:59'),(3,5,NULL,NULL,NULL,'2025-09-13 00:41:59'),(4,1,NULL,NULL,NULL,'2025-09-13 00:41:59'),(5,4,NULL,'(44) 99999-1234',NULL,'2025-09-13 00:41:59');
/*!40000 ALTER TABLE `cliente` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `compra`
--

DROP TABLE IF EXISTS `compra`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `compra` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fornecedor_id` int NOT NULL,
  `data` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `observacao` text COLLATE utf8mb4_unicode_ci,
  `criado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_compra_forn_data` (`fornecedor_id`,`data`),
  CONSTRAINT `fk_compra_fornecedor` FOREIGN KEY (`fornecedor_id`) REFERENCES `fornecedor` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Compras de fornecedores';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `compra`
--

LOCK TABLES `compra` WRITE;
/*!40000 ALTER TABLE `compra` DISABLE KEYS */;
/*!40000 ALTER TABLE `compra` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cupom`
--

DROP TABLE IF EXISTS `cupom`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cupom` (
  `id` int NOT NULL AUTO_INCREMENT,
  `codigo` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` enum('percentual','valor') COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor` decimal(12,2) NOT NULL,
  `inicio` datetime DEFAULT NULL,
  `fim` datetime DEFAULT NULL,
  `usos_max` int DEFAULT NULL,
  `usos_ate_agora` int NOT NULL DEFAULT '0',
  `regras_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cupom_codigo` (`codigo`),
  CONSTRAINT `cupom_chk_1` CHECK (json_valid(`regras_json`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Cupons de desconto';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cupom`
--

LOCK TABLES `cupom` WRITE;
/*!40000 ALTER TABLE `cupom` DISABLE KEYS */;
/*!40000 ALTER TABLE `cupom` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `endereco`
--

DROP TABLE IF EXISTS `endereco`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `endereco` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cliente_id` int NOT NULL,
  `rotulo` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nome` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cep` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logradouro` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `complemento` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bairro` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cidade` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `uf` char(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `principal` tinyint(1) NOT NULL DEFAULT '0',
  `criado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_endereco_cliente` (`cliente_id`,`principal`),
  CONSTRAINT `fk_endereco_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `cliente` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Endereços de clientes (um pode ser principal)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `endereco`
--

LOCK TABLES `endereco` WRITE;
/*!40000 ALTER TABLE `endereco` DISABLE KEYS */;
INSERT INTO `endereco` VALUES (1,2,'Casa','Douglas','87060-110','Rua dos Ipes','312','Casa','Borba Gato','Maringá','PR',1,'2025-09-13 00:44:35'),(2,5,'Apartamento','Patricia Alves de Oliveira','87010-255','Rua Tanaka','50','bloco 3 apto 21','Vila Emilia','Maringá','PR',1,'2025-09-13 00:46:52'),(3,2,'Estudo','Douglas Marcelo Monquero','87010-100','Avenida Colombo','100','Senac','Zona 07','Maringá','PR',0,'2025-09-13 01:16:27'),(4,2,'Trabalho','Douglas','87010-100','Rua das Estrelas','1000','Sala 01','Centro','Maringá','PR',0,'2025-09-13 01:37:52');
/*!40000 ALTER TABLE `endereco` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estoque`
--

DROP TABLE IF EXISTS `estoque`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `estoque` (
  `id` int NOT NULL AUTO_INCREMENT,
  `produto_id` int NOT NULL,
  `quantidade` decimal(10,3) NOT NULL DEFAULT '0.000',
  `minimo` decimal(10,3) NOT NULL DEFAULT '0.000',
  `atualizado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_estoque_produto` (`produto_id`),
  KEY `idx_estoque_minimo` (`minimo`),
  CONSTRAINT `fk_estoque_produto` FOREIGN KEY (`produto_id`) REFERENCES `produto` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Estoque atual por produto (1:1)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estoque`
--

LOCK TABLES `estoque` WRITE;
/*!40000 ALTER TABLE `estoque` DISABLE KEYS */;
/*!40000 ALTER TABLE `estoque` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fornecedor`
--

DROP TABLE IF EXISTS `fornecedor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fornecedor` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cnpj` varchar(18) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contato` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(160) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_fornecedor_cnpj` (`cnpj`),
  KEY `idx_fornecedor_nome` (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Fornecedores';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fornecedor`
--

LOCK TABLES `fornecedor` WRITE;
/*!40000 ALTER TABLE `fornecedor` DISABLE KEYS */;
/*!40000 ALTER TABLE `fornecedor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `item_compra`
--

DROP TABLE IF EXISTS `item_compra`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `item_compra` (
  `id` int NOT NULL AUTO_INCREMENT,
  `compra_id` int NOT NULL,
  `produto_id` int NOT NULL,
  `quantidade` decimal(10,3) NOT NULL,
  `custo_unit` decimal(12,4) NOT NULL,
  `desconto_unit` decimal(12,4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_itemcompra_compra` (`compra_id`),
  KEY `idx_itemcompra_produto` (`produto_id`),
  CONSTRAINT `fk_itemcompra_compra` FOREIGN KEY (`compra_id`) REFERENCES `compra` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_itemcompra_produto` FOREIGN KEY (`produto_id`) REFERENCES `produto` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Itens das compras (entrada de estoque)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item_compra`
--

LOCK TABLES `item_compra` WRITE;
/*!40000 ALTER TABLE `item_compra` DISABLE KEYS */;
/*!40000 ALTER TABLE `item_compra` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `item_pedido`
--

DROP TABLE IF EXISTS `item_pedido`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `item_pedido` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pedido_id` int NOT NULL,
  `produto_id` int NOT NULL,
  `quantidade` decimal(10,3) NOT NULL DEFAULT '1.000',
  `peso_kg` decimal(10,3) DEFAULT NULL,
  `preco_unit` decimal(12,2) NOT NULL,
  `desconto_unit` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_itempedido_pedido` (`pedido_id`),
  KEY `idx_itempedido_produto` (`produto_id`),
  CONSTRAINT `fk_itempedido_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedido` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_itempedido_produto` FOREIGN KEY (`produto_id`) REFERENCES `produto` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Itens de pedido';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item_pedido`
--

LOCK TABLES `item_pedido` WRITE;
/*!40000 ALTER TABLE `item_pedido` DISABLE KEYS */;
/*!40000 ALTER TABLE `item_pedido` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `marca`
--

DROP TABLE IF EXISTS `marca`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `marca` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_marca_nome` (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Marcas dos produtos';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `marca`
--

LOCK TABLES `marca` WRITE;
/*!40000 ALTER TABLE `marca` DISABLE KEYS */;
/*!40000 ALTER TABLE `marca` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mov_caixa`
--

DROP TABLE IF EXISTS `mov_caixa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mov_caixa` (
  `id` int NOT NULL AUTO_INCREMENT,
  `caixa_id` int NOT NULL,
  `tipo` enum('entrada','saida') COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor` decimal(12,2) NOT NULL,
  `descricao` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pedido_id` int DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_movcaixa_caixa` (`caixa_id`,`criado_em`),
  KEY `idx_movcaixa_pedido` (`pedido_id`),
  CONSTRAINT `fk_movcaixa_caixa` FOREIGN KEY (`caixa_id`) REFERENCES `caixa` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_movcaixa_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedido` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Movimentações de caixa (vendas/sangria/suprimento)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mov_caixa`
--

LOCK TABLES `mov_caixa` WRITE;
/*!40000 ALTER TABLE `mov_caixa` DISABLE KEYS */;
/*!40000 ALTER TABLE `mov_caixa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mov_estoque`
--

DROP TABLE IF EXISTS `mov_estoque`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mov_estoque` (
  `id` int NOT NULL AUTO_INCREMENT,
  `produto_id` int NOT NULL,
  `tipo` enum('entrada','saida','ajuste') COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantidade` decimal(10,3) NOT NULL,
  `origem` enum('pedido','compra','ajuste') COLLATE utf8mb4_unicode_ci NOT NULL,
  `referencia_id` int DEFAULT NULL,
  `observacao` text COLLATE utf8mb4_unicode_ci,
  `criado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_movestoque_produto` (`produto_id`,`criado_em`),
  CONSTRAINT `fk_movestoque_produto` FOREIGN KEY (`produto_id`) REFERENCES `produto` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Movimentações de estoque';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mov_estoque`
--

LOCK TABLES `mov_estoque` WRITE;
/*!40000 ALTER TABLE `mov_estoque` DISABLE KEYS */;
/*!40000 ALTER TABLE `mov_estoque` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pedido`
--

DROP TABLE IF EXISTS `pedido`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pedido` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cliente_id` int NOT NULL,
  `endereco_id` int DEFAULT NULL,
  `status` enum('novo','em_separacao','em_transporte','pronto','finalizado','cancelado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'novo',
  `entrega` enum('retirada','entrega') COLLATE utf8mb4_unicode_ci NOT NULL,
  `pagamento` enum('na_entrega','pix','cartao','gateway') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'na_entrega',
  `subtotal` decimal(12,2) NOT NULL,
  `frete` decimal(12,2) NOT NULL DEFAULT '0.00',
  `desconto` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total` decimal(12,2) NOT NULL,
  `codigo_externo` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `atualizado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_pedido_cliente` (`cliente_id`,`criado_em`),
  KEY `idx_pedido_status` (`status`),
  KEY `fk_pedido_endereco` (`endereco_id`),
  KEY `idx_pedido_status_data` (`status`,`criado_em`),
  CONSTRAINT `fk_pedido_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `cliente` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_pedido_endereco` FOREIGN KEY (`endereco_id`) REFERENCES `endereco` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Pedidos da loja (online/retirada)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedido`
--

LOCK TABLES `pedido` WRITE;
/*!40000 ALTER TABLE `pedido` DISABLE KEYS */;
INSERT INTO `pedido` VALUES (1,5,1,'novo','entrega','pix',120.00,10.00,0.00,130.00,'PED-0001','2025-09-13 02:38:18','2025-09-13 02:43:43');
/*!40000 ALTER TABLE `pedido` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pedido_cupom`
--

DROP TABLE IF EXISTS `pedido_cupom`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pedido_cupom` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pedido_id` int NOT NULL,
  `cupom_id` int NOT NULL,
  `valor_desconto_aplicado` decimal(12,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_pedidocupom_pedido` (`pedido_id`),
  KEY `idx_pedidocupom_cupom` (`cupom_id`),
  CONSTRAINT `fk_pedidocupom_cupom` FOREIGN KEY (`cupom_id`) REFERENCES `cupom` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_pedidocupom_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedido` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Aplicações de cupons em pedidos';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedido_cupom`
--

LOCK TABLES `pedido_cupom` WRITE;
/*!40000 ALTER TABLE `pedido_cupom` DISABLE KEYS */;
/*!40000 ALTER TABLE `pedido_cupom` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `preco`
--

DROP TABLE IF EXISTS `preco`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `preco` (
  `id` int NOT NULL AUTO_INCREMENT,
  `produto_id` int NOT NULL,
  `preco_venda` decimal(12,2) NOT NULL,
  `preco_promocional` decimal(12,2) DEFAULT NULL,
  `inicio_promo` datetime DEFAULT NULL,
  `fim_promo` datetime DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_preco_produto` (`produto_id`,`inicio_promo`,`fim_promo`),
  CONSTRAINT `fk_preco_produto` FOREIGN KEY (`produto_id`) REFERENCES `produto` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Histórico de preços por produto';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `preco`
--

LOCK TABLES `preco` WRITE;
/*!40000 ALTER TABLE `preco` DISABLE KEYS */;
/*!40000 ALTER TABLE `preco` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `produto`
--

DROP TABLE IF EXISTS `produto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `produto` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ean` varchar(14) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `categoria_id` int NOT NULL,
  `marca_id` int DEFAULT NULL,
  `unidade_id` int NOT NULL,
  `descricao` text COLLATE utf8mb4_unicode_ci,
  `imagem` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `peso_variavel` tinyint(1) NOT NULL DEFAULT '0',
  `criado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Produtos do catálogo';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produto`
--

LOCK TABLES `produto` WRITE;
/*!40000 ALTER TABLE `produto` DISABLE KEYS */;
/*!40000 ALTER TABLE `produto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `unidade`
--

DROP TABLE IF EXISTS `unidade`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `unidade` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sigla` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_unidade_sigla` (`sigla`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Unidades de medida';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unidade`
--

LOCK TABLES `unidade` WRITE;
/*!40000 ALTER TABLE `unidade` DISABLE KEYS */;
/*!40000 ALTER TABLE `unidade` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuario`
--

DROP TABLE IF EXISTS `usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuario` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `senha_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `perfil` enum('admin','gerente','operador','cliente') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cliente',
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `criado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_usuario_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Usuários do sistema (admin/gerente/operador/cliente)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario`
--

LOCK TABLES `usuario` WRITE;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
INSERT INTO `usuario` VALUES (1,'Operador','operador@mercado.local','18c897bfc3cfce2b6d13cb154176b52b45253de6d82abe848a79d90f81e4f441','operador',1,'2025-09-10 22:40:01'),(2,'Administrador','admin@mercado.local','a14b819a2867d23c914df366addc22a5567d341b61b393878e476846d1b39e2c','admin',1,'2025-09-10 22:40:44'),(3,'Douglas','douglas.monquero@gmail.com','$2y$10$C3CTohQJfarGZZt9I1ZSs.Vde/Tbz1Y8GKsDAgFqHOBYh3esqSlYm','admin',1,'2025-09-10 22:45:37'),(4,'Patricia Alves de Oliveira','paty@gatinha.com.br','$2y$10$I5Oyd5DlUqmml0JsV/5c3uGBi8UCv.ScW73U.LE5ukslxtUkQt4X.','cliente',1,'2025-09-10 22:46:41'),(5,'Lucas Vinicius','lucas@email.com','$2y$10$INlbktG5YUVvhJbEyyVSwO5f9inu1C6L.kKRQi3ocVHyEzzgUUU62','cliente',1,'2025-09-12 22:52:31');
/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-09-13  0:16:33
