<?php
header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/../includes/pendientes_lib.php';

if (!isset($_SESSION['id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

if (!isset($_SESSION['rol']) || $_SESSION['rol'] == 2) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$id_nino = isset($_POST['id_nino']) ? (int)$_POST['id_nino'] : 0;
$task_id = isset($_POST['task_id']) ? trim((string)$_POST['task_id']) : '';
$filename = isset($_POST['filename']) ? trim((string)$_POST['filename']) : '';
$redirect = isset($_POST['redirect']) ? (string)$_POST['redirect'] : '';

if ($id_nino <= 0 || !pendientes_allowed_task_id($task_id) || $filename === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
    exit;
}

// Basic filename sanitization.
$filename = basename(str_replace('\\', '/', $filename));
if ($filename === '' || strpos($filename, '..') !== false) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Nombre inválido']);
    exit;
}

$dir = pendientes_task_evidence_dir($id_nino, $task_id);
$realDir = realpath($dir);
$path = $realDir ? realpath($dir . DIRECTORY_SEPARATOR . $filename) : false;
if (!$realDir || !$path || strpos($path, $realDir) !== 0 || !is_file($path)) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Archivo no encontrado']);
    exit;
}

$ok = @unlink($path);
if (is_file($path . '.txt')) {
    @unlink($path . '.txt');
}

if ($redirect !== '') {
    header('Location: ' . $redirect);
    exit;
}

echo json_encode(['success' => (bool)$ok]);
