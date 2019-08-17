-- MySQL dump 10.13  Distrib 5.7.16, for Win64 (x86_64)
--
-- Host: localhost    Database: ardsecurity
-- ------------------------------------------------------
-- Server version	5.7.16-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `equipos`
--

DROP TABLE IF EXISTS `equipos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `equipos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `domicilio` text NOT NULL,
  `notas` text NOT NULL,
  `alarma` tinyint(1) NOT NULL DEFAULT '0',
  `id_usuario` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `equipos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `equipos`
--

LOCK TABLES `equipos` WRITE;
/*!40000 ALTER TABLE `equipos` DISABLE KEYS */;
INSERT INTO `equipos` VALUES (2,'ArdSecurity','17AV poniente #33, Col Centro, Tapachula, Chiapas','Equipo instalado en planta alta',0,1);
/*!40000 ALTER TABLE `equipos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reportes`
--

DROP TABLE IF EXISTS `reportes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reportes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `momento` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `visto` tinyint(1) DEFAULT '0',
  `id_sensor` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `id_sensor` (`id_sensor`),
  CONSTRAINT `reportes_ibfk_1` FOREIGN KEY (`id_sensor`) REFERENCES `sensores` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reportes`
--

LOCK TABLES `reportes` WRITE;
/*!40000 ALTER TABLE `reportes` DISABLE KEYS */;
INSERT INTO `reportes` VALUES (9,'2017-11-30 01:11:21',0,4),(11,'2017-11-30 01:37:58',0,4),(27,'2017-12-11 02:57:49',0,3),(28,'2017-12-11 02:58:48',0,3),(29,'2017-12-11 03:01:21',0,3),(30,'2017-12-11 03:03:24',0,3),(31,'2017-12-11 03:05:12',0,3),(32,'2017-12-11 03:13:11',0,3),(33,'2017-12-11 03:15:23',0,3),(34,'2017-12-11 03:15:50',0,3),(35,'2017-12-11 03:18:26',0,3),(36,'2017-12-11 03:20:14',0,3),(37,'2017-12-11 03:22:46',0,3),(38,'2017-12-11 03:23:48',0,3),(39,'2017-12-11 03:26:00',0,3),(40,'2017-12-11 03:27:23',0,3),(41,'2017-12-11 03:28:54',0,3),(42,'2017-12-11 03:29:33',0,3),(43,'2017-12-11 03:30:54',0,3),(44,'2017-12-11 03:32:47',0,3),(45,'2017-12-11 03:35:13',0,3),(46,'2017-12-13 08:44:44',0,3),(47,'2017-12-13 08:51:01',0,3),(48,'2017-12-13 08:59:32',0,3),(49,'2017-12-13 09:13:47',0,3),(50,'2017-12-13 09:17:06',0,3);
/*!40000 ALTER TABLE `reportes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sensores`
--

DROP TABLE IF EXISTS `sensores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sensores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `ranura` int(11) NOT NULL,
  `notas` text NOT NULL,
  `habilitado` tinyint(1) DEFAULT '1',
  `alarma` tinyint(1) DEFAULT '0',
  `id_tipo` int(11) NOT NULL,
  `id_equipo` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `id_tipo` (`id_tipo`),
  KEY `id_equipo` (`id_equipo`),
  CONSTRAINT `sensores_ibfk_1` FOREIGN KEY (`id_tipo`) REFERENCES `tipos_sensor` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sensores_ibfk_2` FOREIGN KEY (`id_equipo`) REFERENCES `equipos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sensores`
--

LOCK TABLES `sensores` WRITE;
/*!40000 ALTER TABLE `sensores` DISABLE KEYS */;
INSERT INTO `sensores` VALUES (3,'Barrera Infrarroja',1,'Barrera infrarroja',1,1,1,2),(4,'Sensor Movimiento',6,'Sensor de movimiento puerta principal',1,0,1,2);
/*!40000 ALTER TABLE `sensores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sync`
--

DROP TABLE IF EXISTS `sync`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sync` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `command` varchar(50) NOT NULL,
  `id_equipo` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `id_equipo` (`id_equipo`),
  CONSTRAINT `sync_ibfk_1` FOREIGN KEY (`id_equipo`) REFERENCES `equipos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sync`
--

LOCK TABLES `sync` WRITE;
/*!40000 ALTER TABLE `sync` DISABLE KEYS */;
/*!40000 ALTER TABLE `sync` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipos_sensor`
--

DROP TABLE IF EXISTS `tipos_sensor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipos_sensor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `notas` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipos_sensor`
--

LOCK TABLES `tipos_sensor` WRITE;
/*!40000 ALTER TABLE `tipos_sensor` DISABLE KEYS */;
INSERT INTO `tipos_sensor` VALUES (1,'PIR','Sensor infrarrojo para detectar el movimiento'),(2,'MAGNETICO','Interruptor magnetico para detectar la apertura de una puerta o ventana'),(3,'LASER','Barrera laser para detectar el paso de un ente en determinada area');
/*!40000 ALTER TABLE `tipos_sensor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `correo` varchar(100) NOT NULL,
  `clave` varchar(100) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `alarma` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `correo` (`correo`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'2d.roblero@gmail.com','C3p8z4e4ZoDTmbWctK2Se16UseG5AndbQIAWMyhzQvFvwIITq7dGWs2mAbSd82lYq8EGs/k8lw02a5rGkuRHpA==','Administrador',0),(2,'carlos.ezam@gmail.com','C3p8z4e4ZoDTmbWctK2Se16UseG5AndbQIAWMyhzQvFvwIITq7dGWs2mAbSd82lYq8EGs/k8lw02a5rGkuRHpA==','Administrador',0);
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

-- Dump completed on 2018-02-01 16:50:49
