<?php
header('Content-Type: application/json');

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$file = $_POST['file'] ?? '';
if ($id <= 0 || !$file) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Datos invalidos']);
    exit;
}

$dir = __DIR__ . '/../uploads/exams/' . $id . '/';
$realDir = realpath($dir);
$path = realpath($dir . $file);
if (!$realDir || !$path || strpos($path, $realDir) !== 0 || !is_file($path)) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Archivo no encontrado']);
    exit;
}

$notePath = $path . '.txt';
$success = unlink($path);
if (is_file($notePath)) {
    unlink($notePath);
}

echo json_encode(['success' => $success]);
?>
