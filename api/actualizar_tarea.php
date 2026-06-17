<?php
declare(strict_types=1);
require_once __DIR__ . '/flujos_lib.php';

$session = terapia_flujos_session();
$data = terapia_flujos_input();
$taskId = (int)($data['id_tarea_asignada'] ?? 0);
$estatus = strtolower(trim((string)($data['estatus'] ?? '')));
$observaciones = trim((string)($data['observaciones'] ?? ''));

if ($taskId <= 0 || !in_array($estatus, ['en_proceso', 'completada'], true)) {
    terapia_flujos_response(422, ['success' => false, 'message' => 'Estatus permitido: en_proceso o completada.']);
}

[$db, $conn] = terapia_flujos_db();
$task = terapia_flujos_task_access($conn, $taskId, $session['id']);
if (!$task || $task['estatus'] === 'cancelada') {
    $db->closeConnection();
    terapia_flujos_response(404, ['success' => false, 'message' => 'Tarea no encontrada o cancelada.']);
}

$stmt = $conn->prepare("UPDATE exp_flujo_tareas_asignadas
    SET estatus = ?, observaciones = ?,
        fecha_inicio = CASE WHEN ? = 'en_proceso' AND fecha_inicio IS NULL THEN NOW() ELSE fecha_inicio END,
        fecha_completado = CASE WHEN ? = 'completada' THEN NOW() ELSE fecha_completado END
    WHERE id_tarea_asignada = ?");
if (!$stmt) {
    terapia_flujos_response(500, ['success' => false, 'message' => 'No fue posible preparar la actualizacion.']);
}
$stmt->bind_param('ssssi', $estatus, $observaciones, $estatus, $estatus, $taskId);
$ok = $stmt->execute();
$stmt->close();
if ($ok) {
    terapia_flujos_historial($conn, (int)$task['id_asignacion'], $taskId, 'actualizar_tarea', 'Estatus: ' . $estatus, $session['id']);
    terapia_flujos_actualizar_avance($conn, (int)$task['id_asignacion']);
}
$db->closeConnection();
terapia_flujos_response($ok ? 200 : 500, ['success' => $ok]);
