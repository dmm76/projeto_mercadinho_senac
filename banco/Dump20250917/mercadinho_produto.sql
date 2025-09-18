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
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-09-17 22:27:50
