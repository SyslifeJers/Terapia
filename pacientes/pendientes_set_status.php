<?php
header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/../includes/pendientes_lib.php';
require_once __DIR__ . '/../database/conexion.php';

if (!isset($_SESSION['id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$id_nino = isset($_POST['id_nino']) ? (int)$_POST['id_nino'] : 0;
$task_id = isset($_POST['task_id']) ? trim((string)$_POST['task_id']) : '';
$status = isset($_POST['status']) ? trim((string)$_POST['status']) : '';
$redirect = isset($_POST['redirect']) ? (string)$_POST['redirect'] : '';

if ($id_nino <= 0 || !pendientes_allowed_task_id($task_id)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
    exit;
}

$db = new Database();
$conn = $db->getConnection();

$taskMeta = pendientes_find_task($conn, $task_id);
if (!$taskMeta) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Tarea no encontrada']);
    exit;
}

$normalized = pendientes_normalize_status($status);

// If evidence is required, block completed without files.
$evidence = strtolower((string)($taskMeta['evidencia'] ?? 'none'));
if ($normalized === 'completado' && $evidence === 'required' && !pendientes_task_has_files($id_nino, $task_id)) {
    http_response_code(409);
    echo json_encode(['success' => false, 'message' => 'Se requiere evidencia para completar esta tarea']);
    exit;
}

if (!pendientes_save_patient_task_status($conn, $id_nino, $task_id, $normalized, (int)($_SESSION['id'] ?? 0))) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'No se pudo guardar']);
    exit;
}

$db->closeConnection();

if ($redirect !== '') {
    header('Location: ' . $redirect);
    exit;
}

echo json_encode(['success' => true]);
