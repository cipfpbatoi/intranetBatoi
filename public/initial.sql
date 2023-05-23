-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 172.16.9.14:3306
-- Temps de generació: 23-05-2023 a les 12:03:31
-- Versió del servidor: 8.0.25-0ubuntu0.20.04.1
-- Versió de PHP: 8.1.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de dades: `intranet`
--

--
-- Bolcament de dades per a la taula `departamentos`
--


INSERT INTO `departamentos` (`id`, `cliteral`, `vliteral`, `depcurt`, `didactico`, `idProfesor`) VALUES
                                                                                                     (1, 'INGLES', 'ANGLES', 'Ang', 1, '052782223Y'),
                                                                                                     (2, 'SERVICIOS A LA COMUNIDAD', 'SERVEIS A LA COMUNITAT', 'SCo', 1, '021673101V'),
                                                                                                     (3, 'IMAGEN PERSONAL', 'IMATGE PERSONAL', 'Img', 1, '021663517R'),
                                                                                                     (4, 'PROGRAMAS DE GARANTIA SOCIAL', 'PROGRAMES DE GARANTIA SOCIAL', 'Pgs', 0, NULL),
                                                                                                     (5, 'DEPARTAMENTO ADMINISTRATIVO', 'DEPARTAMENT ADMINISTRACIÓ', 'Adm', 1, '021661601V'),
                                                                                                     (6, 'DEPARTAMENTO SANITARIO', 'DEPARTAMENT SANITARI', 'San', 1, '073993296L'),
                                                                                                     (9, 'EXTENSION CULTURAL', 'EXTENSIO CULTURAL', 'Cul', 0, NULL),
                                                                                                     (10, 'HOSTELERIA Y TURISMO', 'HOSTELERIA I TURISME', 'Hos', 1, '020428584F'),
                                                                                                     (12, 'FORMACION Y ORIENTACION LABORA', 'FORMACIO I ORIENTACIO LABORAL', 'Fol', 1, '052736808Q'),
                                                                                                     (14, 'FRANCES', 'FRANCES', 'Fra', 0, NULL),
                                                                                                     (18, 'ORIENTACION', 'ORIENTACIO', 'Ori', 1, '048293140V'),
                                                                                                     (22, 'CICLOS FORMATIVOS', 'CICLES FORMATIUS', 'Cf', 0, NULL),
                                                                                                     (23, 'FCT', 'FCT', 'Fct', 0, NULL),
                                                                                                     (24, 'DEPARTAMENTO INFORMATICA', 'DEPARTAMENT INFORMÀTICA', 'Inf', 1, '021676764T'),
                                                                                                     (25, 'Seguridad y Medio Ambiente', 'Seguretat i Medi Ambient', 'SMA', 1, '029072738W'),
                                                                                                     (90, 'Personal No Docente', 'Personal No Docent', 'PND', 0, NULL),
                                                                                                     (91, 'Personal Limpieza', 'Personal Neteja', 'NET', 0, NULL),
                                                                                                     (99, 'Desconegut', 'Desconegut', '???', 0, NULL);
--
-- Bolcament de dades per a la taula `ciclos`
--

INSERT INTO `ciclos` (`id`, `ciclo`, `departamento`, `tipo`, `normativa`, `titol`, `rd`, `rd2`, `vliteral`, `cliteral`, `horasFct`, `acronim`, `llocTreball`, `dataSignaturaDual`) VALUES
(3, 'CFM APD (LOE)', 2, 1, 'LOE', 'TÈCNIC EN ATENCIÓ A PERSONES EN SITUACIÓ DE DEPENDÈNCIA', '1593/2011 (BOE 15/12/2011) i l\'Orde 30/2015, de 13 de març (DOGV 25/03/2015)', NULL, 'Atenció a persones en situació de dependència', 'Atención a personas en situación de dependencia', 400, NULL, NULL, NULL),
(4, 'CFM FARMACIA (LOE)', 6, 1, 'LOE', 'TÈCNIC EN FARMÀCIA I PARAFARMÀCIA', '1689/2007 (BOE 17/01/2008) i l\'Orde de 29 de juliol (DOGV 02/09/2009)', NULL, 'Farmàcia i parafarmàcia', 'Farmacia y parafarmacia', 400, NULL, NULL, NULL),
(8, 'CFM CAE (LOGSE)', 6, 1, 'LOGSE', 'TÈCNIC EN CURES AUXILIARS D\'INFERMERIA', '546/1995 (BOE 05/06/95)', NULL, 'Cures auxiliares d\'infermeria', 'Cuidados auxiliares de enfermería', 440, NULL, NULL, NULL),
(12, 'CFM CUINA (LOE)', 10, 1, 'LOE', 'TÈCNIC EN CUINA I GASTRONOMIA', '1396/2007 BOE 23-11-2007 i l\'Orde de 29 de juliol de 2009 (DOGV 03/09/2009)', NULL, 'Cuina i gastronomia', 'Cocina y gastronomía', 400, NULL, NULL, NULL),
(16, 'CFM ESTÈTICA I BELL. (LOE)', 3, 1, 'LOE', 'TÈCNIC EN ESTÈTICA I BELLESA', '256/2011 de 28 de febrero (BOE 7/04/2011) i el Decret 158/2017, de 6 d\'octubre (DOGV 20/10/2017)', NULL, 'Estètica i bellesa', 'Estética y belleza', 400, NULL, NULL, NULL),
(18, 'CFM GESTIÓ ADMVA. (LOE)', 5, 1, 'LOE', 'TÈCNIC EN GESTIÓ ADMINISTRATIVA', '1631/2009 (BOE 1/12/2009) modificat pel Rd 1126/2010 (BOE 11/09/2010) i Orde 37/2012', NULL, 'Gestió administrativa', 'Gestión administrativa', 400, NULL, NULL, NULL),
(20, 'CFM PERRUQUERIA (LOE)', 3, 1, 'LOE', 'TÈCNIC EN PERRUQUERIA I COSMÈTICA CAPILAR', '1588/2011 (BOE 15/12/2011) i l\'Orde 32/2015, de 13 de març (DOGV 26/03/2015)', NULL, 'Perruqueria i cosmètica capilar', 'Peluquería y cosmética capilar', 400, NULL, NULL, NULL),
(22, 'CFM SERV. RESTAURACIÓ (LOE)', 10, 1, 'LOE', 'TÈCNIC EN SERVICIS EN RESTAURACIÓ', '1690/2007 BOE 18-01-2008 i l\'Orde de 29 de juliol de 2009 (DOGV 04/09/2009)', NULL, 'Servicis en restauració', 'Servicios en restauración', 400, NULL, NULL, NULL),
(24, 'CFM SMX  (LOE)', 24, 1, 'LOE', 'TÈCNIC EN SISTEMES MICROINFORMÀTICS I XARXES', '1691/2007, BOE 17-01-2008 i l\'Orde de 29 de juliol de 2009 (DOGV 03/09/2009)', NULL, 'Sistemes microinformàtics i xarxes', 'Sistemas microinformáticos y redes', 380, NULL, NULL, NULL),
(28, 'CFS ADM. I FINANC. (LOE)', 5, 2, 'LOE', 'TÈCNIC SUPERIOR EN ADMINISTRACIÓ I FINANCES.', '1584/2011 de 4 de novembre (BOE 15/12/2011) i l\'Orde 13/2015 de 5 de març de 2015 (DOGV 10/03/2015)', NULL, 'Administració i finances', 'Administración y finanzas', 400, NULL, NULL, NULL),
(29, 'CFS ASSISTENCIA A LA DIRECCIÓ (LOE)', 5, 2, 'LOE', 'TÈCNIC SUPERIOR EN ASSITÈNCIA A LA DIRECCIÓ', '1584/2011 de 4 de novembre (BOE 15/12/2011) i l\'Orde 13/2015 de 5 de març de 2015 (DOGV 10/03/2015)', NULL, 'Assitència a la direcció', 'Asistencia a la dirección', 400, NULL, NULL, NULL),
(30, 'CFS ASIX (LOE)', 24, 2, 'LOE', 'TÈCNIC SUPERIOR EN ADMINISTRACIÓ DE SISTEMES INFORMÀTICS EN XARXA', '1629/2009 (BOE 18-11-2009) i l\'Orde 36/2012 de 22 de juny (DOGV 06/07/2012)', NULL, 'Administració de sistemes informàtics en xarxa', 'Administración de sistemas informáticos en red', 400, NULL, NULL, NULL),
(32, 'CFS DAM (LOE)', 24, 2, 'LOE', 'TÈCNIC SUPERIOR EN DESENROTLLAMENT D\'APLICACIONS MULTIPLATAFORMA', '450/2010, BOE 20-05-2010 i l\'Orde 58/2012 de 5 de setembre de 2012 (DOGV 24/09/2012)', NULL, 'Desenrotllament d\'aplicacions multiplataforma', 'Desarrollo de aplicaciones multiplataforma', 400, 'DAM', 'Desenvolupador d\'aplicacions multiplataforma', NULL),
(35, 'CFS DAW (LOE)', 24, 2, 'LOE', 'TÈCNIC SUPERIOR EN DESENROTLLAMENT D\'APLICACIONS WEB', '686/2010 (BOE 12-06-2010) i l\'Orde 60/2012 de 25 de setembre (DOGV 08/10/2012)', NULL, 'Desenrotllament d\'aplicacions web', 'Desarrollo de aplicaciones  web', 400, 'DAW', NULL, NULL),
(36, 'CFS DIREC. CUINA (LOE)', 10, 2, 'LOE', 'TÈCNIC SUPERIOR EN DIRECCIÓ DE CUINA', '687/2010 (BOE 12-06-2010) i l\'Orde 32/2013 de 26 d\'abril (DOGV 06/05/2013)', NULL, 'Direcció de cuina', 'Dirección de cocina', 400, NULL, NULL, NULL),
(38, 'CFS DIREC.RESTAURACIÓ (LOE)', 10, 2, 'LOE', 'TÈCNIC SUPERIOR EN DIRECCIÓ DE SERVICIS DE RESTAURACIÓ', '688/2010 (BOE 12-06-2010) i l\'Orde 24/2013, de 21 d\'abril (DOGV 25/04/2013)', NULL, 'Direcció en servicis de restauració', 'Dirección en servicios de restauración', 400, NULL, NULL, NULL),
(39, 'CFS EDUC.INFANTIL (LOE)', 2, 2, 'LOE', 'TÈCNIC SUPERIOR EN EDUCACIÓ INFANTIL.', '1394/2007 (BOE 24/11/2007) i l\'Orde de 29 de juliol de 2009 (DOGV 02/09/2009)', NULL, 'Educació infantil', 'Educación infantil', 400, NULL, NULL, NULL),
(42, 'CFS ESTET.INTEG. (LOE)', 3, 2, 'LOE', 'TÈCNIC SUPERIOR EN ESTÈTICA INTEGRAL I BENESTAR', '881/2011, (BOE 23/07/2011) i l\'Orde 19/2015 de 5 de març de 2015 (DOGV 10/03/2015)', NULL, 'Estètica integral i benestar', 'Estética integral y bienestar', 400, NULL, NULL, NULL),
(44, 'CFS INTEGR.SOCIAL (LOE)', 2, 2, 'LOE', 'TÈCNIC SUPERIOR EN INTEGRACIÓ SOCIAL', '1074/2012 de 13 de juliol (BOE 15/08/12) i l\'Orde 29/2017, de 3 de març (DOGV 13/03/2017)', NULL, 'Integració Social', 'Integración Social', 400, NULL, NULL, NULL),
(45, 'CFS LABORATORI (LOE)', 6, 2, 'LOE', 'TÈCNIC SUPERIOR EN LABORATORI CLÍNIC I BIOMÈDIC', '771/2014 (BOE 4/10/2014)', NULL, 'Laboratori clínic i biomèdic', 'Laboratorio clinico y biomédico', 400, NULL, NULL, NULL),
(51, 'CFS RXMN (LOE)', 6, 2, 'LOE', 'TÈCNIC SUPERIOR EN IMATGE PER AL DIAGNÒSTIC I MEDICINA NUCLEAR', '770/2014, de 12 de setembre (BOE 04/10/2014)', NULL, 'Imatge per al diagnòtic i medicina nuclear', 'Imagen para el diagnóstico y medicina nuclear', 400, NULL, NULL, NULL),
(54, 'CFS SALUT AMBIENTAL (LOGSE)', 6, 2, 'LOGSE', 'TÈCNIC SUPERIOR EN SALUT AMBIENTAL', '540/95 BOE 10-06-95', NULL, 'Salut ambiental', 'Salud ambiental', 400, NULL, NULL, NULL),
(56, 'CFS TASOCIT (LOE)', 2, 2, 'LOE', 'TÈCNIC SUPERIOR EN ANIMACIÓ SOCIOCULTURAL I TURÍSTICA', '1684/2011 de 18 de novembre (BOE 27/12/11) i el Decret 120/2017, de 8 de setembre (DOGV 18/09/2017)', NULL, 'Animació Sociocultural i turística', 'Animación Sociocultural y turística', 400, NULL, NULL, NULL),
(57, 'TPB Perruqueria i Estética', 3, 3, 'LOE', 'PROFESSIONAL BÀSIC EN PERRUQUERIA I ESTÈTICA', '127/2014, de 28 febrer, Rd 356/2014, de 16 maig i el D 185/2014, de 31 d\'octubre', NULL, 'FP Bàsica Perruqueria i Estètica', 'FP Básica Peluqueria i Estética', 120, NULL, NULL, NULL),
(58, 'CFS QUIMICA I SALUT AMBIENTAL', 25, 2, 'LOE', 'TÈCNIC SUPERIOR EN QUÍMICA I SALUT AMBIENTAL', '283/2019 (BOE 22/4/2019)', NULL, 'Química i Salut Ambiental', 'Química i Salut Ambiental', 400, 'QSM', NULL, NULL),
(59, 'MASTER CIBERSEGURETAT', 24, 2, 'LOE', 'MASTER SEGURETAT', '283/2019 (BOE 22/4/2019)', NULL, 'Curs d\'EspecialiTzació en Ciberseguretat en Entorns de les Tecnologies de la Información', 'Curso de Especialización en Ciberseguridad en Entornos de las Tecnologías de la Información', 0, 'MCS', NULL, NULL);






--
-- Bolcament de dades per a la taula `horas`
--

INSERT INTO `horas` (`codigo`, `turno`, `hora_ini`, `hora_fin`) VALUES
(1, 'mati', '07:55', '08:50'),
(2, 'mati', '08:50', '09:45'),
(3, 'mati', '09:45', '10:40'),
(4, 'pati', '10:40', '11:00'),
(5, 'mati', '11:00', '11:55'),
(6, 'mati', '11:55', '12:50'),
(7, 'mati', '12:50', '13:45'),
(8, 'mati', '13:45', '14:40'),
(9, 'migdia', '14:40', '14:55'),
(10, 'vesprada', '14:55', '15:50'),
(11, 'vesprada', '15:50', '16:45'),
(12, 'vesprada', '16:45', '17:40'),
(13, 'pati', '17:40', '18:00'),
(14, 'vesprada', '18:00', '18:55'),
(15, 'vesprada', '18:55', '19:50'),
(16, 'vesprada', '19:50', '20:45'),
(17, 'vesprada', '20:45', '21:40'),
(18, 'vesprada', '21:40', '22:35');



--
-- Bolcament de dades per a la taula `menus`
--

INSERT INTO `menus` (`id`, `nombre`, `url`, `class`, `rol`, `menu`, `submenu`, `activo`, `orden`, `ajuda`) VALUES
(1, 'perfil', '/perfil', 'fa-user pull-right', 1, 'topmenu', '', 1, 1, 'manual-profe.html#editar-perfil'),
(2, 'logout', '/logout', 'fa-power-off pull-right', 1, 'topmenu', '', 1, 3, ''),
(3, 'perfil', '/alumno/perfil', 'fa-user pull-right', 1, 'topalumno', '', 1, 1, ''),
(4, 'logout', '/alumno/logout', 'fa-power-off pull-right', 1, 'topalumno', '', 1, 2, ''),
(5, 'link', '', 'fa-external-link', 1, 'general', '', 1, 21, ''),
(6, 'edit', '', 'fa-graduation-cap', 3, 'general', '', 1, 4, ''),
(7, 'institution', '', 'fa-users', 17, 'general', '', 1, 6, ''),
(8, 'direccion', '', 'fa-home', 2, 'general', '', 1, 16, ''),
(9, 'administracion', '', 'fa-gears', 11, 'general', '', 1, 18, ''),
(10, 'gmail', 'https://www.gmail.com', '', 1, 'general', 'link', 1, 2, ''),
(11, 'moodle', 'http://moodle.cipfpbatoi.es', '', 1, 'general', 'link', 1, 1, ''),
(12, 'itaca', 'https://acces.edu.gva.es/', '', 1, 'general', 'link', 1, 3, ''),
(13, 'extraescolar', '/actividad', NULL, 3, 'general', 'paper', 1, 2, 'manual-profe.html#activitats-extraescolars'),
(14, 'comision', '/comision', NULL, 3, 'general', 'paper', 1, 3, 'manual-profe.html#comisions-de-servei'),
(15, 'manipulador', '/curso', '', 2, 'general', 'direccion', 1, 1, ''),
(16, 'baja', '/falta', NULL, 3, 'general', 'paper', 1, 4, 'manual-profe.html#notificació-absències'),
(17, 'grupo', '/grupo', '', 3, 'general', 'edit', 1, 1, 'manual-profe.html#gestió-de-grups'),
(18, 'profesor', '/direccion/profesor', '', 2, 'general', 'direccion', 1, 2, ''),
(19, 'Authcomision', '/direccion/comision', '', 2, 'general', 'direccion', 1, 3, ''),
(20, 'Authactividad', '/direccion/actividad', '', 2, 'general', 'direccion', 1, 4, ''),
(21, 'claustro', '/departamentos', NULL, 3, 'general', 'edit', 1, 2, 'manual-profe.html#claustre'),
(22, 'menu', '/menu', NULL, 11, 'general', 'auxiliar', 1, 2, NULL),
(23, 'inventario', '', 'fa-cubes', 7, 'general', '', 1, 13, NULL),
(24, 'espacios', '/espacio', '', 7, 'general', 'inventario', 1, 3, ''),
(25, 'materiales', '/material', NULL, 7, 'general', 'inventario', 0, 4, NULL),
(26, 'incidencias', '/incidencia', NULL, 3, 'general', 'paper', 1, 5, 'manual-profe.html#gestió-dincidències'),
(27, 'Authfalta', '/direccion/falta', '', 2, 'general', 'direccion', 1, 5, ''),
(28, 'incidenciasmant', '/mantenimiento/incidencia', NULL, 7, 'general', 'incidencias', 1, 1, NULL),
(30, 'programacion', '/programacion', '', 3, 'general', 'edit', 1, 6, 'manual-profe.html#programacions'),
(31, 'Authprogram', '/departamento/programacion', '', 13, 'general', 'jefedep', 1, 1, ''),
(32, 'modulo', '/modulo', '', 11, 'general', 'auxiliar', 0, 1, ''),
(33, 'jefedep', '', 'fa-institution', 13, 'general', '', 1, 11, ''),
(35, 'progstate', '/direccion/programacion/list', '', 2, 'general', 'direccion', 1, 6, ''),
(36, 'expediente', '/expediente', NULL, 3, 'general', 'paper', 1, 1, 'manual-profe.html#expedients'),
(37, 'Authexpediente', '/direccion/expediente', '', 2, 'general', 'direccion', 1, 7, ''),
(38, 'Reunion', '/reunion', '', 3, 'general', 'gTrabajo', 1, 1, 'manual-profe.html#gestió-de-reunions'),
(39, 'gtrabajo', '', 'fa-edit', 3, 'general', '', 1, 10, ''),
(40, 'grtrabajo', '/grupotrabajo', '', 3, 'general', 'gTrabajo', 1, 2, ''),
(42, 'resultados', '/resultado', '', 3, 'general', 'edit', 1, 5, 'manual-profe.html#seguiments'),
(43, 'register', '/alumno/curso', '', 5, 'general', 'alumno', 1, 1, ''),
(44, 'documento', '/documento', NULL, 2, 'general', 'documents', 1, 6, 'manual-profe.html#gestió-de-centre'),
(45, 'nohanfichado', '/direccion/fichar/list', '', 2, 'general', 'control', 1, 1, ''),
(46, 'extraescolares', '/resultado/list', '', 13, 'general', 'jefedep', 1, 2, ''),
(48, 'empresa', '/empresa', NULL, 31, 'general', 'empresas', 1, 1, 'manual-fct-empreses.html'),
(49, 'guardia', '/guardia', '', 3, 'general', 'edit', 1, 4, 'manual-profe.html#guàrdia'),
(50, 'resultados', '/resultado/pdf', '', 17, 'general', 'institution', 1, 1, ''),
(51, 'lfaltas', '/direccion/falta/pdf', '', 2, 'general', 'control', 1, 4, ''),
(52, 'fct', '/fct', NULL, 31, 'general', 'practicas', 1, 1, NULL),
(53, 'fichar', '/fichar', 'fa-ticket', 23, 'general', '', 1, 1, ''),
(54, 'alumno', '', 'fa-graduation-cap', 5, 'general', '', 1, 20, ''),
(55, 'programacion', '/allProgramacion', NULL, 1, 'general', 'documents', 1, 1, 'manual-profe.html#programacions'),
(56, 'orientacion', '', 'fa-paperclip', 29, 'general', '', 1, 12, ''),
(57, 'acttut', '/actividad', '', 29, 'general', 'orientación', 1, 1, ''),
(58, 'tutoria', '/tutoria', '', 29, 'general', 'orientación', 1, 2, ''),
(59, 'tutoria', '/tutoria', '', 17, 'general', 'institution', 1, 2, ''),
(60, 'importacion', '/import', '', 11, 'general', 'administracion', 1, 2, ''),
(70, 'documents', '', 'fa-book', 1, 'general', '', 1, 3, ''),
(71, 'centro', '/documento/2/grupo', '', 3, 'general', 'documents', 1, 3, 'manual-profe.html#informació-de-centre'),
(72, 'proceso', '/documento/1/grupo', '', 1, 'general', 'documents', 1, 2, 'manual-profe.html#gestió-de-centre'),
(73, 'acta', '/documento/3/acta', '', 3, 'general', 'documents', 1, 4, 'manual-profe.html#actes'),
(74, 'proyecto', '/proyecto', '', 1, 'general', 'documents', 1, 5, 'manual-profe.html#projectes'),
(75, 'reserva', '/reserva', NULL, 3, 'general', 'paper', 1, 6, 'manual-profe.html#reservar-espai'),
(76, 'equipo', '/alumno/equipo', '', 5, 'general', 'alumno', 1, 2, ''),
(77, 'progstate', '/departamento/programacion/list', '', 13, 'general', 'jefedep', 1, 3, ''),
(78, 'apitoken', '/apiToken', '', 11, 'general', 'administracion', 1, 4, ''),
(80, 'Controlg', '/direccion/guardia/control', '', 2, 'general', 'control', 1, 5, ''),
(81, 'Controlp', '/direccion/fichar/control', '', 2, 'general', 'control', 1, 3, ''),
(82, 'equipodirectivo', '/equipoDirectivo', '', 3, 'general', 'edit', 1, 3, 'manual-profe.html#equip-directiu'),
(83, 'Controld', '/direccion/fichar/controlDia', '', 2, 'general', 'control', 1, 2, ''),
(84, 'ordentrabajo', '/mantenimiento/ordentrabajo', NULL, 7, 'general', 'incidencias', 1, 2, NULL),
(85, 'Controlreunion', '/direccion/reunion/list', '', 2, 'general', 'control', 1, 6, ''),
(86, 'control', '', 'fa-check', 2, 'general', '', 1, 15, ''),
(87, 'Controlsegui', '/resultado/list', '', 2, 'general', 'control', 0, 7, ''),
(88, 'cicle', '/ciclo', NULL, 11, 'general', 'auxiliar', 1, 3, NULL),
(89, 'practicas', NULL, 'fa-car', 31, 'general', '', 1, 8, ''),
(90, 'colaboracion', '/colaboracion', NULL, 3, 'general', 'empresas', 1, 5, NULL),
(91, 'Horarios', '/direccion/horarios/pdf', '', 2, 'general', 'control', 1, 8, ''),
(93, 'birret', '/itaca', NULL, 3, 'general', 'paper', 1, 7, 'manual-profe.html#oblit-birret'),
(94, 'Authbirret', '/direccion/falta_itaca', '', 2, 'general', 'direccion', 1, 8, ''),
(96, 'empresasc', '/empresaSC', NULL, 31, 'general', 'empresas', 1, 2, NULL),
(97, 'avaluar', '/avalFct', NULL, 31, 'general', 'practicas', 1, 3, 'manual-fct-avaluacio.html'),
(98, 'infdpto', '/direccion/infDpto', '', 2, 'general', 'documents', 1, 7, ''),
(99, 'expediente', '/expedienteO', '', 29, 'general', 'orientación', 0, 3, ''),
(101, 'Authhorarios', '/direccion/horarios/cambiar', '', 2, 'general', 'direccion', 1, 9, ''),
(102, 'Indexdocumento', '/direccion/documento', '', 2, 'general', 'documents', 1, 8, ''),
(103, 'Nuevocurso', '/nuevoCurso', NULL, 11, 'general', 'administracion', 1, 5, ''),
(104, 'Changeschedule', '/horario/change', NULL, 3, 'general', 'paper', 0, 8, 'manual-profe.html#canviar-horari'),
(106, 'modulociclo', '/modulo_ciclo', NULL, 11, 'general', 'auxiliar', 0, 4, ''),
(107, 'Actasfct', '/controlFct', '', 41, 'general', 'practicas', 1, 4, ''),
(108, 'Actualizacion', '/actualizacion', NULL, 11, 'general', 'administracion', 1, 6, ''),
(110, 'dual', '/dual', NULL, 37, 'general', 'dual', 1, 1, NULL),
(111, 'fctxal', '/alumnofct', NULL, 31, 'general', 'practicas', 1, 2, 'manual-fct-alumno.html'),
(112, 'micolaboracion', '/misColaboraciones', NULL, 31, 'general', 'empresas', 1, 4, 'manual-fct-gestio-contactes.html'),
(114, 'poll', '/doPoll', NULL, 5, 'general', 'alumno', 1, 3, NULL),
(116, 'importaprofesor', '/teacherImport', '', 11, 'general', 'administracion', 1, 3, ''),
(117, 'tipoincidencias', '/tipoincidencia', NULL, 11, 'general', 'auxiliar', 1, 5, NULL),
(118, 'paper', NULL, 'fa-pencil', 3, 'general', '', 1, 5, ''),
(120, 'dual', NULL, 'fa-car', 37, 'general', '', 1, 9, NULL),
(121, 'empresa', '/empresa', NULL, 37, 'general', 'dual', 1, 2, NULL),
(122, 'empresasc', '/empresaSC', NULL, 37, 'general', 'dual', 1, 3, NULL),
(123, 'Controlgb', '/direccion/guardiaBiblio/control', NULL, 2, 'general', 'control', 1, 9, NULL),
(124, 'Ppoll', '/ppoll', NULL, 43, 'general', 'qualitat', 1, 1, 'manual-profe.html#enquestes'),
(125, 'Poll', '/poll', NULL, 43, 'general', 'qualitat', 1, 2, 'manual-profe.html#enquestes'),
(126, 'qualitat', NULL, 'fa-line-chart', 43, 'general', '', 1, 2, NULL),
(127, 'enquestes', NULL, 'fa-pie-chart', 3, 'general', '', 1, 19, NULL),
(128, 'mypoll', '/myPoll', NULL, 3, 'general', 'enquestes', 1, 2, NULL),
(129, 'dopoll', '/doPoll', NULL, 1, 'general', 'enquestes', 1, 1, NULL),
(130, 'Spam', '/direccion/myMail', NULL, 2, 'general', 'direccion', 1, 10, NULL),
(131, 'Indexdocumento', '/qualitat/documento', NULL, 43, 'general', 'qualitat', 1, 3, NULL),
(132, 'Sendeval', '/sendAvaluacio', NULL, 11, 'general', 'administracion', 1, 1, NULL),
(133, 'ficurs', '/fiCurs', NULL, 3, 'general', 'edit', 1, 7, 'manual-profe.html#programacions'),
(134, 'facturas', '/direccion/lote', NULL, 2, 'general', 'inventario', 1, 2, NULL),
(135, 'Inventariar', '/inventaria', NULL, 13, 'general', 'jefedep', 1, 5, NULL),
(137, 'Articulos', '/articulo', NULL, 2, 'general', 'inventario', 1, 1, NULL),
(138, 'Muebles', '/inventario', NULL, 7, 'general', 'inventario', 1, 5, NULL),
(140, 'importaemail', '/importEmail', NULL, 11, 'general', 'administracion', 0, 7, NULL),
(141, 'cleancache', '/cleanCache', NULL, 2, 'general', 'administracion', 1, 8, NULL),
(142, 'empresa', '/alumno/empresas', NULL, 5, 'general', 'alumno', 1, 4, NULL),
(143, 'Materialbaja', '/mantenimiento/materialBaja', NULL, 7, 'general', 'inventario', 1, 6, NULL),
(144, 'facturas', '/lote', NULL, 13, 'general', 'jefedep', 1, 4, NULL),
(145, 'incidencias', '', 'fa-wrench', 7, 'general', '', 1, 14, NULL),
(146, 'secure', '/secure', NULL, 2, 'general', 'administracion', 1, 9, NULL),
(147, 'solicitud', '/solicitud', NULL, 17, 'general', 'paper', 1, 9, 'manual-profe.html#solicituds'),
(148, 'solicitudes', '/solicitudes', NULL, 29, 'general', 'orientación', 1, 4, NULL),
(149, 'fse', '/fse/acta', NULL, 17, 'general', 'gTrabajo', 1, 3, 'manual-profe.html#fse'),
(151, 'erasmus', '/empresaEr', NULL, 31, 'general', 'empresas', 1, 3, NULL),
(152, 'empresas', NULL, 'fa-hospital-o', 31, 'general', '', 1, 7, NULL),
(153, 'auxiliar', '', 'fa-table', 11, 'general', '', 1, 17, NULL),
(154, 'departamento', '/departamento', NULL, 11, 'general', 'auxiliar', 1, 6, NULL),
(155, 'task', '/task', NULL, 11, 'general', 'auxiliar', 1, 7, NULL),
(156, 'files', '/files', 'fa-file-archive-o pull-right', 3, 'topmenu', '', 1, 2, NULL),
(157, 'Propuestabaja', '/direccion/materialBaja', NULL, 2, 'general', 'inventario', 1, 7, NULL),
(158, 'Signatures', '/direccion/signatures', NULL, 2, 'general', 'direccion', 1, 11, NULL),
(159, 'ipguardia', '/ipguardia', NULL, 11, 'general', 'auxiliar', 1, 8, NULL),
(160, 'setting', '/setting', NULL, 11, 'general', 'auxiliar', 1, 9, NULL);

-- --------------------------------------------------------


--
-- Bolcament de dades per a la taula `settings`
--

INSERT INTO `settings` (`id`, `collection`, `key`, `value`) VALUES
(1, 'contacto', 'direccion', 'Carrer Societat Unió Musical, 8'),
(2, 'contacto', 'latitude', '38.691455'),
(3, 'contacto', 'longitude', '-0.496455'),
(4, 'contacto', 'telefono', '966 52 76 60'),
(5, 'contacto', 'web', 'https://www.cipfpbatoi.es'),
(6, 'contacto', 'nombre', 'Centre Integrat Formació Professional Batoi'),
(7, 'contacto', 'titulo', 'CIP FP Batoi'),
(8, 'contacto', 'fax', '966 52 76 61'),
(9, 'contacto', 'codi', '03012165'),
(10, 'contacto', 'email', '03012165.secretaria@gva.es'),
(11, 'contacto', 'poblacion', 'Alcoi'),
(12, 'contacto', 'provincia', 'Alacant'),
(13, 'contacto', 'postal', '03802'),
(14, 'contacto', 'mapa', 'https://www.google.es/maps/place/CIP+de+FP+Batoi/@38.691455,-0.4986437,17z/data=!3m1!4b1!4m5!3m4!1s0xd618702fd4eb5b1:0xab5dffe40dc99b43!8m2!3d38.691455!4d-0.496455'),
(15, 'contacto', 'host.web', 'https://intranet.cipfpbatoi.es'),
(16, 'contacto', 'host.email', 'intranet@cipfpbatoi.es'),
(17, 'contacto', 'host.dominio', 'cipfpbatoi.es'),
(18, 'contacto', 'host.externo', 'http://ext.intranet.cipfpbatoi.es'),
(19, 'avisos', 'secretario', '048290231Y'),
(20, 'avisos', 'director', '020823669K'),
(21, 'avisos', 'vicedirector', '021666224V'),
(22, 'avisos', 'jefeEstudios2', '029071324Z'),
(23, 'avisos', 'errores', '021652470V'),
(24, 'avisos', 'material', '048290231Y'),
(25, 'variables', 'controlDiario', '1'),
(26, 'variables', 'diasNoCompleta', '45'),
(27, 'variables', 'reservaAforo', '1.2'),
(28, 'variables', 'comisionFCTexterna', '1'),
(29, 'variables', 'httpFCTexterna', 'http://www.fpxativa.es/admin'),
(30, 'variables', 'enquestaInstructor', 'https://forms.office.com/r/rMqmGzMbTn'),
(31, 'variables', 'actividadImg', '0'),
(32, 'variables', 'altaInstructores', '0'),
(33, 'variables', 'ipDomotica', 'http://172.16.10.74/api/devices/{dispositivo}/action'),
(34, 'variables', 'certificatFol', '2023-06-14'),
(35, 'variables', 'enquestesAutomatiques', '1'),
(36, 'variables', 'convocatoria', '24j281hdofd3'),
(37, 'variables', 'fitxerMatricula', 'email.matricula'),
(38, 'contacto', 'direccion', 'Carrer Societat Unió Musical, 8'),
(39, 'contacto', 'latitude', '38.691455'),
(40, 'contacto', 'longitude', '-0.496455'),
(41, 'contacto', 'telefono', '966 52 76 60'),
(42, 'contacto', 'web', 'http://www.cipfpbatoi.es'),
(43, 'contacto', 'nombre', 'Centre Integrat Formació Professional Batoi'),
(44, 'contacto', 'titulo', 'CIP FP Batoi'),
(45, 'contacto', 'fax', '966 52 76 61'),
(46, 'contacto', 'codi', '03012165'),
(47, 'contacto', 'email', '03012165.secretaria@gva.es'),
(48, 'contacto', 'poblacion', 'Alcoi'),
(49, 'contacto', 'provincia', 'Alacant'),
(50, 'contacto', 'postal', '03802'),
(51, 'contacto', 'host.web', 'https://intranet.my'),
(52, 'contacto', 'host.email', 'igomis@cipfpbatoi.es'),
(53, 'contacto', 'host.dominio', 'cipfpbatoi.es'),
(54, 'contacto', 'host.externo', '0'),
(55, 'avisos', 'secretario', '048290231Y'),
(56, 'avisos', 'director', '020823669K'),
(57, 'avisos', 'vicedirector', '021666224V'),
(58, 'avisos', 'jefeEstudios2', '029071324Z'),
(59, 'avisos', 'errores', '021652470V'),
(60, 'avisos', 'material', '048290231Y'),
(61, 'variables', 'controlDiario', '1'),
(62, 'variables', 'diasNoCompleta', '45'),
(63, 'variables', 'reservaAforo', '1.2'),
(64, 'variables', 'comisionFCTexterna', '1'),
(65, 'variables', 'httpFCTexterna', 'http://www.fpxativa.es/admin'),
(66, 'variables', 'enquestaInstructor', 'https://forms.office.com/r/rMqmGzMbTn'),
(67, 'variables', 'actividadImg', '0'),
(68, 'variables', 'altaInstructores', '0'),
(69, 'variables', 'ipDomotica', 'http://172.16.10.74/api/devices/{dispositivo}/action');



--
-- Bolcament de dades per a la taula `tipoincidencias`
--

INSERT INTO `tipoincidencias` (`id`, `nombre`, `nom`, `idProfesor`, `tipus`) VALUES
(8, 'Mantenimento instalaciones', 'Mantenimiento instal.lacions', '021666224V', 1),
(9, 'Mantenimiento informático', 'Manteniment informàtic', '021637655Z', 1),
(10, 'Intranet', 'Intranet', '021652470V', 2),
(11, 'Sugerimientos, quejas y reclamaciones', 'Suggeriments, queixes i reclamacions', '029009695W', 2);
