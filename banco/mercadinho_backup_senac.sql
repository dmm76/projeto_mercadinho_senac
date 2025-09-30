-- MySQL dump 10.13  Distrib 8.0.42, for Win64 (x86_64)
--
-- Host: localhost    Database: mercadinho
-- ------------------------------------------------------
-- Server version	8.0.42

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
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
  `nome` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Caixa Loja',
  `operador_id` int NOT NULL,
  `terminal_id` int NOT NULL,
  `abertura` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `saldo_inicial` decimal(12,2) NOT NULL DEFAULT '0.00',
  `fechamento` datetime DEFAULT NULL,
  `saldo_final` decimal(12,2) DEFAULT NULL,
  `status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aberto',
  `aberto_em` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fechado_em` datetime DEFAULT NULL,
  `observacao` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_caixa_operador_abertura` (`operador_id`,`abertura`),
  KEY `idx_caixa_terminal` (`terminal_id`),
  KEY `idx_caixa_operador` (`operador_id`),
  CONSTRAINT `fk_caixa_operador` FOREIGN KEY (`operador_id`) REFERENCES `usuario` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_caixa_terminal` FOREIGN KEY (`terminal_id`) REFERENCES `pdv_terminal` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Abertura/fechamento de caixa';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `caixa`
--

LOCK TABLES `caixa` WRITE;
/*!40000 ALTER TABLE `caixa` DISABLE KEYS */;
INSERT INTO `caixa` VALUES (6,'Caixa Loja',9,1,'2025-09-29 16:19:09',100.00,NULL,561.94,'fechado','2025-09-29 16:19:09','2025-09-29 18:23:04',NULL),(7,'Caixa Loja',3,2,'2025-09-29 18:23:34',100.00,NULL,100.00,'fechado','2025-09-29 18:23:34','2025-09-29 18:23:41',NULL),(8,'Caixa Loja',9,1,'2025-09-30 13:25:21',100.00,NULL,200.00,'fechado','2025-09-30 13:25:21','2025-09-30 11:37:16',NULL),(9,'Caixa Loja',3,1,'2025-09-30 11:37:54',100.00,NULL,150.00,'fechado','2025-09-30 11:37:54','2025-09-30 11:38:23',NULL);
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
  `nome` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(140) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ativa` tinyint(1) NOT NULL DEFAULT '1',
  `ordem` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_categoria_slug` (`slug`),
  KEY `idx_categoria_ativa` (`ativa`,`ordem`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Categorias do catálogo';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categoria`
--

LOCK TABLES `categoria` WRITE;
/*!40000 ALTER TABLE `categoria` DISABLE KEYS */;
INSERT INTO `categoria` VALUES (1,'Mercearia','mercearia',1,1),(3,'Padaria','padaria',1,2),(4,'Bebidas','bebidas',1,4),(5,'Laticinios','laticinios',1,1),(6,'Congelados','congelados',1,3),(7,'Hortifruti','hortifruti',1,6),(8,'Acougue','acougue',1,7),(9,'Limpeza','limpeza',1,8),(10,'Higiene','higiene',1,9),(11,'Petshop','petshop',1,10);
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
  `cpf` varchar(14) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nascimento` date DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cliente_cpf` (`cpf`),
  UNIQUE KEY `uq_cliente_usuario` (`usuario_id`),
  CONSTRAINT `fk_cliente_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Cadastro de clientes (pode vincular a um usuário)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cliente`
--

LOCK TABLES `cliente` WRITE;
/*!40000 ALTER TABLE `cliente` DISABLE KEYS */;
INSERT INTO `cliente` VALUES (1,NULL,NULL,NULL,NULL,'2025-09-13 03:41:59'),(2,3,'02168710937','(44) 99901-3434','1976-02-21','2025-09-13 03:41:59'),(3,NULL,NULL,NULL,NULL,'2025-09-13 03:41:59'),(4,NULL,NULL,NULL,NULL,'2025-09-13 03:41:59'),(5,NULL,NULL,'(44) 99999-1234',NULL,'2025-09-13 03:41:59'),(6,NULL,NULL,NULL,NULL,'2025-09-21 23:53:40'),(7,8,NULL,NULL,NULL,'2025-09-25 17:42:43'),(8,9,NULL,NULL,NULL,'2025-09-29 18:50:50');
/*!40000 ALTER TABLE `cliente` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cliente_favorito`
--

DROP TABLE IF EXISTS `cliente_favorito`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cliente_favorito` (
  `cliente_id` int NOT NULL,
  `produto_id` int NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cliente_id`,`produto_id`),
  KEY `fk_cliente_favorito_produto` (`produto_id`),
  CONSTRAINT `fk_cliente_favorito_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `cliente` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cliente_favorito_produto` FOREIGN KEY (`produto_id`) REFERENCES `produto` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cliente_favorito`
--

LOCK TABLES `cliente_favorito` WRITE;
/*!40000 ALTER TABLE `cliente_favorito` DISABLE KEYS */;
INSERT INTO `cliente_favorito` VALUES (2,35,'2025-09-24 03:40:36'),(7,40,'2025-09-25 17:43:08'),(7,44,'2025-09-25 17:42:58'),(7,46,'2025-09-25 17:42:51');
/*!40000 ALTER TABLE `cliente_favorito` ENABLE KEYS */;
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
  `observacao` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
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
-- Table structure for table `contato_mensagens`
--

DROP TABLE IF EXISTS `contato_mensagens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contato_mensagens` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mensagem` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `resposta` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('aberta','respondida','arquivada') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aberta',
  `ip` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `criada_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `respondida_em` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contato_mensagens`
--

LOCK TABLES `contato_mensagens` WRITE;
/*!40000 ALTER TABLE `contato_mensagens` DISABLE KEYS */;
INSERT INTO `contato_mensagens` VALUES (1,'Douglas','douglas@email.com','teste agora','estamos em teste','respondida','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:142.0) Gecko/20100101 Firefox/142.0','2025-09-17 03:37:03','2025-09-17 05:27:39'),(2,'Douglas Marcelo Monquero','douglas@email.com','teste de msg dia 17','resposta respondida','arquivada','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:142.0) Gecko/20100101 Firefox/142.0','2025-09-17 18:56:38',NULL),(3,'Valdir Mendonça','valdir@email.com','Teste de envio 17 as 18horas','teste ok','respondida','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:142.0) Gecko/20100101 Firefox/142.0','2025-09-18 00:21:13','2025-09-18 00:51:22'),(4,'Patricia Monquero','paty@gatinha.com.br','Teste railway','oi amore','respondida','100.64.0.3','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:142.0) Gecko/20100101 Firefox/142.0','2025-09-19 02:48:16','2025-09-19 02:48:34'),(5,'Douglas Marcelo','douglas@email.com','Verificação de mensagens','obrigado pelo teste','respondida','100.64.0.2','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','2025-09-23 21:22:09','2025-09-23 21:22:32');
/*!40000 ALTER TABLE `contato_mensagens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cupom`
--

DROP TABLE IF EXISTS `cupom`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cupom` (
  `id` int NOT NULL AUTO_INCREMENT,
  `codigo` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` enum('percentual','valor') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `rotulo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nome` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cep` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `logradouro` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `complemento` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bairro` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cidade` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `uf` char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `principal` tinyint(1) NOT NULL DEFAULT '0',
  `criado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_endereco_cliente` (`cliente_id`,`principal`),
  CONSTRAINT `fk_endereco_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `cliente` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Endereços de clientes (um pode ser principal)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `endereco`
--

LOCK TABLES `endereco` WRITE;
/*!40000 ALTER TABLE `endereco` DISABLE KEYS */;
INSERT INTO `endereco` VALUES (1,2,'Casa','Douglas','87060-110','Rua dos Ipes','312','Casa','Borba Gato','Maringá','PR',1,'2025-09-13 03:44:35'),(2,5,'Apartamento','Patricia Alves de Oliveira','87010-255','Rua Tanaka','50','bloco 3 apto 21','Vila Emilia','Maringá','PR',1,'2025-09-13 03:46:52'),(3,2,'Estudo','Douglas Marcelo Monquero','87010-100','Avenida Colombo','100','Senac','Zona 07','Maringá','PR',0,'2025-09-13 04:16:27'),(4,2,'Trabalho','Douglas','87010-100','Rua das Estrelas','1000','Sala 01','Centro','Maringá','PR',0,'2025-09-13 04:37:52'),(5,7,'Casa','Ricardo Menile','87033-370','Rua Chile','1673','404A','Jardim Alvorada','Maringá','PR',1,'2025-09-25 17:46:13');
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
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Estoque atual por produto (1:1)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estoque`
--

LOCK TABLES `estoque` WRITE;
/*!40000 ALTER TABLE `estoque` DISABLE KEYS */;
INSERT INTO `estoque` VALUES (1,1,29.000,5.000,'2025-09-24 22:47:04'),(2,2,48.000,5.000,'2025-09-17 02:45:10'),(3,3,95.450,10.000,'2025-09-17 04:11:07'),(4,4,95.000,12.000,'2025-09-17 21:30:33'),(5,5,30.000,5.000,'2025-09-17 21:30:33'),(6,6,58.000,10.000,'2025-09-18 00:50:17'),(7,7,25.000,8.000,'2025-09-24 22:47:04'),(8,8,80.000,10.000,'2025-09-17 21:30:33'),(9,9,70.000,10.000,'2025-09-17 21:30:33'),(10,10,55.000,8.000,'2025-09-17 21:30:33'),(11,11,120.000,15.000,'2025-09-17 21:36:25'),(12,12,45.000,8.000,'2025-09-17 21:36:25'),(13,13,150.000,20.000,'2025-09-17 21:36:25'),(14,14,65.000,10.000,'2025-09-17 21:36:25'),(15,15,120.000,25.000,'2025-09-17 21:36:25'),(16,16,80.000,12.000,'2025-09-17 21:36:25'),(17,17,70.000,10.000,'2025-09-17 21:36:25'),(18,18,40.000,6.000,'2025-09-17 21:36:25'),(19,19,55.000,10.000,'2025-09-17 21:36:25'),(20,20,45.000,8.000,'2025-09-17 21:36:25'),(21,21,60.000,10.000,'2025-09-17 21:36:25'),(22,22,80.000,15.000,'2025-09-17 21:36:25'),(23,23,20.000,12.000,'2025-09-24 22:47:04'),(24,24,70.000,10.000,'2025-09-17 21:36:25'),(25,25,50.000,8.000,'2025-09-17 21:36:25'),(26,26,149.000,20.000,'2025-09-21 23:54:21'),(27,27,120.000,18.000,'2025-09-17 21:36:25'),(28,28,110.000,15.000,'2025-09-17 21:36:25'),(29,29,129.000,20.000,'2025-09-30 01:01:36'),(30,30,115.000,18.000,'2025-09-17 21:36:25'),(31,31,70.000,10.000,'2025-09-17 21:36:25'),(32,32,90.000,15.000,'2025-09-17 21:36:25'),(33,33,140.000,25.000,'2025-09-17 21:36:25'),(34,34,200.000,30.000,'2025-09-17 21:36:25'),(35,35,159.000,25.000,'2025-09-30 14:38:38'),(36,36,60.000,8.000,'2025-09-17 21:36:25'),(37,37,84.760,20.000,'2025-09-21 19:14:28'),(38,38,78.410,18.000,'2025-09-21 19:14:28'),(39,39,117.000,25.000,'2025-09-18 02:00:22'),(40,40,59.000,10.000,'2025-09-25 17:47:10'),(41,41,70.000,12.000,'2025-09-17 21:36:25'),(42,42,106.000,15.000,'2025-09-25 17:47:10'),(43,43,78.000,12.000,'2025-09-18 16:30:16'),(44,44,116.000,20.000,'2025-09-24 03:11:52'),(45,45,75.000,10.000,'2025-09-17 21:38:08'),(46,46,86.000,12.000,'2025-09-30 16:24:04'),(47,47,63.000,12.000,'2025-09-30 16:23:45');
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
  `nome` varchar(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cnpj` varchar(18) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contato` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Itens de pedido';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item_pedido`
--

LOCK TABLES `item_pedido` WRITE;
/*!40000 ALTER TABLE `item_pedido` DISABLE KEYS */;
INSERT INTO `item_pedido` VALUES (3,1,1,2.000,NULL,50.00,0.00),(4,1,2,1.000,NULL,20.00,0.00),(5,2,1,2.000,NULL,50.00,NULL),(6,2,2,1.000,NULL,20.00,NULL),(7,2,3,1.000,NULL,3.99,NULL),(8,3,1,1.000,NULL,50.00,NULL),(9,4,1,2.000,NULL,50.00,NULL),(10,4,2,1.000,NULL,20.00,NULL),(11,5,1,1.000,NULL,50.00,NULL),(12,6,3,3.550,NULL,3.99,NULL),(13,7,47,1.000,NULL,19.90,NULL),(14,7,46,1.000,NULL,16.90,NULL),(15,7,6,2.000,NULL,18.50,NULL),(16,8,39,3.000,NULL,2.99,NULL),(17,8,46,1.000,NULL,16.90,NULL),(18,8,44,3.000,NULL,8.49,NULL),(19,8,42,3.000,NULL,5.49,NULL),(20,9,47,1.000,NULL,19.90,NULL),(21,9,43,2.000,NULL,24.90,NULL),(22,10,47,1.000,NULL,19.90,NULL),(23,11,47,4.000,NULL,19.90,NULL),(24,11,38,4.000,NULL,9.50,NULL),(25,11,37,4.250,NULL,7.80,NULL),(26,12,47,2.000,NULL,19.90,NULL),(27,12,38,2.590,NULL,9.50,NULL),(28,12,37,0.990,NULL,7.80,NULL),(29,13,47,1.000,NULL,19.90,NULL),(30,13,26,1.000,NULL,5.49,NULL),(31,14,44,1.000,NULL,8.49,NULL),(32,15,47,1.000,NULL,19.90,NULL),(33,16,47,1.000,NULL,19.90,NULL),(34,17,47,1.000,NULL,19.90,NULL),(35,18,47,1.000,NULL,19.90,NULL),(36,19,35,1.000,NULL,2.99,NULL),(37,20,47,1.000,NULL,19.90,NULL),(38,20,35,12.000,NULL,2.99,NULL),(39,21,7,15.000,NULL,32.90,NULL),(40,21,1,15.000,NULL,50.00,NULL),(41,21,23,45.000,NULL,44.90,NULL),(42,22,46,1.000,NULL,16.90,NULL),(43,22,40,1.000,NULL,27.90,NULL),(44,22,42,1.000,NULL,5.49,NULL),(45,23,30,1.000,NULL,9.90,0.00),(46,24,1,1.000,NULL,50.00,0.00),(47,25,2,1.000,NULL,20.00,0.00),(48,25,1,2.000,NULL,50.00,0.00),(49,25,11,6.000,NULL,6.99,0.00),(50,27,29,1.000,NULL,5.60,NULL),(51,28,47,1.000,NULL,19.90,NULL),(52,29,35,6.000,NULL,2.99,NULL),(53,30,47,1.000,NULL,19.90,NULL),(54,31,46,1.000,NULL,16.90,NULL),(55,32,30,10.000,NULL,9.90,0.00),(56,34,26,1.000,NULL,5.49,0.00),(57,34,34,6.000,NULL,5.99,0.00),(58,35,35,2.000,NULL,2.99,NULL);
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
  `nome` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_marca_nome` (`nome`)
) ENGINE=InnoDB AUTO_INCREMENT=95 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Marcas dos produtos';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `marca`
--

LOCK TABLES `marca` WRITE;
/*!40000 ALTER TABLE `marca` DISABLE KEYS */;
INSERT INTO `marca` VALUES (30,'Amoara'),(27,'Antarctica'),(3,'Aurora'),(42,'Aurora Alimentos'),(18,'Aviacao'),(8,'Barrao'),(9,'Bela Vista'),(26,'Castelo'),(35,'Colgate'),(31,'Comfort'),(29,'Crystal'),(7,'Do Bem'),(37,'Dog Chow'),(5,'Forno de Minas'),(25,'Gallo'),(22,'Galo'),(1,'Genérica'),(28,'Heineken'),(21,'McCain'),(17,'Nestle'),(34,'Neve'),(33,'OMO'),(36,'Pantene'),(4,'Perdigao'),(6,'Pilao'),(39,'Pullman'),(23,'Renata'),(20,'Sadia'),(19,'Seara'),(2,'Tio João'),(40,'Tirolez'),(24,'Uniao'),(32,'Veja'),(38,'Whiskas'),(41,'Wickbold');
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
  `tipo` enum('entrada','saida') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor` decimal(12,2) NOT NULL,
  `descricao` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pedido_id` int DEFAULT NULL,
  `terminal_id` int DEFAULT NULL,
  `turno_id` int DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_movcaixa_caixa` (`caixa_id`,`criado_em`),
  KEY `idx_movcaixa_pedido` (`pedido_id`),
  KEY `idx_mov_caixa_terminal` (`terminal_id`),
  KEY `idx_mov_caixa_turno` (`turno_id`),
  CONSTRAINT `fk_mov_caixa_terminal` FOREIGN KEY (`terminal_id`) REFERENCES `pdv_terminal` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_mov_caixa_turno` FOREIGN KEY (`turno_id`) REFERENCES `pdv_turno` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_movcaixa_caixa` FOREIGN KEY (`caixa_id`) REFERENCES `caixa` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_movcaixa_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedido` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Movimentações de caixa (vendas/sangria/suprimento)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mov_caixa`
--

LOCK TABLES `mov_caixa` WRITE;
/*!40000 ALTER TABLE `mov_caixa` DISABLE KEYS */;
INSERT INTO `mov_caixa` VALUES (1,6,'entrada',10.00,'Pagamento Dinheiro PDV #23',23,1,1,'2025-09-29 19:24:12'),(2,6,'entrada',10.00,'Pagamento Pix PDV #24',24,1,1,'2025-09-29 19:24:46'),(3,6,'entrada',40.00,'Pagamento Debito PDV #24',24,1,1,'2025-09-29 19:24:50'),(4,6,'entrada',30.00,'Pagamento Debito PDV #24',24,1,1,'2025-09-29 19:24:58'),(5,6,'entrada',10.00,'Pagamento Dinheiro PDV #24',24,1,1,'2025-09-29 19:25:02'),(6,6,'entrada',100.00,'Pagamento Dinheiro PDV #25',25,1,1,'2025-09-29 20:15:08'),(7,6,'entrada',61.94,'Pagamento Credito PDV #25',25,1,1,'2025-09-29 20:15:13'),(8,6,'entrada',200.00,'Pagamento Credito PDV #25',25,1,1,'2025-09-29 20:15:36'),(9,8,'entrada',100.00,'Pagamento Dinheiro PDV #32',32,1,3,'2025-09-30 16:25:50'),(10,9,'entrada',50.00,'Pagamento Dinheiro PDV #34',34,1,4,'2025-09-30 14:38:15');
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
  `tipo` enum('entrada','saida','ajuste') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantidade` decimal(10,3) NOT NULL,
  `origem` enum('pedido','compra','ajuste') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `referencia_id` int DEFAULT NULL,
  `observacao` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `criado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_movestoque_produto` (`produto_id`,`criado_em`),
  CONSTRAINT `fk_movestoque_produto` FOREIGN KEY (`produto_id`) REFERENCES `produto` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Movimentações de estoque';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mov_estoque`
--

LOCK TABLES `mov_estoque` WRITE;
/*!40000 ALTER TABLE `mov_estoque` DISABLE KEYS */;
INSERT INTO `mov_estoque` VALUES (1,1,'saida',2.000,'pedido',2,'Saida por venda','2025-09-17 02:33:56'),(2,2,'saida',1.000,'pedido',2,'Saida por venda','2025-09-17 02:33:56'),(3,3,'saida',1.000,'pedido',2,'Saida por venda','2025-09-17 02:33:56'),(4,1,'saida',1.000,'pedido',3,'Saida por venda','2025-09-17 02:35:06'),(5,1,'saida',2.000,'pedido',4,'Saida por venda','2025-09-17 02:45:10'),(6,2,'saida',1.000,'pedido',4,'Saida por venda','2025-09-17 02:45:10'),(7,1,'saida',1.000,'pedido',5,'Saida por venda','2025-09-17 03:08:49'),(8,3,'saida',3.550,'pedido',6,'Saida por venda','2025-09-17 04:11:07'),(9,47,'saida',1.000,'pedido',7,'Saida por venda','2025-09-18 00:50:17'),(10,46,'saida',1.000,'pedido',7,'Saida por venda','2025-09-18 00:50:17'),(11,6,'saida',2.000,'pedido',7,'Saida por venda','2025-09-18 00:50:17'),(12,39,'saida',3.000,'pedido',8,'Saida por venda','2025-09-18 02:00:22'),(13,46,'saida',1.000,'pedido',8,'Saida por venda','2025-09-18 02:00:22'),(14,44,'saida',3.000,'pedido',8,'Saida por venda','2025-09-18 02:00:22'),(15,42,'saida',3.000,'pedido',8,'Saida por venda','2025-09-18 02:00:22'),(16,47,'saida',1.000,'pedido',9,'Saida por venda','2025-09-18 16:30:16'),(17,43,'saida',2.000,'pedido',9,'Saida por venda','2025-09-18 16:30:16'),(18,47,'saida',1.000,'pedido',10,'Saida por venda','2025-09-19 04:13:33'),(19,47,'saida',4.000,'pedido',11,'Saida por venda','2025-09-21 19:12:57'),(20,38,'saida',4.000,'pedido',11,'Saida por venda','2025-09-21 19:12:57'),(21,37,'saida',4.250,'pedido',11,'Saida por venda','2025-09-21 19:12:57'),(22,47,'saida',2.000,'pedido',12,'Saida por venda','2025-09-21 19:14:28'),(23,38,'saida',2.590,'pedido',12,'Saida por venda','2025-09-21 19:14:28'),(24,37,'saida',0.990,'pedido',12,'Saida por venda','2025-09-21 19:14:28'),(25,47,'saida',1.000,'pedido',13,'Saida por venda','2025-09-21 23:54:21'),(26,26,'saida',1.000,'pedido',13,'Saida por venda','2025-09-21 23:54:21'),(27,44,'saida',1.000,'pedido',14,'Saida por venda','2025-09-24 03:11:52'),(28,47,'saida',1.000,'pedido',15,'Saida por venda','2025-09-24 03:22:18'),(29,47,'saida',1.000,'pedido',16,'Saida por venda','2025-09-24 03:22:40'),(30,47,'saida',1.000,'pedido',17,'Saida por venda','2025-09-24 03:22:52'),(31,47,'saida',1.000,'pedido',18,'Saida por venda','2025-09-24 03:24:33'),(32,35,'saida',1.000,'pedido',19,'Saida por venda','2025-09-24 03:40:44'),(33,47,'saida',1.000,'pedido',20,'Saida por venda','2025-09-24 15:29:50'),(34,35,'saida',12.000,'pedido',20,'Saida por venda','2025-09-24 15:29:50'),(35,7,'saida',15.000,'pedido',21,'Saida por venda','2025-09-24 22:47:04'),(36,1,'saida',15.000,'pedido',21,'Saida por venda','2025-09-24 22:47:04'),(37,23,'saida',45.000,'pedido',21,'Saida por venda','2025-09-24 22:47:04'),(38,46,'saida',1.000,'pedido',22,'Saida por venda','2025-09-25 17:47:10'),(39,40,'saida',1.000,'pedido',22,'Saida por venda','2025-09-25 17:47:10'),(40,42,'saida',1.000,'pedido',22,'Saida por venda','2025-09-25 17:47:10'),(41,30,'saida',1.000,'pedido',23,'Saida por venda','2025-09-29 19:24:16'),(42,1,'saida',1.000,'pedido',24,'Saida por venda','2025-09-29 19:25:04'),(43,2,'saida',1.000,'pedido',25,'Saida por venda','2025-09-29 20:15:42'),(44,1,'saida',2.000,'pedido',25,'Saida por venda','2025-09-29 20:15:42'),(45,11,'saida',6.000,'pedido',25,'Saida por venda','2025-09-29 20:15:42'),(46,29,'saida',1.000,'pedido',27,'Saida por venda','2025-09-30 01:01:36'),(47,47,'saida',1.000,'pedido',28,'Saida por venda','2025-09-30 16:21:57'),(48,35,'saida',6.000,'pedido',29,'Saida por venda','2025-09-30 16:23:03'),(49,47,'saida',1.000,'pedido',30,'Saida por venda','2025-09-30 16:23:45'),(50,46,'saida',1.000,'pedido',31,'Saida por venda','2025-09-30 16:24:04'),(51,30,'saida',10.000,'pedido',32,'Saida por venda','2025-09-30 16:25:53'),(52,26,'saida',1.000,'pedido',34,'Saida por venda','2025-09-30 14:38:19'),(53,34,'saida',6.000,'pedido',34,'Saida por venda','2025-09-30 14:38:19'),(54,35,'saida',2.000,'pedido',35,'Saida por venda','2025-09-30 14:38:38');
/*!40000 ALTER TABLE `mov_estoque` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `cliente_id` int NOT NULL,
  `token_hash` char(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` datetime NOT NULL,
  `used_at` datetime DEFAULT NULL,
  `ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token_hash` (`token_hash`),
  KEY `cliente_id` (`cliente_id`),
  CONSTRAINT `fk_prt_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `cliente` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
INSERT INTO `password_reset_tokens` VALUES (6,2,'f562b1206cbb67271da05e6265bb0cc2e4d55e58d57fe238267f46a36f1ace5c','2025-09-29 18:03:22','2025-09-29 17:03:51','100.64.0.3','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','2025-09-29 17:03:22');
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `pdv_caixa_resumo`
--

DROP TABLE IF EXISTS `pdv_caixa_resumo`;
/*!50001 DROP VIEW IF EXISTS `pdv_caixa_resumo`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `pdv_caixa_resumo` AS SELECT 
 1 AS `pedido_id`,
 1 AS `tipo`,
 1 AS `valor`,
 1 AS `mov_tipo`,
 1 AS `terminal_id`,
 1 AS `turno_id`,
 1 AS `troco`,
 1 AS `total`,
 1 AS `criado_em`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `pdv_evento`
--

DROP TABLE IF EXISTS `pdv_evento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pdv_evento` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `turno_id` bigint NOT NULL,
  `terminal_id` bigint NOT NULL,
  `operador_id` bigint NOT NULL,
  `tipo` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `criado_em` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `turno_id` (`turno_id`,`tipo`),
  CONSTRAINT `pdv_evento_chk_1` CHECK (json_valid(`payload`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pdv_evento`
--

LOCK TABLES `pdv_evento` WRITE;
/*!40000 ALTER TABLE `pdv_evento` DISABLE KEYS */;
/*!40000 ALTER TABLE `pdv_evento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pdv_fiscal_nfce`
--

DROP TABLE IF EXISTS `pdv_fiscal_nfce`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pdv_fiscal_nfce` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `pedido_id` int NOT NULL,
  `numero` int DEFAULT NULL,
  `serie` int DEFAULT NULL,
  `chave` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `protocolo` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `xml_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('autorizada','denegada','cancelada','em_processamento','erro') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'em_processamento',
  `criado_em` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_nfce_pedido` (`pedido_id`),
  CONSTRAINT `fk_nfce_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedido` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pdv_fiscal_nfce`
--

LOCK TABLES `pdv_fiscal_nfce` WRITE;
/*!40000 ALTER TABLE `pdv_fiscal_nfce` DISABLE KEYS */;
/*!40000 ALTER TABLE `pdv_fiscal_nfce` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pdv_pag_cartao`
--

DROP TABLE IF EXISTS `pdv_pag_cartao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pdv_pag_cartao` (
  `pagamento_id` bigint NOT NULL,
  `bandeira` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `parcelas` int DEFAULT NULL,
  `nsu` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `autorizacao` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `adquirente` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`pagamento_id`),
  CONSTRAINT `fk_pagcartao_pagamento` FOREIGN KEY (`pagamento_id`) REFERENCES `pedido_pagamento` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pdv_pag_cartao`
--

LOCK TABLES `pdv_pag_cartao` WRITE;
/*!40000 ALTER TABLE `pdv_pag_cartao` DISABLE KEYS */;
/*!40000 ALTER TABLE `pdv_pag_cartao` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pdv_pag_cheque`
--

DROP TABLE IF EXISTS `pdv_pag_cheque`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pdv_pag_cheque` (
  `pagamento_id` bigint NOT NULL,
  `banco_codigo` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `banco_nome` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `agencia` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `conta` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `numero_cheque` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `bom_para` date DEFAULT NULL,
  PRIMARY KEY (`pagamento_id`),
  CONSTRAINT `fk_pagcheque_pagamento` FOREIGN KEY (`pagamento_id`) REFERENCES `pedido_pagamento` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pdv_pag_cheque`
--

LOCK TABLES `pdv_pag_cheque` WRITE;
/*!40000 ALTER TABLE `pdv_pag_cheque` DISABLE KEYS */;
/*!40000 ALTER TABLE `pdv_pag_cheque` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pdv_pag_pix`
--

DROP TABLE IF EXISTS `pdv_pag_pix`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pdv_pag_pix` (
  `pagamento_id` bigint NOT NULL,
  `txid` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `qr_code` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `status` enum('pendente','pago','expirado','cancelado') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pendente',
  PRIMARY KEY (`pagamento_id`),
  CONSTRAINT `fk_pagpix_pagamento` FOREIGN KEY (`pagamento_id`) REFERENCES `pedido_pagamento` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pdv_pag_pix`
--

LOCK TABLES `pdv_pag_pix` WRITE;
/*!40000 ALTER TABLE `pdv_pag_pix` DISABLE KEYS */;
/*!40000 ALTER TABLE `pdv_pag_pix` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pdv_pedido_meta`
--

DROP TABLE IF EXISTS `pdv_pedido_meta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pdv_pedido_meta` (
  `pedido_id` int NOT NULL,
  `terminal_id` int NOT NULL,
  `turno_id` int NOT NULL,
  `operador_id` int NOT NULL,
  `cpf_na_nota` varchar(14) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `observacao` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `criado_em` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pdv_pedido_meta`
--

LOCK TABLES `pdv_pedido_meta` WRITE;
/*!40000 ALTER TABLE `pdv_pedido_meta` DISABLE KEYS */;
INSERT INTO `pdv_pedido_meta` VALUES (23,1,1,9,NULL,NULL,'2025-09-29 16:19:47'),(24,1,1,9,NULL,NULL,'2025-09-29 16:24:36'),(25,1,1,3,NULL,NULL,'2025-09-29 17:04:08'),(26,1,1,3,NULL,NULL,'2025-09-29 17:19:21'),(32,1,3,9,NULL,NULL,'2025-09-30 13:25:33'),(33,1,3,9,NULL,NULL,'2025-09-30 13:26:16'),(34,1,4,3,NULL,NULL,'2025-09-30 11:37:59');
/*!40000 ALTER TABLE `pdv_pedido_meta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pdv_tef_transacao`
--

DROP TABLE IF EXISTS `pdv_tef_transacao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pdv_tef_transacao` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `pagamento_id` bigint NOT NULL,
  `provedor` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nsu` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `codigo_host` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `comprovante` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `status` enum('aprovado','negado','cancelado','pendente') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `criado_em` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `pagamento_id` (`pagamento_id`),
  CONSTRAINT `fk_tef_pagamento` FOREIGN KEY (`pagamento_id`) REFERENCES `pedido_pagamento` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pdv_tef_transacao`
--

LOCK TABLES `pdv_tef_transacao` WRITE;
/*!40000 ALTER TABLE `pdv_tef_transacao` DISABLE KEYS */;
/*!40000 ALTER TABLE `pdv_tef_transacao` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pdv_terminal`
--

DROP TABLE IF EXISTS `pdv_terminal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pdv_terminal` (
  `id` int NOT NULL AUTO_INCREMENT,
  `loja_id` bigint NOT NULL DEFAULT '1',
  `nome` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `identificador` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `identificador` (`identificador`),
  CONSTRAINT `pdv_terminal_chk_1` CHECK (json_valid(`config`))
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pdv_terminal`
--

LOCK TABLES `pdv_terminal` WRITE;
/*!40000 ALTER TABLE `pdv_terminal` DISABLE KEYS */;
INSERT INTO `pdv_terminal` VALUES (1,1,'Caixa 01',NULL,1,NULL,'2025-09-29 16:06:22'),(2,1,'Caixa 01',NULL,1,NULL,'2025-09-29 18:23:34');
/*!40000 ALTER TABLE `pdv_terminal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pdv_turno`
--

DROP TABLE IF EXISTS `pdv_turno`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pdv_turno` (
  `id` int NOT NULL AUTO_INCREMENT,
  `terminal_id` int NOT NULL,
  `operador_id` int NOT NULL,
  `caixa_id` int DEFAULT NULL,
  `aberto_em` datetime NOT NULL,
  `valor_inicial` decimal(12,2) NOT NULL DEFAULT '0.00',
  `fechado_em` datetime DEFAULT NULL,
  `valor_fechamento` decimal(12,2) DEFAULT NULL,
  `status` enum('aberto','fechado','cancelado') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'aberto',
  PRIMARY KEY (`id`),
  KEY `terminal_id` (`terminal_id`,`status`),
  KEY `idx_turno_terminal` (`terminal_id`),
  KEY `idx_turno_operador` (`operador_id`),
  KEY `idx_turno_caixa` (`caixa_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pdv_turno`
--

LOCK TABLES `pdv_turno` WRITE;
/*!40000 ALTER TABLE `pdv_turno` DISABLE KEYS */;
INSERT INTO `pdv_turno` VALUES (1,1,9,6,'2025-09-29 16:19:09',0.00,'2025-09-29 18:23:04',461.94,'fechado'),(2,2,3,7,'2025-09-29 18:23:34',0.00,'2025-09-29 18:23:41',0.00,'fechado'),(3,1,9,8,'2025-09-30 13:25:21',0.00,'2025-09-30 11:37:16',100.00,'fechado'),(4,1,3,9,'2025-09-30 11:37:54',0.00,'2025-09-30 11:38:23',50.00,'fechado');
/*!40000 ALTER TABLE `pdv_turno` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `pdv_turno_aberto`
--

DROP TABLE IF EXISTS `pdv_turno_aberto`;
/*!50001 DROP VIEW IF EXISTS `pdv_turno_aberto`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `pdv_turno_aberto` AS SELECT 
 1 AS `id`,
 1 AS `terminal_id`,
 1 AS `operador_id`,
 1 AS `caixa_id`,
 1 AS `aberto_em`,
 1 AS `valor_inicial`,
 1 AS `fechado_em`,
 1 AS `valor_fechamento`,
 1 AS `status`,
 1 AS `nome`,
 1 AS `terminal`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `pdv_turnos_abertos`
--

DROP TABLE IF EXISTS `pdv_turnos_abertos`;
/*!50001 DROP VIEW IF EXISTS `pdv_turnos_abertos`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `pdv_turnos_abertos` AS SELECT 
 1 AS `id`,
 1 AS `terminal_id`,
 1 AS `operador_id`,
 1 AS `caixa_id`,
 1 AS `aberto_em`,
 1 AS `valor_inicial`,
 1 AS `fechado_em`,
 1 AS `valor_fechamento`,
 1 AS `status`,
 1 AS `nome`*/;
SET character_set_client = @saved_cs_client;

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
  `status` enum('novo','em_separacao','em_transporte','pronto','finalizado','cancelado') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'novo',
  `canal` enum('online','pdv') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'online',
  `entrega` enum('retirada','entrega') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pagamento` enum('na_entrega','pix','cartao','gateway') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'na_entrega',
  `subtotal` decimal(12,2) NOT NULL,
  `frete` decimal(12,2) NOT NULL DEFAULT '0.00',
  `desconto` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total` decimal(12,2) NOT NULL,
  `troco` decimal(12,2) NOT NULL DEFAULT '0.00',
  `codigo_externo` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `atualizado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_pedido_cliente` (`cliente_id`,`criado_em`),
  KEY `idx_pedido_status` (`status`),
  KEY `fk_pedido_endereco` (`endereco_id`),
  KEY `idx_pedido_status_data` (`status`,`criado_em`),
  CONSTRAINT `fk_pedido_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `cliente` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_pedido_endereco` FOREIGN KEY (`endereco_id`) REFERENCES `endereco` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Pedidos da loja (online/retirada)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedido`
--

LOCK TABLES `pedido` WRITE;
/*!40000 ALTER TABLE `pedido` DISABLE KEYS */;
INSERT INTO `pedido` VALUES (1,5,1,'cancelado','online','entrega','pix',120.00,10.00,0.00,130.00,0.00,'PED-0001','2025-09-13 05:38:18','2025-09-17 19:57:41'),(2,2,NULL,'cancelado','online','retirada','pix',123.99,0.00,0.00,123.99,0.00,NULL,'2025-09-17 02:33:56','2025-09-18 02:25:20'),(3,2,NULL,'pronto','online','retirada','na_entrega',50.00,0.00,0.00,50.00,0.00,NULL,'2025-09-17 02:35:06','2025-09-18 02:55:27'),(4,2,1,'em_transporte','online','entrega','na_entrega',120.00,0.00,0.00,120.00,0.00,NULL,'2025-09-17 02:45:10','2025-09-18 02:14:06'),(5,2,NULL,'cancelado','online','retirada','na_entrega',50.00,0.00,0.00,50.00,0.00,NULL,'2025-09-17 03:08:49','2025-09-18 02:26:41'),(6,5,NULL,'em_transporte','online','retirada','na_entrega',14.16,0.00,0.00,14.16,0.00,NULL,'2025-09-17 04:11:07','2025-09-17 19:57:30'),(7,2,1,'em_transporte','online','entrega','na_entrega',73.80,0.00,0.00,73.80,0.00,NULL,'2025-09-18 00:50:17','2025-09-18 00:50:55'),(8,2,3,'novo','online','entrega','gateway',67.81,0.00,0.00,67.81,0.00,NULL,'2025-09-18 02:00:22','2025-09-18 02:00:22'),(9,5,2,'novo','online','entrega','pix',69.70,0.00,0.00,69.70,0.00,NULL,'2025-09-18 16:30:16','2025-09-18 16:30:16'),(10,2,1,'novo','online','entrega','pix',19.90,0.00,0.00,19.90,0.00,NULL,'2025-09-19 04:13:33','2025-09-19 04:13:33'),(11,2,NULL,'pronto','online','retirada','na_entrega',150.75,0.00,0.00,150.75,0.00,NULL,'2025-09-21 19:12:57','2025-09-23 21:19:22'),(12,2,NULL,'pronto','online','retirada','na_entrega',72.13,0.00,0.00,72.13,0.00,NULL,'2025-09-21 19:14:28','2025-09-23 21:19:18'),(13,6,NULL,'pronto','online','retirada','pix',25.39,0.00,0.00,25.39,0.00,NULL,'2025-09-21 23:54:21','2025-09-23 21:19:11'),(14,2,NULL,'novo','online','retirada','pix',8.49,0.00,0.00,8.49,0.00,NULL,'2025-09-24 03:11:52','2025-09-24 03:11:52'),(15,2,NULL,'novo','online','retirada','pix',19.90,0.00,0.00,19.90,0.00,'PED-000015','2025-09-24 03:22:18','2025-09-24 03:22:18'),(16,2,NULL,'novo','online','retirada','gateway',19.90,0.00,0.00,19.90,0.00,NULL,'2025-09-24 03:22:40','2025-09-24 03:22:40'),(17,2,NULL,'novo','online','retirada','pix',19.90,0.00,0.00,19.90,0.00,'PED-000017','2025-09-24 03:22:52','2025-09-24 03:22:53'),(18,2,NULL,'novo','online','retirada','pix',19.90,0.00,0.00,19.90,0.00,'PED-000018','2025-09-24 03:24:33','2025-09-24 03:24:34'),(19,2,NULL,'em_transporte','online','retirada','pix',2.99,0.00,0.00,2.99,0.00,'PED-000019','2025-09-24 03:40:44','2025-09-24 15:28:51'),(20,2,NULL,'novo','online','retirada','pix',55.78,0.00,0.00,55.78,0.00,'PED-000020','2025-09-24 15:29:50','2025-09-24 15:29:50'),(21,2,NULL,'novo','online','retirada','pix',3264.00,0.00,0.00,3264.00,0.00,'PED-000021','2025-09-24 22:47:04','2025-09-24 22:47:04'),(22,7,NULL,'novo','online','retirada','pix',50.29,0.00,0.00,50.29,0.00,'PED-000022','2025-09-25 17:47:10','2025-09-25 17:47:10'),(23,1,NULL,'finalizado','pdv','retirada','na_entrega',9.90,0.00,0.00,9.90,0.10,NULL,'2025-09-29 19:19:47','2025-09-29 19:24:16'),(24,1,NULL,'finalizado','pdv','retirada','na_entrega',50.00,0.00,0.00,50.00,40.00,NULL,'2025-09-29 19:24:36','2025-09-29 19:25:04'),(25,1,NULL,'finalizado','pdv','retirada','na_entrega',161.94,0.00,0.00,161.94,200.00,NULL,'2025-09-29 20:04:08','2025-09-29 20:15:42'),(26,1,NULL,'novo','pdv','retirada','na_entrega',0.00,0.00,0.00,0.00,0.00,NULL,'2025-09-29 20:19:21','2025-09-29 20:19:21'),(27,2,NULL,'novo','online','retirada','pix',5.60,0.00,0.00,5.60,0.00,'PED-000027','2025-09-30 01:01:36','2025-09-30 01:01:36'),(28,2,1,'novo','online','entrega','pix',19.90,0.00,0.00,19.90,0.00,'PED-000028','2025-09-30 16:21:57','2025-09-30 16:21:57'),(29,2,NULL,'novo','online','retirada','na_entrega',17.94,0.00,0.00,17.94,0.00,NULL,'2025-09-30 16:23:03','2025-09-30 16:23:03'),(30,2,NULL,'novo','online','retirada','cartao',19.90,0.00,0.00,19.90,0.00,NULL,'2025-09-30 16:23:45','2025-09-30 16:23:45'),(31,2,NULL,'novo','online','retirada','gateway',16.90,0.00,0.00,16.90,0.00,NULL,'2025-09-30 16:24:04','2025-09-30 16:24:04'),(32,1,NULL,'finalizado','pdv','retirada','na_entrega',99.00,0.00,0.00,99.00,1.00,NULL,'2025-09-30 16:25:33','2025-09-30 16:25:53'),(33,1,NULL,'novo','pdv','retirada','na_entrega',0.00,0.00,0.00,0.00,0.00,NULL,'2025-09-30 16:26:16','2025-09-30 16:26:16'),(34,1,NULL,'finalizado','pdv','retirada','na_entrega',41.43,0.00,0.00,41.43,8.57,NULL,'2025-09-30 14:37:59','2025-09-30 14:38:19'),(35,2,NULL,'novo','online','retirada','na_entrega',5.98,0.00,0.00,5.98,0.00,NULL,'2025-09-30 14:38:38','2025-09-30 14:38:38');
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
-- Table structure for table `pedido_pagamento`
--

DROP TABLE IF EXISTS `pedido_pagamento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pedido_pagamento` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `pedido_id` int NOT NULL,
  `tipo` enum('dinheiro','credito','debito','cheque','pix') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `valor` decimal(12,2) NOT NULL,
  `criado_em` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `pedido_id` (`pedido_id`),
  KEY `idx_pedidopag_pedido_tipo` (`pedido_id`,`tipo`),
  CONSTRAINT `fk_pedidopag_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedido` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedido_pagamento`
--

LOCK TABLES `pedido_pagamento` WRITE;
/*!40000 ALTER TABLE `pedido_pagamento` DISABLE KEYS */;
INSERT INTO `pedido_pagamento` VALUES (2,23,'dinheiro',10.00,'2025-09-29 16:24:12'),(3,24,'pix',10.00,'2025-09-29 16:24:46'),(4,24,'debito',40.00,'2025-09-29 16:24:50'),(5,24,'debito',30.00,'2025-09-29 16:24:58'),(6,24,'dinheiro',10.00,'2025-09-29 16:25:02'),(7,25,'dinheiro',100.00,'2025-09-29 17:15:07'),(8,25,'credito',61.94,'2025-09-29 17:15:13'),(9,25,'credito',200.00,'2025-09-29 17:15:36'),(10,32,'dinheiro',100.00,'2025-09-30 13:25:50'),(11,34,'dinheiro',50.00,'2025-09-30 11:38:15');
/*!40000 ALTER TABLE `pedido_pagamento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `pedido_pdv`
--

DROP TABLE IF EXISTS `pedido_pdv`;
/*!50001 DROP VIEW IF EXISTS `pedido_pdv`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `pedido_pdv` AS SELECT 
 1 AS `id`,
 1 AS `cliente_id`,
 1 AS `endereco_id`,
 1 AS `status`,
 1 AS `canal`,
 1 AS `entrega`,
 1 AS `pagamento`,
 1 AS `subtotal`,
 1 AS `frete`,
 1 AS `desconto`,
 1 AS `total`,
 1 AS `troco`,
 1 AS `codigo_externo`,
 1 AS `criado_em`,
 1 AS `atualizado_em`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `pedido_pix`
--

DROP TABLE IF EXISTS `pedido_pix`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pedido_pix` (
  `pedido_id` int NOT NULL,
  `mp_payment_id` bigint NOT NULL,
  `status` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_detail` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qr_code` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `qr_code_base64` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ticket_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`pedido_id`),
  CONSTRAINT `fk_pedido_pix_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedido` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedido_pix`
--

LOCK TABLES `pedido_pix` WRITE;
/*!40000 ALTER TABLE `pedido_pix` DISABLE KEYS */;
INSERT INTO `pedido_pix` VALUES (19,1341310033,'pending','pending_waiting_transfer','00020126580014br.gov.bcb.pix0136b76aa9c2-2ec4-4110-954e-ebfe34f05b6152040000530398654042.995802BR5916DOYGlyn.ezwRlORO6006MaZWMH62230519mpqrinter13413100336304E31D','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','https://www.mercadopago.com.br/sandbox/payments/1341310033/ticket?caller_id=1406372264&hash=fe988d81-8d93-42df-9743-5f7dca3c6d50','2025-09-25 00:40:44','2025-09-24 03:40:45','2025-09-24 22:54:48'),(20,1324857326,'pending','pending_waiting_transfer','00020126580014br.gov.bcb.pix0136b76aa9c2-2ec4-4110-954e-ebfe34f05b61520400005303986540555.785802BR5916DOYGlyn.ezwRlORO6006MaZWMH62230519mpqrinter132485732663041314','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','https://www.mercadopago.com.br/sandbox/payments/1324857326/ticket?caller_id=1406372264&hash=9f71f255-1585-46a8-88d1-db722e8787aa','2025-09-25 12:29:50','2025-09-24 15:29:51','2025-09-24 21:36:17'),(21,1341340343,'pending','pending_waiting_transfer','00020126580014br.gov.bcb.pix0136b76aa9c2-2ec4-4110-954e-ebfe34f05b6152040000530398654073264.005802BR5916DOYGlyn.ezwRlORO6006MaZWMH62230519mpqrinter1341340343630427E0','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','https://www.mercadopago.com.br/sandbox/payments/1341340343/ticket?caller_id=1406372264&hash=1c23deee-8f08-4310-b3b4-72ad6bf81692','2025-09-25 19:47:04','2025-09-24 22:47:05','2025-09-29 21:22:53'),(22,1324870620,'pending','pending_waiting_transfer','00020126580014br.gov.bcb.pix0136b76aa9c2-2ec4-4110-954e-ebfe34f05b61520400005303986540550.295802BR5916DOYGlyn.ezwRlORO6006MaZWMH62230519mpqrinter13248706206304E45A','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','https://www.mercadopago.com.br/sandbox/payments/1324870620/ticket?caller_id=1596608240&hash=e0908b37-c9fe-4177-ae52-258962cbc13a','2025-09-26 14:47:10','2025-09-25 17:47:11','2025-09-25 17:48:11'),(27,1324909258,'pending','pending_waiting_transfer','00020126580014br.gov.bcb.pix0136b76aa9c2-2ec4-4110-954e-ebfe34f05b6152040000530398654045.605802BR5916DOYGlyn.ezwRlORO6006MaZWMH62230519mpqrinter13249092586304336D','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','https://www.mercadopago.com.br/sandbox/payments/1324909258/ticket?caller_id=1406372264&hash=ad997e5f-6126-4996-a43c-80fe2e68a3d2','2025-09-30 22:01:36','2025-09-30 01:01:36','2025-09-30 01:01:59'),(28,1341442415,'pending','pending_waiting_transfer','00020126580014br.gov.bcb.pix0136b76aa9c2-2ec4-4110-954e-ebfe34f05b61520400005303986540519.905802BR5916DOYGlyn.ezwRlORO6006MaZWMH62230519mpqrinter13414424156304C06A','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','https://www.mercadopago.com.br/sandbox/payments/1341442415/ticket?caller_id=1406372264&hash=dfed1cfe-b233-4fee-acb4-5c0ef9c236fc','2025-10-01 13:21:57','2025-09-30 16:21:58','2025-09-30 16:22:18');
/*!40000 ALTER TABLE `pedido_pix` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Histórico de preços por produto';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `preco`
--

LOCK TABLES `preco` WRITE;
/*!40000 ALTER TABLE `preco` DISABLE KEYS */;
INSERT INTO `preco` VALUES (1,1,50.00,NULL,NULL,NULL,'2025-09-13 06:27:42'),(2,2,20.00,NULL,NULL,NULL,'2025-09-13 06:27:42'),(3,3,5.99,3.99,NULL,NULL,'2025-09-17 02:32:39'),(4,4,4.99,NULL,NULL,NULL,'2025-09-17 21:30:33'),(5,5,24.90,NULL,NULL,NULL,'2025-09-17 21:30:33'),(6,6,18.50,NULL,NULL,NULL,'2025-09-17 21:30:33'),(7,7,32.90,29.90,'2025-09-17 15:30:33','2025-09-24 15:30:33','2025-09-17 21:30:33'),(8,8,17.99,NULL,NULL,NULL,'2025-09-17 21:30:33'),(9,9,9.49,NULL,NULL,NULL,'2025-09-17 21:30:33'),(10,10,22.50,NULL,NULL,NULL,'2025-09-17 21:30:33'),(11,11,6.99,NULL,NULL,NULL,'2025-09-17 21:36:25'),(12,12,19.90,NULL,NULL,NULL,'2025-09-17 21:36:25'),(13,13,2.89,NULL,NULL,NULL,'2025-09-17 21:36:25'),(14,14,8.49,NULL,NULL,NULL,'2025-09-17 21:36:25'),(15,15,16.90,NULL,NULL,NULL,'2025-09-17 21:36:25'),(16,16,11.90,NULL,NULL,NULL,'2025-09-17 21:36:25'),(17,17,9.50,NULL,NULL,NULL,'2025-09-17 21:36:25'),(18,18,14.90,NULL,NULL,NULL,'2025-09-17 21:36:25'),(19,19,21.90,NULL,NULL,NULL,'2025-09-17 21:36:25'),(20,20,29.90,NULL,NULL,NULL,'2025-09-17 21:36:25'),(21,21,19.90,NULL,NULL,NULL,'2025-09-17 21:36:25'),(22,22,13.90,NULL,NULL,NULL,'2025-09-17 21:36:25'),(23,23,44.90,NULL,NULL,NULL,'2025-09-17 21:36:25'),(24,24,6.50,NULL,NULL,NULL,'2025-09-17 21:36:25'),(25,25,27.90,NULL,NULL,NULL,'2025-09-17 21:36:25'),(26,26,5.49,NULL,NULL,NULL,'2025-09-17 21:36:25'),(27,27,7.99,NULL,NULL,NULL,'2025-09-17 21:36:25'),(28,28,6.90,NULL,NULL,NULL,'2025-09-17 21:36:25'),(29,29,5.60,NULL,NULL,NULL,'2025-09-17 21:36:25'),(30,30,9.90,NULL,NULL,NULL,'2025-09-17 21:36:25'),(31,31,32.90,NULL,NULL,NULL,'2025-09-17 21:36:25'),(32,32,3.99,NULL,NULL,NULL,'2025-09-17 21:36:25'),(33,33,8.99,NULL,NULL,NULL,'2025-09-17 21:36:25'),(34,34,5.99,NULL,NULL,NULL,'2025-09-17 21:36:25'),(35,35,2.99,NULL,NULL,NULL,'2025-09-17 21:36:25'),(36,36,18.90,NULL,NULL,NULL,'2025-09-17 21:36:25'),(37,37,7.80,NULL,NULL,NULL,'2025-09-17 21:36:25'),(38,38,9.50,NULL,NULL,NULL,'2025-09-17 21:36:25'),(39,39,2.99,NULL,NULL,NULL,'2025-09-17 21:36:25'),(40,40,27.90,NULL,NULL,NULL,'2025-09-17 21:36:25'),(41,41,19.90,NULL,NULL,NULL,'2025-09-17 21:36:25'),(42,42,5.49,NULL,NULL,NULL,'2025-09-17 21:36:25'),(43,43,24.90,NULL,NULL,NULL,'2025-09-17 21:37:33'),(44,44,8.49,NULL,NULL,NULL,'2025-09-17 21:38:08'),(45,45,18.90,NULL,NULL,NULL,'2025-09-17 21:38:08'),(46,46,16.90,NULL,NULL,NULL,'2025-09-17 21:38:08'),(47,47,19.90,NULL,NULL,NULL,'2025-09-17 21:38:08');
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
  `nome` varchar(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ean` varchar(14) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `categoria_id` int NOT NULL,
  `marca_id` int DEFAULT NULL,
  `unidade_id` int NOT NULL,
  `descricao` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `imagem` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Produtos do catálogo';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produto`
--

LOCK TABLES `produto` WRITE;
/*!40000 ALTER TABLE `produto` DISABLE KEYS */;
INSERT INTO `produto` VALUES (1,'Arroz Tio João 5kg Tipo 1','ARROZ5','789000000001',1,2,1,'Pacote 5kg','uploads/produtos/19bba88ad394ab08-1758144441.jpg',1,0,'2025-09-13 06:26:58'),(2,'Feijão Carioca 1kg','FEIJAO1','789000000002',1,1,1,'Pacote 1kg','uploads/produtos/4a708d9feef7daea-1758144868.jpg',1,0,'2025-09-13 06:26:58'),(3,'Banana Prata','bananaprta',NULL,1,1,2,'banana prata','uploads/produtos/f0a37618390f6d0e-1758144487.jpg',1,0,'2025-09-17 02:32:39'),(4,'Leite Integral Aurora 1L','LEITEAUR1L','7891150043210',5,3,4,'Leite UHT integral catarinense.','uploads/produtos/73bf2bce587591d5-1758145111.jpg',1,0,'2025-09-17 21:30:33'),(5,'Queijo Minas Frescal 500g','QUEIJOMIN500','7892222000456',5,9,3,'Queijo minas frescal tradicional.','uploads/produtos/7bcd1cffea6f2b89-1758145424.jpg',1,0,'2025-09-17 21:30:33'),(6,'Pao de Queijo Forno de Minas 400g','PAOQFMIN400','7896004001234',6,5,3,'Pao de queijo congelado pronto para assar.','uploads/produtos/0ddb113ff8420180-1758145249.jpg',1,0,'2025-09-17 21:30:33'),(7,'Coxinha Congelada Perdigao 1kg','COXPERD1KG','7892300005678',6,4,2,'Coxinha de frango congelada, pacote 1kg.','uploads/produtos/23ee276665706384-1758144733.jpg',1,0,'2025-09-17 21:30:33'),(8,'Cafe Pilao Tradicional 500g','CAFEPIL500','7894900012346',3,6,3,'Cafe torrado e moido Pilao tradicional.','uploads/produtos/2245b3868c65dcdb-1758144622.jpg',1,0,'2025-09-17 21:30:33'),(9,'Suco de Laranja Do Bem 1L','SUCLDMB1L','7891991009871',4,7,4,'Suco integral de laranja, sem acucar.','uploads/produtos/be237b0ce735aead-1758145631.jpg',1,0,'2025-09-17 21:30:33'),(10,'Erva Mate Barrao 1kg','ERVABARR1K','7897151700150',4,8,2,'Erva mate para chimarrao, moagem grossa.','uploads/produtos/415305279c25b91c-1758144807.jpg',1,0,'2025-09-17 21:30:33'),(11,'Leite Condensado Mooca 395g','LEITECOND395','7891000054321',1,17,3,'Leite condensado tradicional para sobremesas.','uploads/produtos/5f02ceaf8e012dce-1758145062.jpg',1,0,'2025-09-17 21:36:25'),(12,'Manteiga Aviacao 200g','MANTAVI200','7896034501234',5,18,3,'Manteiga extra com sal, pote 200g.','uploads/produtos/d77a74fe15e26e60-1758145198.jpg',1,0,'2025-09-17 21:36:25'),(13,'Iogurte Natural Nestle 170g','IOGUNEST170','7891000256780',5,17,3,'Iogurte natural integral copo 170g.','uploads/produtos/fb375537b706afbf-1758144960.jpg',1,0,'2025-09-17 21:36:25'),(14,'Requeijao Cremoso Tirolez 200g','REQTIROL200','7896036001236',5,40,3,'Requeijao cremoso tradicional.','uploads/produtos/f1de563137b70226-1758145538.jpg',1,0,'2025-09-17 21:36:25'),(15,'Pao Frances kg','PAOFRANCESKG',NULL,3,41,2,'Pao frances assado diariamente.','uploads/produtos/9e2ff9ad86c15929-1758145278.jpg',1,1,'2025-09-17 21:36:25'),(16,'Pao Integral Wickbold 500g','PAOWICK500','7896004005677',3,41,3,'Pao integral fatiado com graos.','uploads/produtos/5c5119a1626f3775-1758145315.jpg',1,0,'2025-09-17 21:36:25'),(17,'Bisnaguinha Pullman 300g','BISNPULL300','7891910007654',3,39,3,'Pao tipo bisnaguinha fofinho.','uploads/produtos/7ffb5e10610578df-1758144540.jpg',1,0,'2025-09-17 21:36:25'),(18,'Bolo de Milho Congelado Seara 400g','BOLOSMIL400','7894904003456',6,19,3,'Bolo de milho congelado pronto.','uploads/produtos/f29f2dbd589c654d-1758144598.jpg',1,0,'2025-09-17 21:36:25'),(19,'Lasanha Sadia Bolonhesa 600g','LASASAD600','7891810009870',6,20,3,'Lasanha congelada sabor bolonhesa.','uploads/produtos/64043b572cf427fa-1758145029.jpg',1,0,'2025-09-17 21:36:25'),(20,'Batata Palito McCain 2kg','BATAMCC2KG','7894904500123',6,21,2,'Batata palito congelada embalagens 2kg.','uploads/produtos/f9776ff2932f154a-1758144515.jpg',1,0,'2025-09-17 21:36:25'),(21,'Pizza Calabresa Perdigao 460g','PIZZAPER460','7892300056784',6,4,3,'Pizza congelada sabor calabresa.','uploads/produtos/2f8c5b6a7af26ea7-1758145381.jpg',1,0,'2025-09-17 21:36:25'),(22,'Frango Inteiro Congelado Seara 2kg','FRANGSEAR2K','7894900303256',8,19,2,'Frango inteiro congelado.','uploads/produtos/31b37116840c5140-1758144924.jpg',1,0,'2025-09-17 21:36:25'),(23,'Contra File Bovino kg','CONTRAFILKG',NULL,8,20,2,'Corte bovino contra file fresco.','uploads/produtos/22b3a7446fafd1ed-1758144704.jpg',1,1,'2025-09-17 21:36:25'),(24,'Mortadela Seara Fatiada 200g','MORTSEAR200','7894900001235',3,19,3,'Mortadela fatiada classica 200g.','uploads/produtos/c68b8ad928fc48a9-1758145221.jpg',1,0,'2025-09-17 21:36:25'),(25,'Cafe Soluvel Nescafe 200g','CAFESOL200','7891000103567',1,17,3,'Cafe soluvel tradicional 200g.','uploads/produtos/b9eabed72a0ed825-1758144647.jpg',1,0,'2025-09-17 21:36:25'),(26,'Acucar Refinado Uniao 1kg','ACUCUNIA1K','7891021000017',1,24,1,'Acucar refinado cristal fino 1kg.','uploads/produtos/1cbb6b33fa6d758f-1758144171.jpg',1,0,'2025-09-17 21:36:25'),(27,'Feijao Preto Sao Joao 1kg','FEIJSAO1KG','7896028701234',1,8,2,'Feijao preto tipo 1.','uploads/produtos/a07c720ba554409f-1758144903.jpg',1,0,'2025-09-17 21:36:25'),(28,'Farinha de Trigo Renata 1kg','FARINREN1K','7896102502345',1,23,2,'Farinha de trigo especial.','uploads/produtos/171a00a459bce0d6-1758144835.jpg',1,0,'2025-09-17 21:36:25'),(29,'Macarrao Espaguete Galo 500g','MACGAL500','7891234009876',1,22,3,'Macarrao espaguete n10.','uploads/produtos/0db02103febae097-1758145164.jpg',1,0,'2025-09-17 21:36:25'),(30,'Arroz Integral Tio Joao 1kg','ARROZINT1K','7896079901233',1,8,2,'Arroz integral grao longo.','uploads/produtos/43cd816d30f8a223-1758144389.jpg',1,0,'2025-09-17 21:36:25'),(31,'Azeite Extra Virgem Gallo 500ml','AZEIGAL500','7891107004567',1,25,5,'Azeite portugues extra virgem 0.5L.','uploads/produtos/750e131763fc9979-1758144467.jpg',1,0,'2025-09-17 21:36:25'),(32,'Vinagre de Alcool Castelo 750ml','VINACAST750','7891040003456',1,26,5,'Vinagre de alcool culinario.','uploads/produtos/290b4c439da511f5-1758145730.jpg',1,0,'2025-09-17 21:36:25'),(33,'Refrigerante Guarana Antarctica 2L','REFRGAU2L','7891991012345',4,27,4,'Refrigerante guarana garrafa 2 litros.','uploads/produtos/a43734bcee9d7119-1758145504.jpg',1,0,'2025-09-17 21:36:25'),(34,'Cerveja Heineken Long Neck 330ml','CERVHEI330','7894321654321',4,28,5,'Cerveja premium long neck 330ml.','uploads/produtos/6f5fb3cd68805d63-1758144677.jpg',1,0,'2025-09-17 21:36:25'),(35,'Agua Mineral Crystal 1.5L','AGCRYS15L','7894900223456',4,29,4,'Agua mineral sem gas 1.5L.','uploads/produtos/618fcdb05930e5da-1758144218.jpg',1,0,'2025-09-17 21:36:25'),(36,'Suco de Uva Aurora Integral 1.5L','SUCUAUR15L','7891149101234',4,3,4,'Suco de uva integral 1.5L.','uploads/produtos/374bf8811bebfb52-1758145671.jpg',1,0,'2025-09-17 21:36:25'),(37,'Maca Gala kg','MACAGALAKG',NULL,7,3,2,'Maca gala selecionada.','uploads/produtos/539441599911698d-1758145137.jpg',1,1,'2025-09-17 21:36:25'),(38,'Tomate Italiano kg','TOMATITALKG',NULL,7,3,2,'Tomate italiano fresco.','uploads/produtos/4ffaeaa623b81c0e-1758145704.jpg',1,1,'2025-09-17 21:36:25'),(39,'Alface Crespa unidade','ALFACECRESP',NULL,7,3,1,'Alface crespa colhida no dia.','uploads/produtos/555e5020b133c69c-1758144307.jpg',1,0,'2025-09-17 21:36:25'),(40,'Sabao em Po OMO Lavagem Perfeita 1.6kg','SABOMO16KG','7891150067890',9,33,1,'Sabao em po lavagem perfeita 1.6kg.','uploads/produtos/0833a3ad4ef5bd7c-1758145567.jpg',1,0,'2025-09-17 21:36:25'),(41,'Amaciante Comfort Concentrado 2L','AMACCOMF2L','7891021006543',9,31,4,'Amaciante concentrado fragrancia original.','uploads/produtos/27dce4b8e089bb38-1758144361.jpg',1,0,'2025-09-17 21:36:25'),(42,'Desinfetante Veja Multiuso 500ml','DESINFVEJA500','7891035009876',9,32,5,'Desinfetante multiuso perfumado.','uploads/produtos/99e4e24d1ae75b2d-1758144781.jpg',1,0,'2025-09-17 21:36:25'),(43,'Papel Higienico Neve Folha Dupla 12x30m','PAPNEVE12','7896079904567',10,34,6,'Papel higienico folha dupla pacote 12 rolos.','uploads/produtos/1e3ca05300b80d91-1758145347.jpg',1,0,'2025-09-17 21:37:33'),(44,'Creme Dental Colgate Total 90g','CREMCOLG90','7891000098765',10,35,3,'Creme dental protecao total 12h.','uploads/produtos/d3d437910e1596f5-1758144757.jpg',1,0,'2025-09-17 21:38:08'),(45,'Shampoo Pantene Liso Extremo 400ml','SHAMPANT400','7891021007890',10,36,5,'Shampoo pantene liso extremo 400ml.','uploads/produtos/282e2010f944bc91-1758145596.jpg',1,0,'2025-09-17 21:38:08'),(46,'Racao Dog Chow Adulto 1kg','RACDOGCH1K','7896044023456',11,37,6,'Racao seca premium para cães adultos.','uploads/produtos/50514bce6ca260ff-1758145454.jpg',1,0,'2025-09-17 21:38:08'),(47,'Racao Whiskas Carne 1kg','RACWHISK1K','7896021109876',11,38,12,'Racao seca para gatos sabor carne.','uploads/produtos/e2ce2c04b5eaae15-1758145479.jpg',1,0,'2025-09-17 21:38:08');
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
  `sigla` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_unidade_sigla` (`sigla`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Unidades de medida';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unidade`
--

LOCK TABLES `unidade` WRITE;
/*!40000 ALTER TABLE `unidade` DISABLE KEYS */;
INSERT INTO `unidade` VALUES (1,'UN','Unidade'),(2,'KG','Quilo'),(3,'G','Gramas'),(4,'L','Litro'),(5,'ML','Mililitro'),(6,'PCT','Pacote'),(7,'CX','Caixa'),(12,'PC','Peca'),(13,'BL','Blister');
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
  `nome` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `senha_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `perfil` enum('admin','gerente','operador','cliente') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cliente',
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `criado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_usuario_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Usuários do sistema (admin/gerente/operador/cliente)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario`
--

LOCK TABLES `usuario` WRITE;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
INSERT INTO `usuario` VALUES (3,'Douglas Marcelo Monquero','douglas.monquero@gmail.com','$2y$10$Pj7ioPNBs8rTdZY2IWNJROojOfEqJDiNsFOxifCcttLtN.7x0bBai','admin',1,'2025-09-11 01:45:37'),(7,'Elton','sh.elton@hotmail.com','$2y$10$oOB.PQAlHGpqtQdp5N8kPevrSP9XjvcBPuomM3gE.WR00ecsd4HsK','cliente',1,'2025-09-24 21:55:49'),(8,'Ricardo Menile','ricardomenile@gmail.com','$2y$10$YDPBizhI8X5kC0jfusTb1.7UzqgJwXdX19YvoO3aJdKajhKMcPHXS','cliente',1,'2025-09-25 17:42:22'),(9,'Operador Teste','operador@email.com','$2y$10$Pj7ioPNBs8rTdZY2IWNJROojOfEqJDiNsFOxifCcttLtN.7x0bBai','operador',1,'2025-09-29 18:48:44');
/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Final view structure for view `pdv_caixa_resumo`
--

/*!50001 DROP VIEW IF EXISTS `pdv_caixa_resumo`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`dba`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `pdv_caixa_resumo` AS select `p`.`id` AS `pedido_id`,`pm`.`tipo` AS `tipo`,`pm`.`valor` AS `valor`,`mca`.`tipo` AS `mov_tipo`,`meta`.`terminal_id` AS `terminal_id`,`meta`.`turno_id` AS `turno_id`,`p`.`troco` AS `troco`,`p`.`total` AS `total`,`p`.`criado_em` AS `criado_em` from (((`pedido` `p` join `pdv_pedido_meta` `meta` on((`meta`.`pedido_id` = `p`.`id`))) left join `pedido_pagamento` `pm` on((`pm`.`pedido_id` = `p`.`id`))) left join `mov_caixa` `mca` on((`mca`.`pedido_id` = `p`.`id`))) where (`p`.`canal` = 'pdv') */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `pdv_turno_aberto`
--

/*!50001 DROP VIEW IF EXISTS `pdv_turno_aberto`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`dba`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `pdv_turno_aberto` AS select `pta`.`id` AS `id`,`pta`.`terminal_id` AS `terminal_id`,`pta`.`operador_id` AS `operador_id`,`pta`.`caixa_id` AS `caixa_id`,`pta`.`aberto_em` AS `aberto_em`,`pta`.`valor_inicial` AS `valor_inicial`,`pta`.`fechado_em` AS `fechado_em`,`pta`.`valor_fechamento` AS `valor_fechamento`,`pta`.`status` AS `status`,`pta`.`nome` AS `nome`,`pta`.`nome` AS `terminal` from `pdv_turnos_abertos` `pta` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `pdv_turnos_abertos`
--

/*!50001 DROP VIEW IF EXISTS `pdv_turnos_abertos`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`dba`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `pdv_turnos_abertos` AS select `t`.`id` AS `id`,`t`.`terminal_id` AS `terminal_id`,`t`.`operador_id` AS `operador_id`,`t`.`caixa_id` AS `caixa_id`,`t`.`aberto_em` AS `aberto_em`,`t`.`valor_inicial` AS `valor_inicial`,`t`.`fechado_em` AS `fechado_em`,`t`.`valor_fechamento` AS `valor_fechamento`,`t`.`status` AS `status`,`term`.`nome` AS `nome` from (`pdv_turno` `t` join `pdv_terminal` `term` on((`term`.`id` = `t`.`terminal_id`))) where (`t`.`status` = 'aberto') */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `pedido_pdv`
--

/*!50001 DROP VIEW IF EXISTS `pedido_pdv`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`dba`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `pedido_pdv` AS select `p`.`id` AS `id`,`p`.`cliente_id` AS `cliente_id`,`p`.`endereco_id` AS `endereco_id`,`p`.`status` AS `status`,`p`.`canal` AS `canal`,`p`.`entrega` AS `entrega`,`p`.`pagamento` AS `pagamento`,`p`.`subtotal` AS `subtotal`,`p`.`frete` AS `frete`,`p`.`desconto` AS `desconto`,`p`.`total` AS `total`,`p`.`troco` AS `troco`,`p`.`codigo_externo` AS `codigo_externo`,`p`.`criado_em` AS `criado_em`,`p`.`atualizado_em` AS `atualizado_em` from `pedido` `p` where (`p`.`canal` = 'pdv') */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-09-30 12:23:04
