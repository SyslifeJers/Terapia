<?php
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] == 2) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

function rrmdir($dir) {
    if (!is_dir($dir)) return false;
    $items = array_diff(scandir($dir), ['.', '..']);
    foreach ($items as $it) {
        $path = $dir . DIRECTORY_SEPARATOR . $it;
        if (is_dir($path)) {
            rrmdir($path);
        } else {
            @unlink($path);
        }
    }
    return @rmdir($dir);
}

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$dirRel = isset($_POST['dir']) ? trim((string)$_POST['dir']) : '';
$dirRel = trim(str_replace('\\', '/', $dirRel), '/');

if ($id <= 0 || $dirRel === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Datos invalidos']);
    exit;
}

if (strcasecmp($dirRel, 'general') === 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'No se puede eliminar General']);
    exit;
}

$base = __DIR__ . '/../uploads/exams/' . $id . '/';
$baseReal = realpath($base);
if (!$baseReal) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Carpeta no encontrada']);
    exit;
}

$target = $base . $dirRel;
$targetReal = realpath($target);
if (!$targetReal || strpos($targetReal, $baseReal) !== 0 || !is_dir($targetReal)) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Seccion no encontrada']);
    exit;
}

$ok = rrmdir($targetReal);
echo json_encode(['success' => (bool)$ok]);
?>
