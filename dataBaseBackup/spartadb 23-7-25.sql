CREATE DATABASE  IF NOT EXISTS `spartadb` /*!40100 DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci */;
USE `spartadb`;
-- MySQL dump 10.13  Distrib 8.0.38, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: spartadb
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

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
-- Table structure for table `cuotas`
--

DROP TABLE IF EXISTS `cuotas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cuotas` (
  `idcuota` int(11) NOT NULL AUTO_INCREMENT,
  `idsocio` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `fecha_emision` date NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  `fecha_pago` date DEFAULT NULL,
  `estado` enum('PENDIENTE','PAGADA','VENCIDA') DEFAULT 'PENDIENTE',
  `metodo_pago` enum('EFECTIVO','TARJETA','TRANSFERENCIA','OTRO') DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  PRIMARY KEY (`idcuota`),
  KEY `idsocio` (`idsocio`),
  CONSTRAINT `cuotas_ibfk_1` FOREIGN KEY (`idsocio`) REFERENCES `socios` (`idsocio`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cuotas`
--

LOCK TABLES `cuotas` WRITE;
/*!40000 ALTER TABLE `cuotas` DISABLE KEYS */;
INSERT INTO `cuotas` VALUES (51,136,0.00,'2025-07-23','2025-08-23',NULL,'PENDIENTE',NULL,'Cuota generada automáticamente al registrar el socio.');
/*!40000 ALTER TABLE `cuotas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ejercicios`
--

DROP TABLE IF EXISTS `ejercicios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ejercicios` (
  `idejercicio` int(11) NOT NULL AUTO_INCREMENT,
  `nomb_ejer` varchar(50) DEFAULT NULL,
  `descripcion` varchar(150) DEFAULT NULL,
  `grupo_mus` enum('PIERNA','BRAZO','PECHO','ESPALDA','HOMBRO','ABDOMEN') DEFAULT NULL,
  `nivel_dificultad` enum('PRINCIPIANTE','INTERMEDIO','AVANZADO') DEFAULT NULL,
  PRIMARY KEY (`idejercicio`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ejercicios`
--

LOCK TABLES `ejercicios` WRITE;
/*!40000 ALTER TABLE `ejercicios` DISABLE KEYS */;
INSERT INTO `ejercicios` VALUES (27,'Bicep con mancuerna','12 reps','BRAZO','PRINCIPIANTE'),(28,'Pantorrilla con Prensa','','PIERNA','PRINCIPIANTE');
/*!40000 ALTER TABLE `ejercicios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ejercicios_media`
--

DROP TABLE IF EXISTS `ejercicios_media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ejercicios_media` (
  `id_media` int(11) NOT NULL AUTO_INCREMENT,
  `idejercicio` int(11) NOT NULL,
  `tipo_media` enum('IMAGEN','VIDEO_LINK') NOT NULL,
  `url_media` varchar(255) NOT NULL,
  `orden` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_media`),
  KEY `idejercicio` (`idejercicio`),
  CONSTRAINT `ejercicios_media_ibfk_1` FOREIGN KEY (`idejercicio`) REFERENCES `ejercicios` (`idejercicio`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ejercicios_media`
--

LOCK TABLES `ejercicios_media` WRITE;
/*!40000 ALTER TABLE `ejercicios_media` DISABLE KEYS */;
INSERT INTO `ejercicios_media` VALUES (1,28,'VIDEO_LINK','https://youtu.be/dwnHE7vmpvE',1),(2,28,'IMAGEN','/spartanproject/uploads/ejercicios/ejer_28_687fe3ae9f23d.webp',2);
/*!40000 ALTER TABLE `ejercicios_media` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `planes_entrenamiento`
--

DROP TABLE IF EXISTS `planes_entrenamiento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `planes_entrenamiento` (
  `idplan` int(11) NOT NULL AUTO_INCREMENT,
  `idsocio` int(11) NOT NULL,
  `tipo_plan` enum('MASMUSCULAR','BAJARPESO','OTRO') NOT NULL,
  `descripcion_plan` varchar(100) DEFAULT NULL,
  `peso_actual` decimal(5,2) DEFAULT NULL,
  `altura` int(11) DEFAULT NULL,
  `disponibilidad` varchar(100) DEFAULT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `observaciones` text DEFAULT NULL,
  PRIMARY KEY (`idplan`),
  KEY `idsocio` (`idsocio`),
  CONSTRAINT `planes_entrenamiento_ibfk_1` FOREIGN KEY (`idsocio`) REFERENCES `socios` (`idsocio`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `planes_entrenamiento`
--

LOCK TABLES `planes_entrenamiento` WRITE;
/*!40000 ALTER TABLE `planes_entrenamiento` DISABLE KEYS */;
/*!40000 ALTER TABLE `planes_entrenamiento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rutinas`
--

DROP TABLE IF EXISTS `rutinas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rutinas` (
  `idrutina` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `nivel_dificultad` varchar(50) NOT NULL,
  `activa` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`idrutina`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rutinas`
--

LOCK TABLES `rutinas` WRITE;
/*!40000 ALTER TABLE `rutinas` DISABLE KEYS */;
INSERT INTO `rutinas` VALUES (2,'Arnold','asdsadasd','Principiante',1);
/*!40000 ALTER TABLE `rutinas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rutinas_dias`
--

DROP TABLE IF EXISTS `rutinas_dias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rutinas_dias` (
  `iddia` int(11) NOT NULL AUTO_INCREMENT,
  `idrutina` int(11) NOT NULL,
  `dia_semana` enum('LUNES','MARTES','MIERCOLES','JUEVES','VIERNES','SABADO','DOMINGO') NOT NULL,
  PRIMARY KEY (`iddia`),
  UNIQUE KEY `idx_rutina_dia_unico` (`idrutina`,`dia_semana`),
  CONSTRAINT `rutinas_dias_ibfk_1` FOREIGN KEY (`idrutina`) REFERENCES `rutinas` (`idrutina`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rutinas_dias`
--

LOCK TABLES `rutinas_dias` WRITE;
/*!40000 ALTER TABLE `rutinas_dias` DISABLE KEYS */;
INSERT INTO `rutinas_dias` VALUES (2,2,'LUNES');
/*!40000 ALTER TABLE `rutinas_dias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rutinas_ejercicios`
--

DROP TABLE IF EXISTS `rutinas_ejercicios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rutinas_ejercicios` (
  `idejercicio_rutina` int(11) NOT NULL AUTO_INCREMENT,
  `iddia` int(11) NOT NULL,
  `idejercicio` int(11) NOT NULL,
  `repeticiones` varchar(50) DEFAULT NULL,
  `tiempo_descanso_seg` int(11) DEFAULT 60,
  `orden` int(11) DEFAULT 0,
  PRIMARY KEY (`idejercicio_rutina`),
  KEY `iddia` (`iddia`),
  KEY `idejercicio` (`idejercicio`),
  CONSTRAINT `rutinas_ejercicios_ibfk_1` FOREIGN KEY (`iddia`) REFERENCES `rutinas_dias` (`iddia`) ON DELETE CASCADE,
  CONSTRAINT `rutinas_ejercicios_ibfk_2` FOREIGN KEY (`idejercicio`) REFERENCES `ejercicios` (`idejercicio`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rutinas_ejercicios`
--

LOCK TABLES `rutinas_ejercicios` WRITE;
/*!40000 ALTER TABLE `rutinas_ejercicios` DISABLE KEYS */;
INSERT INTO `rutinas_ejercicios` VALUES (1,2,27,'4 x 12',180,1);
/*!40000 ALTER TABLE `rutinas_ejercicios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seguimiento_entrenamiento`
--

DROP TABLE IF EXISTS `seguimiento_entrenamiento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `seguimiento_entrenamiento` (
  `idseguimiento` int(11) NOT NULL AUTO_INCREMENT,
  `idplan` int(11) NOT NULL,
  `fecha_seguimiento` date NOT NULL,
  `peso` decimal(5,2) DEFAULT NULL,
  `medidas` text DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  PRIMARY KEY (`idseguimiento`),
  KEY `idplan` (`idplan`),
  CONSTRAINT `seguimiento_entrenamiento_ibfk_1` FOREIGN KEY (`idplan`) REFERENCES `planes_entrenamiento` (`idplan`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seguimiento_entrenamiento`
--

LOCK TABLES `seguimiento_entrenamiento` WRITE;
/*!40000 ALTER TABLE `seguimiento_entrenamiento` DISABLE KEYS */;
/*!40000 ALTER TABLE `seguimiento_entrenamiento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `socios`
--

DROP TABLE IF EXISTS `socios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `socios` (
  `idsocio` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(30) DEFAULT NULL,
  `apellido` varchar(30) DEFAULT NULL,
  `dni` varchar(10) DEFAULT NULL,
  `direc` varchar(30) DEFAULT NULL,
  `telef` varchar(15) DEFAULT NULL,
  `email` varchar(30) DEFAULT NULL,
  `fechalta` date DEFAULT NULL,
  `probfis` tinytext DEFAULT NULL,
  `foto` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`idsocio`)
) ENGINE=InnoDB AUTO_INCREMENT=137 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `socios`
--

LOCK TABLES `socios` WRITE;
/*!40000 ALTER TABLE `socios` DISABLE KEYS */;
INSERT INTO `socios` VALUES (136,'Enzo','Moreyra','31582309','Paraguay','03704253175','enzomoreyra85@gmail.com','2025-07-23','','C:/xampp/htdocs/spartanproject/Socios/uploads/6880f50d65e78_Captura de pantalla 2025-01-28 231631.png');
/*!40000 ALTER TABLE `socios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `socios_rutinas_asignadas`
--

DROP TABLE IF EXISTS `socios_rutinas_asignadas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `socios_rutinas_asignadas` (
  `id_asignacion` int(11) NOT NULL AUTO_INCREMENT,
  `idsocio` int(11) NOT NULL,
  `idrutina` int(11) NOT NULL,
  `fecha_asignacion` datetime NOT NULL DEFAULT current_timestamp(),
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_asignacion`),
  KEY `idsocio` (`idsocio`),
  KEY `idrutina` (`idrutina`),
  CONSTRAINT `socios_rutinas_asignadas_ibfk_1` FOREIGN KEY (`idsocio`) REFERENCES `socios` (`idsocio`) ON DELETE CASCADE,
  CONSTRAINT `socios_rutinas_asignadas_ibfk_2` FOREIGN KEY (`idrutina`) REFERENCES `rutinas` (`idrutina`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `socios_rutinas_asignadas`
--

LOCK TABLES `socios_rutinas_asignadas` WRITE;
/*!40000 ALTER TABLE `socios_rutinas_asignadas` DISABLE KEYS */;
/*!40000 ALTER TABLE `socios_rutinas_asignadas` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-07-23 16:44:27
