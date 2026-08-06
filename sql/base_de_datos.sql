-- MySQL dump 10.13  Distrib 8.0.44, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: sonido_interior
-- ------------------------------------------------------
-- Server version	8.0.44

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
-- Table structure for table `carrito`
--

DROP TABLE IF EXISTS `carrito`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `carrito` (
  `id_carrito` int NOT NULL AUTO_INCREMENT,
  `fecha_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_usuario` int NOT NULL,
  PRIMARY KEY (`id_carrito`),
  UNIQUE KEY `uq_carrito_usuario` (`id_usuario`),
  CONSTRAINT `fk_carrito_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carrito`
--

LOCK TABLES `carrito` WRITE;
/*!40000 ALTER TABLE `carrito` DISABLE KEYS */;
INSERT INTO `carrito` VALUES (1,'2026-07-30 13:40:12',1),(2,'2026-07-31 11:05:29',2),(3,'2026-07-31 12:15:37',3),(4,'2026-08-01 18:53:02',4);
/*!40000 ALTER TABLE `carrito` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carrito_producto`
--

DROP TABLE IF EXISTS `carrito_producto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `carrito_producto` (
  `id_carrito_producto` int NOT NULL AUTO_INCREMENT,
  `cantidad` int NOT NULL DEFAULT '1',
  `precio_unitario` decimal(10,2) NOT NULL,
  `id_carrito` int NOT NULL,
  `id_producto` int NOT NULL,
  PRIMARY KEY (`id_carrito_producto`),
  UNIQUE KEY `uq_carrito_producto` (`id_carrito`,`id_producto`),
  KEY `fk_carritoprod_producto` (`id_producto`),
  CONSTRAINT `fk_carritoprod_carrito` FOREIGN KEY (`id_carrito`) REFERENCES `carrito` (`id_carrito`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_carritoprod_producto` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `chk_carritoprod_cantidad` CHECK ((`cantidad` > 0))
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carrito_producto`
--

LOCK TABLES `carrito_producto` WRITE;
/*!40000 ALTER TABLE `carrito_producto` DISABLE KEYS */;
/*!40000 ALTER TABLE `carrito_producto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categorias`
--

DROP TABLE IF EXISTS `categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categorias` (
  `id_categoria` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias`
--

LOCK TABLES `categorias` WRITE;
/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
INSERT INTO `categorias` VALUES (1,'Cuencos pequeños','Cuencos tibetanos de tamaño reducido, ideales para iniciación y meditación personal.',1),(2,'Cuencos medianos','Cuencos equilibrados con una vibración prolongada, perfectos para terapias de sonido.',1),(3,'Cuencos grandes','Cuencos de gran formato con sonidos graves y profundos, excelentes para sesiones grupales.',1),(4,'Cojines','Por si no era tontería la movida esta del cuenco que suena, ahora también tienes el cojín, para que no le falte de na, que no que no.',1),(5,'Mazas','Para pegarte de hostias si te portas mal.',1),(6,'Cuencos grabados.','Pues nada que dijimos que no sabíamos que hacer ya con los cuencos para darle bombo, y el tonto del pueblo se puso a grabarlos para darle una vuelta de tuerca más al asunto.',1),(7,'tontos','tontos',0),(8,'Sets de meditación','Conjunto de utensilios para iniciarte en el estado de trance y elevarte como el humo azul.',1);
/*!40000 ALTER TABLE `categorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detalle_pedido`
--

DROP TABLE IF EXISTS `detalle_pedido`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detalle_pedido` (
  `id_detalle` int NOT NULL AUTO_INCREMENT,
  `cantidad` int NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `id_pedido` int NOT NULL,
  `id_producto` int NOT NULL,
  PRIMARY KEY (`id_detalle`),
  KEY `fk_detalle_pedido` (`id_pedido`),
  KEY `fk_detalle_producto` (`id_producto`),
  CONSTRAINT `fk_detalle_pedido` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id_pedido`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_detalle_producto` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `chk_detalle_cantidad` CHECK ((`cantidad` > 0)),
  CONSTRAINT `chk_detalle_precio` CHECK ((`precio_unitario` >= 0))
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_pedido`
--

LOCK TABLES `detalle_pedido` WRITE;
/*!40000 ALTER TABLE `detalle_pedido` DISABLE KEYS */;
INSERT INTO `detalle_pedido` VALUES (1,33,45.00,1485.00,1,14),(2,1,33.00,33.00,2,12),(3,1,33.00,33.00,3,16);
/*!40000 ALTER TABLE `detalle_pedido` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mensajes`
--

DROP TABLE IF EXISTS `mensajes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mensajes` (
  `id_mensaje` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `motivo` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mensaje` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_envio` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `leido` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_mensaje`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mensajes`
--

LOCK TABLES `mensajes` WRITE;
/*!40000 ALTER TABLE `mensajes` DISABLE KEYS */;
INSERT INTO `mensajes` VALUES (1,'María Del Carmen','mainen1985@gmail.com',NULL,'Duda existencial','El cuenco que agranda el busto es efectivo?','2026-07-27 12:05:57',0),(2,'María Del Carmen','mainen1985@gmail.com',NULL,'Soy pesada','Me llama la atención la propaganada de mierda que teneis','2026-07-27 12:18:44',0),(3,'antonia','mainen1985@gmail.com',NULL,'hascjhd','rrrrrrrrrrrr rrrrrrrrrrrrrrr rrrrrrrrrrrrrr','2026-07-27 12:26:51',0),(4,'María Del Carmen','mainen1985@gmail.com',NULL,'Duda existencial','eeeeeeeeee eeeeeeeeeeeeeeeeee eeeeeeeeeeeeeeeeeeeeeeee','2026-07-27 12:27:07',0),(5,'Julia','julia@test.com',NULL,'Maza','Oye pone en la web que la maza es para pegarle a quien yo kiera??','2026-07-27 18:21:07',0),(6,'María Del Carmen','mainen1985@gmail.com',NULL,'Soy yo otra vez','Oye que es que me dejó pensando lo del cuenco robusto que te hace crecer el busto.','2026-07-27 18:22:57',0),(7,'prunia','mainen1985@gmail.com',NULL,'jajaja','jajajajajajajajajajajajajajajajajajaja','2026-07-27 18:30:54',0),(8,'Juana','juan@gmail.com','676201134','Me interesa trabajar con ustedes','Soy desarrolladora full Stack y me gustaría haceros una web en condiciones, tienen mi teléfono, desechen ya a Maricarmen porque abusa un poco de la IA.','2026-07-27 18:40:23',0),(9,'laura','laura@test.com','900600899','Algo','No sé como tenéis la poca vergüenza de decir que curáis a la gente con sonidos, estáis de la olla tíos.','2026-08-01 19:28:32',0);
/*!40000 ALTER TABLE `mensajes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expira` datetime NOT NULL,
  `creado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `token` (`token`),
  KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pedidos`
--

DROP TABLE IF EXISTS `pedidos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pedidos` (
  `id_pedido` int NOT NULL AUTO_INCREMENT,
  `fecha_pedido` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PENDIENTE',
  `total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `direccion_envio` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_usuario` int NOT NULL,
  PRIMARY KEY (`id_pedido`),
  KEY `fk_pedidos_usuario` (`id_usuario`),
  CONSTRAINT `fk_pedidos_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `chk_pedidos_estado` CHECK ((`estado` in (_utf8mb4'PENDIENTE',_utf8mb4'PAGADO',_utf8mb4'ENVIADO',_utf8mb4'ENTREGADO',_utf8mb4'CANCELADO'))),
  CONSTRAINT `chk_pedidos_total` CHECK ((`total` >= 0))
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedidos`
--

LOCK TABLES `pedidos` WRITE;
/*!40000 ALTER TABLE `pedidos` DISABLE KEYS */;
INSERT INTO `pedidos` VALUES (1,'2026-08-01 12:03:43','PAGADO',1485.00,'Calle Pollo , 6, Madrid',3),(2,'2026-08-01 12:04:51','PAGADO',33.00,'Calle Pollo , 6, Madrid',3),(3,'2026-08-01 19:21:46','PAGADO',33.00,'Calle Jilguero, 14, San Fernando',4);
/*!40000 ALTER TABLE `pedidos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `productos`
--

DROP TABLE IF EXISTS `productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `productos` (
  `id_producto` int NOT NULL AUTO_INCREMENT,
  `id_categoria` int NOT NULL,
  `nombre` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `precio` decimal(10,2) NOT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `imagen` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `diametro` decimal(5,2) DEFAULT NULL,
  `peso` decimal(8,2) DEFAULT NULL,
  `material` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nota_musical` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `procedencia` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `fecha_alta` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_producto`),
  KEY `fk_productos_categoria` (`id_categoria`),
  CONSTRAINT `fk_productos_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `chk_productos_precio` CHECK ((`precio` >= 0)),
  CONSTRAINT `chk_productos_stock` CHECK ((`stock` >= 0))
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `productos`
--

LOCK TABLES `productos` WRITE;
/*!40000 ALTER TABLE `productos` DISABLE KEYS */;
INSERT INTO `productos` VALUES (1,2,'Cuenco Taj Mahal','Una pasada de cuenco.',80.00,39,'Cuenco_Taj_Mahal.png',18.00,568.00,'Oro y Plata','Cuenco_Taj_Mahal.mp3','Nepal',1,'2026-07-20 17:07:40'),(2,1,'Cuenco Sueño Oriental','Cuenco mágico sin más.',34.00,66,'Cuenco_Sueno_Oriental.png',45.00,780.00,'Cobre y Aluminio','Cuenco_Sueno_Oriental.mp3','Nepal',1,'2026-07-20 17:09:24'),(3,2,'Cuenco Rayo de Luna','El cuenco de tu vida si eres nervioso.',88.00,45,'Cuenco_Rayo_de_Luna.png',22.00,900.00,'Oro y Plata','Cuenco_Rayo_de_Luna.mp3','Nepal',1,'2026-07-20 17:19:31'),(4,3,'Cuenco Magia del Sahara','Te retrae al Sahara con una sola nota.',55.00,34,'Cuenco_Magia_del_Sahara.png',32.00,700.00,'Cobre y Aluminio','Cuenco_Magia_del_Sahara.mp3','Nepal',1,'2026-07-20 17:20:57'),(5,1,'Cuenco Ancestral','Vibración chackra corona.',55.00,55,'Cuenco_Ancestral.png',34.00,600.00,'Oro y Plata','Cuenco_Ancestral.mp3','Nepal',1,'2026-07-20 17:39:14'),(6,3,'Cuenco Hada del Bosque','El sonido del bosque',77.00,66,'Cuenco_Hada_del_Bosque.png',22.00,500.00,'Cobre y Latón','Cuenco_Hada_del_Bosque.mp3','Budapest',1,'2026-07-21 09:53:02'),(7,1,'Cuenco Frío Polar','Simula el frío del desierto por la noche',55.00,55,'Cuenco_Frio_Polar.png',22.00,400.00,'Plata y Acero','Cuenco_Frio_Polar.mp3','El Cairo',1,'2026-07-21 09:56:06'),(8,4,'Cojín para cuenco','Cojín para cuenco tibetano.',22.00,44,'Cojin_para_cuenco.webp',13.00,20.00,'Seda ',NULL,'Nepal',1,'2026-07-21 10:01:33'),(9,4,'Cojin tibetano','Cojin para cuenco tipo donut.',11.00,21,'Cojin_tibetano.webp',12.00,34.00,'Nylon',NULL,'Budapest',1,'2026-07-21 10:02:26'),(10,4,'Cojin cuenco','Cojín para cuenco tibetano de Lino',22.00,33,'Cojin_cuenco.webp',11.00,680.00,'Lino y Seda',NULL,'Budapest',1,'2026-07-21 18:15:17'),(11,4,'Cojin tibetano','Cojín para tu cuenco.',33.00,33,'Cojin_tibetano1784650964.webp',12.00,456.00,'Seda ',NULL,'Budapest',1,'2026-07-21 18:22:44'),(12,2,'Cuenco Juan Sin Miedo','Cuenco Latón increíble',33.00,32,'Cuenco_Juan_Sin_Miedo.png',33.00,333.00,'Cobre y Latón','Cuenco_Juan_Sin_Miedo.mp3','Budapest',1,'2026-07-24 20:45:05'),(13,2,'Cuenco Espuma de Venus','El cuenco más sonoro de todos los tiempos.',23.00,30,'Cuenco_Espuma_de_Venus.png',22.00,678.00,'Plata y Acero','Cuenco_Espuma_de_Venus.mp3','Nepal',1,'2026-07-24 20:50:57'),(14,5,'Maza','Una maza para machacar cosas con el cuenco y esto, no sé para que es la verdad, si la descripcion es muy larga no cabrá en la tarjeta del div.',45.00,6,'Maza.webp',12.00,678.00,'Oro y Plata',NULL,'Budapest',1,'2026-07-25 21:55:04'),(15,1,'Cuenco Luz Atardecer','Cuenco robusto para agrandar el busto.',88.00,86,'Cuenco_Luz_Atardecer.png',12.00,777.00,'Cobre y Aluminio','Cuenco_Luz_Atardecer.mp3','Nepal',1,'2026-07-25 21:57:02'),(16,5,'Maza Chunga','Maza para moler cosas en tu cuenco y hacer ruido también, es muy variopinta.',33.00,32,'Maza_Chunga.webp',5.00,450.00,'Cobre y Aluminio',NULL,'Nepal',1,'2026-08-01 14:46:15'),(17,2,'Cuenco Cuéntame un Cuenco','Cuenco que te cuenta un cuento según le vas haciendo sonar.',66.00,44,'Cuenco_Cuentame_un_Cuenco.png',44.00,675.00,'Oro y Plata','Cuenco_Cuentame_un_Cuenco.mp3','Nepal',1,'2026-08-01 14:50:47'),(18,3,'Cuenco Vapores del Ganges','Cuenco que te desplaza al Ganges de inmediato y te provoca las mismas sensaciones que aspirar los vapores del río Ganges.',66.00,55,'Cuenco_Vapores_del_Ganges.png',13.00,790.00,'Cobre y Aluminio','Cuenco_Vapores_del_Ganges.mp3','Nepal',1,'2026-08-02 11:25:21'),(19,5,'Maza','Maza para reventar paredes.',67.00,7,'Maza1785663861.webp',55.00,456.00,'Oro y Plata',NULL,'Budapest',1,'2026-08-02 11:44:21');
/*!40000 ALTER TABLE `productos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `usuario` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'CLIENTE',
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `uq_usuarios_email` (`email`),
  UNIQUE KEY `uq_usuarios_usuario` (`usuario`),
  CONSTRAINT `chk_usuarios_rol` CHECK ((`rol` in (_utf8mb4'CLIENTE',_utf8mb4'ADMIN')))
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'Juana','juana@gmail.com','juana','$2y$10$7GRbloGney/IVK.ytc6M8.xzdju16eEVzLERaD69XPR6fftCf3.HK','CLIENTE','2026-07-25 12:05:35'),(2,'Amaia','amaia@gmail.com','amaia','$2y$10$uwix4.yFvQ6L6QxA7BXAkueVFUVt0RKGZBCpFUq9Z6jErkaCTSw7a','ADMIN','2026-07-25 12:06:51'),(3,'antonio','antonio@gmail.com','Antonio','$2y$10$2BJscC9E8iEkDT5ipTy2dOkBFUSROAKwGLnQlzi6auHXjwU3E6ON2','CLIENTE','2026-07-25 13:09:15'),(4,NULL,'macarena@gmail.com','macarena','$2y$10$h1fQQs9X/nNyYKC5tNLSEuDDW9nwNeVI44o35LSbRAidDR2rM.AW6','CLIENTE','2026-08-01 18:52:54');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-08-02 14:45:34
