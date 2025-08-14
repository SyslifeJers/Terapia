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
$stmt = $conn->prepare("SELECT fecha_valoracion, participacion, atencion, tarea_casa FROM exp_valoraciones_sesion WHERE id_nino = ? ORDER BY fecha_valoracion DESC LIMIT 10");
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $evaluaciones[] = $row;
}
$stmt->close();

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
    $line = sprintf('%s - P:%s A:%s T:%s', $ev['fecha_valoracion'], $ev['participacion'], $ev['atencion'], $ev['tarea_casa']);
    $pdf->text(40, $y, $line, 8);
    $y += 10;
}

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="reporte_paciente_' . $id . '.pdf"');
echo $pdf->output();
