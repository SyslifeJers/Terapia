<?php
declare(strict_types=1);
require_once __DIR__ . '/flujos_lib.php';

$session = terapia_flujos_session();
$taskId = isset($_POST['id_tarea_asignada']) ? (int)$_POST['id_tarea_asignada'] : 0;
$comentario = trim((string)($_POST['comentario'] ?? ''));

if ($taskId <= 0 || !isset($_FILES['file'])) {
    terapia_flujos_response(422, ['success' => false, 'message' => 'Tarea y archivo son obligatorios.']);
}

if ((int)($_FILES['file']['size'] ?? 0) > 8 * 1024 * 1024) {
    terapia_flujos_response(422, ['success' => false, 'message' => 'El archivo excede 8 MB.']);
}

[$db, $conn] = terapia_flujos_db();
$task = terapia_flujos_task_access($conn, $taskId, $session['id']);
if (!$task || (int)$task['requiere_documento'] !== 1) {
    $db->closeConnection();
    terapia_flujos_response(404, ['success' => false, 'message' => 'Tarea no encontrada o no requiere documento.']);
}

$original = (string)($_FILES['file']['name'] ?? '');
$info = pathinfo($original);
$ext = strtolower((string)($info['extension'] ?? ''));
if (!in_array($ext, ['pdf', 'jpg', 'jpeg', 'png'], true)) {
    $db->closeConnection();
    terapia_flujos_response(422, ['success' => false, 'message' => 'Solo se permiten PDF, JPG o PNG.']);
}

$tmpPath = (string)($_FILES['file']['tmp_name'] ?? '');
$realMime = $tmpPath !== '' ? terapia_flujos_detect_mime($tmpPath) : '';
$allowedMime = ['application/pdf', 'image/jpeg', 'image/png'];
if ($realMime !== '' && !in_array($realMime, $allowedMime, true)) {
    $db->closeConnection();
    terapia_flujos_response(422, ['success' => false, 'message' => 'El tipo real del archivo no es permitido.']);
}

$base = preg_replace('/[^A-Za-z0-9_-]/', '_', (string)($info['filename'] ?? 'documento'));
$filename = trim((string)$base, '_') . '_' . time() . '.' . $ext;
$dir = terapia_flujos_upload_dir($taskId);
if (!is_dir($dir)) {
    @mkdir($dir, 0777, true);
}
$target = rtrim($dir, '/\\') . DIRECTORY_SEPARATOR . $filename;
if (!@move_uploaded_file($tmpPath, $target)) {
    $db->closeConnection();
    terapia_flujos_response(500, ['success' => false, 'message' => 'No fue posible guardar el archivo.']);
}

$ruta = '/uploads/flujos/tareas/' . $taskId . '/' . $filename;
$mime = (string)($_FILES['file']['type'] ?? '');
$size = (int)($_FILES['file']['size'] ?? 0);
$stmt = $conn->prepare('INSERT INTO exp_flujo_documentos (id_tarea_asignada, ruta, nombre_original, mime, tamano, comentario, usuario_subida) VALUES (?, ?, ?, ?, ?, ?, ?)');
if (!$stmt) {
    $db->closeConnection();
    terapia_flujos_response(500, ['success' => false, 'message' => 'No fue posible registrar el documento.']);
}
$stmt->bind_param('isssisi', $taskId, $ruta, $original, $mime, $size, $comentario, $session['id']);
$ok = $stmt->execute();
$documentId = (int)$conn->insert_id;
$stmt->close();
if ($ok) {
    terapia_flujos_historial($conn, (int)$task['id_asignacion'], $taskId, 'subir_documento', 'Documento adjunto: ' . $original, $session['id']);
}
$db->closeConnection();
terapia_flujos_response($ok ? 201 : 500, ['success' => $ok, 'id_documento' => $documentId, 'ruta' => $ruta]);
