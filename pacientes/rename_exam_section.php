<?php
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['rol'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$oldDir = isset($_POST['old_dir']) ? trim((string)$_POST['old_dir']) : '';
$newName = isset($_POST['new_name']) ? trim((string)$_POST['new_name']) : '';

if ($id <= 0 || $oldDir === '' || $newName === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Datos invalidos']);
    exit;
}

// No permitir renombrar "General" (raiz)
if (strcasecmp($oldDir, 'general') === 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'No se puede renombrar General']);
    exit;
}

$tmp = strtolower($newName);
$tmp = preg_replace('/[^a-z0-9]+/', '_', $tmp);
$tmp = trim($tmp, '_');
$newDir = $tmp !== '' ? substr($tmp, 0, 64) : '';

if ($newDir === '' || strcasecmp($newDir, 'general') === 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Nombre de seccion invalido']);
    exit;
}

$base = __DIR__ . '/../uploads/exams/' . $id . '/';
$baseReal = realpath($base);
if (!$baseReal) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Paciente sin carpeta de archivos']);
    exit;
}

$oldPath = $base . $oldDir;
$newPath = $base . $newDir;

$oldReal = realpath($oldPath);
if (!$oldReal || strpos($oldReal, $baseReal) !== 0 || !is_dir($oldReal)) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Seccion no encontrada']);
    exit;
}

if (is_dir($newPath)) {
    http_response_code(409);
    echo json_encode(['success' => false, 'message' => 'Ya existe una seccion con ese nombre']);
    exit;
}

if (!@rename($oldPath, $newPath)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'No se pudo renombrar']);
    exit;
}

$metaPath = rtrim($newPath, '/\\') . DIRECTORY_SEPARATOR . 'section.json';
$meta = [
    'name' => $newName,
    'dir' => $newDir,
    'updated_at' => date('c')
];
@file_put_contents($metaPath, json_encode($meta, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

echo json_encode(['success' => true, 'old_dir' => $oldDir, 'new_dir' => $newDir, 'name' => $newName]);
?>
