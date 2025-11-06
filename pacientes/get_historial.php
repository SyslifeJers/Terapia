<?php
require_once '../database/conexion.php';
header('Content-Type: application/json');

$db = new Database();
$conn = $db->getConnection();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$tipo = $_GET['tipo'] ?? '';

$datos = [];

$columnaSeccion = $conn->query("SHOW COLUMNS FROM exp_valoracion_detalle LIKE 'seccion'");
$tieneSeccion = $columnaSeccion && $columnaSeccion->num_rows > 0;
if ($columnaSeccion) {
    $columnaSeccion->free();
}

$camposSesion = ['id_valoracion', 'fecha_valoracion', 'observaciones'];
$columnasLegacy = [
    'participacion' => false,
    'atencion' => false,
    'tarea_casa' => false,
];
$columnasSesion = $conn->query("SHOW COLUMNS FROM exp_valoraciones_sesion WHERE Field IN ('participacion','atencion','tarea_casa')");
if ($columnasSesion) {
    while ($col = $columnasSesion->fetch_assoc()) {
        $campo = $col['Field'] ?? '';
        if (isset($columnasLegacy[$campo])) {
            $columnasLegacy[$campo] = true;
            $camposSesion[] = $campo;
        }
    }
    $columnasSesion->free();
}
$selectSesion = implode(', ', $camposSesion);

if ($id > 0 && ($tipo === 'evaluacion' || $tipo === 'progreso')) {
    if ($tipo === 'evaluacion') {
        $stmt = $conn->prepare("SELECT $selectSesion FROM exp_valoraciones_sesion WHERE id_nino = ? ORDER BY fecha_valoracion DESC");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                foreach ($columnasLegacy as $campo => $presente) {
                    if (!array_key_exists($campo, $row)) {
                        $row[$campo] = null;
                    }
                }
                $row['detalles'] = [];
                $row['metricas'] = [];
                $datos[$row['id_valoracion']] = $row;
            }
        }
        $stmt->close();

        $ids = array_keys($datos);
        if (!empty($ids)) {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $types = str_repeat('i', count($ids));
            $selectColumnas = $tieneSeccion ? 'seccion, criterio, puntaje' : 'NULL AS seccion, criterio, puntaje';
            $stmtDetalles = $conn->prepare("SELECT id_valoracion, $selectColumnas FROM exp_valoracion_detalle WHERE id_valoracion IN ($placeholders) ORDER BY id_valoracion ASC, id_detalle ASC");
            if ($stmtDetalles) {
                $bind = [];
                $bind[] = &$types;
                foreach ($ids as $key => $value) {
                    $bind[] = &$ids[$key];
                }
                call_user_func_array([$stmtDetalles, 'bind_param'], $bind);
                $stmtDetalles->execute();
                $resDetalles = $stmtDetalles->get_result();
                if ($resDetalles) {
                    while ($detalle = $resDetalles->fetch_assoc()) {
                        $idValoracion = $detalle['id_valoracion'];
                        if (isset($datos[$idValoracion])) {
                            $datos[$idValoracion]['detalles'][] = [
                                'seccion' => $tieneSeccion ? ($detalle['seccion'] ?? null) : null,
                                'criterio' => $detalle['criterio'],
                                'puntaje' => (float)$detalle['puntaje'],
                            ];
                        }
                    }
                }
                $stmtDetalles->close();
            }

            $stmtMetricas = $conn->prepare("SELECT mv.id_valoracion, m.clave, m.nombre, mv.puntaje FROM exp_valoracion_metrica_valor mv INNER JOIN exp_valoracion_metrica m ON m.id_metrica = mv.id_metrica WHERE mv.id_valoracion IN ($placeholders) ORDER BY mv.id_valoracion, m.nombre");
            if ($stmtMetricas) {
                $bindMetricas = [];
                $bindMetricas[] = &$types;
                foreach ($ids as $key => $value) {
                    $bindMetricas[] = &$ids[$key];
                }
                call_user_func_array([$stmtMetricas, 'bind_param'], $bindMetricas);
                $stmtMetricas->execute();
                $resMetricas = $stmtMetricas->get_result();
                if ($resMetricas) {
                    while ($metrica = $resMetricas->fetch_assoc()) {
                        $idValoracion = (int)$metrica['id_valoracion'];
                        if (!isset($datos[$idValoracion])) {
                            continue;
                        }
                        $claveMetrica = (string)$metrica['clave'];
                        $nombreMetrica = $metrica['nombre'] ?? $claveMetrica;
                        $puntajeMetrica = isset($metrica['puntaje']) ? (float)$metrica['puntaje'] : null;
                        if ($puntajeMetrica === null) {
                            continue;
                        }
                        $datos[$idValoracion]['metricas'][$claveMetrica] = [
                            'nombre' => $nombreMetrica,
                            'puntaje' => $puntajeMetrica,
                        ];
                        if (array_key_exists($claveMetrica, $datos[$idValoracion])) {
                            $datos[$idValoracion][$claveMetrica] = $puntajeMetrica;
                        }
                    }
                }
                $stmtMetricas->close();
            }
        }

        foreach ($datos as &$evaluacion) {
            if (empty($evaluacion['detalles'])) {
                $legacy = [];
                if (!empty($evaluacion['metricas'])) {
                    foreach ($evaluacion['metricas'] as $meta) {
                        $legacy[] = [
                            'seccion' => null,
                            'criterio' => $meta['nombre'],
                            'puntaje' => (float)$meta['puntaje'],
                        ];
                    }
                } else {
                    if ($evaluacion['participacion'] !== null) {
                        $legacy[] = ['seccion' => null, 'criterio' => 'Participación', 'puntaje' => (float)$evaluacion['participacion']];
                    }
                    if ($evaluacion['atencion'] !== null) {
                        $legacy[] = ['seccion' => null, 'criterio' => 'Atención', 'puntaje' => (float)$evaluacion['atencion']];
                    }
                    if ($evaluacion['tarea_casa'] !== null) {
                        $legacy[] = ['seccion' => null, 'criterio' => 'Tarea en casa', 'puntaje' => (float)$evaluacion['tarea_casa']];
                    }
                }
                $evaluacion['detalles'] = $legacy;
            }
            $conteo = count($evaluacion['detalles']);
            $suma = 0;
            foreach ($evaluacion['detalles'] as $detalle) {
                $suma += (float)$detalle['puntaje'];
            }
            $evaluacion['promedio'] = $conteo ? round($suma / $conteo, 2) : null;
        }
        unset($evaluacion);

        $datos = array_values($datos);
    } else {
        $stmt = $conn->prepare("SELECT fecha_registro, lenguaje, motricidad, atencion, memoria, social, observaciones FROM exp_progreso_general WHERE id_nino = ? ORDER BY fecha_registro DESC");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            $datos = $result->fetch_all(MYSQLI_ASSOC);
        }
        $stmt->close();
    }
}

$db->closeConnection();

echo json_encode($datos);
