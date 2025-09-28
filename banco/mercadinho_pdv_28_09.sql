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
  `terminal_id` int DEFAULT NULL,
  `turno_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_caixa_operador_abertura` (`operador_id`,`abertura`),
  KEY `fk_caixa_terminal` (`terminal_id`),
  KEY `fk_caixa_turno` (`turno_id`),
  CONSTRAINT `fk_caixa_operador` FOREIGN KEY (`operador_id`) REFERENCES `usuario` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_caixa_terminal` FOREIGN KEY (`terminal_id`) REFERENCES `pdv_terminal` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_caixa_turno` FOREIGN KEY (`turno_id`) REFERENCES `pdv_turno` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Abertura/fechamento de caixa';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `caixa`
--

LOCK TABLES `caixa` WRITE;
/*!40000 ALTER TABLE `caixa` DISABLE KEYS */;
INSERT INTO `caixa` VALUES (1,1,'2025-09-25 22:03:56',0.00,'2025-09-25 22:03:56',150.00,NULL,1,1),(2,1,'2025-09-25 22:04:11',100.00,NULL,NULL,NULL,1,2);
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
INSERT INTO `cliente` VALUES (1,2,NULL,NULL,NULL,'2025-09-13 00:41:59'),(2,3,'02168710937','(44) 99901-3434','1976-02-21','2025-09-13 00:41:59'),(3,5,NULL,NULL,NULL,'2025-09-13 00:41:59'),(4,1,NULL,NULL,NULL,'2025-09-13 00:41:59'),(5,4,NULL,'(44) 99999-1234',NULL,'2025-09-13 00:41:59'),(6,6,NULL,NULL,NULL,'2025-09-18 17:56:45');
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
INSERT INTO `cliente_favorito` VALUES (2,35,'2025-09-24 19:42:07');
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
INSERT INTO `contato_mensagens` VALUES (1,'Douglas','douglas@email.com','teste agora','estamos em teste','respondida','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:142.0) Gecko/20100101 Firefox/142.0','2025-09-17 00:37:03','2025-09-17 02:27:39'),(2,'Douglas Marcelo Monquero','douglas@email.com','teste de msg dia 17','resposta respondida','arquivada','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:142.0) Gecko/20100101 Firefox/142.0','2025-09-17 15:56:38',NULL),(3,'Valdir Mendonça','valdir@email.com','Teste de envio 17 as 18horas','teste continua ok','respondida','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:142.0) Gecko/20100101 Firefox/142.0','2025-09-17 21:21:13','2025-09-23 18:24:49');
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
INSERT INTO `endereco` VALUES (1,2,'Casa','Douglas','87060-110','Rua dos Ipês','312','Casa','Conjunto Habitacional Inocente Vila Nova Júnior','Maringá','PR',1,'2025-09-13 00:44:35'),(2,5,'Apartamento','Patricia Alves de Oliveira','87010-255','Rua Tanaka','50','bloco 3 apto 21','Vila Emilia','Maringá','PR',1,'2025-09-13 00:46:52'),(3,2,'Estudo','Douglas Marcelo Monquero','87020-000','Avenida Colombo','6225','Senac','Zona 7','Maringá','PR',0,'2025-09-13 01:16:27'),(4,2,'Trabalho','Douglas','87010-100','Rua Antônio Valdir Zanutto','100','Sala 01','Jardim Novo Horizonte','Maringá','PR',0,'2025-09-13 01:37:52');
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
INSERT INTO `estoque` VALUES (1,1,44.000,5.000,'2025-09-17 00:08:49'),(2,2,48.000,5.000,'2025-09-16 23:45:10'),(3,3,95.450,10.000,'2025-09-17 01:11:07'),(4,4,95.000,12.000,'2025-09-17 18:30:33'),(5,5,30.000,5.000,'2025-09-17 18:30:33'),(6,6,58.000,10.000,'2025-09-17 21:50:17'),(7,7,40.000,8.000,'2025-09-17 18:30:33'),(8,8,80.000,10.000,'2025-09-17 18:30:33'),(9,9,70.000,10.000,'2025-09-17 18:30:33'),(10,10,55.000,8.000,'2025-09-17 18:30:33'),(11,11,120.000,15.000,'2025-09-17 18:36:25'),(12,12,45.000,8.000,'2025-09-17 18:36:25'),(13,13,150.000,20.000,'2025-09-17 18:36:25'),(14,14,65.000,10.000,'2025-09-17 18:36:25'),(15,15,120.000,25.000,'2025-09-17 18:36:25'),(16,16,80.000,12.000,'2025-09-17 18:36:25'),(17,17,70.000,10.000,'2025-09-17 18:36:25'),(18,18,40.000,6.000,'2025-09-17 18:36:25'),(19,19,55.000,10.000,'2025-09-17 18:36:25'),(20,20,45.000,8.000,'2025-09-17 18:36:25'),(21,21,60.000,10.000,'2025-09-17 18:36:25'),(22,22,80.000,15.000,'2025-09-17 18:36:25'),(23,23,65.000,12.000,'2025-09-17 18:36:25'),(24,24,70.000,10.000,'2025-09-17 18:36:25'),(25,25,50.000,8.000,'2025-09-17 18:36:25'),(26,26,90.000,10.000,'2025-09-23 18:27:40'),(27,27,120.000,18.000,'2025-09-17 18:36:25'),(28,28,110.000,15.000,'2025-09-17 18:36:25'),(29,29,130.000,20.000,'2025-09-17 18:36:25'),(30,30,115.000,18.000,'2025-09-17 18:36:25'),(31,31,70.000,10.000,'2025-09-17 18:36:25'),(32,32,90.000,15.000,'2025-09-17 18:36:25'),(33,33,140.000,25.000,'2025-09-17 18:36:25'),(34,34,200.000,30.000,'2025-09-17 18:36:25'),(35,35,178.000,25.000,'2025-09-24 19:42:16'),(36,36,58.000,8.000,'2025-09-23 18:12:53'),(37,37,89.500,20.000,'2025-09-18 17:58:11'),(38,38,85.000,18.000,'2025-09-17 18:36:25'),(39,39,117.000,25.000,'2025-09-17 23:00:22'),(40,40,60.000,10.000,'2025-09-17 18:36:25'),(41,41,70.000,12.000,'2025-09-17 18:36:25'),(42,42,107.000,15.000,'2025-09-17 23:00:22'),(43,43,80.000,12.000,'2025-09-17 18:37:33'),(44,44,116.000,20.000,'2025-09-18 18:59:18'),(45,45,74.000,10.000,'2025-09-24 18:31:09'),(46,46,88.000,12.000,'2025-09-17 23:00:22'),(47,47,72.000,12.000,'2025-09-23 18:16:20');
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
  KEY `idx_itempedido_produto_pedido` (`produto_id`,`pedido_id`),
  CONSTRAINT `fk_itempedido_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedido` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_itempedido_produto` FOREIGN KEY (`produto_id`) REFERENCES `produto` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Itens de pedido';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item_pedido`
--

LOCK TABLES `item_pedido` WRITE;
/*!40000 ALTER TABLE `item_pedido` DISABLE KEYS */;
INSERT INTO `item_pedido` VALUES (3,1,1,2.000,NULL,50.00,0.00),(4,1,2,1.000,NULL,20.00,0.00),(5,2,1,2.000,NULL,50.00,NULL),(6,2,2,1.000,NULL,20.00,NULL),(7,2,3,1.000,NULL,3.99,NULL),(8,3,1,1.000,NULL,50.00,NULL),(9,4,1,2.000,NULL,50.00,NULL),(10,4,2,1.000,NULL,20.00,NULL),(11,5,1,1.000,NULL,50.00,NULL),(12,6,3,3.550,NULL,3.99,NULL),(13,7,47,1.000,NULL,19.90,NULL),(14,7,46,1.000,NULL,16.90,NULL),(15,7,6,2.000,NULL,18.50,NULL),(16,8,39,3.000,NULL,2.99,NULL),(17,8,46,1.000,NULL,16.90,NULL),(18,8,44,3.000,NULL,8.49,NULL),(19,8,42,3.000,NULL,5.49,NULL),(20,9,47,3.000,NULL,19.90,NULL),(21,9,37,0.500,NULL,7.80,NULL),(22,10,26,2.000,NULL,5.49,NULL),(23,11,44,1.000,NULL,8.49,NULL),(24,12,47,3.000,NULL,19.90,NULL),(25,13,36,2.000,NULL,18.90,NULL),(26,14,47,1.000,NULL,19.90,NULL),(27,15,45,1.000,NULL,18.90,NULL),(28,16,35,2.000,NULL,2.99,NULL),(29,17,1,1.000,NULL,50.00,0.00),(38,31,30,1.006,NULL,9.90,0.00),(39,31,8,1.000,NULL,17.99,0.00),(40,33,28,4.000,NULL,6.90,0.00),(41,33,22,3.560,NULL,13.90,0.00),(42,63,30,1.000,NULL,9.90,0.00),(43,69,30,1.000,NULL,9.90,0.00);
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
  `terminal_id` int DEFAULT NULL,
  `turno_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_movcaixa_caixa` (`caixa_id`,`criado_em`),
  KEY `idx_movcaixa_pedido` (`pedido_id`),
  KEY `fk_movcaixa_terminal` (`terminal_id`),
  KEY `fk_movcaixa_turno` (`turno_id`),
  CONSTRAINT `fk_movcaixa_caixa` FOREIGN KEY (`caixa_id`) REFERENCES `caixa` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_movcaixa_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedido` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_movcaixa_terminal` FOREIGN KEY (`terminal_id`) REFERENCES `pdv_terminal` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_movcaixa_turno` FOREIGN KEY (`turno_id`) REFERENCES `pdv_turno` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Movimentações de caixa (vendas/sangria/suprimento)';
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
  KEY `idx_movestoque_origem_ref` (`origem`,`referencia_id`),
  CONSTRAINT `fk_movestoque_produto` FOREIGN KEY (`produto_id`) REFERENCES `produto` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Movimentações de estoque';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mov_estoque`
--

LOCK TABLES `mov_estoque` WRITE;
/*!40000 ALTER TABLE `mov_estoque` DISABLE KEYS */;
INSERT INTO `mov_estoque` VALUES (1,1,'saida',2.000,'pedido',2,'Saida por venda','2025-09-16 23:33:56'),(2,2,'saida',1.000,'pedido',2,'Saida por venda','2025-09-16 23:33:56'),(3,3,'saida',1.000,'pedido',2,'Saida por venda','2025-09-16 23:33:56'),(4,1,'saida',1.000,'pedido',3,'Saida por venda','2025-09-16 23:35:06'),(5,1,'saida',2.000,'pedido',4,'Saida por venda','2025-09-16 23:45:10'),(6,2,'saida',1.000,'pedido',4,'Saida por venda','2025-09-16 23:45:10'),(7,1,'saida',1.000,'pedido',5,'Saida por venda','2025-09-17 00:08:49'),(8,3,'saida',3.550,'pedido',6,'Saida por venda','2025-09-17 01:11:07'),(9,47,'saida',1.000,'pedido',7,'Saida por venda','2025-09-17 21:50:17'),(10,46,'saida',1.000,'pedido',7,'Saida por venda','2025-09-17 21:50:17'),(11,6,'saida',2.000,'pedido',7,'Saida por venda','2025-09-17 21:50:17'),(12,39,'saida',3.000,'pedido',8,'Saida por venda','2025-09-17 23:00:22'),(13,46,'saida',1.000,'pedido',8,'Saida por venda','2025-09-17 23:00:22'),(14,44,'saida',3.000,'pedido',8,'Saida por venda','2025-09-17 23:00:22'),(15,42,'saida',3.000,'pedido',8,'Saida por venda','2025-09-17 23:00:22'),(16,47,'saida',3.000,'pedido',9,'Saida por venda','2025-09-18 17:58:11'),(17,37,'saida',0.500,'pedido',9,'Saida por venda','2025-09-18 17:58:11'),(18,26,'saida',2.000,'pedido',10,'Saida por venda','2025-09-18 18:05:00'),(19,44,'saida',1.000,'pedido',11,'Saida por venda','2025-09-18 18:59:18'),(20,47,'saida',3.000,'pedido',12,'Saida por venda','2025-09-23 18:10:44'),(21,36,'saida',2.000,'pedido',13,'Saida por venda','2025-09-23 18:12:53'),(22,47,'saida',1.000,'pedido',14,'Saida por venda','2025-09-23 18:16:20'),(23,45,'saida',1.000,'pedido',15,'Saida por venda','2025-09-24 18:31:09'),(24,35,'saida',2.000,'pedido',16,'Saida por venda','2025-09-24 19:42:16'),(25,30,'saida',1.006,'pedido',31,'Saida por venda','2025-09-27 16:24:59'),(26,8,'saida',1.000,'pedido',31,'Saida por venda','2025-09-27 16:24:59'),(27,28,'saida',4.000,'pedido',33,'Saida por venda','2025-09-27 16:27:52'),(28,22,'saida',3.560,'pedido',33,'Saida por venda','2025-09-27 16:27:52'),(29,30,'saida',1.000,'pedido',63,'Saida por venda','2025-09-27 17:22:52'),(30,30,'saida',1.000,'pedido',69,'Saida por venda','2025-09-27 17:58:42');
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
  `token_hash` char(64) COLLATE utf8mb4_general_ci NOT NULL,
  `expires_at` datetime NOT NULL,
  `used_at` datetime DEFAULT NULL,
  `ip` varchar(45) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token_hash` (`token_hash`),
  KEY `cliente_id` (`cliente_id`),
  CONSTRAINT `fk_prt_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `cliente` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
INSERT INTO `password_reset_tokens` VALUES (4,2,'7b083921b5051b1e543dd73c4aad4265266bfce258ee64e81820d336d927abde','2025-09-25 21:45:27',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0','2025-09-25 17:45:27');
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
  `tipo` varchar(40) COLLATE utf8mb4_general_ci NOT NULL,
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
  `chave` varchar(60) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `protocolo` varchar(60) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `xml_path` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('autorizada','denegada','cancelada','em_processamento','erro') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'em_processamento',
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
  `bandeira` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `parcelas` int DEFAULT NULL,
  `nsu` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `autorizacao` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `adquirente` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`pagamento_id`),
  CONSTRAINT `fk_pagcartao_pagamento` FOREIGN KEY (`pagamento_id`) REFERENCES `pedido_pagamento` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pdv_pag_cartao`
--

LOCK TABLES `pdv_pag_cartao` WRITE;
/*!40000 ALTER TABLE `pdv_pag_cartao` DISABLE KEYS */;
INSERT INTO `pdv_pag_cartao` VALUES (5,'VISA',1,'NSU123','AUTH456','Demo');
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
  `banco_codigo` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `banco_nome` varchar(60) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `agencia` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `conta` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `numero_cheque` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
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
  `txid` varchar(60) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `qr_code` text COLLATE utf8mb4_general_ci,
  `status` enum('pendente','pago','expirado','cancelado') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pendente',
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
  `cpf_na_nota` varchar(14) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `observacao` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
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
INSERT INTO `pdv_pedido_meta` VALUES (17,1,1,1,NULL,NULL,'2025-09-25 15:34:26'),(22,1,1,1,NULL,NULL,'2025-09-27 11:43:51'),(23,1,1,1,NULL,NULL,'2025-09-27 12:02:48'),(24,1,1,1,NULL,NULL,'2025-09-27 12:03:13'),(25,1,1,1,NULL,NULL,'2025-09-27 12:08:27'),(26,1,1,1,NULL,NULL,'2025-09-27 12:16:29'),(27,1,1,1,NULL,NULL,'2025-09-27 12:25:56'),(28,1,1,1,NULL,NULL,'2025-09-27 12:37:52'),(29,1,1,1,NULL,NULL,'2025-09-27 13:21:58'),(30,1,1,1,NULL,NULL,'2025-09-27 13:22:10'),(31,1,1,1,NULL,NULL,'2025-09-27 13:22:17'),(32,1,1,1,NULL,NULL,'2025-09-27 13:25:01'),(33,1,1,1,NULL,NULL,'2025-09-27 13:25:29'),(34,1,1,1,NULL,NULL,'2025-09-27 13:27:54'),(35,1,1,1,NULL,NULL,'2025-09-27 13:32:21'),(36,1,1,1,NULL,NULL,'2025-09-27 13:35:51'),(37,1,1,1,NULL,NULL,'2025-09-27 13:38:55'),(38,1,1,1,NULL,NULL,'2025-09-27 13:39:01'),(39,1,1,1,NULL,NULL,'2025-09-27 13:39:45'),(40,1,1,1,NULL,NULL,'2025-09-27 13:40:44'),(41,1,1,1,NULL,NULL,'2025-09-27 13:43:29'),(42,1,1,1,NULL,NULL,'2025-09-27 13:45:39'),(43,1,1,1,NULL,NULL,'2025-09-27 13:47:48'),(44,1,1,1,NULL,NULL,'2025-09-27 13:48:13'),(45,1,1,1,NULL,NULL,'2025-09-27 13:48:25'),(46,1,1,1,NULL,NULL,'2025-09-27 13:48:45'),(47,1,1,1,NULL,NULL,'2025-09-27 13:53:25'),(48,1,1,1,NULL,NULL,'2025-09-27 13:54:07'),(49,1,1,1,NULL,NULL,'2025-09-27 13:55:40'),(50,1,1,1,NULL,NULL,'2025-09-27 14:00:36'),(51,1,1,1,NULL,NULL,'2025-09-27 14:01:25'),(52,1,1,1,NULL,NULL,'2025-09-27 14:05:04'),(53,1,1,1,NULL,NULL,'2025-09-27 14:05:18'),(54,1,1,1,NULL,NULL,'2025-09-27 14:05:30'),(55,1,1,1,NULL,NULL,'2025-09-27 14:06:24'),(56,1,1,1,NULL,NULL,'2025-09-27 14:18:43'),(60,1,1,1,NULL,NULL,'2025-09-27 14:22:14'),(63,1,1,1,NULL,NULL,'2025-09-27 14:22:20'),(67,1,1,1,NULL,NULL,'2025-09-27 14:26:06'),(68,1,1,1,NULL,NULL,'2025-09-27 14:57:12'),(69,1,1,1,NULL,NULL,'2025-09-27 14:57:29'),(71,1,1,1,NULL,NULL,'2025-09-27 15:04:23'),(72,1,1,1,NULL,NULL,'2025-09-27 16:04:12'),(73,1,1,1,NULL,NULL,'2025-09-27 16:07:01'),(74,1,1,1,NULL,NULL,'2025-09-28 11:36:49'),(75,1,1,1,NULL,NULL,'2025-09-28 13:53:19');
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
  `provedor` varchar(40) COLLATE utf8mb4_general_ci NOT NULL,
  `nsu` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `codigo_host` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `comprovante` text COLLATE utf8mb4_general_ci,
  `status` enum('aprovado','negado','cancelado','pendente') COLLATE utf8mb4_general_ci NOT NULL,
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
  `loja_id` bigint NOT NULL,
  `nome` varchar(60) COLLATE utf8mb4_general_ci NOT NULL,
  `identificador` varchar(60) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `identificador` (`identificador`),
  CONSTRAINT `pdv_terminal_chk_1` CHECK (json_valid(`config`))
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pdv_terminal`
--

LOCK TABLES `pdv_terminal` WRITE;
/*!40000 ALTER TABLE `pdv_terminal` DISABLE KEYS */;
INSERT INTO `pdv_terminal` VALUES (1,1,'Caixa 01','HOST-CAIXA01',1,NULL,'2025-09-25 15:33:50');
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
  `status` enum('aberto','fechado','cancelado') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'aberto',
  PRIMARY KEY (`id`),
  KEY `terminal_id` (`terminal_id`,`status`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pdv_turno`
--

LOCK TABLES `pdv_turno` WRITE;
/*!40000 ALTER TABLE `pdv_turno` DISABLE KEYS */;
INSERT INTO `pdv_turno` VALUES (1,1,1,1,'2025-09-25 15:34:11',100.00,'2025-09-25 22:03:56',150.00,'fechado'),(2,1,1,2,'2025-09-25 22:04:11',100.00,NULL,NULL,'aberto');
/*!40000 ALTER TABLE `pdv_turno` ENABLE KEYS */;
UNLOCK TABLES;

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
 1 AS `terminal`*/;
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
  `status` enum('novo','em_separacao','em_transporte','pronto','finalizado','cancelado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'novo',
  `canal` enum('online','pdv') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'online',
  `entrega` enum('retirada','entrega') COLLATE utf8mb4_unicode_ci NOT NULL,
  `pagamento` enum('na_entrega','pix','cartao','gateway') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'na_entrega',
  `subtotal` decimal(12,2) NOT NULL,
  `frete` decimal(12,2) NOT NULL DEFAULT '0.00',
  `desconto` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total` decimal(12,2) NOT NULL,
  `troco` decimal(12,2) NOT NULL DEFAULT '0.00',
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
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Pedidos da loja (online/retirada)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedido`
--

LOCK TABLES `pedido` WRITE;
/*!40000 ALTER TABLE `pedido` DISABLE KEYS */;
INSERT INTO `pedido` VALUES (1,5,1,'cancelado','online','entrega','pix',120.00,10.00,0.00,130.00,0.00,'PED-0001','2025-09-13 02:38:18','2025-09-25 19:13:55'),(2,2,NULL,'cancelado','online','retirada','pix',123.99,0.00,0.00,123.99,0.00,NULL,'2025-09-16 23:33:56','2025-09-25 19:13:55'),(3,2,NULL,'pronto','online','retirada','na_entrega',50.00,0.00,0.00,50.00,0.00,NULL,'2025-09-16 23:35:06','2025-09-25 19:13:55'),(4,2,1,'em_transporte','online','entrega','na_entrega',120.00,0.00,0.00,120.00,0.00,NULL,'2025-09-16 23:45:10','2025-09-25 19:13:55'),(5,2,NULL,'cancelado','online','retirada','na_entrega',50.00,0.00,0.00,50.00,0.00,NULL,'2025-09-17 00:08:49','2025-09-25 19:13:55'),(6,5,NULL,'em_transporte','online','retirada','na_entrega',14.16,0.00,0.00,14.16,0.00,NULL,'2025-09-17 01:11:07','2025-09-25 19:13:55'),(7,2,1,'em_transporte','online','entrega','na_entrega',73.80,0.00,0.00,73.80,0.00,NULL,'2025-09-17 21:50:17','2025-09-25 19:13:55'),(8,2,3,'em_transporte','online','entrega','gateway',67.81,0.00,0.00,67.81,0.00,NULL,'2025-09-17 23:00:22','2025-09-25 19:13:55'),(9,6,NULL,'pronto','online','retirada','pix',63.60,0.00,0.00,63.60,0.00,NULL,'2025-09-18 17:58:11','2025-09-25 19:13:55'),(10,2,NULL,'em_transporte','online','retirada','gateway',10.98,0.00,0.00,10.98,0.00,NULL,'2025-09-18 18:05:00','2025-09-25 19:13:55'),(11,2,NULL,'em_transporte','online','retirada','pix',8.49,0.00,0.00,8.49,0.00,NULL,'2025-09-18 18:59:18','2025-09-25 19:13:55'),(12,2,1,'em_transporte','online','entrega','pix',59.70,0.00,0.00,59.70,0.00,NULL,'2025-09-23 18:10:44','2025-09-25 19:13:55'),(13,2,NULL,'cancelado','online','retirada','pix',37.80,0.00,0.00,37.80,0.00,NULL,'2025-09-23 18:12:53','2025-09-25 19:13:55'),(14,2,NULL,'em_transporte','online','retirada','na_entrega',19.90,0.00,0.00,19.90,0.00,NULL,'2025-09-23 18:16:20','2025-09-25 19:13:55'),(15,2,NULL,'novo','online','retirada','pix',18.90,0.00,0.00,18.90,0.00,'PED-000015','2025-09-24 18:31:09','2025-09-25 19:13:55'),(16,2,NULL,'novo','online','retirada','pix',5.98,0.00,0.00,5.98,0.00,'PED-000016','2025-09-24 19:42:16','2025-09-25 19:13:55'),(17,1,NULL,'novo','pdv','retirada','na_entrega',50.00,0.00,0.00,50.00,0.00,NULL,'2025-09-25 18:34:19','2025-09-25 18:34:19'),(22,1,NULL,'novo','pdv','retirada','na_entrega',0.00,0.00,0.00,0.00,0.00,NULL,'2025-09-27 14:43:51','2025-09-27 14:43:51'),(23,1,NULL,'novo','pdv','retirada','na_entrega',0.00,0.00,0.00,0.00,0.00,NULL,'2025-09-27 15:02:48','2025-09-27 15:02:48'),(24,1,NULL,'novo','pdv','retirada','na_entrega',0.00,0.00,0.00,0.00,0.00,NULL,'2025-09-27 15:03:13','2025-09-27 15:03:13'),(25,1,NULL,'novo','pdv','retirada','na_entrega',0.00,0.00,0.00,0.00,0.00,NULL,'2025-09-27 15:08:27','2025-09-27 15:08:27'),(26,1,NULL,'novo','pdv','retirada','na_entrega',0.00,0.00,0.00,0.00,0.00,NULL,'2025-09-27 15:16:29','2025-09-27 15:16:29'),(27,1,NULL,'novo','pdv','retirada','na_entrega',0.00,0.00,0.00,0.00,0.00,NULL,'2025-09-27 15:25:56','2025-09-27 15:25:56'),(28,1,NULL,'novo','pdv','retirada','na_entrega',0.00,0.00,0.00,0.00,0.00,NULL,'2025-09-27 15:37:52','2025-09-27 15:37:52'),(29,1,NULL,'novo','pdv','retirada','na_entrega',0.00,0.00,0.00,0.00,0.00,NULL,'2025-09-27 16:21:58','2025-09-27 16:21:58'),(30,1,NULL,'novo','pdv','retirada','na_entrega',0.00,0.00,0.00,0.00,0.00,NULL,'2025-09-27 16:22:10','2025-09-27 16:22:10'),(31,1,NULL,'finalizado','pdv','retirada','na_entrega',27.95,0.00,0.10,27.85,0.00,NULL,'2025-09-27 16:22:17','2025-09-27 16:24:59'),(32,1,NULL,'novo','pdv','retirada','na_entrega',0.00,0.00,0.00,0.00,0.00,NULL,'2025-09-27 16:25:01','2025-09-27 16:25:01'),(33,1,NULL,'finalizado','pdv','retirada','na_entrega',77.08,0.00,0.00,77.08,0.00,NULL,'2025-09-27 16:25:29','2025-09-27 16:27:52'),(34,1,NULL,'novo','pdv','retirada','na_entrega',0.00,0.00,0.00,0.00,0.00,NULL,'2025-09-27 16:27:54','2025-09-27 16:27:54'),(35,1,NULL,'novo','pdv','retirada','na_entrega',0.00,0.00,0.00,0.00,0.00,NULL,'2025-09-27 16:32:21','2025-09-27 16:32:21'),(36,1,NULL,'novo','pdv','retirada','na_entrega',0.00,0.00,0.00,0.00,0.00,NULL,'2025-09-27 16:35:51','2025-09-27 16:35:51'),(37,1,NULL,'novo','pdv','retirada','na_entrega',0.00,0.00,0.00,0.00,0.00,NULL,'2025-09-27 16:38:55','2025-09-27 16:38:55'),(38,1,NULL,'novo','pdv','retirada','na_entrega',0.00,0.00,0.00,0.00,0.00,NULL,'2025-09-27 16:39:01','2025-09-27 16:39:01'),(39,1,NULL,'novo','pdv','retirada','na_entrega',0.00,0.00,0.00,0.00,0.00,NULL,'2025-09-27 16:39:45','2025-09-27 16:39:45'),(40,1,NULL,'novo','pdv','retirada','na_entrega',0.00,0.00,0.00,0.00,0.00,NULL,'2025-09-27 16:40:44','2025-09-27 16:40:44'),(41,1,NULL,'novo','pdv','retirada','na_entrega',0.00,0.00,0.00,0.00,0.00,NULL,'2025-09-27 16:43:29','2025-09-27 16:43:29'),(42,1,NULL,'novo','pdv','retirada','na_entrega',0.00,0.00,0.00,0.00,0.00,NULL,'2025-09-27 16:45:39','2025-09-27 16:45:39'),(43,1,NULL,'novo','pdv','retirada','na_entrega',0.00,0.00,0.00,0.00,0.00,NULL,'2025-09-27 16:47:48','2025-09-27 16:47:48'),(44,1,NULL,'novo','pdv','retirada','na_entrega',0.00,0.00,0.00,0.00,0.00,NULL,'2025-09-27 16:48:13','2025-09-27 16:48:13'),(45,1,NULL,'novo','pdv','retirada','na_entrega',0.00,0.00,0.00,0.00,0.00,NULL,'2025-09-27 16:48:25','2025-09-27 16:48:25'),(46,1,NULL,'novo','pdv','retirada','na_entrega',0.00,0.00,0.00,0.00,0.00,NULL,'2025-09-27 16:48:45','2025-09-27 16:48:45'),(47,1,NULL,'novo','pdv','retirada','na_entrega',0.00,0.00,0.00,0.00,0.00,NULL,'2025-09-27 16:53:25','2025-09-27 16:53:25'),(48,1,NULL,'novo','pdv','retirada','na_entrega',0.00,0.00,0.00,0.00,0.00,NULL,'2025-09-27 16:54:07','2025-09-27 16:54:07'),(49,1,NULL,'novo','pdv','retirada','na_entrega',0.00,0.00,0.00,0.00,0.00,NULL,'2025-09-27 16:55:40','2025-09-27 16:55:40'),(50,1,NULL,'novo','pdv','retirada','na_entrega',0.00,0.00,0.00,0.00,0.00,NULL,'2025-09-27 17:00:36','2025-09-27 17:00:36'),(51,1,NULL,'novo','pdv','retirada','na_entrega',0.00,0.00,0.00,0.00,0.00,NULL,'2025-09-27 17:01:25','2025-09-27 17:01:25'),(52,1,NULL,'novo','pdv','retirada','na_entrega',0.00,0.00,0.00,0.00,0.00,NULL,'2025-09-27 17:05:04','2025-09-27 17:05:04'),(53,1,NULL,'novo','pdv','retirada','na_entrega',0.00,0.00,0.00,0.00,0.00,NULL,'2025-09-27 17:05:18','2025-09-27 17:05:18'),(54,1,NULL,'novo','pdv','retirada','na_entrega',0.00,0.00,0.00,0.00,0.00,NULL,'2025-09-27 17:05:30','2025-09-27 17:05:30'),(55,1,NULL,'novo','pdv','retirada','na_entrega',0.00,0.00,0.00,0.00,0.00,NULL,'2025-09-27 17:06:24','2025-09-27 17:06:24'),(56,1,NULL,'novo','pdv','retirada','na_entrega',0.00,0.00,0.00,0.00,0.00,NULL,'2025-09-27 17:18:43','2025-09-27 17:18:43'),(60,1,NULL,'novo','pdv','retirada','na_entrega',0.00,0.00,0.00,0.00,0.00,NULL,'2025-09-27 17:22:14','2025-09-27 17:22:14'),(63,1,NULL,'finalizado','pdv','retirada','na_entrega',9.90,0.00,0.00,9.90,0.00,NULL,'2025-09-27 17:22:20','2025-09-27 17:22:52'),(67,1,NULL,'novo','pdv','retirada','na_entrega',0.00,0.00,0.00,0.00,0.00,NULL,'2025-09-27 17:26:06','2025-09-27 17:26:06'),(68,1,NULL,'novo','pdv','retirada','na_entrega',0.00,0.00,0.00,0.00,0.00,NULL,'2025-09-27 17:57:12','2025-09-27 17:57:12'),(69,1,NULL,'finalizado','pdv','retirada','na_entrega',9.90,0.00,0.00,9.90,0.00,NULL,'2025-09-27 17:57:29','2025-09-27 17:58:42'),(71,1,NULL,'novo','pdv','retirada','na_entrega',0.00,0.00,0.00,0.00,0.00,NULL,'2025-09-27 18:04:23','2025-09-27 18:04:23'),(72,1,NULL,'novo','pdv','retirada','na_entrega',0.00,0.00,0.00,0.00,0.00,NULL,'2025-09-27 19:04:12','2025-09-27 19:04:12'),(73,1,NULL,'novo','pdv','retirada','na_entrega',0.00,0.00,0.00,0.00,0.00,NULL,'2025-09-27 19:07:01','2025-09-27 19:07:01'),(74,1,NULL,'novo','pdv','retirada','na_entrega',0.00,0.00,0.00,0.00,0.00,NULL,'2025-09-28 14:36:49','2025-09-28 14:36:49'),(75,1,NULL,'novo','pdv','retirada','na_entrega',0.00,0.00,0.00,0.00,0.00,NULL,'2025-09-28 16:53:19','2025-09-28 16:53:19');
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
  `tipo` enum('dinheiro','credito','debito','cheque','pix') COLLATE utf8mb4_general_ci NOT NULL,
  `valor` decimal(12,2) NOT NULL,
  `criado_em` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `pedido_id` (`pedido_id`),
  KEY `idx_pedidopag_pedido_tipo` (`pedido_id`,`tipo`),
  CONSTRAINT `fk_pedidopag_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedido` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedido_pagamento`
--

LOCK TABLES `pedido_pagamento` WRITE;
/*!40000 ALTER TABLE `pedido_pagamento` DISABLE KEYS */;
INSERT INTO `pedido_pagamento` VALUES (5,17,'credito',30.00,'2025-09-25 15:38:31'),(6,17,'dinheiro',20.00,'2025-09-25 15:38:45');
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
INSERT INTO `pedido_pix` VALUES (15,1341342135,'pending','pending_waiting_transfer','00020126580014br.gov.bcb.pix0136b76aa9c2-2ec4-4110-954e-ebfe34f05b61520400005303986540518.905802BR5916DOYGlyn.ezwRlORO6006MaZWMH62230519mpqrinter13413421356304A645','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','https://www.mercadopago.com.br/sandbox/payments/1341342135/ticket?caller_id=1406372264&hash=bdbce810-8ff9-4d46-a3c8-bcb5879fee4d','2025-09-25 19:38:43','2025-09-24 19:38:43','2025-09-24 19:41:44'),(16,1341340273,'pending','pending_waiting_transfer','00020126580014br.gov.bcb.pix0136b76aa9c2-2ec4-4110-954e-ebfe34f05b6152040000530398654045.985802BR5916DOYGlyn.ezwRlORO6006MaZWMH62230519mpqrinter134134027363044099','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','https://www.mercadopago.com.br/sandbox/payments/1341340273/ticket?caller_id=1406372264&hash=bed8b231-ee63-4157-b7e7-eae082b94e33','2025-09-25 19:42:18','2025-09-24 19:42:18','2025-09-24 19:42:18');
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
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Histórico de preços por produto';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `preco`
--

LOCK TABLES `preco` WRITE;
/*!40000 ALTER TABLE `preco` DISABLE KEYS */;
INSERT INTO `preco` VALUES (1,1,50.00,NULL,NULL,NULL,'2025-09-13 03:27:42'),(2,2,20.00,NULL,NULL,NULL,'2025-09-13 03:27:42'),(3,3,5.99,3.99,NULL,NULL,'2025-09-16 23:32:39'),(4,4,4.99,NULL,NULL,NULL,'2025-09-17 18:30:33'),(5,5,24.90,NULL,NULL,NULL,'2025-09-17 18:30:33'),(6,6,18.50,NULL,NULL,NULL,'2025-09-17 18:30:33'),(7,7,32.90,29.90,'2025-09-17 15:30:33','2025-09-24 15:30:33','2025-09-17 18:30:33'),(8,8,17.99,NULL,NULL,NULL,'2025-09-17 18:30:33'),(9,9,9.49,NULL,NULL,NULL,'2025-09-17 18:30:33'),(10,10,22.50,NULL,NULL,NULL,'2025-09-17 18:30:33'),(11,11,6.99,NULL,NULL,NULL,'2025-09-17 18:36:25'),(12,12,19.90,NULL,NULL,NULL,'2025-09-17 18:36:25'),(13,13,2.89,NULL,NULL,NULL,'2025-09-17 18:36:25'),(14,14,8.49,NULL,NULL,NULL,'2025-09-17 18:36:25'),(15,15,16.90,NULL,NULL,NULL,'2025-09-17 18:36:25'),(16,16,11.90,NULL,NULL,NULL,'2025-09-17 18:36:25'),(17,17,9.50,NULL,NULL,NULL,'2025-09-17 18:36:25'),(18,18,14.90,NULL,NULL,NULL,'2025-09-17 18:36:25'),(19,19,21.90,NULL,NULL,NULL,'2025-09-17 18:36:25'),(20,20,29.90,NULL,NULL,NULL,'2025-09-17 18:36:25'),(21,21,19.90,NULL,NULL,NULL,'2025-09-17 18:36:25'),(22,22,13.90,NULL,NULL,NULL,'2025-09-17 18:36:25'),(23,23,44.90,NULL,NULL,NULL,'2025-09-17 18:36:25'),(24,24,6.50,NULL,NULL,NULL,'2025-09-17 18:36:25'),(25,25,27.90,NULL,NULL,NULL,'2025-09-17 18:36:25'),(26,26,5.49,NULL,NULL,NULL,'2025-09-17 18:36:25'),(27,27,7.99,NULL,NULL,NULL,'2025-09-17 18:36:25'),(28,28,6.90,NULL,NULL,NULL,'2025-09-17 18:36:25'),(29,29,5.60,NULL,NULL,NULL,'2025-09-17 18:36:25'),(30,30,9.90,NULL,NULL,NULL,'2025-09-17 18:36:25'),(31,31,32.90,NULL,NULL,NULL,'2025-09-17 18:36:25'),(32,32,3.99,NULL,NULL,NULL,'2025-09-17 18:36:25'),(33,33,8.99,NULL,NULL,NULL,'2025-09-17 18:36:25'),(34,34,5.99,NULL,NULL,NULL,'2025-09-17 18:36:25'),(35,35,2.99,NULL,NULL,NULL,'2025-09-17 18:36:25'),(36,36,18.90,NULL,NULL,NULL,'2025-09-17 18:36:25'),(37,37,7.80,NULL,NULL,NULL,'2025-09-17 18:36:25'),(38,38,9.50,NULL,NULL,NULL,'2025-09-17 18:36:25'),(39,39,2.99,NULL,NULL,NULL,'2025-09-17 18:36:25'),(40,40,27.90,NULL,NULL,NULL,'2025-09-17 18:36:25'),(41,41,19.90,NULL,NULL,NULL,'2025-09-17 18:36:25'),(42,42,5.49,NULL,NULL,NULL,'2025-09-17 18:36:25'),(43,43,24.90,NULL,NULL,NULL,'2025-09-17 18:37:33'),(44,44,8.49,NULL,NULL,NULL,'2025-09-17 18:38:08'),(45,45,18.90,NULL,NULL,NULL,'2025-09-17 18:38:08'),(46,46,16.90,NULL,NULL,NULL,'2025-09-17 18:38:08'),(47,47,19.90,NULL,NULL,NULL,'2025-09-17 18:38:08'),(48,26,9.90,4.99,NULL,NULL,'2025-09-18 18:03:13');
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
INSERT INTO `produto` VALUES (1,'Arroz Tio João 5kg Tipo 1','ARROZ5','789000000001',1,2,1,'Pacote 5kg','uploads/produtos/19bba88ad394ab08-1758144441.jpg',1,0,'2025-09-13 03:26:58'),(2,'Feijão Carioca 1kg','FEIJAO1','789000000002',1,1,1,'Pacote 1kg','uploads/produtos/4a708d9feef7daea-1758144868.jpg',1,0,'2025-09-13 03:26:58'),(3,'Banana Prata','bananaprta',NULL,1,1,2,'banana prata','uploads/produtos/f0a37618390f6d0e-1758144487.jpg',1,0,'2025-09-16 23:32:39'),(4,'Leite Integral Aurora 1L','LEITEAUR1L','7891150043210',5,3,4,'Leite UHT integral catarinense.','uploads/produtos/73bf2bce587591d5-1758145111.jpg',1,0,'2025-09-17 18:30:33'),(5,'Queijo Minas Frescal 500g','QUEIJOMIN500','7892222000456',5,9,3,'Queijo minas frescal tradicional.','uploads/produtos/7bcd1cffea6f2b89-1758145424.jpg',1,0,'2025-09-17 18:30:33'),(6,'Pao de Queijo Forno de Minas 400g','PAOQFMIN400','7896004001234',6,5,3,'Pao de queijo congelado pronto para assar.','uploads/produtos/0ddb113ff8420180-1758145249.jpg',1,0,'2025-09-17 18:30:33'),(7,'Coxinha Congelada Perdigao 1kg','COXPERD1KG','7892300005678',6,4,2,'Coxinha de frango congelada, pacote 1kg.','uploads/produtos/23ee276665706384-1758144733.jpg',1,0,'2025-09-17 18:30:33'),(8,'Cafe Pilao Tradicional 500g','CAFEPIL500','7894900012346',3,6,3,'Cafe torrado e moido Pilao tradicional.','uploads/produtos/2245b3868c65dcdb-1758144622.jpg',1,0,'2025-09-17 18:30:33'),(9,'Suco de Laranja Do Bem 1L','SUCLDMB1L','7891991009871',4,7,4,'Suco integral de laranja, sem acucar.','uploads/produtos/be237b0ce735aead-1758145631.jpg',1,0,'2025-09-17 18:30:33'),(10,'Erva Mate Barrao 1kg','ERVABARR1K','7897151700150',4,8,2,'Erva mate para chimarrao, moagem grossa.','uploads/produtos/415305279c25b91c-1758144807.jpg',1,0,'2025-09-17 18:30:33'),(11,'Leite Condensado Mooca 395g','LEITECOND395','7891000054321',1,17,3,'Leite condensado tradicional para sobremesas.','uploads/produtos/5f02ceaf8e012dce-1758145062.jpg',1,0,'2025-09-17 18:36:25'),(12,'Manteiga Aviacao 200g','MANTAVI200','7896034501234',5,18,3,'Manteiga extra com sal, pote 200g.','uploads/produtos/d77a74fe15e26e60-1758145198.jpg',1,0,'2025-09-17 18:36:25'),(13,'Iogurte Natural Nestle 170g','IOGUNEST170','7891000256780',5,17,3,'Iogurte natural integral copo 170g.','uploads/produtos/fb375537b706afbf-1758144960.jpg',1,0,'2025-09-17 18:36:25'),(14,'Requeijao Cremoso Tirolez 200g','REQTIROL200','7896036001236',5,40,3,'Requeijao cremoso tradicional.','uploads/produtos/f1de563137b70226-1758145538.jpg',1,0,'2025-09-17 18:36:25'),(15,'Pao Frances kg','PAOFRANCESKG',NULL,3,41,2,'Pao frances assado diariamente.','uploads/produtos/9e2ff9ad86c15929-1758145278.jpg',1,1,'2025-09-17 18:36:25'),(16,'Pao Integral Wickbold 500g','PAOWICK500','7896004005677',3,41,3,'Pao integral fatiado com graos.','uploads/produtos/5c5119a1626f3775-1758145315.jpg',1,0,'2025-09-17 18:36:25'),(17,'Bisnaguinha Pullman 300g','BISNPULL300','7891910007654',3,39,3,'Pao tipo bisnaguinha fofinho.','uploads/produtos/7ffb5e10610578df-1758144540.jpg',1,0,'2025-09-17 18:36:25'),(18,'Bolo de Milho Congelado Seara 400g','BOLOSMIL400','7894904003456',6,19,3,'Bolo de milho congelado pronto.','uploads/produtos/f29f2dbd589c654d-1758144598.jpg',1,0,'2025-09-17 18:36:25'),(19,'Lasanha Sadia Bolonhesa 600g','LASASAD600','7891810009870',6,20,3,'Lasanha congelada sabor bolonhesa.','uploads/produtos/64043b572cf427fa-1758145029.jpg',1,0,'2025-09-17 18:36:25'),(20,'Batata Palito McCain 2kg','BATAMCC2KG','7894904500123',6,21,2,'Batata palito congelada embalagens 2kg.','uploads/produtos/f9776ff2932f154a-1758144515.jpg',1,0,'2025-09-17 18:36:25'),(21,'Pizza Calabresa Perdigao 460g','PIZZAPER460','7892300056784',6,4,3,'Pizza congelada sabor calabresa.','uploads/produtos/2f8c5b6a7af26ea7-1758145381.jpg',1,0,'2025-09-17 18:36:25'),(22,'Frango Inteiro Congelado Seara 2kg','FRANGSEAR2K','7894900303256',8,19,2,'Frango inteiro congelado.','uploads/produtos/31b37116840c5140-1758144924.jpg',1,0,'2025-09-17 18:36:25'),(23,'Contra File Bovino kg','CONTRAFILKG',NULL,8,20,2,'Corte bovino contra file fresco.','uploads/produtos/22b3a7446fafd1ed-1758144704.jpg',1,1,'2025-09-17 18:36:25'),(24,'Mortadela Seara Fatiada 200g','MORTSEAR200','7894900001235',3,19,3,'Mortadela fatiada classica 200g.','uploads/produtos/c68b8ad928fc48a9-1758145221.jpg',1,0,'2025-09-17 18:36:25'),(25,'Cafe Soluvel Nescafe 200g','CAFESOL200','7891000103567',1,17,3,'Cafe soluvel tradicional 200g.','uploads/produtos/b9eabed72a0ed825-1758144647.jpg',1,0,'2025-09-17 18:36:25'),(26,'Acucar Refinado Uniao 1kg','ACUCUNIA1K','7891021000017',1,24,6,'Acucar refinado cristal fino 1kg.','uploads/produtos/1cbb6b33fa6d758f-1758144171.jpg',1,0,'2025-09-17 18:36:25'),(27,'Feijao Preto Sao Joao 1kg','FEIJSAO1KG','7896028701234',1,8,2,'Feijao preto tipo 1.','uploads/produtos/a07c720ba554409f-1758144903.jpg',1,0,'2025-09-17 18:36:25'),(28,'Farinha de Trigo Renata 1kg','FARINREN1K','7896102502345',1,23,2,'Farinha de trigo especial.','uploads/produtos/171a00a459bce0d6-1758144835.jpg',1,0,'2025-09-17 18:36:25'),(29,'Macarrao Espaguete Galo 500g','MACGAL500','7891234009876',1,22,3,'Macarrao espaguete n10.','uploads/produtos/0db02103febae097-1758145164.jpg',1,0,'2025-09-17 18:36:25'),(30,'Arroz Integral Tio Joao 1kg','ARROZINT1K','7896079901233',1,8,2,'Arroz integral grao longo.','uploads/produtos/43cd816d30f8a223-1758144389.jpg',1,0,'2025-09-17 18:36:25'),(31,'Azeite Extra Virgem Gallo 500ml','AZEIGAL500','7891107004567',1,25,5,'Azeite portugues extra virgem 0.5L.','uploads/produtos/750e131763fc9979-1758144467.jpg',1,0,'2025-09-17 18:36:25'),(32,'Vinagre de Alcool Castelo 750ml','VINACAST750','7891040003456',1,26,5,'Vinagre de alcool culinario.','uploads/produtos/290b4c439da511f5-1758145730.jpg',1,0,'2025-09-17 18:36:25'),(33,'Refrigerante Guarana Antarctica 2L','REFRGAU2L','7891991012345',4,27,4,'Refrigerante guarana garrafa 2 litros.','uploads/produtos/a43734bcee9d7119-1758145504.jpg',1,0,'2025-09-17 18:36:25'),(34,'Cerveja Heineken Long Neck 330ml','CERVHEI330','7894321654321',4,28,5,'Cerveja premium long neck 330ml.','uploads/produtos/6f5fb3cd68805d63-1758144677.jpg',1,0,'2025-09-17 18:36:25'),(35,'Agua Mineral Crystal 1.5L','AGCRYS15L','7894900223456',4,29,4,'Agua mineral sem gas 1.5L.','uploads/produtos/618fcdb05930e5da-1758144218.jpg',1,0,'2025-09-17 18:36:25'),(36,'Suco de Uva Aurora Integral 1.5L','SUCUAUR15L','7891149101234',4,3,4,'Suco de uva integral 1.5L.','uploads/produtos/374bf8811bebfb52-1758145671.jpg',1,0,'2025-09-17 18:36:25'),(37,'Maca Gala kg','MACAGALAKG',NULL,7,3,2,'Maca gala selecionada.','uploads/produtos/539441599911698d-1758145137.jpg',1,1,'2025-09-17 18:36:25'),(38,'Tomate Italiano kg','TOMATITALKG',NULL,7,3,2,'Tomate italiano fresco.','uploads/produtos/4ffaeaa623b81c0e-1758145704.jpg',1,1,'2025-09-17 18:36:25'),(39,'Alface Crespa unidade','ALFACECRESP',NULL,7,3,1,'Alface crespa colhida no dia.','uploads/produtos/555e5020b133c69c-1758144307.jpg',1,0,'2025-09-17 18:36:25'),(40,'Sabao em Po OMO Lavagem Perfeita 1.6kg','SABOMO16KG','7891150067890',9,33,1,'Sabao em po lavagem perfeita 1.6kg.','uploads/produtos/0833a3ad4ef5bd7c-1758145567.jpg',1,0,'2025-09-17 18:36:25'),(41,'Amaciante Comfort Concentrado 2L','AMACCOMF2L','7891021006543',9,31,4,'Amaciante concentrado fragrancia original.','uploads/produtos/27dce4b8e089bb38-1758144361.jpg',1,0,'2025-09-17 18:36:25'),(42,'Desinfetante Veja Multiuso 500ml','DESINFVEJA500','7891035009876',9,32,5,'Desinfetante multiuso perfumado.','uploads/produtos/99e4e24d1ae75b2d-1758144781.jpg',1,0,'2025-09-17 18:36:25'),(43,'Papel Higienico Neve Folha Dupla 12x30m','PAPNEVE12','7896079904567',10,34,6,'Papel higienico folha dupla pacote 12 rolos.','uploads/produtos/1e3ca05300b80d91-1758145347.jpg',1,0,'2025-09-17 18:37:33'),(44,'Creme Dental Colgate Total 90g','CREMCOLG90','7891000098765',10,35,3,'Creme dental protecao total 12h.','uploads/produtos/d3d437910e1596f5-1758144757.jpg',1,0,'2025-09-17 18:38:08'),(45,'Shampoo Pantene Liso Extremo 400ml','SHAMPANT400','7891021007890',10,36,5,'Shampoo pantene liso extremo 400ml.','uploads/produtos/282e2010f944bc91-1758145596.jpg',1,0,'2025-09-17 18:38:08'),(46,'Racao Dog Chow Adulto 1kg','RACDOGCH1K','7896044023456',11,37,6,'Racao seca premium para cães adultos.','uploads/produtos/50514bce6ca260ff-1758145454.jpg',1,0,'2025-09-17 18:38:08'),(47,'Racao Whiskas Carne 1kg','RACWHISK1K','7896021109876',11,38,12,'Racao seca para gatos sabor carne.','uploads/produtos/e2ce2c04b5eaae15-1758145479.jpg',1,0,'2025-09-17 18:38:08');
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
INSERT INTO `usuario` VALUES (1,'João Ferlini','operador@mercado.local','18c897bfc3cfce2b6d13cb154176b52b45253de6d82abe848a79d90f81e4f441','operador',1,'2025-09-10 22:40:01'),(2,'Administrador','admin@mercado.local','a14b819a2867d23c914df366addc22a5567d341b61b393878e476846d1b39e2c','admin',1,'2025-09-10 22:40:44'),(3,'Douglas Marcelo Monquero','douglas.monquero@gmail.com','$2y$10$I5Oyd5DlUqmml0JsV/5c3uGBi8UCv.ScW73U.LE5ukslxtUkQt4X.','admin',1,'2025-09-10 22:45:37'),(4,'Patricia Alves de Oliveira','paty@gatinha.com.br','$2y$10$I5Oyd5DlUqmml0JsV/5c3uGBi8UCv.ScW73U.LE5ukslxtUkQt4X.','cliente',1,'2025-09-10 22:46:41'),(5,'Lucas Vinicius','lucas@email.com','$2y$10$INlbktG5YUVvhJbEyyVSwO5f9inu1C6L.kKRQi3ocVHyEzzgUUU62','gerente',1,'2025-09-12 22:52:31'),(6,'Douglas Cliente','cliente@gmail.com','$2y$10$./OhFnHDYC504U1Oe9.wku8B9V/WHzkDnEwrEMqRFsobkSuIxRivy','cliente',1,'2025-09-18 17:56:35');
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
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`dba`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `pdv_caixa_resumo` AS select `p`.`id` AS `pedido_id`,`pm`.`tipo` AS `tipo`,`pm`.`valor` AS `valor`,`mca`.`tipo` AS `mov_tipo`,`meta`.`terminal_id` AS `terminal_id`,`meta`.`turno_id` AS `turno_id`,`p`.`troco` AS `troco`,`p`.`total` AS `total`,`p`.`criado_em` AS `criado_em` from (((`pedido` `p` join `pdv_pedido_meta` `meta` on((`meta`.`pedido_id` = `p`.`id`))) left join `pedido_pagamento` `pm` on((`pm`.`pedido_id` = `p`.`id`))) left join `mov_caixa` `mca` on((`mca`.`pedido_id` = `p`.`id`))) where (`p`.`canal` = 'pdv') */;
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
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`dba`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `pdv_turnos_abertos` AS select `t`.`id` AS `id`,`t`.`terminal_id` AS `terminal_id`,`t`.`operador_id` AS `operador_id`,`t`.`caixa_id` AS `caixa_id`,`t`.`aberto_em` AS `aberto_em`,`t`.`valor_inicial` AS `valor_inicial`,`t`.`fechado_em` AS `fechado_em`,`t`.`valor_fechamento` AS `valor_fechamento`,`t`.`status` AS `status`,`term`.`nome` AS `terminal` from (`pdv_turno` `t` join `pdv_terminal` `term` on((`term`.`id` = `t`.`terminal_id`))) where (`t`.`status` = 'aberto') */;
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
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`dba`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `pedido_pdv` AS select `pedido`.`id` AS `id`,`pedido`.`cliente_id` AS `cliente_id`,`pedido`.`endereco_id` AS `endereco_id`,`pedido`.`status` AS `status`,`pedido`.`canal` AS `canal`,`pedido`.`entrega` AS `entrega`,`pedido`.`pagamento` AS `pagamento`,`pedido`.`subtotal` AS `subtotal`,`pedido`.`frete` AS `frete`,`pedido`.`desconto` AS `desconto`,`pedido`.`total` AS `total`,`pedido`.`troco` AS `troco`,`pedido`.`codigo_externo` AS `codigo_externo`,`pedido`.`criado_em` AS `criado_em`,`pedido`.`atualizado_em` AS `atualizado_em` from `pedido` where (`pedido`.`canal` = 'pdv') */;
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

-- Dump completed on 2025-09-28 14:02:37
