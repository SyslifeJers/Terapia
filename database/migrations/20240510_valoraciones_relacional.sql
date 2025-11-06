-- Migración para normalizar las valoraciones de sesión
START TRANSACTION;

-- Tabla de catálogos por criterio/niño
CREATE TABLE IF NOT EXISTS `exp_valoracion_catalogo` (
    `id_catalogo` INT AUTO_INCREMENT PRIMARY KEY,
    `id_nino` INT NULL,
    `seccion` VARCHAR(255) NULL,
    `criterio` VARCHAR(255) NOT NULL,
    `puntaje_default` TINYINT NOT NULL DEFAULT 5,
    `orden` INT NOT NULL DEFAULT 0,
    `creado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `actualizado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_catalogo_nino` FOREIGN KEY (`id_nino`) REFERENCES `nino`(`Id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Índice único para evitar duplicados por niño/sección/criterio
SET @sql := (
    SELECT IF(
        EXISTS (
            SELECT 1
            FROM information_schema.STATISTICS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'exp_valoracion_catalogo'
              AND INDEX_NAME = 'idx_catalogo_nino_seccion_criterio'
        ),
        'SELECT 1',
        'CREATE UNIQUE INDEX `idx_catalogo_nino_seccion_criterio` ON `exp_valoracion_catalogo`(`id_nino`, `seccion`, `criterio`)' 
    )
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Detalle de criterios por valoración
CREATE TABLE IF NOT EXISTS `exp_valoracion_detalle` (
    `id_detalle` INT AUTO_INCREMENT PRIMARY KEY,
    `id_valoracion` INT NOT NULL,
    `id_catalogo` INT NULL,
    `seccion` VARCHAR(255) NULL,
    `criterio` VARCHAR(255) NOT NULL,
    `puntaje` TINYINT NOT NULL,
    CONSTRAINT `fk_detalle_valoracion` FOREIGN KEY (`id_valoracion`) REFERENCES `exp_valoraciones_sesion`(`id_valoracion`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Asegura columnas nuevas si la tabla existía previamente
SET @sql := (
    SELECT IF(
        EXISTS (
            SELECT 1 FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'exp_valoracion_detalle'
              AND COLUMN_NAME = 'id_catalogo'
        ),
        'SELECT 1',
        'ALTER TABLE `exp_valoracion_detalle` ADD COLUMN `id_catalogo` INT NULL AFTER `id_valoracion`'
    )
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql := (
    SELECT IF(
        EXISTS (
            SELECT 1 FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'exp_valoracion_detalle'
              AND COLUMN_NAME = 'seccion'
        ),
        'SELECT 1',
        'ALTER TABLE `exp_valoracion_detalle` ADD COLUMN `seccion` VARCHAR(255) NULL AFTER `id_catalogo`'
    )
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Vuelve a crear la llave foránea hacia el catálogo si no existía
SET @sql := (
    SELECT IF(
        EXISTS (
            SELECT 1 FROM information_schema.TABLE_CONSTRAINTS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'exp_valoracion_detalle'
              AND CONSTRAINT_NAME = 'fk_detalle_catalogo'
        ),
        'SELECT 1',
        'ALTER TABLE `exp_valoracion_detalle` ADD CONSTRAINT `fk_detalle_catalogo` FOREIGN KEY (`id_catalogo`) REFERENCES `exp_valoracion_catalogo`(`id_catalogo`) ON DELETE SET NULL'
    )
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Catálogo de métricas principales
CREATE TABLE IF NOT EXISTS `exp_valoracion_metrica` (
    `id_metrica` INT AUTO_INCREMENT PRIMARY KEY,
    `clave` VARCHAR(64) NOT NULL UNIQUE,
    `nombre` VARCHAR(100) NOT NULL,
    `descripcion` VARCHAR(255) NULL,
    `activo` TINYINT(1) NOT NULL DEFAULT 1,
    `creado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `actualizado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `exp_valoracion_metrica_valor` (
    `id_valor` INT AUTO_INCREMENT PRIMARY KEY,
    `id_valoracion` INT NOT NULL,
    `id_metrica` INT NOT NULL,
    `puntaje` TINYINT NOT NULL,
    `creado_en` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `idx_valoracion_metrica` (`id_valoracion`, `id_metrica`),
    CONSTRAINT `fk_metrica_valor_valoracion` FOREIGN KEY (`id_valoracion`) REFERENCES `exp_valoraciones_sesion`(`id_valoracion`) ON DELETE CASCADE,
    CONSTRAINT `fk_metrica_valor_metrica` FOREIGN KEY (`id_metrica`) REFERENCES `exp_valoracion_metrica`(`id_metrica`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Semilla para las métricas heredadas
INSERT INTO `exp_valoracion_metrica` (`clave`, `nombre`)
VALUES
    ('participacion', 'Participación'),
    ('atencion', 'Atención'),
    ('tarea_casa', 'Tarea en casa')
ON DUPLICATE KEY UPDATE `nombre` = VALUES(`nombre`);

-- Semilla para el catálogo base global
INSERT INTO `exp_valoracion_catalogo` (`id_nino`, `seccion`, `criterio`, `puntaje_default`, `orden`)
VALUES
    (NULL, 'General', 'Permanencia', 5, 1),
    (NULL, 'General', 'Irritabilidad', 5, 2),
    (NULL, 'General', 'Habilidades sociales', 5, 3),
    (NULL, 'General', 'Atención conjunta', 5, 4),
    (NULL, 'General', 'Seguimiento de indicaciones', 5, 5),
    (NULL, 'General', 'Cognición', 5, 6),
    (NULL, 'General', 'Comunicación receptiva', 5, 7),
    (NULL, 'General', 'Comunicación expresiva', 5, 8)
ON DUPLICATE KEY UPDATE `puntaje_default` = VALUES(`puntaje_default`), `orden` = VALUES(`orden`);

-- Migra los valores heredados hacia la tabla relacional
INSERT INTO `exp_valoracion_metrica_valor` (`id_valoracion`, `id_metrica`, `puntaje`)
SELECT v.`id_valoracion`, m.`id_metrica`, v.`participacion`
FROM `exp_valoraciones_sesion` v
JOIN `exp_valoracion_metrica` m ON m.`clave` = 'participacion'
WHERE v.`participacion` IS NOT NULL
ON DUPLICATE KEY UPDATE `puntaje` = VALUES(`puntaje`);

INSERT INTO `exp_valoracion_metrica_valor` (`id_valoracion`, `id_metrica`, `puntaje`)
SELECT v.`id_valoracion`, m.`id_metrica`, v.`atencion`
FROM `exp_valoraciones_sesion` v
JOIN `exp_valoracion_metrica` m ON m.`clave` = 'atencion'
WHERE v.`atencion` IS NOT NULL
ON DUPLICATE KEY UPDATE `puntaje` = VALUES(`puntaje`);

INSERT INTO `exp_valoracion_metrica_valor` (`id_valoracion`, `id_metrica`, `puntaje`)
SELECT v.`id_valoracion`, m.`id_metrica`, v.`tarea_casa`
FROM `exp_valoraciones_sesion` v
JOIN `exp_valoracion_metrica` m ON m.`clave` = 'tarea_casa'
WHERE v.`tarea_casa` IS NOT NULL
ON DUPLICATE KEY UPDATE `puntaje` = VALUES(`puntaje`);

-- Opcional: elimina las columnas antiguas si ya no se requieren
SET @sql := (
    SELECT IF(
        EXISTS (
            SELECT 1 FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'exp_valoraciones_sesion'
              AND COLUMN_NAME = 'participacion'
        ),
        'ALTER TABLE `exp_valoraciones_sesion` DROP COLUMN `participacion`',
        'SELECT 1'
    )
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql := (
    SELECT IF(
        EXISTS (
            SELECT 1 FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'exp_valoraciones_sesion'
              AND COLUMN_NAME = 'atencion'
        ),
        'ALTER TABLE `exp_valoraciones_sesion` DROP COLUMN `atencion`',
        'SELECT 1'
    )
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql := (
    SELECT IF(
        EXISTS (
            SELECT 1 FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'exp_valoraciones_sesion'
              AND COLUMN_NAME = 'tarea_casa'
        ),
        'ALTER TABLE `exp_valoraciones_sesion` DROP COLUMN `tarea_casa`',
        'SELECT 1'
    )
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

COMMIT;
