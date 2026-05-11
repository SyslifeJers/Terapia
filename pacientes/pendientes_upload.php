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
$note = isset($_POST['note']) ? trim((string)$_POST['note']) : '';
$redirect = isset($_POST['redirect']) ? (string)$_POST['redirect'] : '';

if ($id_nino <= 0 || !pendientes_allowed_task_id($task_id) || !isset($_FILES['file'])) {
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

$allowed = ['pdf', 'png', 'jpg', 'jpeg', 'gif'];
$metaAllowed = isset($taskMeta['tipos']) && is_array($taskMeta['tipos']) ? array_map('strtolower', $taskMeta['tipos']) : [];
if (!empty($metaAllowed)) {
    $allowed = array_values(array_intersect($allowed, $metaAllowed));
}

$info = pathinfo((string)($_FILES['file']['name'] ?? ''));
$ext = strtolower((string)($info['extension'] ?? ''));
if ($ext === '' || !in_array($ext, $allowed, true)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Tipo de archivo no permitido']);
    exit;
}

$base = preg_replace('/[^A-Za-z0-9_-]/', '_', (string)($info['filename'] ?? 'archivo'));
$base = trim($base, '_');
if ($base === '') $base = 'archivo';
$filename = $base . '_' . time() . '.' . $ext;

$dir = pendientes_task_evidence_dir($id_nino, $task_id);
if (!is_dir($dir)) {
    @mkdir($dir, 0777, true);
}

$target = rtrim($dir, '/\\') . DIRECTORY_SEPARATOR . $filename;
if (!@move_uploaded_file($_FILES['file']['tmp_name'], $target)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al guardar']);
    exit;
}

if ($note !== '') {
    @file_put_contents($target . '.txt', $note);
}

// If it was "no iniciado" and now has evidence, nudge to "en proceso".
    $st = pendientes_load_patient_status($conn, $id_nino);
    $curr = $st['tasks'][$task_id]['status'] ?? 'no_iniciado';
    $curr = pendientes_normalize_status((string)$curr);
    if ($curr === 'no_iniciado') {
        pendientes_save_patient_task_status($conn, $id_nino, $task_id, 'en_proceso', (int)($_SESSION['id'] ?? 0));
    }

$db->closeConnection();

if ($redirect !== '') {
    header('Location: ' . $redirect);
    exit;
}

echo json_encode(['success' => true, 'file' => $filename]);
