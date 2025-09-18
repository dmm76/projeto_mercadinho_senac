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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Cadastro de clientes (pode vincular a um usuário)';
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
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Estoque atual por produto (1:1)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estoque`
--

LOCK TABLES `estoque` WRITE;
/*!40000 ALTER TABLE `estoque` DISABLE KEYS */;
INSERT INTO `estoque` VALUES (1,1,44.000,5.000,'2025-09-17 00:08:49'),(2,2,48.000,5.000,'2025-09-16 23:45:10'),(3,3,95.450,10.000,'2025-09-17 01:11:07'),(4,4,95.000,12.000,'2025-09-17 18:30:33'),(5,5,30.000,5.000,'2025-09-17 18:30:33'),(6,6,58.000,10.000,'2025-09-17 21:50:17'),(7,7,40.000,8.000,'2025-09-17 18:30:33'),(8,8,80.000,10.000,'2025-09-17 18:30:33'),(9,9,70.000,10.000,'2025-09-17 18:30:33'),(10,10,55.000,8.000,'2025-09-17 18:30:33'),(11,11,120.000,15.000,'2025-09-17 18:36:25'),(12,12,45.000,8.000,'2025-09-17 18:36:25'),(13,13,150.000,20.000,'2025-09-17 18:36:25'),(14,14,65.000,10.000,'2025-09-17 18:36:25'),(15,15,120.000,25.000,'2025-09-17 18:36:25'),(16,16,80.000,12.000,'2025-09-17 18:36:25'),(17,17,70.000,10.000,'2025-09-17 18:36:25'),(18,18,40.000,6.000,'2025-09-17 18:36:25'),(19,19,55.000,10.000,'2025-09-17 18:36:25'),(20,20,45.000,8.000,'2025-09-17 18:36:25'),(21,21,60.000,10.000,'2025-09-17 18:36:25'),(22,22,80.000,15.000,'2025-09-17 18:36:25'),(23,23,65.000,12.000,'2025-09-17 18:36:25'),(24,24,70.000,10.000,'2025-09-17 18:36:25'),(25,25,50.000,8.000,'2025-09-17 18:36:25'),(26,26,150.000,20.000,'2025-09-17 18:36:25'),(27,27,120.000,18.000,'2025-09-17 18:36:25'),(28,28,110.000,15.000,'2025-09-17 18:36:25'),(29,29,130.000,20.000,'2025-09-17 18:36:25'),(30,30,115.000,18.000,'2025-09-17 18:36:25'),(31,31,70.000,10.000,'2025-09-17 18:36:25'),(32,32,90.000,15.000,'2025-09-17 18:36:25'),(33,33,140.000,25.000,'2025-09-17 18:36:25'),(34,34,200.000,30.000,'2025-09-17 18:36:25'),(35,35,180.000,25.000,'2025-09-17 18:36:25'),(36,36,60.000,8.000,'2025-09-17 18:36:25'),(37,37,90.000,20.000,'2025-09-17 18:36:25'),(38,38,85.000,18.000,'2025-09-17 18:36:25'),(39,39,117.000,25.000,'2025-09-17 23:00:22'),(40,40,60.000,10.000,'2025-09-17 18:36:25'),(41,41,70.000,12.000,'2025-09-17 18:36:25'),(42,42,107.000,15.000,'2025-09-17 23:00:22'),(43,43,80.000,12.000,'2025-09-17 18:37:33'),(44,44,117.000,20.000,'2025-09-17 23:00:22'),(45,45,75.000,10.000,'2025-09-17 18:38:08'),(46,46,88.000,12.000,'2025-09-17 23:00:22'),(47,47,79.000,12.000,'2025-09-17 21:50:17');
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
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Itens de pedido';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item_pedido`
--

LOCK TABLES `item_pedido` WRITE;
/*!40000 ALTER TABLE `item_pedido` DISABLE KEYS */;
INSERT INTO `item_pedido` VALUES (3,1,1,2.000,NULL,50.00,0.00),(4,1,2,1.000,NULL,20.00,0.00),(5,2,1,2.000,NULL,50.00,NULL),(6,2,2,1.000,NULL,20.00,NULL),(7,2,3,1.000,NULL,3.99,NULL),(8,3,1,1.000,NULL,50.00,NULL),(9,4,1,2.000,NULL,50.00,NULL),(10,4,2,1.000,NULL,20.00,NULL),(11,5,1,1.000,NULL,50.00,NULL),(12,6,3,3.550,NULL,3.99,NULL),(13,7,47,1.000,NULL,19.90,NULL),(14,7,46,1.000,NULL,16.90,NULL),(15,7,6,2.000,NULL,18.50,NULL),(16,8,39,3.000,NULL,2.99,NULL),(17,8,46,1.000,NULL,16.90,NULL),(18,8,44,3.000,NULL,8.49,NULL),(19,8,42,3.000,NULL,5.49,NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Movimentações de estoque';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mov_estoque`
--

LOCK TABLES `mov_estoque` WRITE;
/*!40000 ALTER TABLE `mov_estoque` DISABLE KEYS */;
INSERT INTO `mov_estoque` VALUES (1,1,'saida',2.000,'pedido',2,'Saida por venda','2025-09-16 23:33:56'),(2,2,'saida',1.000,'pedido',2,'Saida por venda','2025-09-16 23:33:56'),(3,3,'saida',1.000,'pedido',2,'Saida por venda','2025-09-16 23:33:56'),(4,1,'saida',1.000,'pedido',3,'Saida por venda','2025-09-16 23:35:06'),(5,1,'saida',2.000,'pedido',4,'Saida por venda','2025-09-16 23:45:10'),(6,2,'saida',1.000,'pedido',4,'Saida por venda','2025-09-16 23:45:10'),(7,1,'saida',1.000,'pedido',5,'Saida por venda','2025-09-17 00:08:49'),(8,3,'saida',3.550,'pedido',6,'Saida por venda','2025-09-17 01:11:07'),(9,47,'saida',1.000,'pedido',7,'Saida por venda','2025-09-17 21:50:17'),(10,46,'saida',1.000,'pedido',7,'Saida por venda','2025-09-17 21:50:17'),(11,6,'saida',2.000,'pedido',7,'Saida por venda','2025-09-17 21:50:17'),(12,39,'saida',3.000,'pedido',8,'Saida por venda','2025-09-17 23:00:22'),(13,46,'saida',1.000,'pedido',8,'Saida por venda','2025-09-17 23:00:22'),(14,44,'saida',3.000,'pedido',8,'Saida por venda','2025-09-17 23:00:22'),(15,42,'saida',3.000,'pedido',8,'Saida por venda','2025-09-17 23:00:22');
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Pedidos da loja (online/retirada)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedido`
--

LOCK TABLES `pedido` WRITE;
/*!40000 ALTER TABLE `pedido` DISABLE KEYS */;
INSERT INTO `pedido` VALUES (1,5,1,'cancelado','entrega','pix',120.00,10.00,0.00,130.00,'PED-0001','2025-09-13 02:38:18','2025-09-17 16:57:41'),(2,2,NULL,'cancelado','retirada','pix',123.99,0.00,0.00,123.99,NULL,'2025-09-16 23:33:56','2025-09-17 23:25:20'),(3,2,NULL,'pronto','retirada','na_entrega',50.00,0.00,0.00,50.00,NULL,'2025-09-16 23:35:06','2025-09-17 23:55:27'),(4,2,1,'em_transporte','entrega','na_entrega',120.00,0.00,0.00,120.00,NULL,'2025-09-16 23:45:10','2025-09-17 23:14:06'),(5,2,NULL,'cancelado','retirada','na_entrega',50.00,0.00,0.00,50.00,NULL,'2025-09-17 00:08:49','2025-09-17 23:26:41'),(6,5,NULL,'em_transporte','retirada','na_entrega',14.16,0.00,0.00,14.16,NULL,'2025-09-17 01:11:07','2025-09-17 16:57:30'),(7,2,1,'em_transporte','entrega','na_entrega',73.80,0.00,0.00,73.80,NULL,'2025-09-17 21:50:17','2025-09-17 21:50:55'),(8,2,3,'novo','entrega','gateway',67.81,0.00,0.00,67.81,NULL,'2025-09-17 23:00:22','2025-09-17 23:00:22');
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
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Histórico de preços por produto';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `preco`
--

LOCK TABLES `preco` WRITE;
/*!40000 ALTER TABLE `preco` DISABLE KEYS */;
INSERT INTO `preco` VALUES (1,1,50.00,NULL,NULL,NULL,'2025-09-13 03:27:42'),(2,2,20.00,NULL,NULL,NULL,'2025-09-13 03:27:42'),(3,3,5.99,3.99,NULL,NULL,'2025-09-16 23:32:39'),(4,4,4.99,NULL,NULL,NULL,'2025-09-17 18:30:33'),(5,5,24.90,NULL,NULL,NULL,'2025-09-17 18:30:33'),(6,6,18.50,NULL,NULL,NULL,'2025-09-17 18:30:33'),(7,7,32.90,29.90,'2025-09-17 15:30:33','2025-09-24 15:30:33','2025-09-17 18:30:33'),(8,8,17.99,NULL,NULL,NULL,'2025-09-17 18:30:33'),(9,9,9.49,NULL,NULL,NULL,'2025-09-17 18:30:33'),(10,10,22.50,NULL,NULL,NULL,'2025-09-17 18:30:33'),(11,11,6.99,NULL,NULL,NULL,'2025-09-17 18:36:25'),(12,12,19.90,NULL,NULL,NULL,'2025-09-17 18:36:25'),(13,13,2.89,NULL,NULL,NULL,'2025-09-17 18:36:25'),(14,14,8.49,NULL,NULL,NULL,'2025-09-17 18:36:25'),(15,15,16.90,NULL,NULL,NULL,'2025-09-17 18:36:25'),(16,16,11.90,NULL,NULL,NULL,'2025-09-17 18:36:25'),(17,17,9.50,NULL,NULL,NULL,'2025-09-17 18:36:25'),(18,18,14.90,NULL,NULL,NULL,'2025-09-17 18:36:25'),(19,19,21.90,NULL,NULL,NULL,'2025-09-17 18:36:25'),(20,20,29.90,NULL,NULL,NULL,'2025-09-17 18:36:25'),(21,21,19.90,NULL,NULL,NULL,'2025-09-17 18:36:25'),(22,22,13.90,NULL,NULL,NULL,'2025-09-17 18:36:25'),(23,23,44.90,NULL,NULL,NULL,'2025-09-17 18:36:25'),(24,24,6.50,NULL,NULL,NULL,'2025-09-17 18:36:25'),(25,25,27.90,NULL,NULL,NULL,'2025-09-17 18:36:25'),(26,26,5.49,NULL,NULL,NULL,'2025-09-17 18:36:25'),(27,27,7.99,NULL,NULL,NULL,'2025-09-17 18:36:25'),(28,28,6.90,NULL,NULL,NULL,'2025-09-17 18:36:25'),(29,29,5.60,NULL,NULL,NULL,'2025-09-17 18:36:25'),(30,30,9.90,NULL,NULL,NULL,'2025-09-17 18:36:25'),(31,31,32.90,NULL,NULL,NULL,'2025-09-17 18:36:25'),(32,32,3.99,NULL,NULL,NULL,'2025-09-17 18:36:25'),(33,33,8.99,NULL,NULL,NULL,'2025-09-17 18:36:25'),(34,34,5.99,NULL,NULL,NULL,'2025-09-17 18:36:25'),(35,35,2.99,NULL,NULL,NULL,'2025-09-17 18:36:25'),(36,36,18.90,NULL,NULL,NULL,'2025-09-17 18:36:25'),(37,37,7.80,NULL,NULL,NULL,'2025-09-17 18:36:25'),(38,38,9.50,NULL,NULL,NULL,'2025-09-17 18:36:25'),(39,39,2.99,NULL,NULL,NULL,'2025-09-17 18:36:25'),(40,40,27.90,NULL,NULL,NULL,'2025-09-17 18:36:25'),(41,41,19.90,NULL,NULL,NULL,'2025-09-17 18:36:25'),(42,42,5.49,NULL,NULL,NULL,'2025-09-17 18:36:25'),(43,43,24.90,NULL,NULL,NULL,'2025-09-17 18:37:33'),(44,44,8.49,NULL,NULL,NULL,'2025-09-17 18:38:08'),(45,45,18.90,NULL,NULL,NULL,'2025-09-17 18:38:08'),(46,46,16.90,NULL,NULL,NULL,'2025-09-17 18:38:08'),(47,47,19.90,NULL,NULL,NULL,'2025-09-17 18:38:08');
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
INSERT INTO `produto` VALUES (1,'Arroz Tio João 5kg Tipo 1','ARROZ5','789000000001',1,2,1,'Pacote 5kg','uploads/produtos/19bba88ad394ab08-1758144441.jpg',1,0,'2025-09-13 03:26:58'),(2,'Feijão Carioca 1kg','FEIJAO1','789000000002',1,1,1,'Pacote 1kg','uploads/produtos/4a708d9feef7daea-1758144868.jpg',1,0,'2025-09-13 03:26:58'),(3,'Banana Prata','bananaprta',NULL,1,1,2,'banana prata','uploads/produtos/f0a37618390f6d0e-1758144487.jpg',1,0,'2025-09-16 23:32:39'),(4,'Leite Integral Aurora 1L','LEITEAUR1L','7891150043210',5,3,4,'Leite UHT integral catarinense.','uploads/produtos/73bf2bce587591d5-1758145111.jpg',1,0,'2025-09-17 18:30:33'),(5,'Queijo Minas Frescal 500g','QUEIJOMIN500','7892222000456',5,9,3,'Queijo minas frescal tradicional.','uploads/produtos/7bcd1cffea6f2b89-1758145424.jpg',1,0,'2025-09-17 18:30:33'),(6,'Pao de Queijo Forno de Minas 400g','PAOQFMIN400','7896004001234',6,5,3,'Pao de queijo congelado pronto para assar.','uploads/produtos/0ddb113ff8420180-1758145249.jpg',1,0,'2025-09-17 18:30:33'),(7,'Coxinha Congelada Perdigao 1kg','COXPERD1KG','7892300005678',6,4,2,'Coxinha de frango congelada, pacote 1kg.','uploads/produtos/23ee276665706384-1758144733.jpg',1,0,'2025-09-17 18:30:33'),(8,'Cafe Pilao Tradicional 500g','CAFEPIL500','7894900012346',3,6,3,'Cafe torrado e moido Pilao tradicional.','uploads/produtos/2245b3868c65dcdb-1758144622.jpg',1,0,'2025-09-17 18:30:33'),(9,'Suco de Laranja Do Bem 1L','SUCLDMB1L','7891991009871',4,7,4,'Suco integral de laranja, sem acucar.','uploads/produtos/be237b0ce735aead-1758145631.jpg',1,0,'2025-09-17 18:30:33'),(10,'Erva Mate Barrao 1kg','ERVABARR1K','7897151700150',4,8,2,'Erva mate para chimarrao, moagem grossa.','uploads/produtos/415305279c25b91c-1758144807.jpg',1,0,'2025-09-17 18:30:33'),(11,'Leite Condensado Mooca 395g','LEITECOND395','7891000054321',1,17,3,'Leite condensado tradicional para sobremesas.','uploads/produtos/5f02ceaf8e012dce-1758145062.jpg',1,0,'2025-09-17 18:36:25'),(12,'Manteiga Aviacao 200g','MANTAVI200','7896034501234',5,18,3,'Manteiga extra com sal, pote 200g.','uploads/produtos/d77a74fe15e26e60-1758145198.jpg',1,0,'2025-09-17 18:36:25'),(13,'Iogurte Natural Nestle 170g','IOGUNEST170','7891000256780',5,17,3,'Iogurte natural integral copo 170g.','uploads/produtos/fb375537b706afbf-1758144960.jpg',1,0,'2025-09-17 18:36:25'),(14,'Requeijao Cremoso Tirolez 200g','REQTIROL200','7896036001236',5,40,3,'Requeijao cremoso tradicional.','uploads/produtos/f1de563137b70226-1758145538.jpg',1,0,'2025-09-17 18:36:25'),(15,'Pao Frances kg','PAOFRANCESKG',NULL,3,41,2,'Pao frances assado diariamente.','uploads/produtos/9e2ff9ad86c15929-1758145278.jpg',1,1,'2025-09-17 18:36:25'),(16,'Pao Integral Wickbold 500g','PAOWICK500','7896004005677',3,41,3,'Pao integral fatiado com graos.','uploads/produtos/5c5119a1626f3775-1758145315.jpg',1,0,'2025-09-17 18:36:25'),(17,'Bisnaguinha Pullman 300g','BISNPULL300','7891910007654',3,39,3,'Pao tipo bisnaguinha fofinho.','uploads/produtos/7ffb5e10610578df-1758144540.jpg',1,0,'2025-09-17 18:36:25'),(18,'Bolo de Milho Congelado Seara 400g','BOLOSMIL400','7894904003456',6,19,3,'Bolo de milho congelado pronto.','uploads/produtos/f29f2dbd589c654d-1758144598.jpg',1,0,'2025-09-17 18:36:25'),(19,'Lasanha Sadia Bolonhesa 600g','LASASAD600','7891810009870',6,20,3,'Lasanha congelada sabor bolonhesa.','uploads/produtos/64043b572cf427fa-1758145029.jpg',1,0,'2025-09-17 18:36:25'),(20,'Batata Palito McCain 2kg','BATAMCC2KG','7894904500123',6,21,2,'Batata palito congelada embalagens 2kg.','uploads/produtos/f9776ff2932f154a-1758144515.jpg',1,0,'2025-09-17 18:36:25'),(21,'Pizza Calabresa Perdigao 460g','PIZZAPER460','7892300056784',6,4,3,'Pizza congelada sabor calabresa.','uploads/produtos/2f8c5b6a7af26ea7-1758145381.jpg',1,0,'2025-09-17 18:36:25'),(22,'Frango Inteiro Congelado Seara 2kg','FRANGSEAR2K','7894900303256',8,19,2,'Frango inteiro congelado.','uploads/produtos/31b37116840c5140-1758144924.jpg',1,0,'2025-09-17 18:36:25'),(23,'Contra File Bovino kg','CONTRAFILKG',NULL,8,20,2,'Corte bovino contra file fresco.','uploads/produtos/22b3a7446fafd1ed-1758144704.jpg',1,1,'2025-09-17 18:36:25'),(24,'Mortadela Seara Fatiada 200g','MORTSEAR200','7894900001235',3,19,3,'Mortadela fatiada classica 200g.','uploads/produtos/c68b8ad928fc48a9-1758145221.jpg',1,0,'2025-09-17 18:36:25'),(25,'Cafe Soluvel Nescafe 200g','CAFESOL200','7891000103567',1,17,3,'Cafe soluvel tradicional 200g.','uploads/produtos/b9eabed72a0ed825-1758144647.jpg',1,0,'2025-09-17 18:36:25'),(26,'Acucar Refinado Uniao 1kg','ACUCUNIA1K','7891021000017',1,24,2,'Acucar refinado cristal fino 1kg.','uploads/produtos/1cbb6b33fa6d758f-1758144171.jpg',1,0,'2025-09-17 18:36:25'),(27,'Feijao Preto Sao Joao 1kg','FEIJSAO1KG','7896028701234',1,8,2,'Feijao preto tipo 1.','uploads/produtos/a07c720ba554409f-1758144903.jpg',1,0,'2025-09-17 18:36:25'),(28,'Farinha de Trigo Renata 1kg','FARINREN1K','7896102502345',1,23,2,'Farinha de trigo especial.','uploads/produtos/171a00a459bce0d6-1758144835.jpg',1,0,'2025-09-17 18:36:25'),(29,'Macarrao Espaguete Galo 500g','MACGAL500','7891234009876',1,22,3,'Macarrao espaguete n10.','uploads/produtos/0db02103febae097-1758145164.jpg',1,0,'2025-09-17 18:36:25'),(30,'Arroz Integral Tio Joao 1kg','ARROZINT1K','7896079901233',1,8,2,'Arroz integral grao longo.','uploads/produtos/43cd816d30f8a223-1758144389.jpg',1,0,'2025-09-17 18:36:25'),(31,'Azeite Extra Virgem Gallo 500ml','AZEIGAL500','7891107004567',1,25,5,'Azeite portugues extra virgem 0.5L.','uploads/produtos/750e131763fc9979-1758144467.jpg',1,0,'2025-09-17 18:36:25'),(32,'Vinagre de Alcool Castelo 750ml','VINACAST750','7891040003456',1,26,5,'Vinagre de alcool culinario.','uploads/produtos/290b4c439da511f5-1758145730.jpg',1,0,'2025-09-17 18:36:25'),(33,'Refrigerante Guarana Antarctica 2L','REFRGAU2L','7891991012345',4,27,4,'Refrigerante guarana garrafa 2 litros.','uploads/produtos/a43734bcee9d7119-1758145504.jpg',1,0,'2025-09-17 18:36:25'),(34,'Cerveja Heineken Long Neck 330ml','CERVHEI330','7894321654321',4,28,5,'Cerveja premium long neck 330ml.','uploads/produtos/6f5fb3cd68805d63-1758144677.jpg',1,0,'2025-09-17 18:36:25'),(35,'Agua Mineral Crystal 1.5L','AGCRYS15L','7894900223456',4,29,4,'Agua mineral sem gas 1.5L.','uploads/produtos/618fcdb05930e5da-1758144218.jpg',1,0,'2025-09-17 18:36:25'),(36,'Suco de Uva Aurora Integral 1.5L','SUCUAUR15L','7891149101234',4,3,4,'Suco de uva integral 1.5L.','uploads/produtos/374bf8811bebfb52-1758145671.jpg',1,0,'2025-09-17 18:36:25'),(37,'Maca Gala kg','MACAGALAKG',NULL,7,3,2,'Maca gala selecionada.','uploads/produtos/539441599911698d-1758145137.jpg',1,1,'2025-09-17 18:36:25'),(38,'Tomate Italiano kg','TOMATITALKG',NULL,7,3,2,'Tomate italiano fresco.','uploads/produtos/4ffaeaa623b81c0e-1758145704.jpg',1,1,'2025-09-17 18:36:25'),(39,'Alface Crespa unidade','ALFACECRESP',NULL,7,3,1,'Alface crespa colhida no dia.','uploads/produtos/555e5020b133c69c-1758144307.jpg',1,0,'2025-09-17 18:36:25'),(40,'Sabao em Po OMO Lavagem Perfeita 1.6kg','SABOMO16KG','7891150067890',9,33,1,'Sabao em po lavagem perfeita 1.6kg.','uploads/produtos/0833a3ad4ef5bd7c-1758145567.jpg',1,0,'2025-09-17 18:36:25'),(41,'Amaciante Comfort Concentrado 2L','AMACCOMF2L','7891021006543',9,31,4,'Amaciante concentrado fragrancia original.','uploads/produtos/27dce4b8e089bb38-1758144361.jpg',1,0,'2025-09-17 18:36:25'),(42,'Desinfetante Veja Multiuso 500ml','DESINFVEJA500','7891035009876',9,32,5,'Desinfetante multiuso perfumado.','uploads/produtos/99e4e24d1ae75b2d-1758144781.jpg',1,0,'2025-09-17 18:36:25'),(43,'Papel Higienico Neve Folha Dupla 12x30m','PAPNEVE12','7896079904567',10,34,6,'Papel higienico folha dupla pacote 12 rolos.','uploads/produtos/1e3ca05300b80d91-1758145347.jpg',1,0,'2025-09-17 18:37:33'),(44,'Creme Dental Colgate Total 90g','CREMCOLG90','7891000098765',10,35,3,'Creme dental protecao total 12h.','uploads/produtos/d3d437910e1596f5-1758144757.jpg',1,0,'2025-09-17 18:38:08'),(45,'Shampoo Pantene Liso Extremo 400ml','SHAMPANT400','7891021007890',10,36,5,'Shampoo pantene liso extremo 400ml.','uploads/produtos/282e2010f944bc91-1758145596.jpg',1,0,'2025-09-17 18:38:08'),(46,'Racao Dog Chow Adulto 1kg','RACDOGCH1K','7896044023456',11,37,6,'Racao seca premium para cães adultos.','uploads/produtos/50514bce6ca260ff-1758145454.jpg',1,0,'2025-09-17 18:38:08'),(47,'Racao Whiskas Carne 1kg','RACWHISK1K','7896021109876',11,38,12,'Racao seca para gatos sabor carne.','uploads/produtos/e2ce2c04b5eaae15-1758145479.jpg',1,0,'2025-09-17 18:38:08');
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Usuários do sistema (admin/gerente/operador/cliente)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario`
--

LOCK TABLES `usuario` WRITE;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
INSERT INTO `usuario` VALUES (1,'João Ferlini','operador@mercado.local','18c897bfc3cfce2b6d13cb154176b52b45253de6d82abe848a79d90f81e4f441','operador',1,'2025-09-10 22:40:01'),(2,'Administrador','admin@mercado.local','a14b819a2867d23c914df366addc22a5567d341b61b393878e476846d1b39e2c','admin',1,'2025-09-10 22:40:44'),(3,'Douglas','douglas.monquero@gmail.com','$2y$10$C3CTohQJfarGZZt9I1ZSs.Vde/Tbz1Y8GKsDAgFqHOBYh3esqSlYm','admin',1,'2025-09-10 22:45:37'),(4,'Patricia Alves de Oliveira','paty@gatinha.com.br','$2y$10$I5Oyd5DlUqmml0JsV/5c3uGBi8UCv.ScW73U.LE5ukslxtUkQt4X.','cliente',1,'2025-09-10 22:46:41'),(5,'Lucas Vinicius','lucas@email.com','$2y$10$INlbktG5YUVvhJbEyyVSwO5f9inu1C6L.kKRQi3ocVHyEzzgUUU62','gerente',1,'2025-09-12 22:52:31');
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

-- Dump completed on 2025-09-17 22:28:05
