-- Queries for common tables
-- pacientes
SELECT `id`, `name`, `activo`, `edad`, `Observacion`, `FechaIngreso`, `idtutor` FROM `nino` WHERE 1;

-- citas
SELECT `id`, `IdNino`, `IdUsuario`, `idGenerado`, `fecha`, `costo`, `Programado`, `Estatus`, `Tipo`, `FormaPago` FROM `Cita` WHERE 1;

-- areas
SELECT `id_area`, `nombre_area`, `descripcion` FROM `exp_areas_evaluacion` WHERE 1;

-- evaluaciones
SELECT `id_evaluacion`, `id_nino`, `id_usuario`, `id_area`, `fecha`, `observaciones` FROM `exp_evaluaciones` WHERE 1;

-- valoraciones por sesión
CREATE TABLE `exp_valoraciones_sesion` (
    `id_valoracion` INT AUTO_INCREMENT PRIMARY KEY,
    `id_nino` INT NOT NULL,
    `id_usuario` INT NOT NULL,
    `participacion` TINYINT,
    `atencion` TINYINT,
    `tarea_casa` TINYINT,
    `observaciones` TEXT,
    `fecha_valoracion` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`id_nino`) REFERENCES `nino`(`Id`),
    FOREIGN KEY (`id_usuario`) REFERENCES `Usuarios`(`id_usuario`)
);

-- progreso general del paciente
CREATE TABLE `exp_progreso_general` (
    `id_progreso` INT AUTO_INCREMENT PRIMARY KEY,
    `id_nino` INT NOT NULL,
    `id_usuario` INT NOT NULL,
    `lenguaje` TINYINT,
    `motricidad` TINYINT,
    `atencion` TINYINT,
    `memoria` TINYINT,
    `social` TINYINT,
    `observaciones` TEXT,
    `fecha_registro` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`id_nino`) REFERENCES `nino`(`Id`),
    FOREIGN KEY (`id_usuario`) REFERENCES `Usuarios`(`id_usuario`)
);

CREATE TABLE `exp_evaluaciones` (
  `id_evaluacion` int(11) NOT NULL,
  `id_nino` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_area` int(11) NOT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `observaciones` text DEFAULT NULL
) ;


CREATE TABLE `exp_opciones_pregunta` (
  `id_opcion` int(11) NOT NULL,
  `id_pregunta` int(11) NOT NULL,
  `texto_opcion` varchar(255) NOT NULL,
  `es_correcta` tinyint(1) DEFAULT 0
) ;


CREATE TABLE `exp_preguntas_evaluacion` (
  `id_pregunta` int(11) NOT NULL,
  `id_area` int(11) NOT NULL,
  `texto_pregunta` text NOT NULL,
  `tipo_respuesta` varchar(50) DEFAULT 'texto',
  `es_multiple` tinyint(1) DEFAULT 0
) ;
