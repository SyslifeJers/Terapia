<?php
require_once '../database/conexion.php';
require_once '../libreria/SimplePDF.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$db = new Database();
$conn = $db->getConnection();

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
    $stmt = $conn->prepare("SELECT id_valoracion, fecha_valoracion, observaciones FROM exp_valoraciones_sesion WHERE id_nino = ? ORDER BY fecha_valoracion DESC LIMIT 10");
    if ($stmt) {
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $idsValoraciones = [];
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $row['id_valoracion'] = (int)$row['id_valoracion'];
                $row['observaciones'] = $row['observaciones'] ?? '';
                $row['criterios'] = [];
                $evaluaciones[] = $row;
                $idsValoraciones[] = $row['id_valoracion'];
            }
        }
        $stmt->close();

        if (!empty($idsValoraciones)) {
            $listaIds = implode(',', array_map('intval', $idsValoraciones));
            $sqlDet = "SELECT vd.id_valoracion, vd.valor, c.nombre FROM exp_valoracion_detalle vd INNER JOIN exp_criterios_evaluacion c ON c.id_criterio = vd.id_criterio WHERE vd.id_valoracion IN ($listaIds) ORDER BY c.nombre ASC";
            $detRes = $conn->query($sqlDet);
            if ($detRes) {
                $indexMap = array_flip($idsValoraciones);
                while ($det = $detRes->fetch_assoc()) {
                    $idVal = (int)$det['id_valoracion'];
                    if (!isset($indexMap[$idVal])) {
                        continue;
                    }
                    $evaluaciones[$indexMap[$idVal]]['criterios'][] = [
                        'nombre' => $det['nombre'],
                        'valor' => (float)$det['valor'],
                    ];
                }
            }
        }
    }

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
if (!empty($evaluaciones)) {
    foreach ($evaluaciones as $ev) {
        $criteriosTexto = [];
        foreach ($ev['criterios'] as $criterio) {
            $criteriosTexto[] = $criterio['nombre'] . ': ' . $criterio['valor'];
        }
        $line = $ev['fecha_valoracion'];
        if (!empty($criteriosTexto)) {
            $line .= ' - ' . implode(', ', $criteriosTexto);
        }
        if (!empty($ev['observaciones'])) {
            $line .= ' (Obs: ' . $ev['observaciones'] . ')';
        }
        $pdf->text(40, $y, $line, 8);
        $y += 10;
    }
} else {
    $pdf->text(40, $y, 'Sin evaluaciones registradas.', 8);
    $y += 10;
}

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="reporte_paciente_' . $id . '.pdf"');
echo $pdf->output();
