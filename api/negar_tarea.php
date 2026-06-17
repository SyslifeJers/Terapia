<?php
declare(strict_types=1);
require_once __DIR__ . '/flujos_lib.php';

$session = terapia_flujos_session();
$data = terapia_flujos_input();
$taskId = (int)($data['id_tarea_asignada'] ?? 0);
$motivo = trim((string)($data['motivo'] ?? ''));
$observaciones = trim((string)($data['observaciones'] ?? ''));

if ($taskId <= 0 || $motivo === '' || $observaciones === '') {
    terapia_flujos_response(422, ['success' => false, 'message' => 'Tarea, motivo y observaciones son obligatorios.']);
}

[$db, $conn] = terapia_flujos_db();
$task = terapia_flujos_task_access($conn, $taskId, $session['id']);
if (!$task || $task['estatus'] === 'cancelada') {
    $db->closeConnection();
    terapia_flujos_response(404, ['success' => false, 'message' => 'Tarea no encontrada o cancelada.']);
}

$stmt = $conn->prepare("UPDATE exp_flujo_tareas_asignadas SET estatus = 'negada', motivo_negacion = ?, observaciones_negacion = ?, fecha_negada = NOW() WHERE id_tarea_asignada = ?");
if (!$stmt) {
    terapia_flujos_response(500, ['success' => false, 'message' => 'No fue posible preparar la negacion.']);
}
$stmt->bind_param('ssi', $motivo, $observaciones, $taskId);
$ok = $stmt->execute();
$stmt->close();
if ($ok) {
    terapia_flujos_historial($conn, (int)$task['id_asignacion'], $taskId, 'negar_tarea', 'Motivo: ' . $motivo, $session['id']);
    terapia_flujos_actualizar_avance($conn, (int)$task['id_asignacion']);
}
$db->closeConnection();
terapia_flujos_response($ok ? 200 : 500, ['success' => $ok]);
