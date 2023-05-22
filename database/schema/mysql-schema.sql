/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `action_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `action_events` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `batch_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `actionable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `actionable_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fields` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'running',
  `exception` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `original` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `changes` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `action_events_actionable_type_actionable_id_index` (`actionable_type`,`actionable_id`),
  KEY `action_events_batch_id_model_type_model_id_index` (`batch_id`,`model_type`,`model_id`),
  KEY `action_events_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `actividad_grupo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `actividad_grupo` (
  `idActividad` int unsigned NOT NULL,
  `idGrupo` varchar(5) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`idActividad`,`idGrupo`),
  KEY `actividad_grupo_idgrupo_foreign` (`idGrupo`),
  CONSTRAINT `actividad_grupo_idactividad_foreign` FOREIGN KEY (`idActividad`) REFERENCES `actividades` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `actividad_grupo_idgrupo_foreign` FOREIGN KEY (`idGrupo`) REFERENCES `grupos` (`codigo`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `actividad_profesor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `actividad_profesor` (
  `idActividad` int unsigned NOT NULL,
  `idProfesor` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `coordinador` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idActividad`,`idProfesor`),
  KEY `actividad_profesor_idprofesor_foreign` (`idProfesor`),
  CONSTRAINT `actividad_profesor_idactividad_foreign` FOREIGN KEY (`idActividad`) REFERENCES `actividades` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `actividad_profesor_idprofesor_foreign` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`dni`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `actividades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `actividades` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(75) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `objetivos` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `desde` datetime NOT NULL,
  `hasta` datetime NOT NULL,
  `comentarios` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `estado` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `extraescolar` tinyint(1) NOT NULL DEFAULT '1',
  `fueraCentro` tinyint(1) NOT NULL DEFAULT '1',
  `idDocumento` int unsigned DEFAULT NULL,
  `transport` tinyint NOT NULL DEFAULT '0',
  `desenvolupament` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `valoracio` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `aspectes` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `dades` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `recomanada` tinyint(1) NOT NULL DEFAULT '1',
  `poll` tinyint(1) NOT NULL DEFAULT '1',
  `image1` varchar(60) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image2` varchar(60) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `image3` varchar(60) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `actividades_iddocumento_foreign` (`idDocumento`),
  CONSTRAINT `actividades_iddocumento_foreign` FOREIGN KEY (`idDocumento`) REFERENCES `documentos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activities` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `action` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `model_class` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `model_id` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `author_id` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `comentari` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `document` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `activities_author_id_index` (`author_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `adjuntos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `adjuntos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `referencesTo` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `extension` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `size` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `route` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `alumno_fcts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `alumno_fcts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `idFct` int unsigned NOT NULL,
  `idAlumno` varchar(8) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `calificacion` tinyint(1) DEFAULT NULL,
  `calProyecto` tinyint(1) DEFAULT NULL,
  `actas` tinyint(1) NOT NULL DEFAULT '0',
  `insercion` tinyint(1) NOT NULL DEFAULT '0',
  `horas` smallint DEFAULT NULL,
  `desde` date DEFAULT NULL,
  `hasta` date DEFAULT NULL,
  `correoAlumno` tinyint(1) NOT NULL DEFAULT '0',
  `pg0301` tinyint(1) NOT NULL DEFAULT '0',
  `beca` double(8,2) NOT NULL DEFAULT '0.00',
  `a56` tinyint(1) NOT NULL DEFAULT '0',
  `idSao` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `realizadas` smallint NOT NULL DEFAULT '0',
  `horas_diarias` tinyint NOT NULL DEFAULT '0',
  `actualizacion` date DEFAULT NULL,
  `autorizacion` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `alumno_fcts_idFct_idAlumno_unique` (`idFct`,`idAlumno`),
  KEY `alumno_fcts_idAlumno_foreign` (`idAlumno`),
  KEY `alumno_fcts_idsao_index` (`idSao`),
  CONSTRAINT `alumno_fcts_idalumno_foreign` FOREIGN KEY (`idAlumno`) REFERENCES `alumnos` (`nia`) ON UPDATE CASCADE,
  CONSTRAINT `alumno_fcts_idfct_foreign` FOREIGN KEY (`idFct`) REFERENCES `fcts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `alumno_resultados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `alumno_resultados` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `idAlumno` varchar(8) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `idModuloGrupo` int unsigned NOT NULL,
  `nota` tinyint NOT NULL DEFAULT '0',
  `valoraciones` tinyint NOT NULL DEFAULT '0',
  `observaciones` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `alumno_resultados_unique` (`idAlumno`,`idModuloGrupo`),
  KEY `alumno_resultados_idmodulogrupo_foreign` (`idModuloGrupo`),
  CONSTRAINT `alumno_resultados_idalumno_foreign` FOREIGN KEY (`idAlumno`) REFERENCES `alumnos` (`nia`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `alumno_resultados_idmodulogrupo_foreign` FOREIGN KEY (`idModuloGrupo`) REFERENCES `modulo_grupos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `alumno_reuniones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `alumno_reuniones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `idReunion` int unsigned NOT NULL,
  `idAlumno` varchar(8) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `capacitats` tinyint NOT NULL DEFAULT '0',
  `sent` tinyint(1) NOT NULL DEFAULT '0',
  `token` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `asistencias_idreunion_foreign` (`idReunion`),
  KEY `asistencias_idAlumno_foreign` (`idAlumno`),
  CONSTRAINT `alumno_reuniones_idalumno_foreign` FOREIGN KEY (`idAlumno`) REFERENCES `alumnos` (`nia`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `alumno_reuniones_idreunion_foreign` FOREIGN KEY (`idReunion`) REFERENCES `reuniones` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `alumnos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `alumnos` (
  `nia` varchar(8) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `dni` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `nombre` varchar(25) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `apellido1` varchar(25) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `apellido2` varchar(25) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `password` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `email` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `expediente` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `domicilio` varchar(90) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `provincia` varchar(2) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `municipio` varchar(5) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `telef1` varchar(14) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `telef2` varchar(14) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `sexo` varchar(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `fecha_nac` date NOT NULL,
  `codigo_postal` varchar(5) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `fecha_ingreso` date DEFAULT NULL,
  `fecha_matricula` date NOT NULL,
  `foto` varchar(60) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `repite` tinyint unsigned NOT NULL,
  `turno` varchar(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `trabaja` varchar(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `rol` int NOT NULL DEFAULT '5',
  `remember_token` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `last_logged` timestamp NULL DEFAULT NULL,
  `baja` date DEFAULT NULL,
  `idioma` varchar(2) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'ca',
  `fol` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`nia`),
  UNIQUE KEY `alumnos_dni_unique` (`dni`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `alumnos_cursos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `alumnos_cursos` (
  `idAlumno` varchar(8) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `idCurso` int unsigned NOT NULL,
  `finalizado` tinyint NOT NULL,
  `registrado` varchar(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `alumnos_cursos_idalumno_foreign` (`idAlumno`),
  KEY `alumnos_cursos_idcurso_foreign` (`idCurso`),
  CONSTRAINT `alumnos_cursos_idalumno_foreign` FOREIGN KEY (`idAlumno`) REFERENCES `alumnos` (`nia`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `alumnos_cursos_idcurso_foreign` FOREIGN KEY (`idCurso`) REFERENCES `cursos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `alumnos_grupos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `alumnos_grupos` (
  `idAlumno` varchar(8) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `idGrupo` varchar(5) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `subGrupo` varchar(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `posicion` varchar(2) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`idAlumno`,`idGrupo`),
  KEY `alumnos_grupos_idgrupo_foreign` (`idGrupo`),
  CONSTRAINT `alumnos_grupos_idalumno_foreign` FOREIGN KEY (`idAlumno`) REFERENCES `alumnos` (`nia`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `alumnos_grupos_idgrupo_foreign` FOREIGN KEY (`idGrupo`) REFERENCES `grupos` (`codigo`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `alumnos_password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `alumnos_password_resets` (
  `email` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `alumnos_password_resets_email_index` (`email`),
  KEY `alumnos_password_resets_token_index` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `articulos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `articulos` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fichero` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `articulos_lote`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `articulos_lote` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `lote_id` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `articulo_id` int unsigned NOT NULL,
  `marca` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `modelo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unidades` smallint NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `articulos_lote_lote_id_foreign` (`lote_id`),
  KEY `articulos_lote_articulo_id_foreign` (`articulo_id`),
  CONSTRAINT `articulos_lote_articulo_id_foreign` FOREIGN KEY (`articulo_id`) REFERENCES `articulos` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `articulos_lote_lote_id_foreign` FOREIGN KEY (`lote_id`) REFERENCES `lotes` (`registre`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `asistencias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `asistencias` (
  `id` int NOT NULL AUTO_INCREMENT,
  `idReunion` int unsigned NOT NULL,
  `idProfesor` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `asiste` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `asistencias_idprofesor_foreign` (`idProfesor`),
  KEY `asistencias_idreunion_foreign` (`idReunion`),
  CONSTRAINT `asistencias_idprofesor_foreign` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`dni`) ON UPDATE CASCADE,
  CONSTRAINT `asistencias_idreunion_foreign` FOREIGN KEY (`idReunion`) REFERENCES `reuniones` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `autorizaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `autorizaciones` (
  `idActividad` int unsigned NOT NULL,
  `idAlumno` varchar(8) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `autorizado` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idActividad`,`idAlumno`),
  KEY `autorizaciones_idalumno_foreign` (`idAlumno`),
  CONSTRAINT `autorizaciones_idactividad_foreign` FOREIGN KEY (`idActividad`) REFERENCES `actividades` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `autorizaciones_idalumno_foreign` FOREIGN KEY (`idAlumno`) REFERENCES `alumnos` (`nia`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `centros`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `centros` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `idEmpresa` int unsigned NOT NULL,
  `direccion` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `localidad` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `email` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `telefono` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `dni` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `observaciones` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `horarios` varchar(256) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `idioma` varchar(2) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `codiPostal` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `idSao` varchar(8) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `centros_idempresa_foreign` (`idEmpresa`),
  CONSTRAINT `centros_idempresa_foreign` FOREIGN KEY (`idEmpresa`) REFERENCES `empresas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `centros_instructores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `centros_instructores` (
  `idCentro` int unsigned NOT NULL,
  `idInstructor` varchar(12) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`idCentro`,`idInstructor`),
  KEY `centros_instructores_idinstructor_foreign` (`idInstructor`),
  CONSTRAINT `centros_instructores_idcentro_foreign` FOREIGN KEY (`idCentro`) REFERENCES `centros` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `centros_instructores_idinstructor_foreign` FOREIGN KEY (`idInstructor`) REFERENCES `instructores` (`dni`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ciclos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ciclos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ciclo` varchar(50) NOT NULL,
  `departamento` tinyint NOT NULL,
  `tipo` tinyint unsigned NOT NULL,
  `normativa` varchar(10) NOT NULL DEFAULT 'LOE',
  `titol` varchar(100) DEFAULT NULL,
  `rd` varchar(100) DEFAULT NULL,
  `rd2` varchar(100) DEFAULT NULL,
  `vliteral` varchar(100) DEFAULT NULL,
  `cliteral` varchar(100) DEFAULT NULL,
  `horasFct` smallint NOT NULL DEFAULT '400',
  `acronim` varchar(10) DEFAULT NULL,
  `llocTreball` varchar(100) DEFAULT NULL,
  `dataSignaturaDual` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ciclos_departamento_foreign` (`departamento`),
  CONSTRAINT `ciclos_departamento_foreign` FOREIGN KEY (`departamento`) REFERENCES `departamentos` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `colaboracion_votes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `colaboracion_votes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `option_id` int unsigned NOT NULL,
  `idColaboracion` int unsigned NOT NULL,
  `value` tinyint DEFAULT NULL,
  `text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `curs` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `colaboracion_votes_option_id_foreign` (`option_id`),
  KEY `colaboracion_votes_idcolaboracion_foreign` (`idColaboracion`),
  CONSTRAINT `colaboracion_votes_idcolaboracion_foreign` FOREIGN KEY (`idColaboracion`) REFERENCES `colaboraciones` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `colaboracion_votes_option_id_foreign` FOREIGN KEY (`option_id`) REFERENCES `options` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `colaboraciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `colaboraciones` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `idCiclo` int NOT NULL,
  `contacto` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `tutor` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `telefono` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `puestos` tinyint DEFAULT '1',
  `idCentro` int unsigned NOT NULL,
  `email` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci DEFAULT NULL,
  `estado` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idCiclo` (`idCiclo`),
  KEY `idCentro` (`idCentro`),
  CONSTRAINT `colaboraciones_idCentro_foreign` FOREIGN KEY (`idCentro`) REFERENCES `centros` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `colaboraciones_idCiclo_foreign` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `colaboradores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `colaboradores` (
  `idFct` int unsigned NOT NULL,
  `idInstructor` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `horas` smallint DEFAULT NULL,
  `name` varchar(80) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`idFct`,`idInstructor`),
  KEY `instructor_fcts_idinstructor_foreign` (`idInstructor`),
  CONSTRAINT `instructor_fcts_idfct_foreign` FOREIGN KEY (`idFct`) REFERENCES `fcts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `comision_fcts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `comision_fcts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `idFct` int unsigned NOT NULL,
  `idComision` int unsigned NOT NULL,
  `hora_ini` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `aviso` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `comision_fcts_idFct_idComision_unique` (`idFct`,`idComision`),
  KEY `comision_fcts_idcomision_foreign` (`idComision`),
  CONSTRAINT `comision_fcts_idcomision_foreign` FOREIGN KEY (`idComision`) REFERENCES `comisiones` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `comision_fcts_idfct_foreign` FOREIGN KEY (`idFct`) REFERENCES `fcts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `comisiones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `comisiones` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `idProfesor` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `servicio` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `alojamiento` decimal(5,2) NOT NULL DEFAULT '0.00',
  `comida` decimal(5,2) NOT NULL DEFAULT '0.00',
  `gastos` decimal(5,2) NOT NULL DEFAULT '0.00',
  `marca` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `matricula` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `medio` varchar(80) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `kilometraje` int unsigned DEFAULT NULL,
  `desde` datetime NOT NULL,
  `hasta` datetime NOT NULL,
  `estado` tinyint NOT NULL DEFAULT '0',
  `fct` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `itinerario` varchar(254) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `idDocumento` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `comisiones_idprofesor_foreign` (`idProfesor`),
  KEY `comisiones_iddocumento_foreign` (`idDocumento`),
  CONSTRAINT `comisiones_iddocumento_foreign` FOREIGN KEY (`idDocumento`) REFERENCES `documentos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `comisiones_idprofesor_foreign` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`dni`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cursos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cursos` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `titulo` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `tipo` varchar(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `comentarios` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `profesorado` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `activo` varchar(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `horas` tinyint unsigned NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `hora_ini` time NOT NULL,
  `hora_fin` time NOT NULL,
  `aforo` smallint DEFAULT NULL,
  `fichero` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `archivada` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `departamentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `departamentos` (
  `id` tinyint NOT NULL,
  `cliteral` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `vliteral` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `depcurt` varchar(3) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `didactico` tinyint NOT NULL DEFAULT '1',
  `idProfesor` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `departamentos_idprofesor_foreign` (`idProfesor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `documentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `documentos` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `tipoDocumento` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `curso` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `idDocumento` int DEFAULT NULL,
  `propietario` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `supervisor` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `descripcion` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `ciclo` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `modulo` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `grupo` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `fichero` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `tags` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `rol` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `enlace` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `detalle` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `empresas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empresas` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `cif` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `concierto` int DEFAULT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `email` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `direccion` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `localidad` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `telefono` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `dual` tinyint(1) NOT NULL DEFAULT '0',
  `actividad` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `delitos` tinyint(1) NOT NULL DEFAULT '0',
  `menores` tinyint(1) NOT NULL DEFAULT '0',
  `observaciones` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `sao` tinyint(1) NOT NULL DEFAULT '1',
  `copia_anexe1` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `creador` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `europa` tinyint(1) NOT NULL DEFAULT '0',
  `fichero` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `gerente` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `idSao` varchar(8) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `empresas_cif_unique` (`cif`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `espacios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `espacios` (
  `aula` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `descripcion` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `idDepartamento` tinyint(1) NOT NULL,
  `gMati` varchar(5) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `gVesprada` varchar(5) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `reservable` tinyint(1) NOT NULL DEFAULT '0',
  `dispositivo` tinyint DEFAULT NULL,
  PRIMARY KEY (`aula`),
  KEY `espacios_departamento_foreign` (`idDepartamento`),
  CONSTRAINT `espacios_iddepartamento_foreign` FOREIGN KEY (`idDepartamento`) REFERENCES `departamentos` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `expedientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expedientes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `idAlumno` varchar(8) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `idProfesor` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `tipo` tinyint NOT NULL DEFAULT '0',
  `explicacion` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `fecha` date NOT NULL,
  `fechasolucion` date DEFAULT NULL,
  `estado` tinyint NOT NULL DEFAULT '0',
  `idModulo` varchar(12) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `idDocumento` int unsigned DEFAULT NULL,
  `idAcompanyant` varchar(12) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `expedientes_idprofesor_foreign` (`idProfesor`),
  KEY `expedientes_idalumno_foreign` (`idAlumno`),
  KEY `expedientes_idmodulo_foreign` (`idModulo`),
  KEY `expedientes_iddocumento_foreign` (`idDocumento`),
  CONSTRAINT `expedientes_idalumno_foreign` FOREIGN KEY (`idAlumno`) REFERENCES `alumnos` (`nia`) ON UPDATE CASCADE,
  CONSTRAINT `expedientes_iddocumento_foreign` FOREIGN KEY (`idDocumento`) REFERENCES `documentos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `expedientes_idmodulo_foreign` FOREIGN KEY (`idModulo`) REFERENCES `modulos` (`codigo`) ON UPDATE CASCADE,
  CONSTRAINT `expedientes_idprofesor_foreign` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`dni`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `connection` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `faltas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `faltas` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `idProfesor` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `desde` date NOT NULL,
  `hasta` date DEFAULT NULL,
  `motivos` varchar(2) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `observaciones` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `estado` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `fichero` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `hora_ini` time DEFAULT NULL,
  `hora_fin` time DEFAULT NULL,
  `dia_completo` tinyint(1) DEFAULT NULL,
  `baja` tinyint(1) DEFAULT NULL,
  `idDocumento` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `faltas_idprofesor_foreign` (`idProfesor`),
  KEY `faltas_iddocumento_foreign` (`idDocumento`),
  CONSTRAINT `faltas_iddocumento_foreign` FOREIGN KEY (`idDocumento`) REFERENCES `documentos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `faltas_idprofesor_foreign` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`dni`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `faltas_itaca`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `faltas_itaca` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `idProfesor` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `dia` date NOT NULL,
  `sesion_orden` tinyint NOT NULL,
  `estado` tinyint NOT NULL DEFAULT '0',
  `enCentro` tinyint(1) NOT NULL DEFAULT '0',
  `idGrupo` varchar(5) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `justificacion` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `idDocumento` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `faltas_itaca_idprofesor_foreign` (`idProfesor`),
  KEY `faltas_itaca_idgrupo_foreign` (`idGrupo`),
  KEY `faltas_itaca_iddocumento_foreign` (`idDocumento`),
  CONSTRAINT `faltas_itaca_iddocumento_foreign` FOREIGN KEY (`idDocumento`) REFERENCES `documentos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `faltas_itaca_idgrupo_foreign` FOREIGN KEY (`idGrupo`) REFERENCES `grupos` (`codigo`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `faltas_itaca_idprofesor_foreign` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`dni`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `faltas_profesores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `faltas_profesores` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `idProfesor` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `dia` date NOT NULL,
  `entrada` time DEFAULT NULL,
  `salida` time DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `faltas_profesores_idprofesor_foreign` (`idProfesor`),
  CONSTRAINT `faltas_profesores_idprofesor_foreign` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`dni`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `fcts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fcts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `idColaboracion` int unsigned DEFAULT NULL,
  `asociacion` tinyint NOT NULL,
  `correoInstructor` tinyint(1) NOT NULL DEFAULT '0',
  `idInstructor` varchar(12) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `cotutor` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fcts_idcolaboracion_foreign` (`idColaboracion`),
  KEY `instructor_fcts_idinstructor_foreign` (`idInstructor`),
  KEY `fcts_cotutor_foreign` (`cotutor`),
  CONSTRAINT `fcts_cotutor_foreign` FOREIGN KEY (`cotutor`) REFERENCES `profesores` (`dni`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fcts_idcolaboracion_foreign` FOREIGN KEY (`idColaboracion`) REFERENCES `colaboraciones` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fcts_idInstructor_foreing` FOREIGN KEY (`idInstructor`) REFERENCES `instructores` (`dni`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `grupos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `grupos` (
  `codigo` varchar(5) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `nombre` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `turno` varchar(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `tutor` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `idCiclo` tinyint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `curso` tinyint unsigned NOT NULL DEFAULT '2',
  `acta_pendiente` tinyint(1) NOT NULL DEFAULT '0',
  `tutorDual` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `fol` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`codigo`),
  KEY `turno` (`turno`),
  KEY `tutor` (`tutor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `grupos_trabajo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `grupos_trabajo` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `objetivos` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `literal` varchar(40) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `guardias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `guardias` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `idProfesor` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `dia` date NOT NULL,
  `hora` tinyint NOT NULL,
  `realizada` tinyint NOT NULL,
  `observaciones` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `obs_personal` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `guardias_idprofesor_foreign` (`idProfesor`),
  CONSTRAINT `guardias_idprofesor_foreign` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`dni`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `horarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `horarios` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `dia_semana` enum('L','M','X','J','V') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `sesion_orden` tinyint NOT NULL,
  `idProfesor` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `modulo` varchar(12) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `idGrupo` varchar(5) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `aula` varchar(5) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `ocupacion` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `plantilla` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `horas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `horas` (
  `codigo` tinyint NOT NULL,
  `turno` varchar(8) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `hora_ini` varchar(5) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `hora_fin` varchar(5) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `incidencias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `incidencias` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `material` int DEFAULT NULL,
  `descripcion` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `estado` tinyint NOT NULL,
  `espacio` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `idProfesor` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `responsable` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `tipo` tinyint NOT NULL,
  `prioridad` tinyint NOT NULL,
  `fecha` date NOT NULL,
  `observaciones` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `solucion` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `fechasolucion` date DEFAULT NULL,
  `orden` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `incidencias_espacio_foreign` (`espacio`),
  KEY `incidencias_idprofesor_foreign` (`idProfesor`),
  KEY `incidencias_tipo_foreign` (`tipo`),
  KEY `incidencias_orden_foreign` (`orden`),
  CONSTRAINT `incidencias_espacio_foreign` FOREIGN KEY (`espacio`) REFERENCES `espacios` (`aula`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `incidencias_idprofesor_foreign` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`dni`) ON UPDATE CASCADE,
  CONSTRAINT `incidencias_orden_foreign` FOREIGN KEY (`orden`) REFERENCES `ordenes_trabajo` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `incidencias_tipo_foreign` FOREIGN KEY (`tipo`) REFERENCES `tipoincidencias` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `instructores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `instructores` (
  `dni` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `name` varchar(60) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `email` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `telefono` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `departamento` varchar(80) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `surnames` varchar(60) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `colaborador` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`dni`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_reserved_at_index` (`queue`,`reserved_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `lotes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lotes` (
  `registre` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `procedencia` tinyint(1) DEFAULT NULL,
  `proveedor` varchar(90) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fechaAlta` date DEFAULT NULL,
  `factura` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `departamento_id` tinyint DEFAULT NULL,
  PRIMARY KEY (`registre`),
  KEY `lotes_departamento_id_foreign` (`departamento_id`),
  CONSTRAINT `lotes_departamento_id_foreign` FOREIGN KEY (`departamento_id`) REFERENCES `departamentos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `materiales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `materiales` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nserieprov` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `descripcion` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `marca` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `modelo` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `procedencia` tinyint DEFAULT NULL,
  `estado` tinyint NOT NULL DEFAULT '1',
  `espacio` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `unidades` smallint NOT NULL DEFAULT '1',
  `ISBN` varchar(35) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `fechaultimoinventario` date DEFAULT NULL,
  `fechabaja` date DEFAULT NULL,
  `tipo` smallint DEFAULT NULL,
  `proveedor` varchar(90) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `inventariable` tinyint(1) NOT NULL DEFAULT '0',
  `articulo_lote_id` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `espacios` (`espacio`),
  KEY `materiales_articulo_lote_id_foreign` (`articulo_lote_id`),
  CONSTRAINT `materiales_articulo_lote_id_foreign` FOREIGN KEY (`articulo_lote_id`) REFERENCES `articulos_lote` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `materiales_espacio_foreign` FOREIGN KEY (`espacio`) REFERENCES `espacios` (`aula`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `materiales_baja`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `materiales_baja` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `idMaterial` int unsigned NOT NULL,
  `idProfesor` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `tipo` tinyint DEFAULT '0',
  `nuevoEstado` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `motivo` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` tinyint DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `materiales_baja_idprofesor_foreign` (`idProfesor`),
  KEY `materiales_foreign` (`idMaterial`),
  CONSTRAINT `materiales_baja_idmaterial_foreign` FOREIGN KEY (`idMaterial`) REFERENCES `materiales` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `materiales_baja_idprofesor_foreign` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`dni`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `menus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `menus` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `url` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `class` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `rol` tinyint NOT NULL,
  `menu` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `submenu` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT '',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `orden` smallint NOT NULL DEFAULT '9999',
  `ajuda` varchar(120) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `miembros`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `miembros` (
  `idGrupoTrabajo` int unsigned NOT NULL,
  `idProfesor` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `coordinador` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idGrupoTrabajo`,`idProfesor`),
  KEY `miembros_idprofesor_foreign` (`idProfesor`),
  CONSTRAINT `miembros_idgrupotrabajo_foreign` FOREIGN KEY (`idGrupoTrabajo`) REFERENCES `grupos_trabajo` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `miembros_idprofesor_foreign` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`dni`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `modulo_ciclos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modulo_ciclos` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `idModulo` varchar(12) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `idCiclo` int NOT NULL,
  `curso` varchar(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `enlace` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `idDepartamento` tinyint DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `modulo_ciclos_idmodulo_idciclo_unique` (`idModulo`,`idCiclo`),
  KEY `modulo_ciclos_idciclo_foreign` (`idCiclo`),
  KEY `modulo_ciclos_idmodulo_index` (`idModulo`),
  KEY `modulo_ciclos_iddepartamento_foreign` (`idDepartamento`),
  CONSTRAINT `modulo_ciclos_idciclo_foreign` FOREIGN KEY (`idCiclo`) REFERENCES `ciclos` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `modulo_ciclos_iddepartamento_foreign` FOREIGN KEY (`idDepartamento`) REFERENCES `departamentos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `modulo_ciclos_idmodulo_foreign` FOREIGN KEY (`idModulo`) REFERENCES `modulos` (`codigo`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `modulo_grupos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modulo_grupos` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `idModuloCiclo` int unsigned NOT NULL,
  `idGrupo` varchar(5) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `modulo_grupos_idmodulociclo_idgrupo_unique` (`idModuloCiclo`,`idGrupo`),
  KEY `modulo_grupos_idgrupo_foreign` (`idGrupo`),
  CONSTRAINT `modulo_grupos_idgrupo_foreign` FOREIGN KEY (`idGrupo`) REFERENCES `grupos` (`codigo`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `modulo_grupos_idmodulociclo_foreign` FOREIGN KEY (`idModuloCiclo`) REFERENCES `modulo_ciclos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `modulos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modulos` (
  `codigo` varchar(12) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `cliteral` varchar(160) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `vliteral` varchar(160) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `municipios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `municipios` (
  `provincias_id` varchar(2) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `cod_municipio` varchar(4) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `municipio` varchar(60) NOT NULL,
  PRIMARY KEY (`provincias_id`,`cod_municipio`),
  KEY `fk_municipios_provincias1_idx` (`provincias_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` char(36) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `notifiable_id` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `data` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ocupaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ocupaciones` (
  `codigo` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `nombre` varchar(60) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `nom` varchar(60) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `options` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `question` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `scala` int NOT NULL DEFAULT '10',
  `ppoll_id` int unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `options_ppoll_id_foreign` (`ppoll_id`),
  CONSTRAINT `options_ppoll_id_foreign` FOREIGN KEY (`ppoll_id`) REFERENCES `ppolls` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ordenes_reuniones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ordenes_reuniones` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `idReunion` int unsigned NOT NULL,
  `tarea` tinyint(1) NOT NULL DEFAULT '0',
  `idProfesor` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `realizada` tinyint(1) NOT NULL DEFAULT '0',
  `orden` tinyint NOT NULL DEFAULT '1',
  `descripcion` varchar(120) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `resumen` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `ordenes_reuniones_idreunion_foreign` (`idReunion`),
  CONSTRAINT `ordenes_reuniones_idreunion_foreign` FOREIGN KEY (`idReunion`) REFERENCES `reuniones` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ordenes_trabajo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ordenes_trabajo` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `estado` tinyint NOT NULL DEFAULT '0',
  `idProfesor` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `tipo` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ordenes_trabajo_idprofesor_foreign` (`idProfesor`),
  CONSTRAINT `ordenes_trabajo_idprofesor_foreign` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`dni`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `email` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`),
  KEY `password_resets_token_index` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `polls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `polls` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `desde` date DEFAULT NULL,
  `hasta` date DEFAULT NULL,
  `idModelo` int unsigned DEFAULT NULL,
  `idPPoll` int unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `polls_idppoll_foreign` (`idPPoll`),
  CONSTRAINT `polls_idppoll_foreign` FOREIGN KEY (`idPPoll`) REFERENCES `ppolls` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ppolls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ppolls` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `what` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `anonymous` tinyint NOT NULL DEFAULT '1',
  `remains` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `profesores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `profesores` (
  `dni` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `codigo` smallint NOT NULL,
  `nombre` varchar(25) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `apellido1` varchar(25) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `apellido2` varchar(25) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `password` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `emailItaca` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `email` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `domicilio` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `movil1` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `movil2` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `sexo` varchar(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `codigo_postal` varchar(5) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `departamento` tinyint DEFAULT NULL,
  `fecha_ingreso` date DEFAULT NULL,
  `fecha_nac` date DEFAULT NULL,
  `fecha_baja` date DEFAULT NULL,
  `fecha_ant` date DEFAULT NULL,
  `sustituye_a` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `foto` varchar(60) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `rol` bigint NOT NULL DEFAULT '3',
  `remember_token` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `last_logged` timestamp NULL DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `idioma` varchar(2) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'ca',
  `api_token` varchar(60) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `mostrar` tinyint(1) NOT NULL DEFAULT '0',
  `especialitat` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `changePassword` date DEFAULT NULL,
  PRIMARY KEY (`dni`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `profesores_password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `profesores_password_resets` (
  `email` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `profesores_password_resets_email_index` (`email`),
  KEY `profesores_password_resets_token_index` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `programaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `programaciones` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `fichero` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `anexos` tinyint NOT NULL DEFAULT '0',
  `estado` tinyint NOT NULL DEFAULT '0',
  `checkList` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `ciclo` varchar(80) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `criterios` tinyint NOT NULL DEFAULT '0',
  `metodologia` tinyint NOT NULL DEFAULT '0',
  `propuestas` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `idModuloCiclo` int unsigned DEFAULT NULL,
  `curso` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `profesor` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `programaciones_idmodulociclo_foreign` (`idModuloCiclo`),
  CONSTRAINT `programaciones_idmodulociclo_foreign` FOREIGN KEY (`idModuloCiclo`) REFERENCES `modulo_ciclos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `provincias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `provincias` (
  `id` varchar(2) NOT NULL,
  `nombre` varchar(60) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `reservas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reservas` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `dia` date NOT NULL,
  `hora` tinyint NOT NULL,
  `idProfesor` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `idEspacio` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `observaciones` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `reservas_idprofesor_foreign` (`idProfesor`),
  KEY `reservas_idespacio_foreign` (`idEspacio`),
  CONSTRAINT `reservas_idespacio_foreign` FOREIGN KEY (`idEspacio`) REFERENCES `espacios` (`aula`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `reservas_idprofesor_foreign` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`dni`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `resultados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `resultados` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `evaluacion` tinyint NOT NULL,
  `matriculados` tinyint NOT NULL,
  `evaluados` tinyint NOT NULL,
  `aprobados` tinyint NOT NULL,
  `observaciones` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `idProfesor` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `udProg` tinyint DEFAULT NULL,
  `udImp` tinyint DEFAULT NULL,
  `idModuloGrupo` int unsigned DEFAULT NULL,
  `adquiridosSI` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `adquiridosNO` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `resultados_idmodulogrupo_evaluacion_unique` (`idModuloGrupo`,`evaluacion`),
  KEY `resultados_idprofesor_foreign` (`idProfesor`),
  CONSTRAINT `resultados_idmodulogrupo_foreign` FOREIGN KEY (`idModuloGrupo`) REFERENCES `modulo_grupos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `resultados_idprofesor_foreign` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`dni`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `reuniones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reuniones` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `tipo` tinyint NOT NULL DEFAULT '0',
  `grupo` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `curso` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `numero` varchar(2) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `fecha` datetime NOT NULL,
  `descripcion` varchar(120) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `objetivos` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `idProfesor` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `idEspacio` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `archivada` tinyint(1) NOT NULL DEFAULT '0',
  `fichero` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reuniones_idprofesor_foreign` (`idProfesor`),
  CONSTRAINT `reuniones_idprofesor_foreign` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`dni`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `signatures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `signatures` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tipus` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `idProfesor` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `idSao` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sendTo` tinyint(1) NOT NULL DEFAULT '0',
  `signed` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `signatures_idprofesor_foreign` (`idProfesor`),
  KEY `signatures_idsao_foreign` (`idSao`),
  CONSTRAINT `signatures_idprofesor_foreign` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`dni`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `signatures_idsao_foreign` FOREIGN KEY (`idSao`) REFERENCES `alumno_fcts` (`idSao`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `solicitudes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `solicitudes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `idAlumno` varchar(8) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `idProfesor` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `text1` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `text2` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `text3` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fecha` date NOT NULL,
  `fechasolucion` date DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '0',
  `idOrientador` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `solucion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `solicitudes_idalumno_foreign` (`idAlumno`),
  KEY `solicitudes_idprofesor_foreign` (`idProfesor`),
  KEY `solicitudes_idorientador_foreign` (`idOrientador`),
  CONSTRAINT `solicitudes_idalumno_foreign` FOREIGN KEY (`idAlumno`) REFERENCES `alumnos` (`nia`) ON UPDATE CASCADE,
  CONSTRAINT `solicitudes_idorientador_foreign` FOREIGN KEY (`idOrientador`) REFERENCES `profesores` (`dni`) ON UPDATE CASCADE,
  CONSTRAINT `solicitudes_idprofesor_foreign` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`dni`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tasks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `vencimiento` date NOT NULL,
  `fichero` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `enlace` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `destinatario` tinyint NOT NULL DEFAULT '1',
  `informativa` tinyint(1) NOT NULL DEFAULT '1',
  `action` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activa` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tasks_profesores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tasks_profesores` (
  `id_task` bigint unsigned NOT NULL,
  `id_profesor` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `check` tinyint(1) NOT NULL DEFAULT '0',
  `valid` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  KEY `tasks_profesores_id_task_foreign` (`id_task`),
  KEY `tasks_profesores_id_profesor_foreign` (`id_profesor`),
  CONSTRAINT `tasks_profesores_id_profesor_foreign` FOREIGN KEY (`id_profesor`) REFERENCES `profesores` (`dni`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tasks_profesores_id_task_foreign` FOREIGN KEY (`id_task`) REFERENCES `tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tipo_expedientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipo_expedientes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol` tinyint NOT NULL,
  `orientacion` tinyint(1) NOT NULL DEFAULT '0',
  `informe` tinyint(1) NOT NULL DEFAULT '0',
  `vista` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tipoincidencias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipoincidencias` (
  `id` tinyint NOT NULL,
  `nombre` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `idProfesor` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `tipus` tinyint DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tipoincidencias_idprofesor_index` (`idProfesor`),
  CONSTRAINT `tipoincidencias_idprofesor_foreign` FOREIGN KEY (`idProfesor`) REFERENCES `profesores` (`dni`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tutorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tutorias` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `descripcion` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `fichero` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `obligatoria` tinyint(1) NOT NULL,
  `desde` date NOT NULL,
  `hasta` date NOT NULL,
  `grupos` tinyint NOT NULL DEFAULT '0',
  `tipo` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tutorias_grupos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tutorias_grupos` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `idTutoria` int unsigned NOT NULL,
  `idGrupo` varchar(5) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `observaciones` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `fecha` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `tutorias_grupos_idtutoria_foreign` (`idTutoria`),
  KEY `tutorias_grupos_idgrupo_foreign` (`idGrupo`),
  CONSTRAINT `tutorias_grupos_idgrupo_foreign` FOREIGN KEY (`idGrupo`) REFERENCES `grupos` (`codigo`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tutorias_grupos_idtutoria_foreign` FOREIGN KEY (`idTutoria`) REFERENCES `tutorias` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `votes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `votes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `option_id` int unsigned NOT NULL,
  `idOption1` int unsigned NOT NULL,
  `idOption2` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `value` tinyint DEFAULT NULL,
  `text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `idPoll` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `votes_option_id_foreign` (`option_id`),
  KEY `votes_idmodulogrupo_foreign` (`idOption1`),
  KEY `votes_user_id_index` (`user_id`),
  KEY `votes_idprofesor_index` (`idOption2`),
  KEY `votes_idpoll_foreign` (`idPoll`),
  CONSTRAINT `votes_idpoll_foreign` FOREIGN KEY (`idPoll`) REFERENCES `polls` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `votes_option_id_foreign` FOREIGN KEY (`option_id`) REFERENCES `options` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `migrations` VALUES (1,'2022_03_15_172037_create_action_events_table',0);
INSERT INTO `migrations` VALUES (2,'2022_03_15_172037_create_actividad_grupo_table',0);
INSERT INTO `migrations` VALUES (3,'2022_03_15_172037_create_actividad_profesor_table',0);
INSERT INTO `migrations` VALUES (4,'2022_03_15_172037_create_actividades_table',0);
INSERT INTO `migrations` VALUES (5,'2022_03_15_172037_create_activities_table',0);
INSERT INTO `migrations` VALUES (6,'2022_03_15_172037_create_adjuntos_table',0);
INSERT INTO `migrations` VALUES (7,'2022_03_15_172037_create_alumno_fcts_table',0);
INSERT INTO `migrations` VALUES (8,'2022_03_15_172037_create_alumno_resultados_table',0);
INSERT INTO `migrations` VALUES (9,'2022_03_15_172037_create_alumno_reuniones_table',0);
INSERT INTO `migrations` VALUES (10,'2022_03_15_172037_create_alumnos_table',0);
INSERT INTO `migrations` VALUES (11,'2022_03_15_172037_create_alumnos_cursos_table',0);
INSERT INTO `migrations` VALUES (12,'2022_03_15_172037_create_alumnos_grupos_table',0);
INSERT INTO `migrations` VALUES (13,'2022_03_15_172037_create_alumnos_password_resets_table',0);
INSERT INTO `migrations` VALUES (14,'2022_03_15_172037_create_articulos_table',0);
INSERT INTO `migrations` VALUES (15,'2022_03_15_172037_create_articulos_lote_table',0);
INSERT INTO `migrations` VALUES (16,'2022_03_15_172037_create_asistencias_table',0);
INSERT INTO `migrations` VALUES (17,'2022_03_15_172037_create_autorizaciones_table',0);
INSERT INTO `migrations` VALUES (18,'2022_03_15_172037_create_centros_table',0);
INSERT INTO `migrations` VALUES (19,'2022_03_15_172037_create_centros_instructores_table',0);
INSERT INTO `migrations` VALUES (20,'2022_03_15_172037_create_ciclos_table',0);
INSERT INTO `migrations` VALUES (21,'2022_03_15_172037_create_colaboracion_votes_table',0);
INSERT INTO `migrations` VALUES (22,'2022_03_15_172037_create_colaboraciones_table',0);
INSERT INTO `migrations` VALUES (23,'2022_03_15_172037_create_colaboradores_table',0);
INSERT INTO `migrations` VALUES (24,'2022_03_15_172037_create_comision_fcts_table',0);
INSERT INTO `migrations` VALUES (25,'2022_03_15_172037_create_comisiones_table',0);
INSERT INTO `migrations` VALUES (26,'2022_03_15_172037_create_cursos_table',0);
INSERT INTO `migrations` VALUES (27,'2022_03_15_172037_create_departamentos_table',0);
INSERT INTO `migrations` VALUES (28,'2022_03_15_172037_create_documentos_table',0);
INSERT INTO `migrations` VALUES (29,'2022_03_15_172037_create_empresas_table',0);
INSERT INTO `migrations` VALUES (30,'2022_03_15_172037_create_espacios_table',0);
INSERT INTO `migrations` VALUES (31,'2022_03_15_172037_create_expedientes_table',0);
INSERT INTO `migrations` VALUES (32,'2022_03_15_172037_create_failed_jobs_table',0);
INSERT INTO `migrations` VALUES (33,'2022_03_15_172037_create_faltas_table',0);
INSERT INTO `migrations` VALUES (34,'2022_03_15_172037_create_faltas_itaca_table',0);
INSERT INTO `migrations` VALUES (35,'2022_03_15_172037_create_faltas_profesores_table',0);
INSERT INTO `migrations` VALUES (36,'2022_03_15_172037_create_fcts_table',0);
INSERT INTO `migrations` VALUES (37,'2022_03_15_172037_create_grupos_table',0);
INSERT INTO `migrations` VALUES (38,'2022_03_15_172037_create_grupos_trabajo_table',0);
INSERT INTO `migrations` VALUES (39,'2022_03_15_172037_create_guardias_table',0);
INSERT INTO `migrations` VALUES (40,'2022_03_15_172037_create_horarios_table',0);
INSERT INTO `migrations` VALUES (41,'2022_03_15_172037_create_horas_table',0);
INSERT INTO `migrations` VALUES (42,'2022_03_15_172037_create_incidencias_table',0);
INSERT INTO `migrations` VALUES (43,'2022_03_15_172037_create_instructores_table',0);
INSERT INTO `migrations` VALUES (44,'2022_03_15_172037_create_jobs_table',0);
INSERT INTO `migrations` VALUES (45,'2022_03_15_172037_create_lotes_table',0);
INSERT INTO `migrations` VALUES (46,'2022_03_15_172037_create_materiales_table',0);
INSERT INTO `migrations` VALUES (47,'2022_03_15_172037_create_menus_table',0);
INSERT INTO `migrations` VALUES (48,'2022_03_15_172037_create_miembros_table',0);
INSERT INTO `migrations` VALUES (49,'2022_03_15_172037_create_modulo_ciclos_table',0);
INSERT INTO `migrations` VALUES (50,'2022_03_15_172037_create_modulo_grupos_table',0);
INSERT INTO `migrations` VALUES (51,'2022_03_15_172037_create_modulos_table',0);
INSERT INTO `migrations` VALUES (52,'2022_03_15_172037_create_municipios_table',0);
INSERT INTO `migrations` VALUES (53,'2022_03_15_172037_create_notifications_table',0);
INSERT INTO `migrations` VALUES (54,'2022_03_15_172037_create_ocupaciones_table',0);
INSERT INTO `migrations` VALUES (55,'2022_03_15_172037_create_options_table',0);
INSERT INTO `migrations` VALUES (56,'2022_03_15_172037_create_ordenes_reuniones_table',0);
INSERT INTO `migrations` VALUES (57,'2022_03_15_172037_create_ordenes_trabajo_table',0);
INSERT INTO `migrations` VALUES (58,'2022_03_15_172037_create_password_resets_table',0);
INSERT INTO `migrations` VALUES (59,'2022_03_15_172037_create_polls_table',0);
INSERT INTO `migrations` VALUES (60,'2022_03_15_172037_create_ppolls_table',0);
INSERT INTO `migrations` VALUES (61,'2022_03_15_172037_create_profesores_table',0);
INSERT INTO `migrations` VALUES (62,'2022_03_15_172037_create_profesores_password_resets_table',0);
INSERT INTO `migrations` VALUES (63,'2022_03_15_172037_create_programaciones_table',0);
INSERT INTO `migrations` VALUES (64,'2022_03_15_172037_create_provincias_table',0);
INSERT INTO `migrations` VALUES (65,'2022_03_15_172037_create_reservas_table',0);
INSERT INTO `migrations` VALUES (66,'2022_03_15_172037_create_resultados_table',0);
INSERT INTO `migrations` VALUES (67,'2022_03_15_172037_create_reuniones_table',0);
INSERT INTO `migrations` VALUES (68,'2022_03_15_172037_create_tasks_table',0);
INSERT INTO `migrations` VALUES (69,'2022_03_15_172037_create_tasks_profesores_table',0);
INSERT INTO `migrations` VALUES (70,'2022_03_15_172037_create_tipo_expedientes_table',0);
INSERT INTO `migrations` VALUES (71,'2022_03_15_172037_create_tipoincidencias_table',0);
INSERT INTO `migrations` VALUES (72,'2022_03_15_172037_create_tutorias_table',0);
INSERT INTO `migrations` VALUES (73,'2022_03_15_172037_create_tutorias_grupos_table',0);
INSERT INTO `migrations` VALUES (74,'2022_03_15_172037_create_votes_table',0);
INSERT INTO `migrations` VALUES (75,'2022_03_15_172039_add_foreign_keys_to_actividad_grupo_table',0);
INSERT INTO `migrations` VALUES (76,'2022_03_15_172039_add_foreign_keys_to_actividad_profesor_table',0);
INSERT INTO `migrations` VALUES (77,'2022_03_15_172039_add_foreign_keys_to_actividades_table',0);
INSERT INTO `migrations` VALUES (78,'2022_03_15_172039_add_foreign_keys_to_alumno_fcts_table',0);
INSERT INTO `migrations` VALUES (79,'2022_03_15_172039_add_foreign_keys_to_alumno_resultados_table',0);
INSERT INTO `migrations` VALUES (80,'2022_03_15_172039_add_foreign_keys_to_alumno_reuniones_table',0);
INSERT INTO `migrations` VALUES (81,'2022_03_15_172039_add_foreign_keys_to_alumnos_cursos_table',0);
INSERT INTO `migrations` VALUES (82,'2022_03_15_172039_add_foreign_keys_to_alumnos_grupos_table',0);
INSERT INTO `migrations` VALUES (83,'2022_03_15_172039_add_foreign_keys_to_articulos_lote_table',0);
INSERT INTO `migrations` VALUES (84,'2022_03_15_172039_add_foreign_keys_to_asistencias_table',0);
INSERT INTO `migrations` VALUES (85,'2022_03_15_172039_add_foreign_keys_to_autorizaciones_table',0);
INSERT INTO `migrations` VALUES (86,'2022_03_15_172039_add_foreign_keys_to_centros_table',0);
INSERT INTO `migrations` VALUES (87,'2022_03_15_172039_add_foreign_keys_to_centros_instructores_table',0);
INSERT INTO `migrations` VALUES (88,'2022_03_15_172039_add_foreign_keys_to_ciclos_table',0);
INSERT INTO `migrations` VALUES (89,'2022_03_15_172039_add_foreign_keys_to_colaboracion_votes_table',0);
INSERT INTO `migrations` VALUES (90,'2022_03_15_172039_add_foreign_keys_to_colaboraciones_table',0);
INSERT INTO `migrations` VALUES (91,'2022_03_15_172039_add_foreign_keys_to_colaboradores_table',0);
INSERT INTO `migrations` VALUES (92,'2022_03_15_172039_add_foreign_keys_to_comision_fcts_table',0);
INSERT INTO `migrations` VALUES (93,'2022_03_15_172039_add_foreign_keys_to_comisiones_table',0);
INSERT INTO `migrations` VALUES (94,'2022_03_15_172039_add_foreign_keys_to_espacios_table',0);
INSERT INTO `migrations` VALUES (95,'2022_03_15_172039_add_foreign_keys_to_expedientes_table',0);
INSERT INTO `migrations` VALUES (96,'2022_03_15_172039_add_foreign_keys_to_faltas_table',0);
INSERT INTO `migrations` VALUES (97,'2022_03_15_172039_add_foreign_keys_to_faltas_itaca_table',0);
INSERT INTO `migrations` VALUES (98,'2022_03_15_172039_add_foreign_keys_to_faltas_profesores_table',0);
INSERT INTO `migrations` VALUES (99,'2022_03_15_172039_add_foreign_keys_to_fcts_table',0);
INSERT INTO `migrations` VALUES (100,'2022_03_15_172039_add_foreign_keys_to_guardias_table',0);
INSERT INTO `migrations` VALUES (101,'2022_03_15_172039_add_foreign_keys_to_incidencias_table',0);
INSERT INTO `migrations` VALUES (102,'2022_03_15_172039_add_foreign_keys_to_materiales_table',0);
INSERT INTO `migrations` VALUES (103,'2022_03_15_172039_add_foreign_keys_to_miembros_table',0);
INSERT INTO `migrations` VALUES (104,'2022_03_15_172039_add_foreign_keys_to_modulo_ciclos_table',0);
INSERT INTO `migrations` VALUES (105,'2022_03_15_172039_add_foreign_keys_to_modulo_grupos_table',0);
INSERT INTO `migrations` VALUES (106,'2022_03_15_172039_add_foreign_keys_to_options_table',0);
INSERT INTO `migrations` VALUES (107,'2022_03_15_172039_add_foreign_keys_to_ordenes_reuniones_table',0);
INSERT INTO `migrations` VALUES (108,'2022_03_15_172039_add_foreign_keys_to_ordenes_trabajo_table',0);
INSERT INTO `migrations` VALUES (109,'2022_03_15_172039_add_foreign_keys_to_polls_table',0);
INSERT INTO `migrations` VALUES (110,'2022_03_15_172039_add_foreign_keys_to_programaciones_table',0);
INSERT INTO `migrations` VALUES (111,'2022_03_15_172039_add_foreign_keys_to_reservas_table',0);
INSERT INTO `migrations` VALUES (112,'2022_03_15_172039_add_foreign_keys_to_resultados_table',0);
INSERT INTO `migrations` VALUES (113,'2022_03_15_172039_add_foreign_keys_to_reuniones_table',0);
INSERT INTO `migrations` VALUES (114,'2022_03_15_172039_add_foreign_keys_to_tasks_profesores_table',0);
INSERT INTO `migrations` VALUES (115,'2022_03_15_172039_add_foreign_keys_to_tipoincidencias_table',0);
INSERT INTO `migrations` VALUES (116,'2022_03_15_172039_add_foreign_keys_to_tutorias_grupos_table',0);
INSERT INTO `migrations` VALUES (117,'2022_03_15_172039_add_foreign_keys_to_votes_table',0);
INSERT INTO `migrations` VALUES (118,'2022_04_12_111332_alter_lote_table',1);
INSERT INTO `migrations` VALUES (119,'2022_04_28_111332_alter_fct_table',2);
INSERT INTO `migrations` VALUES (120,'2022_04_28_111532_alter_fct_table_1',3);
INSERT INTO `migrations` VALUES (121,'2022_05_17_111532_alter_espacios_table',4);
INSERT INTO `migrations` VALUES (122,'2022_05_23_195731_create_solicitudes_table',5);
INSERT INTO `migrations` VALUES (123,'2022_05_23_202039_add_foreign_keys_to_solicitudes_table',5);
INSERT INTO `migrations` VALUES (124,'2022_07_05_111532_alter_profesores_table',6);
INSERT INTO `migrations` VALUES (125,'2022_09_21_111532_alter_solicitudes_table',7);
INSERT INTO `migrations` VALUES (126,'2022_10_18_111532_alter_comisiones_table',8);
INSERT INTO `migrations` VALUES (127,'2022_10_21_111530_alter_alumno_fcts_table_1',9);
INSERT INTO `migrations` VALUES (128,'2022_10_22_111532_alter_empresas_table',9);
INSERT INTO `migrations` VALUES (129,'2022_10_31_111530_alter_alumno_fcts_table_2',10);
INSERT INTO `migrations` VALUES (130,'2022_11_05_111532_alter_centros_table',10);
INSERT INTO `migrations` VALUES (131,'2022_11_06_111532_alter_adjuntos_table',10);
INSERT INTO `migrations` VALUES (132,'2022_11_18_111530_alter_alumno_fcts_table_3',11);
INSERT INTO `migrations` VALUES (133,'2022_12_06_111530_alter_colaboraciones_table',12);
INSERT INTO `migrations` VALUES (134,'2022_12_22_111530_alter_colaboradores_table',13);
INSERT INTO `migrations` VALUES (135,'2023_01_03_195731_create_erasmus_table',14);
INSERT INTO `migrations` VALUES (136,'2023_01_03_201532_alter_fct_table_2',14);
INSERT INTO `migrations` VALUES (137,'2023_03_03_201532_alter_fct_table_3',15);
INSERT INTO `migrations` VALUES (139,'2023_03_28_172037_create_materiales_baja_table',16);
INSERT INTO `migrations` VALUES (140,'2023_04_17_172037_create_signatures_table',17);
INSERT INTO `migrations` VALUES (143,'2022_12_18_111530_alter_instructores_table',18);
INSERT INTO `migrations` VALUES (144,'2023_05_15_172037_create_settings_table',18);
