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
  `cpf` varchar(14) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nascimento` date DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cliente_cpf` (`cpf`),
  UNIQUE KEY `uq_cliente_usuario` (`usuario_id`),
  CONSTRAINT `fk_cliente_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Cadastro de clientes (pode vincular a um usuário)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cliente`
--

LOCK TABLES `cliente` WRITE;
/*!40000 ALTER TABLE `cliente` DISABLE KEYS */;
INSERT INTO `cliente` VALUES (1,2,NULL,NULL,NULL,'2025-09-13 00:41:59'),(2,3,'02168710937','(44) 99901-3434','1976-02-21','2025-09-13 00:41:59'),(3,5,NULL,NULL,NULL,'2025-09-13 00:41:59'),(4,1,NULL,NULL,NULL,'2025-09-13 00:41:59'),(5,4,NULL,'(44) 99999-1234',NULL,'2025-09-13 00:41:59'),(6,6,NULL,NULL,NULL,'2025-09-21 16:38:58');
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
INSERT INTO `cliente_favorito` VALUES (2,35,'2025-09-23 23:43:06'),(6,37,'2025-09-21 18:22:55');
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
-- Table structure for table `contato_mensagens`
--

DROP TABLE IF EXISTS `contato_mensagens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contato_mensagens` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mensagem` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `resposta` text COLLATE utf8mb4_unicode_ci,
  `status` enum('aberta','respondida','arquivada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aberta',
  `ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `criada_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `respondida_em` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contato_mensagens`
--

LOCK TABLES `contato_mensagens` WRITE;
/*!40000 ALTER TABLE `contato_mensagens` DISABLE KEYS */;
INSERT INTO `contato_mensagens` VALUES (1,'Douglas','douglas@email.com','teste agora','estamos em teste','respondida','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:142.0) Gecko/20100101 Firefox/142.0','2025-09-17 00:37:03','2025-09-17 02:27:39'),(2,'Douglas Marcelo Monquero','douglas@email.com','teste de msg dia 17','resposta respondida','arquivada','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:142.0) Gecko/20100101 Firefox/142.0','2025-09-17 15:56:38',NULL),(3,'Valdir Mendonça','valdir@email.com','Teste de envio 17 as 18horas','teste ok','respondida','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:142.0) Gecko/20100101 Firefox/142.0','2025-09-17 21:21:13','2025-09-17 21:51:22');
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Endereços de clientes (um pode ser principal)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `endereco`
--

LOCK TABLES `endereco` WRITE;
/*!40000 ALTER TABLE `endereco` DISABLE KEYS */;
INSERT INTO `endereco` VALUES (1,2,'Casa','Douglas','87060-110','Rua dos Ipes','312','Casa','Borba Gato','Maringá','PR',1,'2025-09-13 00:44:35'),(2,5,'Apartamento','Patricia Alves de Oliveira','87010-255','Rua Tanaka','50','bloco 3 apto 21','Vila Emilia','Maringá','PR',1,'2025-09-13 00:46:52'),(3,2,'Estudo','Douglas Marcelo Monquero','87010-100','Avenida Colombo','100','Senac','Zona 07','Maringá','PR',0,'2025-09-13 01:16:27'),(4,2,'Trabalho','Douglas','87010-100','Rua das Estrelas','1000','Sala 01','Centro','Maringá','PR',0,'2025-09-13 01:37:52'),(5,6,'Casa','Henrique','87010-010','Rua das Cores','333','Casa','Jd Floriano','Floriano','PR',1,'2025-09-21 16:40:34'),(6,3,'Casa','Lucas','87060-110','Avenida Paranavai','312','Casa','Zona 06','Maringá','PR',1,'2025-09-21 18:35:09');
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
INSERT INTO `estoque` VALUES (1,1,43.000,5.000,'2025-09-21 19:27:50'),(2,2,48.000,5.000,'2025-09-16 23:45:10'),(3,3,95.450,10.000,'2025-09-17 01:11:07'),(4,4,95.000,12.000,'2025-09-17 18:30:33'),(5,5,30.000,5.000,'2025-09-17 18:30:33'),(6,6,58.000,10.000,'2025-09-17 21:50:17'),(7,7,40.000,8.000,'2025-09-17 18:30:33'),(8,8,80.000,10.000,'2025-09-17 18:30:33'),(9,9,70.000,10.000,'2025-09-17 18:30:33'),(10,10,55.000,8.000,'2025-09-17 18:30:33'),(11,11,120.000,15.000,'2025-09-17 18:36:25'),(12,12,45.000,8.000,'2025-09-17 18:36:25'),(13,13,150.000,20.000,'2025-09-17 18:36:25'),(14,14,65.000,10.000,'2025-09-17 18:36:25'),(15,15,120.000,25.000,'2025-09-17 18:36:25'),(16,16,80.000,12.000,'2025-09-17 18:36:25'),(17,17,70.000,10.000,'2025-09-17 18:36:25'),(18,18,40.000,6.000,'2025-09-17 18:36:25'),(19,19,55.000,10.000,'2025-09-17 18:36:25'),(20,20,45.000,8.000,'2025-09-17 18:36:25'),(21,21,60.000,10.000,'2025-09-17 18:36:25'),(22,22,80.000,15.000,'2025-09-17 18:36:25'),(23,23,65.000,12.000,'2025-09-17 18:36:25'),(24,24,70.000,10.000,'2025-09-17 18:36:25'),(25,25,50.000,8.000,'2025-09-17 18:36:25'),(26,26,150.000,20.000,'2025-09-17 18:36:25'),(27,27,120.000,18.000,'2025-09-17 18:36:25'),(28,28,110.000,15.000,'2025-09-17 18:36:25'),(29,29,130.000,20.000,'2025-09-17 18:36:25'),(30,30,115.000,18.000,'2025-09-17 18:36:25'),(31,31,70.000,10.000,'2025-09-17 18:36:25'),(32,32,90.000,15.000,'2025-09-17 18:36:25'),(33,33,140.000,25.000,'2025-09-17 18:36:25'),(34,34,200.000,30.000,'2025-09-17 18:36:25'),(35,35,174.000,25.000,'2025-09-24 01:11:49'),(36,36,60.000,8.000,'2025-09-17 18:36:25'),(37,37,88.500,20.000,'2025-09-21 17:59:49'),(38,38,84.750,18.000,'2025-09-21 17:59:49'),(39,39,117.000,25.000,'2025-09-17 23:00:22'),(40,40,60.000,10.000,'2025-09-17 18:36:25'),(41,41,70.000,12.000,'2025-09-17 18:36:25'),(42,42,104.000,15.000,'2025-09-21 18:18:57'),(43,43,78.000,12.000,'2025-09-18 13:30:16'),(44,44,115.000,20.000,'2025-09-21 18:35:24'),(45,45,68.000,10.000,'2025-09-23 22:41:48'),(46,46,86.000,12.000,'2025-09-23 22:51:27'),(47,47,76.000,12.000,'2025-09-24 01:16:08');
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
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Itens de pedido';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item_pedido`
--

LOCK TABLES `item_pedido` WRITE;
/*!40000 ALTER TABLE `item_pedido` DISABLE KEYS */;
INSERT INTO `item_pedido` VALUES (3,1,1,2.000,NULL,50.00,0.00),(4,1,2,1.000,NULL,20.00,0.00),(5,2,1,2.000,NULL,50.00,NULL),(6,2,2,1.000,NULL,20.00,NULL),(7,2,3,1.000,NULL,3.99,NULL),(8,3,1,1.000,NULL,50.00,NULL),(9,4,1,2.000,NULL,50.00,NULL),(10,4,2,1.000,NULL,20.00,NULL),(11,5,1,1.000,NULL,50.00,NULL),(12,6,3,3.550,NULL,3.99,NULL),(13,7,47,1.000,NULL,19.90,NULL),(14,7,46,1.000,NULL,16.90,NULL),(15,7,6,2.000,NULL,18.50,NULL),(16,8,39,3.000,NULL,2.99,NULL),(17,8,46,1.000,NULL,16.90,NULL),(18,8,44,3.000,NULL,8.49,NULL),(19,8,42,3.000,NULL,5.49,NULL),(20,9,47,1.000,NULL,19.90,NULL),(21,9,43,2.000,NULL,24.90,NULL),(22,10,47,1.000,NULL,19.90,NULL),(23,11,38,0.250,NULL,9.50,NULL),(24,11,37,1.500,NULL,7.80,NULL),(25,12,45,2.000,NULL,18.90,NULL),(26,12,42,3.000,NULL,5.49,NULL),(27,13,44,2.000,NULL,8.49,NULL),(28,13,45,2.000,NULL,18.90,NULL),(29,14,1,1.000,NULL,55.00,NULL),(30,15,45,1.000,NULL,18.90,NULL),(31,16,46,1.000,NULL,16.90,NULL),(32,17,45,1.000,NULL,18.90,NULL),(33,18,45,1.000,NULL,18.90,NULL),(34,19,46,1.000,NULL,16.90,NULL),(35,20,35,1.000,NULL,2.99,NULL),(36,21,35,2.000,NULL,2.99,NULL),(37,22,35,1.000,NULL,2.99,NULL),(38,23,35,1.000,NULL,2.99,NULL),(39,24,35,1.000,NULL,2.99,NULL),(40,25,47,1.000,NULL,19.90,NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Movimentações de estoque';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mov_estoque`
--

LOCK TABLES `mov_estoque` WRITE;
/*!40000 ALTER TABLE `mov_estoque` DISABLE KEYS */;
INSERT INTO `mov_estoque` VALUES (1,1,'saida',2.000,'pedido',2,'Saida por venda','2025-09-16 23:33:56'),(2,2,'saida',1.000,'pedido',2,'Saida por venda','2025-09-16 23:33:56'),(3,3,'saida',1.000,'pedido',2,'Saida por venda','2025-09-16 23:33:56'),(4,1,'saida',1.000,'pedido',3,'Saida por venda','2025-09-16 23:35:06'),(5,1,'saida',2.000,'pedido',4,'Saida por venda','2025-09-16 23:45:10'),(6,2,'saida',1.000,'pedido',4,'Saida por venda','2025-09-16 23:45:10'),(7,1,'saida',1.000,'pedido',5,'Saida por venda','2025-09-17 00:08:49'),(8,3,'saida',3.550,'pedido',6,'Saida por venda','2025-09-17 01:11:07'),(9,47,'saida',1.000,'pedido',7,'Saida por venda','2025-09-17 21:50:17'),(10,46,'saida',1.000,'pedido',7,'Saida por venda','2025-09-17 21:50:17'),(11,6,'saida',2.000,'pedido',7,'Saida por venda','2025-09-17 21:50:17'),(12,39,'saida',3.000,'pedido',8,'Saida por venda','2025-09-17 23:00:22'),(13,46,'saida',1.000,'pedido',8,'Saida por venda','2025-09-17 23:00:22'),(14,44,'saida',3.000,'pedido',8,'Saida por venda','2025-09-17 23:00:22'),(15,42,'saida',3.000,'pedido',8,'Saida por venda','2025-09-17 23:00:22'),(16,47,'saida',1.000,'pedido',9,'Saida por venda','2025-09-18 13:30:16'),(17,43,'saida',2.000,'pedido',9,'Saida por venda','2025-09-18 13:30:16'),(18,47,'saida',1.000,'pedido',10,'Saida por venda','2025-09-21 16:54:53'),(19,38,'saida',0.250,'pedido',11,'Saida por venda','2025-09-21 17:59:49'),(20,37,'saida',1.500,'pedido',11,'Saida por venda','2025-09-21 17:59:49'),(21,45,'saida',2.000,'pedido',12,'Saida por venda','2025-09-21 18:18:57'),(22,42,'saida',3.000,'pedido',12,'Saida por venda','2025-09-21 18:18:57'),(23,44,'saida',2.000,'pedido',13,'Saida por venda','2025-09-21 18:35:24'),(24,45,'saida',2.000,'pedido',13,'Saida por venda','2025-09-21 18:35:24'),(25,1,'saida',1.000,'pedido',14,'Saida por venda','2025-09-21 19:27:50'),(26,45,'saida',1.000,'pedido',15,'Saida por venda','2025-09-23 22:30:54'),(27,46,'saida',1.000,'pedido',16,'Saida por venda','2025-09-23 22:31:20'),(28,45,'saida',1.000,'pedido',17,'Saida por venda','2025-09-23 22:38:40'),(29,45,'saida',1.000,'pedido',18,'Saida por venda','2025-09-23 22:41:48'),(30,46,'saida',1.000,'pedido',19,'Saida por venda','2025-09-23 22:51:27'),(31,35,'saida',1.000,'pedido',20,'Saida por venda','2025-09-23 23:16:50'),(32,35,'saida',2.000,'pedido',21,'Saida por venda','2025-09-24 00:01:41'),(33,35,'saida',1.000,'pedido',22,'Saida por venda','2025-09-24 00:15:59'),(34,35,'saida',1.000,'pedido',23,'Saida por venda','2025-09-24 00:45:44'),(35,35,'saida',1.000,'pedido',24,'Saida por venda','2025-09-24 01:11:49'),(36,47,'saida',1.000,'pedido',25,'Saida por venda','2025-09-24 01:16:08');
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Pedidos da loja (online/retirada)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedido`
--

LOCK TABLES `pedido` WRITE;
/*!40000 ALTER TABLE `pedido` DISABLE KEYS */;
INSERT INTO `pedido` VALUES (1,5,1,'cancelado','entrega','pix',120.00,10.00,0.00,130.00,'PED-0001','2025-09-13 02:38:18','2025-09-17 16:57:41'),(2,2,NULL,'cancelado','retirada','pix',123.99,0.00,0.00,123.99,NULL,'2025-09-16 23:33:56','2025-09-17 23:25:20'),(3,2,NULL,'pronto','retirada','na_entrega',50.00,0.00,0.00,50.00,NULL,'2025-09-16 23:35:06','2025-09-17 23:55:27'),(4,2,1,'em_transporte','entrega','na_entrega',120.00,0.00,0.00,120.00,NULL,'2025-09-16 23:45:10','2025-09-17 23:14:06'),(5,2,NULL,'cancelado','retirada','na_entrega',50.00,0.00,0.00,50.00,NULL,'2025-09-17 00:08:49','2025-09-17 23:26:41'),(6,5,NULL,'em_transporte','retirada','na_entrega',14.16,0.00,0.00,14.16,NULL,'2025-09-17 01:11:07','2025-09-17 16:57:30'),(7,2,1,'em_transporte','entrega','na_entrega',73.80,0.00,0.00,73.80,NULL,'2025-09-17 21:50:17','2025-09-17 21:50:55'),(8,2,3,'novo','entrega','gateway',67.81,0.00,0.00,67.81,NULL,'2025-09-17 23:00:22','2025-09-17 23:00:22'),(9,5,2,'novo','entrega','pix',69.70,0.00,0.00,69.70,NULL,'2025-09-18 13:30:16','2025-09-18 13:30:16'),(10,6,NULL,'novo','retirada','na_entrega',19.90,0.00,0.00,19.90,NULL,'2025-09-21 16:54:53','2025-09-21 16:54:53'),(11,6,NULL,'novo','retirada','na_entrega',14.08,0.00,0.00,14.08,NULL,'2025-09-21 17:59:49','2025-09-21 17:59:49'),(12,6,5,'novo','entrega','pix',54.27,0.00,0.00,54.27,NULL,'2025-09-21 18:18:57','2025-09-21 18:18:57'),(13,3,NULL,'novo','retirada','pix',54.78,0.00,0.00,54.78,NULL,'2025-09-21 18:35:24','2025-09-21 18:35:24'),(14,2,NULL,'novo','retirada','na_entrega',55.00,0.00,0.00,55.00,NULL,'2025-09-21 19:27:50','2025-09-21 19:27:50'),(15,2,NULL,'novo','retirada','gateway',18.90,0.00,0.00,18.90,NULL,'2025-09-23 22:30:54','2025-09-23 22:30:54'),(16,2,NULL,'novo','retirada','pix',16.90,0.00,0.00,16.90,NULL,'2025-09-23 22:31:20','2025-09-23 22:31:20'),(17,2,NULL,'novo','retirada','pix',18.90,0.00,0.00,18.90,'PED-000017','2025-09-23 22:38:40','2025-09-23 22:38:41'),(18,2,NULL,'novo','retirada','pix',18.90,0.00,0.00,18.90,'PED-000018','2025-09-23 22:41:48','2025-09-23 22:41:49'),(19,2,NULL,'novo','retirada','na_entrega',16.90,0.00,0.00,16.90,NULL,'2025-09-23 22:51:27','2025-09-23 22:51:27'),(20,2,NULL,'novo','retirada','pix',2.99,0.00,0.00,2.99,'PED-000020','2025-09-23 23:16:50','2025-09-23 23:16:51'),(21,2,NULL,'novo','retirada','pix',5.98,0.00,0.00,5.98,'PED-000021','2025-09-24 00:01:41','2025-09-24 00:01:42'),(22,2,NULL,'novo','retirada','gateway',2.99,0.00,0.00,2.99,NULL,'2025-09-24 00:15:59','2025-09-24 00:15:59'),(23,2,NULL,'novo','retirada','gateway',2.99,0.00,0.00,2.99,NULL,'2025-09-24 00:45:44','2025-09-24 00:45:44'),(24,2,NULL,'novo','retirada','pix',2.99,0.00,0.00,2.99,'PED-000024','2025-09-24 01:11:49','2025-09-24 01:11:50'),(25,2,NULL,'novo','retirada','pix',19.90,0.00,0.00,19.90,'PED-000025','2025-09-24 01:16:08','2025-09-24 01:16:08');
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
-- Table structure for table `pedido_pix`
--

DROP TABLE IF EXISTS `pedido_pix`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pedido_pix` (
  `pedido_id` int NOT NULL,
  `mp_payment_id` bigint NOT NULL,
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_detail` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qr_code` text COLLATE utf8mb4_unicode_ci,
  `qr_code_base64` mediumtext COLLATE utf8mb4_unicode_ci,
  `ticket_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
INSERT INTO `pedido_pix` VALUES (17,1324854612,'pending','pending_waiting_transfer','00020126580014br.gov.bcb.pix0136b76aa9c2-2ec4-4110-954e-ebfe34f05b61520400005303986540518.905802BR5916DOYGlyn.ezwRlORO6006MaZWMH62230519mpqrinter1324854612630441C9','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','https://www.mercadopago.com.br/sandbox/payments/1324854612/ticket?caller_id=1406372264&hash=068d7488-26a2-4e0b-93f6-ff01f18aaacc','2025-09-24 22:38:38','2025-09-23 22:38:41','2025-09-24 00:14:12'),(18,1324854628,'pending','pending_waiting_transfer','00020126580014br.gov.bcb.pix0136b76aa9c2-2ec4-4110-954e-ebfe34f05b61520400005303986540518.905802BR5916DOYGlyn.ezwRlORO6006MaZWMH62230519mpqrinter13248546286304C987','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','https://www.mercadopago.com.br/sandbox/payments/1324854628/ticket?caller_id=1406372264&hash=4b94cf18-d082-4b8e-b57f-80e05ea0d687','2025-09-24 22:41:46','2025-09-23 22:41:49','2025-09-23 22:49:06'),(20,1324854924,'pending','pending_waiting_transfer','00020126580014br.gov.bcb.pix0136b76aa9c2-2ec4-4110-954e-ebfe34f05b6152040000530398654042.995802BR5916DOYGlyn.ezwRlORO6006MaZWMH62230519mpqrinter132485492463040093','iVBORw0KGgoAAAANSUhEUgAABWQAAAVkAQAAAAB79iscAAAN3UlEQVR4Xu3XQZZcuQpF0ZhBzX+WNYP4y1wQCBRZnZSdz//cRlgSIO2XPb/eD8q/r37yk4P2XtDeC9p7QXsvaO8F7b2gvRe094L2XtDeC9p7QXsvaO8F7b2gvRe094L2XtDeC9p7QXsvaO8F7b2gvRe094L2XtDeC9p7QXsvaO8F7b2gvRe094L2XtDeC9p7QXsvaO8F7b2gvZeqffX88+vMfmKbfXUs+mr13/VzmohYi8/GKqueuBmtV9GiVRUtWlXRolUVLVpV0aJVFe2jtXmeW7vktS6OeMspp7fbVZFR3T7cW9Bm0Cpo0Spo0Spo0Spo0Spo0SoP1+b8x6nW4tvX+qB2ixXiguzzn60wLm0MtGgVtGgVtGgVtGgVtGgVtGiVv1BrJ0mpj8WLXo1ktV61Ndu/3myrhH5goM1mtGv5uc3vRKtmW6FFqxVatFqhRasVWrRaof3RWr8ub9/erqtT8gJL+8j5pbY5MVbfWh7b/BK0aHUJWrS6BC1aXYIWrS5Bi1aXoEWrS36qtm3rJe3ZTLjb7W3Wc2rJD7ezyfCgRaugRaugRaugRaugRaugRas8WduyAX7jz2Sg/a6fyUD7XT+Tgfa7fiYD7Xf9TAba7/qZDLTf9TMZaL/rZzLQftfPZKD9rp/JeLz2HPsf3sv/r9e2GWvNF3Obt9QWS1atELd8EbRoFbRoFbRoFbRoFbRoFbRolSdrN0DOt2erJ1qyUIaVOKuf+/E+W53OLGhb0Pqux46/uD1a0G5nFrQtaH3XY8df3B4taLczC9oWtL7rseMvbo8WtNuZBW0LWt/12PEXt0fLH9NuN9UnMgZIWT7xWhPv/Z0t577cWtUSZ2u77+Lt2KJFqy1atNqiRastWrTaokWrLVq02v5cbbu9XTIK27Y+EWee/Nx5n3+hpZHjgv2qtTwq0KJFu7YRtPU0C9sWbbSgRbuv0OaZB207zy3aFbT1NAvbFm20fNLGi6O3vbi1jCeir0g+peLzFsv5obVE+19Bi1ZBi1ZBi1ZBi1ZBi1ZB+0O19sT2jhe21UC1sflOttRsH3lqafehRRtBi1ZBi1ZBi1ZBi1ZBi1Z5rtbiHa8B9ZixVcM9+rYPrx8ZsdZctetH0GYf2i1f3GRBu1ZoRxXtMWizD+2WL26yoF0rtKOK9hi02Yd2y7gzt/Z2/LQW+7deEBkTH6untAm0Hz1oJwXtrJ6CFq2CFq2CFq2CFq2C9g9r/e1/FyBQnrxkftUYe/lZzfZGu2qs0KJFW6po0aJFW6to0aJFW6to/wptveTDdvAsYWzJxwZvW7WWvLlt0WbQruWR17ZoewtatJ3XtmgzaNfyyGtbtL0FLdrOa1u0GbRrmUe6qQHqO1tfVmshkh95uq9OfLwvgxZt9PWTw2jO+2rry2otRNDuR30053219WW1FiJo96M+mvO+2vqyWgsRtPtRH815X219Wa2FCNr9qI/mvK+2vqzWQgTtftRHc95XW19WayGCdj/qoznvq60vq7UQQbsf9dGc99XWl9VaiKDNePc//o4/ZoXtzH/iYl/FBVXWmi1W/XBVrcYFa2wtlexAi7bf5AW0vYp2PuGruACtja2lkh1o0fabvIC2V9HOJ3wVF6C1sbVUsgPt/622duRoFE4t3meZ79TCljqb1e21rKJFixYt2tVnQYtWQYtWQYtWQfvXaT/I6ugmG4VIvSpvyULyLPH4GEOLdhU8aCPjWbslC2gjA4W2C6LgQRsZz9otWUAbGSi0XRAFD9rIeNZuyQLayECh7YIoeJ6r9WezI7atapvW5y3RNz5oi8/mVQ0ff6Wc8KBFq6BFq6BFq6BFq6BFq6BFqzxXmxf7QP7YaLydW2+ONHe7oLY0WX5QXtD+cn62lmjRetCiVdCiVdCiVdCiVdCiVR6orRd7hwZ8aruk/WR13blRbJuJCS9tfwxLmYuWXJ970a4L0J48aHWWDWUuWnJ97kW7LkB78qDVWTaUuWjJ9bkX7boA7cmDVmfZUOaiJdfnXrTrArQnzx/X1sSopa4scWc2j8/4EJ/YkoX64VvBgxatghatghatghatghatghat8nCt32oDQc7589l7bc09z2p1+zuM7ft8lQftPENrQYtWQYtWQYtWQYtWQYtWeaD2i1VA7ax6IqOvtWxfev4gy/wMtKMPbaS+eFqhRasVWrRaoUWrFVq0WqFFq9UP0raOGHVFVNvFXo3VuKD9nAoxm2fjg3xsLW03H3ujnWf5BtpRjdW4YMpOZ/kG2lGN1bhgyk5n+QbaUY3VuGDKTmf5BtpRjdW4YMpOZ/kG2lGN1bhgyk5n+QbaUY3VuGDKTmf5BtpRjdW4YMpOZ/kG2lGN1bhgyk5n+cZfqXXKzOp9Veh87Jz4vvrslir7MOtB24LWetGiVS9atOpFi1a9aNGqFy1a9T5LOzvyC7Il3Q2fF9TZ8aJSx+KW5s4/hgctWgUtWgUtWgUtWgUtWgUtWuW52jZfodtNlvN1VtiM1ufJ7bxqjJ2/tIyhdYoFLVoFLVoFLVoFLVoFLVoF7WO0kexoA4f5Qm6U5LWv8r6In25/kVrIoEWroEWroEWroEWroEWroEWrPFlrAzO1EIC63VCtamlbT35k64vmMYZ2q1ra1oP2hdaC9oXWgvaF1oL2hdaC9oXWgvb1DG0reLWORl9Wt216mqz2bWnXjxa0aONs35W0Alq0KqBFqwJatCqgRasCWrQq/DRt7bVt3Omx6kbxlpgdfXGLVxMaOVXzgix40KJV0KJV0KJV0KJV0KJV0KJVnqsdig8oX2VLJp+IauurskRZS1xQf2JiVdfSdvMJtGj79o0WbVyHNlriArS1JYMWrYIWrYIWrfIbtXUqtvXOfGcrtIn6kZssq3U13WMMbUygjaBFq6BFq6BFq6BFq6BFqzxXu440YDlfnH2RbK6fsQEyOVFfyzesuv0dPGjRKmjRKmjRKmjRKmjRKmjRKn+Ntt1Zf95VNj7DqttXtU+riYfa9vylaK2KFq2qaNGqihatqmjRqooWrapoH61NqHXkO/NOS574NsbaxJmcb2S2P0YtWNDOCbRoo4oWrapo0aqKFq2qaNGqivZZWj8qAxVg86frNoVX5+dmztXtg+oqg9aqJ0/kXEWLtm/tFrQxkfHqyRM5V9Gi7Vu7BW1MZLx68kTOVbRo+9ZuQRsTGa+ePJFzFe1P07736zINmtug2Kp+X7tlftUI2rjOt2jRaosWrbZo0WqLFq22aNFqi/Yv045nY8rPMs2Ts5usrrZmS95WPyPerdscQ2tBi1ZBi1ZBi1ZBi1ZBi1ZB+2ht68gXM/G2bfabFC9sV53+Dt7cvmW7Pic8aK2wXYW2PYEWLVrvQotWQYtWQYtWQfsM7XaTXxeF001ZrTN5u1Xtlpxtsmips9HSvhQt2ghatApatApatApatApatMqTtTngxT5V5636+oTfyDU51j789C2jJdd+DdoStF7U9vSDFi1aL6BFqwJatCqgRasC2p+hXUc6G+544uSuBcs2O97ON7b42fa5+1VrmUdo0eoILVodoUWrI7RodYQWrY7QotXRI7T5du39eIllnrVPa9Dxpe2sXRBVD1q0Clq0Clq0Clq0Clq0Clq0ynO1lo8DA2WJT6stwasXWPLZzIdPG5d6c5n8lRz1lZ2hVdCiVdCiVdCiVdCiVdCiVdD+SG201W3c5Kt3vcRPI/WCmMi0M5fFQ7mt39yCFq2CFq2CFq2CFq2CFq2CFq3yZO17B2yoQWlVm40vbRfkma832fjw05M+tpbKmDqNoi05Pelja6mMqdMo2pLTkz62lsqYOo2iLTk96WNrqYyp0yjaktOTPraWypg6jaItOT3pY2upjKnTKNqS05M+tpbKmDqNoi05Pelja6mMqdMo2pLTkz62lsqYOo2iLTk96WNrqYyp0+hP0NbeQGUh+7xwImchJursdl99Y0P5RDTXoEWroEWroEWroEWroEWroEWrPF5rvbYNhSev21ZfTFjyg7bm0+fWbGNrYi3R/graN1oL2jdaC9o3WgvaN1oL2jdaC9r307Sn2KBd7Af2xLyuvpjfYrOZxtv6/mvWgvbji2i3+BRatJpCi1ZTaNFqCi1aTaFFq6kfqK1Tlo8KW+UXxOzw2FlMtLO8oN18mvWg/fAi2hq06wwtWrQvtGgjaNcZWrRoXz9em+extanTaH3WcnrbWqyQ1S31m+NL62wLWrQKWrQKWrQKWrQKWrQKWrTKw7V+ja1iu3rjuu3tOrt9XzaPsa0wxiybxYM2gzaC9sMYWrSrGa1tVm+Mou1BG9Xzs2gjaD+MoUW7mq9pa2K0XhL5+H1+ZtqGz21esN2CNs+syw/QRtBqws/QWtB2HtoWtGhXIZtb0KItZ9blB2gjaDXhZ79HW6v5jiVXkToR8e38Uq8a+UMf2kydiPgWrQUtWgUtWgUtWgUtWgUtWuUh2ratl1i2d7xq5OaOs9FiZ03bJibDg9aCFq2CFq2CFq2CFq2CFq2C9tHaFmvbKP4F+Vhsrf+Ls1i1z6j3bR/UWtbsWqJF61nlrQ0t2n6JtaDtD82WNbuWaNF6VnlrQ4u2X2ItaPtDs2XNriVatI8J2ntBey9o7wXtvaC9F7T3gvZe0N4L2ntBey9o7wXtvaC9F7T3gvZe0N4L2ntBey9o7wXtvaC9F7T3gvZe0N4L2ntBey9o7wXtvaC9F7T3gvZe0N4L2ntBey9o7wXtvaC9F7T3gvZeHqb9H7SJK7+ebWqkAAAAAElFTkSuQmCC','https://www.mercadopago.com.br/sandbox/payments/1324854924/ticket?caller_id=1406372264&hash=758eb385-bbe6-4e01-8a19-4e86dc75bb5b','2025-09-24 23:16:48','2025-09-23 23:16:52','2025-09-23 23:19:38'),(21,1341308241,'pending','pending_waiting_transfer','00020126580014br.gov.bcb.pix0136b76aa9c2-2ec4-4110-954e-ebfe34f05b6152040000530398654045.985802BR5916DOYGlyn.ezwRlORO6006MaZWMH62230519mpqrinter1341308241630448CF','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','https://www.mercadopago.com.br/sandbox/payments/1341308241/ticket?caller_id=1406372264&hash=1c070187-9765-4e08-b53c-b3eeebd53d3d','2025-09-25 00:01:40','2025-09-24 00:01:43','2025-09-24 01:15:40'),(24,1341308743,'pending','pending_waiting_transfer','00020126580014br.gov.bcb.pix0136b76aa9c2-2ec4-4110-954e-ebfe34f05b6152040000530398654042.995802BR5916DOYGlyn.ezwRlORO6006MaZWMH62230519mpqrinter13413087436304C7E1','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','https://www.mercadopago.com.br/sandbox/payments/1341308743/ticket?caller_id=1406372264&hash=a0327816-e1eb-4bfd-a4af-99b6917a8b70','2025-09-25 01:14:53','2025-09-24 01:14:56','2025-09-24 01:15:30'),(25,1341310311,'pending','pending_waiting_transfer','00020126580014br.gov.bcb.pix0136b76aa9c2-2ec4-4110-954e-ebfe34f05b61520400005303986540519.905802BR5916DOYGlyn.ezwRlORO6006MaZWMH62230519mpqrinter13413103116304B4B8','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','https://www.mercadopago.com.br/sandbox/payments/1341310311/ticket?caller_id=1406372264&hash=d0a1fab1-0f57-43a6-9a31-aee03df66634','2025-09-25 01:16:06','2025-09-24 01:16:09','2025-09-24 01:24:36');
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
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Histórico de preços por produto';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `preco`
--

LOCK TABLES `preco` WRITE;
/*!40000 ALTER TABLE `preco` DISABLE KEYS */;
INSERT INTO `preco` VALUES (1,1,50.00,NULL,NULL,NULL,'2025-09-13 03:27:42'),(2,2,20.00,NULL,NULL,NULL,'2025-09-13 03:27:42'),(3,3,5.99,3.99,NULL,NULL,'2025-09-16 23:32:39'),(4,4,4.99,NULL,NULL,NULL,'2025-09-17 18:30:33'),(5,5,24.90,NULL,NULL,NULL,'2025-09-17 18:30:33'),(6,6,18.50,NULL,NULL,NULL,'2025-09-17 18:30:33'),(7,7,32.90,29.90,'2025-09-17 15:30:33','2025-09-24 15:30:33','2025-09-17 18:30:33'),(8,8,17.99,NULL,NULL,NULL,'2025-09-17 18:30:33'),(9,9,9.49,NULL,NULL,NULL,'2025-09-17 18:30:33'),(10,10,22.50,NULL,NULL,NULL,'2025-09-17 18:30:33'),(11,11,6.99,NULL,NULL,NULL,'2025-09-17 18:36:25'),(12,12,19.90,NULL,NULL,NULL,'2025-09-17 18:36:25'),(13,13,2.89,NULL,NULL,NULL,'2025-09-17 18:36:25'),(14,14,8.49,NULL,NULL,NULL,'2025-09-17 18:36:25'),(15,15,16.90,NULL,NULL,NULL,'2025-09-17 18:36:25'),(16,16,11.90,NULL,NULL,NULL,'2025-09-17 18:36:25'),(17,17,9.50,NULL,NULL,NULL,'2025-09-17 18:36:25'),(18,18,14.90,NULL,NULL,NULL,'2025-09-17 18:36:25'),(19,19,21.90,NULL,NULL,NULL,'2025-09-17 18:36:25'),(20,20,29.90,NULL,NULL,NULL,'2025-09-17 18:36:25'),(21,21,19.90,NULL,NULL,NULL,'2025-09-17 18:36:25'),(22,22,13.90,NULL,NULL,NULL,'2025-09-17 18:36:25'),(23,23,44.90,NULL,NULL,NULL,'2025-09-17 18:36:25'),(24,24,6.50,NULL,NULL,NULL,'2025-09-17 18:36:25'),(25,25,27.90,NULL,NULL,NULL,'2025-09-17 18:36:25'),(26,26,5.49,NULL,NULL,NULL,'2025-09-17 18:36:25'),(27,27,7.99,NULL,NULL,NULL,'2025-09-17 18:36:25'),(28,28,6.90,NULL,NULL,NULL,'2025-09-17 18:36:25'),(29,29,5.60,NULL,NULL,NULL,'2025-09-17 18:36:25'),(30,30,9.90,NULL,NULL,NULL,'2025-09-17 18:36:25'),(31,31,32.90,NULL,NULL,NULL,'2025-09-17 18:36:25'),(32,32,3.99,NULL,NULL,NULL,'2025-09-17 18:36:25'),(33,33,8.99,NULL,NULL,NULL,'2025-09-17 18:36:25'),(34,34,5.99,NULL,NULL,NULL,'2025-09-17 18:36:25'),(35,35,2.99,NULL,NULL,NULL,'2025-09-17 18:36:25'),(36,36,18.90,NULL,NULL,NULL,'2025-09-17 18:36:25'),(37,37,7.80,NULL,NULL,NULL,'2025-09-17 18:36:25'),(38,38,9.50,NULL,NULL,NULL,'2025-09-17 18:36:25'),(39,39,2.99,NULL,NULL,NULL,'2025-09-17 18:36:25'),(40,40,27.90,NULL,NULL,NULL,'2025-09-17 18:36:25'),(41,41,19.90,NULL,NULL,NULL,'2025-09-17 18:36:25'),(42,42,5.49,NULL,NULL,NULL,'2025-09-17 18:36:25'),(43,43,24.90,NULL,NULL,NULL,'2025-09-17 18:37:33'),(44,44,8.49,NULL,NULL,NULL,'2025-09-17 18:38:08'),(45,45,18.90,NULL,NULL,NULL,'2025-09-17 18:38:08'),(46,46,16.90,NULL,NULL,NULL,'2025-09-17 18:38:08'),(47,47,19.90,NULL,NULL,NULL,'2025-09-17 18:38:08'),(48,26,5.58,NULL,NULL,NULL,'2025-09-21 19:24:52'),(49,1,55.00,NULL,NULL,NULL,'2025-09-21 19:25:52');
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
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Produtos do catálogo';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produto`
--

LOCK TABLES `produto` WRITE;
/*!40000 ALTER TABLE `produto` DISABLE KEYS */;
INSERT INTO `produto` VALUES (1,'Arroz Tio João 5kg Tipo 1','ARROZ5','789000000001',1,2,1,'Pacote 5kg','uploads/produtos/19bba88ad394ab08-1758144441.jpg',1,0,'2025-09-13 03:26:58'),(2,'Feijão Carioca 1kg','FEIJAO1','789000000002',1,1,1,'Pacote 1kg','uploads/produtos/4a708d9feef7daea-1758144868.jpg',1,0,'2025-09-13 03:26:58'),(3,'Banana Prata','bananaprta',NULL,1,1,2,'banana prata','uploads/produtos/f0a37618390f6d0e-1758144487.jpg',1,0,'2025-09-16 23:32:39'),(4,'Leite Integral Aurora 1L','LEITEAUR1L','7891150043210',5,3,4,'Leite UHT integral catarinense.','uploads/produtos/73bf2bce587591d5-1758145111.jpg',1,0,'2025-09-17 18:30:33'),(5,'Queijo Minas Frescal 500g','QUEIJOMIN500','7892222000456',5,9,3,'Queijo minas frescal tradicional.','uploads/produtos/7bcd1cffea6f2b89-1758145424.jpg',1,0,'2025-09-17 18:30:33'),(6,'Pao de Queijo Forno de Minas 400g','PAOQFMIN400','7896004001234',6,5,3,'Pao de queijo congelado pronto para assar.','uploads/produtos/0ddb113ff8420180-1758145249.jpg',1,0,'2025-09-17 18:30:33'),(7,'Coxinha Congelada Perdigao 1kg','COXPERD1KG','7892300005678',6,4,2,'Coxinha de frango congelada, pacote 1kg.','uploads/produtos/23ee276665706384-1758144733.jpg',1,0,'2025-09-17 18:30:33'),(8,'Cafe Pilao Tradicional 500g','CAFEPIL500','7894900012346',3,6,3,'Cafe torrado e moido Pilao tradicional.','uploads/produtos/2245b3868c65dcdb-1758144622.jpg',1,0,'2025-09-17 18:30:33'),(9,'Suco de Laranja Do Bem 1L','SUCLDMB1L','7891991009871',4,7,4,'Suco integral de laranja, sem acucar.','uploads/produtos/be237b0ce735aead-1758145631.jpg',1,0,'2025-09-17 18:30:33'),(10,'Erva Mate Barrao 1kg','ERVABARR1K','7897151700150',4,8,2,'Erva mate para chimarrao, moagem grossa.','uploads/produtos/415305279c25b91c-1758144807.jpg',1,0,'2025-09-17 18:30:33'),(11,'Leite Condensado Mooca 395g','LEITECOND395','7891000054321',1,17,3,'Leite condensado tradicional para sobremesas.','uploads/produtos/5f02ceaf8e012dce-1758145062.jpg',1,0,'2025-09-17 18:36:25'),(12,'Manteiga Aviacao 200g','MANTAVI200','7896034501234',5,18,3,'Manteiga extra com sal, pote 200g.','uploads/produtos/d77a74fe15e26e60-1758145198.jpg',1,0,'2025-09-17 18:36:25'),(13,'Iogurte Natural Nestle 170g','IOGUNEST170','7891000256780',5,17,3,'Iogurte natural integral copo 170g.','uploads/produtos/fb375537b706afbf-1758144960.jpg',1,0,'2025-09-17 18:36:25'),(14,'Requeijao Cremoso Tirolez 200g','REQTIROL200','7896036001236',5,40,3,'Requeijao cremoso tradicional.','uploads/produtos/f1de563137b70226-1758145538.jpg',1,0,'2025-09-17 18:36:25'),(15,'Pao Frances kg','PAOFRANCESKG',NULL,3,41,2,'Pao frances assado diariamente.','uploads/produtos/9e2ff9ad86c15929-1758145278.jpg',1,1,'2025-09-17 18:36:25'),(16,'Pao Integral Wickbold 500g','PAOWICK500','7896004005677',3,41,3,'Pao integral fatiado com graos.','uploads/produtos/5c5119a1626f3775-1758145315.jpg',1,0,'2025-09-17 18:36:25'),(17,'Bisnaguinha Pullman 300g','BISNPULL300','7891910007654',3,39,3,'Pao tipo bisnaguinha fofinho.','uploads/produtos/7ffb5e10610578df-1758144540.jpg',1,0,'2025-09-17 18:36:25'),(18,'Bolo de Milho Congelado Seara 400g','BOLOSMIL400','7894904003456',6,19,3,'Bolo de milho congelado pronto.','uploads/produtos/f29f2dbd589c654d-1758144598.jpg',1,0,'2025-09-17 18:36:25'),(19,'Lasanha Sadia Bolonhesa 600g','LASASAD600','7891810009870',6,20,3,'Lasanha congelada sabor bolonhesa.','uploads/produtos/64043b572cf427fa-1758145029.jpg',1,0,'2025-09-17 18:36:25'),(20,'Batata Palito McCain 2kg','BATAMCC2KG','7894904500123',6,21,2,'Batata palito congelada embalagens 2kg.','uploads/produtos/f9776ff2932f154a-1758144515.jpg',1,0,'2025-09-17 18:36:25'),(21,'Pizza Calabresa Perdigao 460g','PIZZAPER460','7892300056784',6,4,3,'Pizza congelada sabor calabresa.','uploads/produtos/2f8c5b6a7af26ea7-1758145381.jpg',1,0,'2025-09-17 18:36:25'),(22,'Frango Inteiro Congelado Seara 2kg','FRANGSEAR2K','7894900303256',8,19,2,'Frango inteiro congelado.','uploads/produtos/31b37116840c5140-1758144924.jpg',1,0,'2025-09-17 18:36:25'),(23,'Contra File Bovino kg','CONTRAFILKG',NULL,8,20,2,'Corte bovino contra file fresco.','uploads/produtos/22b3a7446fafd1ed-1758144704.jpg',1,1,'2025-09-17 18:36:25'),(24,'Mortadela Seara Fatiada 200g','MORTSEAR200','7894900001235',3,19,3,'Mortadela fatiada classica 200g.','uploads/produtos/c68b8ad928fc48a9-1758145221.jpg',1,0,'2025-09-17 18:36:25'),(25,'Cafe Soluvel Nescafe 200g','CAFESOL200','7891000103567',1,17,3,'Cafe soluvel tradicional 200g.','uploads/produtos/b9eabed72a0ed825-1758144647.jpg',1,0,'2025-09-17 18:36:25'),(26,'Açucar Refinado Uniao 1kg','ACUCUNIA1K','7891021000017',1,24,2,'Acucar refinado cristal fino 1kg.','uploads/produtos/1cbb6b33fa6d758f-1758144171.jpg',1,0,'2025-09-17 18:36:25'),(27,'Feijao Preto Sao Joao 1kg','FEIJSAO1KG','7896028701234',1,8,2,'Feijao preto tipo 1.','uploads/produtos/a07c720ba554409f-1758144903.jpg',1,0,'2025-09-17 18:36:25'),(28,'Farinha de Trigo Renata 1kg','FARINREN1K','7896102502345',1,23,2,'Farinha de trigo especial.','uploads/produtos/171a00a459bce0d6-1758144835.jpg',1,0,'2025-09-17 18:36:25'),(29,'Macarrao Espaguete Galo 500g','MACGAL500','7891234009876',1,22,3,'Macarrao espaguete n10.','uploads/produtos/0db02103febae097-1758145164.jpg',1,0,'2025-09-17 18:36:25'),(30,'Arroz Integral Tio Joao 1kg','ARROZINT1K','7896079901233',1,8,2,'Arroz integral grao longo.','uploads/produtos/43cd816d30f8a223-1758144389.jpg',1,0,'2025-09-17 18:36:25'),(31,'Azeite Extra Virgem Gallo 500ml','AZEIGAL500','7891107004567',1,25,5,'Azeite portugues extra virgem 0.5L.','uploads/produtos/750e131763fc9979-1758144467.jpg',1,0,'2025-09-17 18:36:25'),(32,'Vinagre de Alcool Castelo 750ml','VINACAST750','7891040003456',1,26,5,'Vinagre de alcool culinario.','uploads/produtos/290b4c439da511f5-1758145730.jpg',1,0,'2025-09-17 18:36:25'),(33,'Refrigerante Guarana Antarctica 2L','REFRGAU2L','7891991012345',4,27,4,'Refrigerante guarana garrafa 2 litros.','uploads/produtos/a43734bcee9d7119-1758145504.jpg',1,0,'2025-09-17 18:36:25'),(34,'Cerveja Heineken Long Neck 330ml','CERVHEI330','7894321654321',4,28,5,'Cerveja premium long neck 330ml.','uploads/produtos/6f5fb3cd68805d63-1758144677.jpg',1,0,'2025-09-17 18:36:25'),(35,'Agua Mineral Crystal 1.5L','AGCRYS15L','7894900223456',4,29,4,'Agua mineral sem gas 1.5L.','uploads/produtos/618fcdb05930e5da-1758144218.jpg',1,0,'2025-09-17 18:36:25'),(36,'Suco de Uva Aurora Integral 1.5L','SUCUAUR15L','7891149101234',4,3,4,'Suco de uva integral 1.5L.','uploads/produtos/374bf8811bebfb52-1758145671.jpg',1,0,'2025-09-17 18:36:25'),(37,'Maca Gala kg','MACAGALAKG',NULL,7,3,2,'Maca gala selecionada.','uploads/produtos/539441599911698d-1758145137.jpg',1,1,'2025-09-17 18:36:25'),(38,'Tomate Italiano kg','TOMATITALKG',NULL,7,3,2,'Tomate italiano fresco.','uploads/produtos/4ffaeaa623b81c0e-1758145704.jpg',1,1,'2025-09-17 18:36:25'),(39,'Alface Crespa unidade','ALFACECRESP',NULL,7,3,1,'Alface crespa colhida no dia.','uploads/produtos/555e5020b133c69c-1758144307.jpg',1,0,'2025-09-17 18:36:25'),(40,'Sabao em Po OMO Lavagem Perfeita 1.6kg','SABOMO16KG','7891150067890',9,33,1,'Sabao em po lavagem perfeita 1.6kg.','uploads/produtos/0833a3ad4ef5bd7c-1758145567.jpg',1,0,'2025-09-17 18:36:25'),(41,'Amaciante Comfort Concentrado 2L','AMACCOMF2L','7891021006543',9,31,4,'Amaciante concentrado fragrancia original.','uploads/produtos/27dce4b8e089bb38-1758144361.jpg',1,0,'2025-09-17 18:36:25'),(42,'Desinfetante Veja Multiuso 500ml','DESINFVEJA500','7891035009876',9,32,5,'Desinfetante multiuso perfumado.','uploads/produtos/99e4e24d1ae75b2d-1758144781.jpg',1,0,'2025-09-17 18:36:25'),(43,'Papel Higienico Neve Folha Dupla 12x30m','PAPNEVE12','7896079904567',10,34,6,'Papel higienico folha dupla pacote 12 rolos.','uploads/produtos/1e3ca05300b80d91-1758145347.jpg',1,0,'2025-09-17 18:37:33'),(44,'Creme Dental Colgate Total 90g','CREMCOLG90','7891000098765',10,35,3,'Creme dental protecao total 12h.','uploads/produtos/d3d437910e1596f5-1758144757.jpg',1,0,'2025-09-17 18:38:08'),(45,'Shampoo Pantene Liso Extremo 400ml','SHAMPANT400','7891021007890',10,36,5,'Shampoo pantene liso extremo 400ml.','uploads/produtos/282e2010f944bc91-1758145596.jpg',1,0,'2025-09-17 18:38:08'),(46,'Racao Dog Chow Adulto 1kg','RACDOGCH1K','7896044023456',11,37,6,'Racao seca premium para cães adultos.','uploads/produtos/50514bce6ca260ff-1758145454.jpg',1,0,'2025-09-17 18:38:08'),(47,'Racao Whiskas Carne 1kg','RACWHISK1K','7896021109876',11,38,12,'Racao seca para gatos sabor carne.','uploads/produtos/e2ce2c04b5eaae15-1758145479.jpg',1,0,'2025-09-17 18:38:08');
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
  `nome` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `senha_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `perfil` enum('admin','gerente','operador','cliente') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cliente',
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `criado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_usuario_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Usuários do sistema (admin/gerente/operador/cliente)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario`
--

LOCK TABLES `usuario` WRITE;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
INSERT INTO `usuario` VALUES (1,'João Ferlini','operador@mercado.local','18c897bfc3cfce2b6d13cb154176b52b45253de6d82abe848a79d90f81e4f441','operador',1,'2025-09-10 22:40:01'),(2,'Administrador','admin@mercado.local','a14b819a2867d23c914df366addc22a5567d341b61b393878e476846d1b39e2c','admin',1,'2025-09-10 22:40:44'),(3,'Douglas Marcelo Monquero','douglas.monquero@gmail.com','$2y$10$C3CTohQJfarGZZt9I1ZSs.Vde/Tbz1Y8GKsDAgFqHOBYh3esqSlYm','admin',1,'2025-09-10 22:45:37'),(4,'Patricia Alves de Oliveira','paty@gatinha.com.br','$2y$10$I5Oyd5DlUqmml0JsV/5c3uGBi8UCv.ScW73U.LE5ukslxtUkQt4X.','cliente',1,'2025-09-10 22:46:41'),(5,'Lucas Vinicius','lucas@email.com','$2y$10$INlbktG5YUVvhJbEyyVSwO5f9inu1C6L.kKRQi3ocVHyEzzgUUU62','gerente',1,'2025-09-12 22:52:31'),(6,'Henrique Iglesias','henrique@email.com','$2y$10$Ha1wdQjXTi5ztVq7MXXh9uhxpj7OgI8IBY8iGhIuJJco.lemMy9w.','cliente',1,'2025-09-21 16:38:51');
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

-- Dump completed on 2025-09-24 23:29:23
