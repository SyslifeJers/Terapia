# Reglas Del Proyecto `Terapia`

Este proyecto debe seguir las reglas generales de `../REGLAS_PROYECTOS.md`.

## Reglas Especificas

1. Usar `America/Mexico_City` para evaluaciones, citas, calendario, reuniones y seguimiento de pacientes.
2. No interpolar variables directamente en SQL; usar `prepare()` y `bind_param()`.
3. No usar `SHOW TABLES` ni scripts que requieran permisos administrativos de base de datos.
4. No aplicar `DROP TABLE`, `TRUNCATE`, `ALTER TABLE` o borrados masivos sin respaldo y confirmacion.
5. No usar procedimientos almacenados (`CREATE PROCEDURE`, `CALL`, `DROP PROCEDURE`).
6. Leer citas desde `Cita`; no duplicarlas en tablas propias de `Terapia`.
7. Leer reuniones desde las tablas compartidas `ReunionInterna*`; no duplicarlas en tablas `exp_` o `spu_`.
8. Mantener las tablas `spu_*` para el sistema de tareas/pendientes del paciente.
9. Antes de cambiar `Usuarios`, `Cita`, `nino` o `Estatus`, revisar impacto en `git`.
