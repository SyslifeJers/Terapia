<?php
declare(strict_types=1);
require_once __DIR__ . '/flujos_lib.php';

$session = terapia_flujos_session();
$taskId = isset($_GET['id_tarea_asignada']) ? (int)$_GET['id_tarea_asignada'] : 0;
if ($taskId <= 0) {
    terapia_flujos_response(422, ['success' => false, 'message' => 'id_tarea_asignada es obligatorio.']);
}

[$db, $conn] = terapia_flujos_db();
$task = terapia_flujos_task_access($conn, $taskId, $session['id']);
if (!$task) {
    $db->closeConnection();
    terapia_flujos_response(404, ['success' => false, 'message' => 'Tarea no encontrada.']);
}

$stmtDocs = $conn->prepare('SELECT * FROM exp_flujo_documentos WHERE id_tarea_asignada = ? AND activo = 1 ORDER BY fecha_subida DESC');
$stmtDocs->bind_param('i', $taskId);
$stmtDocs->execute();
$docs = $stmtDocs->get_result()->fetch_all(MYSQLI_ASSOC);
$stmtDocs->close();

$stmtHist = $conn->prepare('SELECT * FROM exp_flujo_historial WHERE id_tarea_asignada = ? ORDER BY fecha DESC');
$stmtHist->bind_param('i', $taskId);
$stmtHist->execute();
$historial = $stmtHist->get_result()->fetch_all(MYSQLI_ASSOC);
$stmtHist->close();
$db->closeConnection();

$task['documentos'] = $docs;
$task['historial'] = $historial;
terapia_flujos_response(200, ['success' => true, 'data' => $task]);
