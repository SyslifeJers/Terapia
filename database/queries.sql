-- Queries for common tables
-- pacientes
SELECT `id`, `name`, `activo`, `edad`, `Observacion`, `FechaIngreso`, `idtutor` FROM `nino` WHERE 1;

-- citas
SELECT `id`, `IdNino`, `IdUsuario`, `idGenerado`, `fecha`, `costo`, `Programado`, `Estatus`, `Tipo`, `FormaPago` FROM `Cita` WHERE 1;

-- areas
SELECT `id_area`, `nombre_area`, `descripcion` FROM `exp_areas_evaluacion` WHERE 1;

-- evaluaciones
SELECT `id_evaluacion`, `id_nino`, `id_usuario`, `id_area`, `fecha`, `observaciones` FROM `exp_evaluaciones` WHERE 1;
