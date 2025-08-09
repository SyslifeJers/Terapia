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
