<?php
require_once '../database/conexion.php';
require_once '../libreria/SimplePDF.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$db = new Database();
$conn = $db->getConnection();

$columnaSeccionDetalle = $conn->query("SHOW COLUMNS FROM exp_valoracion_detalle LIKE 'seccion'");
$tieneSeccionDetalle = $columnaSeccionDetalle && $columnaSeccionDetalle->num_rows > 0;
if ($columnaSeccionDetalle) {
    $columnaSeccionDetalle->free();
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

$paciente = [];
if ($id > 0) {
    $stmt = $conn->prepare("SELECT Id, name, edad, Observacion FROM nino WHERE Id = ? LIMIT 1");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $paciente = $res ? $res->fetch_assoc() : [];
    $stmt->close();
}

$evaluaciones = [];
$stmt = $conn->prepare("SELECT $selectSesion FROM exp_valoraciones_sesion WHERE id_nino = ? ORDER BY fecha_valoracion DESC LIMIT 10");
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$evaluacionMap = [];
while ($row = $res->fetch_assoc()) {
    foreach ($columnasLegacy as $campo => $presente) {
        if (!array_key_exists($campo, $row)) {
            $row[$campo] = null;
        }
    }
    $row['detalles'] = [];
    $row['metricas'] = [];
    $evaluacionMap[$row['id_valoracion']] = $row;
}
$stmt->close();

$ids = array_keys($evaluacionMap);
if (!empty($ids)) {
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $types = str_repeat('i', count($ids));
    $selectColumnas = $tieneSeccionDetalle ? 'seccion, criterio, puntaje' : 'NULL AS seccion, criterio, puntaje';
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
                $idVal = $detalle['id_valoracion'];
                if (isset($evaluacionMap[$idVal])) {
                    $evaluacionMap[$idVal]['detalles'][] = [
                        'seccion' => $tieneSeccionDetalle ? ($detalle['seccion'] ?? null) : null,
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
                $idVal = (int)$metrica['id_valoracion'];
                if (!isset($evaluacionMap[$idVal])) {
                    continue;
                }
                $claveMetrica = (string)$metrica['clave'];
                $nombreMetrica = $metrica['nombre'] ?? $claveMetrica;
                $puntajeMetrica = isset($metrica['puntaje']) ? (float)$metrica['puntaje'] : null;
                if ($puntajeMetrica === null) {
                    continue;
                }
                $evaluacionMap[$idVal]['metricas'][$claveMetrica] = [
                    'nombre' => $nombreMetrica,
                    'puntaje' => $puntajeMetrica,
                ];
                if (array_key_exists($claveMetrica, $evaluacionMap[$idVal])) {
                    $evaluacionMap[$idVal][$claveMetrica] = $puntajeMetrica;
                }
            }
        }
        $stmtMetricas->close();
    }
}

foreach ($evaluacionMap as &$eval) {
    if (empty($eval['detalles'])) {
        $legacy = [];
        if (!empty($eval['metricas'])) {
            foreach ($eval['metricas'] as $meta) {
                $legacy[] = [
                    'seccion' => null,
                    'criterio' => $meta['nombre'],
                    'puntaje' => (float)$meta['puntaje'],
                ];
            }
        } else {
            if ($eval['participacion'] !== null) {
                $legacy[] = ['seccion' => null, 'criterio' => 'Participación', 'puntaje' => (float)$eval['participacion']];
            }
            if ($eval['atencion'] !== null) {
                $legacy[] = ['seccion' => null, 'criterio' => 'Atención', 'puntaje' => (float)$eval['atencion']];
            }
            if ($eval['tarea_casa'] !== null) {
                $legacy[] = ['seccion' => null, 'criterio' => 'Tarea en casa', 'puntaje' => (float)$eval['tarea_casa']];
            }
        }
        $eval['detalles'] = $legacy;
    }
}
unset($eval);

$evaluaciones = array_values($evaluacionMap);

$sql = "SELECT lenguaje, motricidad, atencion, memoria, social FROM exp_progreso_general WHERE id_nino = ? ORDER BY fecha_registro ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$prom = ['lenguaje'=>0,'motricidad'=>0,'atencion'=>0,'memoria'=>0,'social'=>0];
$count = 0;
while ($r = $res->fetch_assoc()) {
    $prom['lenguaje'] += (float)$r['lenguaje'];
    $prom['motricidad'] += (float)$r['motricidad'];
    $prom['atencion'] += (float)$r['atencion'];
    $prom['memoria'] += (float)$r['memoria'];
    $prom['social'] += (float)$r['social'];
    $count++;
}
$stmt->close();
$db->closeConnection();
if ($count) {
    foreach ($prom as $k => $v) {
        $prom[$k] = round($v / $count, 2);
    }
}

$pdf = new SimplePDF();
$pdf->addPage();
$y = 40;
$pdf->text(40, $y, 'Reporte General de Paciente', 16);
$y += 20;
$pdf->text(40, $y, 'Nombre: ' . ($paciente['name'] ?? ''));
$y += 10;
$pdf->text(40, $y, 'Edad: ' . ($paciente['edad'] ?? ''));
$y += 10;
$pdf->text(40, $y, 'Observacion: ' . ($paciente['Observacion'] ?? ''));
$y += 20;
$pdf->text(40, $y, 'Promedio de progreso:');
$y += 10;
$x = 40;
$barW = 20;
foreach ($prom as $k => $v) {
    $h = $v * 10; // escala simple
    $pdf->rect($x, $y, $barW, $h, true);
    $pdf->text($x, $y + $h + 5, $k . ': ' . $v, 8);
    $x += $barW + 10;
}
$y += 120;
$pdf->text(40, $y, 'Últimas evaluaciones:');
$y += 10;
foreach ($evaluaciones as $ev) {
    $detalles = [];
    $grupos = [];
    foreach ($ev['detalles'] as $detalle) {
        $seccionNombre = isset($detalle['seccion']) && trim((string)$detalle['seccion']) !== '' ? trim((string)$detalle['seccion']) : 'General';
        $grupos[$seccionNombre][] = $detalle;
    }
    foreach ($grupos as $seccionNombre => $listaDetalle) {
        $prefijo = ($seccionNombre !== 'General' || count($grupos) > 1) ? $seccionNombre . ': ' : '';
        $items = [];
        foreach ($listaDetalle as $detalle) {
            $items[] = $detalle['criterio'] . ': ' . $detalle['puntaje'];
        }
        $detalles[] = $prefijo . implode(', ', $items);
    }
    $fecha = $ev['fecha_valoracion'] ?? '';
    $detalleTexto = implode(', ', $detalles);
    $line = $fecha;
    if ($detalleTexto !== '') {
        $line .= ($line !== '' ? ' - ' : '') . $detalleTexto;
    }
    $pdf->text(40, $y, $line, 8);
    $y += 10;
    if (!empty($ev['observaciones'])) {
        $pdf->text(40, $y, 'Obs: ' . $ev['observaciones'], 7);
        $y += 10;
    }
}

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="reporte_paciente_' . $id . '.pdf"');
echo $pdf->output();
