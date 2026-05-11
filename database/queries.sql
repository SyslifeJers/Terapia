-- Queries for common tables
-- pacientes
SELECT `id`, `name`, `activo`, `edad`, `Observacion`, `FechaIngreso`, `idtutor` FROM `nino` WHERE 1;

-- citas
SELECT `id`, `IdNino`, `IdUsuario`, `idGenerado`, `fecha`, `costo`, `Programado`, `Estatus`, `Tipo`, `FormaPago` FROM `Cita` WHERE 1;

-- areas
SELECT `id_area`, `nombre_area`, `descripcion` FROM `exp_areas_evaluacion` WHERE 1;

-- evaluaciones
SELECT `id_evaluacion`, `id_nino`, `id_usuario`, `id_area`, `fecha`, `observaciones` FROM `exp_evaluaciones` WHERE 1;

-- examenes
SELECT `id_examen`, `id_area`, `id_usuario`, `nombre_examen`, `fecha_creacion` FROM `exp_examenes` WHERE 1;

-- secciones de examen
SELECT `id_seccion`, `id_examen`, `nombre_seccion` FROM `exp_secciones_examen` WHERE 1;

CREATE TABLE `exp_valoraciones_sesion` (
    `id_valoracion` INT AUTO_INCREMENT PRIMARY KEY,
    `id_nino` INT NOT NULL,
    `id_usuario` INT NOT NULL,
    `observaciones` TEXT,
    `fecha_valoracion` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`id_nino`) REFERENCES `nino`(`Id`),
    FOREIGN KEY (`id_usuario`) REFERENCES `Usuarios`(`id_usuario`)
);

CREATE TABLE `exp_criterios_evaluacion` (
    `id_criterio` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(150) NOT NULL,
    `descripcion` TEXT
);

-- criterios de evaluación sugeridos
-- INSERT INTO `exp_criterios_evaluacion` (`nombre`) VALUES
--     ('Permanencia'),
--     ('Irritabilidad'),
--     ('Habilidades sociales'),
--     ('Atención conjunta'),
--     ('Seguimiento de indicaciones'),
--     ('Cognición'),
--     ('Comunicación receptiva'),
--     ('Comunicación expresiva');

CREATE TABLE `exp_nino_criterio` (
    `id_nino` INT NOT NULL,
    `id_criterio` INT NOT NULL,
    PRIMARY KEY (`id_nino`, `id_criterio`),
    FOREIGN KEY (`id_nino`) REFERENCES `nino`(`Id`) ON DELETE CASCADE,
    FOREIGN KEY (`id_criterio`) REFERENCES `exp_criterios_evaluacion`(`id_criterio`) ON DELETE CASCADE
);

CREATE TABLE `exp_valoracion_detalle` (
    `id_detalle` INT AUTO_INCREMENT PRIMARY KEY,
    `id_valoracion` INT NOT NULL,
    `id_criterio` INT NOT NULL,
    `valor` TINYINT NOT NULL,
    FOREIGN KEY (`id_valoracion`) REFERENCES `exp_valoraciones_sesion`(`id_valoracion`) ON DELETE CASCADE,
    FOREIGN KEY (`id_criterio`) REFERENCES `exp_criterios_evaluacion`(`id_criterio`)
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

-- examenes
CREATE TABLE `exp_examenes` (
    `id_examen` INT AUTO_INCREMENT PRIMARY KEY,
    `id_area` INT NOT NULL,
    `id_usuario` INT NOT NULL,
    `nombre_examen` VARCHAR(255) NOT NULL,
    `fecha_creacion` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`id_area`) REFERENCES `exp_areas_evaluacion`(`id_area`),
    FOREIGN KEY (`id_usuario`) REFERENCES `Usuarios`(`id_usuario`)
);

-- secciones de examen
CREATE TABLE `exp_secciones_examen` (
    `id_seccion` INT AUTO_INCREMENT PRIMARY KEY,
    `id_examen` INT NOT NULL,
    `nombre_seccion` VARCHAR(255) NOT NULL,
    FOREIGN KEY (`id_examen`) REFERENCES `exp_examenes`(`id_examen`)
);

-- preguntas de evaluacion
CREATE TABLE `exp_preguntas_evaluacion` (
    `id_pregunta` INT AUTO_INCREMENT PRIMARY KEY,
    `id_seccion` INT NOT NULL,
    `pregunta` TEXT NOT NULL,
    FOREIGN KEY (`id_seccion`) REFERENCES `exp_secciones_examen`(`id_seccion`)
);

-- opciones disponibles para preguntas
CREATE TABLE `exp_opciones_pregunta` (
    `id_opcion` INT AUTO_INCREMENT PRIMARY KEY,
    `texto` VARCHAR(255) NOT NULL
);

-- relacion muchas-a-muchas entre preguntas y opciones
CREATE TABLE `exp_pregunta_opcion` (
    `id_pregunta` INT NOT NULL,
    `id_opcion` INT NOT NULL,
    PRIMARY KEY (`id_pregunta`, `id_opcion`),
    FOREIGN KEY (`id_pregunta`) REFERENCES `exp_preguntas_evaluacion`(`id_pregunta`),
    FOREIGN KEY (`id_opcion`) REFERENCES `exp_opciones_pregunta`(`id_opcion`)
);

-- evaluacion de examen aplicada
CREATE TABLE `exp_evaluacion_examen` (
    `id_eval` INT AUTO_INCREMENT PRIMARY KEY,
    `id_examen` INT NOT NULL,
    `id_nino` INT NOT NULL,
    `id_usuario` INT NOT NULL,
    `respuestas` TEXT NOT NULL,
    `status` TINYINT DEFAULT 0,
    `fecha` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`id_examen`) REFERENCES `exp_examenes`(`id_examen`),
    FOREIGN KEY (`id_nino`) REFERENCES `nino`(`Id`),
    FOREIGN KEY (`id_usuario`) REFERENCES `Usuarios`(`id`)
);

-- evaluaciones fotográficas de paciente
CREATE TABLE `exp_evaluacion_fotos` (
    `id_eval_foto` INT AUTO_INCREMENT PRIMARY KEY,
    `id_nino` INT NOT NULL,
    `titulo` VARCHAR(255) NOT NULL,
    `seccion` VARCHAR(255) NOT NULL,
    `fecha` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`id_nino`) REFERENCES `nino`(`Id`)
);

CREATE TABLE `exp_evaluacion_fotos_imagenes` (
    `id_imagen` INT AUTO_INCREMENT PRIMARY KEY,
    `id_eval_foto` INT NOT NULL,
    `ruta` VARCHAR(255) NOT NULL,
    FOREIGN KEY (`id_eval_foto`) REFERENCES `exp_evaluacion_fotos`(`id_eval_foto`)
);

-- seguimiento de pendientes por flujo/perfil/tarea
DROP TABLE IF EXISTS `spu_paciente_tareas`;
DROP TABLE IF EXISTS `spu_paciente_flujos`;
DROP TABLE IF EXISTS `spu_tareas`;
DROP TABLE IF EXISTS `spu_perfiles`;
DROP TABLE IF EXISTS `spu_flujos`;

CREATE TABLE `spu_flujos` (
    `id_flujo` INT AUTO_INCREMENT PRIMARY KEY,
    `slug` VARCHAR(80) NOT NULL UNIQUE,
    `nombre` VARCHAR(150) NOT NULL,
    `descripcion` TEXT NULL,
    `icon` VARCHAR(80) NULL,
    `color` VARCHAR(20) NULL,
    `orden` INT NOT NULL DEFAULT 0,
    `activo` TINYINT(1) NOT NULL DEFAULT 1,
    `creado_en` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `actualizado_en` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE `spu_perfiles` (
    `id_perfil` INT AUTO_INCREMENT PRIMARY KEY,
    `id_flujo` INT NOT NULL,
    `slug` VARCHAR(80) NOT NULL UNIQUE,
    `nombre` VARCHAR(150) NOT NULL,
    `descripcion` TEXT NULL,
    `icon` VARCHAR(80) NULL,
    `color` VARCHAR(20) NULL,
    `orden` INT NOT NULL DEFAULT 0,
    `activo` TINYINT(1) NOT NULL DEFAULT 1,
    `creado_en` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `actualizado_en` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`id_flujo`) REFERENCES `spu_flujos`(`id_flujo`) ON DELETE CASCADE
);

CREATE TABLE `spu_tareas` (
    `id_tarea` INT AUTO_INCREMENT PRIMARY KEY,
    `id_perfil` INT NOT NULL,
    `slug` VARCHAR(80) NOT NULL UNIQUE,
    `titulo` VARCHAR(180) NOT NULL,
    `descripcion` TEXT NULL,
    `evidencia` ENUM('none','optional','required') NOT NULL DEFAULT 'none',
    `alerta_tipo` ENUM('none','citas') NOT NULL DEFAULT 'none',
    `alerta_cantidad` INT NOT NULL DEFAULT 0,
    `tipos_permitidos` TEXT NULL,
    `orden` INT NOT NULL DEFAULT 0,
    `activo` TINYINT(1) NOT NULL DEFAULT 1,
    `creado_en` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `actualizado_en` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`id_perfil`) REFERENCES `spu_perfiles`(`id_perfil`) ON DELETE CASCADE
);

CREATE TABLE `spu_paciente_flujos` (
    `id_paciente_flujo` INT AUTO_INCREMENT PRIMARY KEY,
    `id_nino` INT NOT NULL,
    `id_flujo` INT NOT NULL,
    `activo` TINYINT(1) NOT NULL DEFAULT 1,
    `actualizado_por` INT NULL,
    `creado_en` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `actualizado_en` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_spu_paciente_flujo` (`id_nino`, `id_flujo`),
    FOREIGN KEY (`id_flujo`) REFERENCES `spu_flujos`(`id_flujo`) ON DELETE CASCADE
);

CREATE TABLE `spu_paciente_tareas` (
    `id_paciente_tarea` INT AUTO_INCREMENT PRIMARY KEY,
    `id_nino` INT NOT NULL,
    `id_tarea` INT NOT NULL,
    `status` ENUM('no_iniciado','en_proceso','completado') NOT NULL DEFAULT 'no_iniciado',
    `actualizado_por` INT NULL,
    `completado_en` DATETIME NULL,
    `creado_en` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `actualizado_en` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_spu_paciente_tarea` (`id_nino`, `id_tarea`),
    FOREIGN KEY (`id_tarea`) REFERENCES `spu_tareas`(`id_tarea`) ON DELETE CASCADE
);
