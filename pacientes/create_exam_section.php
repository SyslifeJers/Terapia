<?php
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['rol'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$name = isset($_POST['name']) ? trim((string)$_POST['name']) : '';

if ($id <= 0 || $name === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Datos invalidos']);
    exit;
}

// No permitir crear "General" como carpeta (General = raiz)
if (strcasecmp($name, 'General') === 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'General ya existe']);
    exit;
}

$tmp = strtolower($name);
$tmp = preg_replace('/[^a-z0-9]+/', '_', $tmp);
$tmp = trim($tmp, '_');
$dir = $tmp !== '' ? substr($tmp, 0, 64) : '';

if ($dir === '' || strcasecmp($dir, 'general') === 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Nombre de seccion invalido']);
    exit;
}

$base = __DIR__ . '/../uploads/exams/' . $id . '/';
if (!is_dir($base)) {
    if (!@mkdir($base, 0777, true)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'No se pudo crear carpeta base']);
        exit;
    }
}

$baseReal = realpath($base);
if (!$baseReal) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Ruta invalida']);
    exit;
}

$target = $base . $dir . '/';
if (!is_dir($target)) {
    if (!@mkdir($target, 0777, true)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'No se pudo crear seccion']);
        exit;
    }
}

$targetReal = realpath($target);
if (!$targetReal || strpos($targetReal, $baseReal) !== 0) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Ruta invalida']);
    exit;
}

$metaPath = rtrim($targetReal, '/\\') . DIRECTORY_SEPARATOR . 'section.json';
$meta = [
    'name' => $name,
    'dir' => $dir,
    'updated_at' => date('c')
];
@file_put_contents($metaPath, json_encode($meta, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

echo json_encode(['success' => true, 'dir' => $dir, 'name' => $name]);
?>
