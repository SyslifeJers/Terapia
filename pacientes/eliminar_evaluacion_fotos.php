<?php
header('Content-Type: application/json');
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] == 2) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}
require_once '../database/conexion.php';

$id_eval = intval($_POST['id_eval'] ?? 0);
if ($id_eval <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Datos invalidos']);
    exit;
}

$db = new Database();
$conn = $db->getConnection();

$stmt = $conn->prepare("SELECT id_nino, seccion FROM exp_evaluacion_fotos WHERE id_eval_foto=?");
$stmt->bind_param('i', $id_eval);
$stmt->execute();
$result = $stmt->get_result();
$info = $result->fetch_assoc();
$stmt->close();
if (!$info) {
    $db->closeConnection();
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Evaluación no encontrada']);
    exit;
}
$id_nino = $info['id_nino'];
$seccion_dir = preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($info['seccion']));
$baseDir = __DIR__ . '/../uploads/pacientes/' . $id_nino . '/evaluaciones/' . $seccion_dir . '/' . $id_eval;

function rrmdir($dir) {
    if (!is_dir($dir)) return;
    foreach (scandir($dir) as $item) {
        if ($item === '.' || $item === '..') continue;
        $path = $dir . '/' . $item;
        if (is_dir($path)) {
            rrmdir($path);
        } else {
            @unlink($path);
        }
    }
    @rmdir($dir);
}
rrmdir($baseDir);

$stmt = $conn->prepare("DELETE FROM exp_evaluacion_fotos_imagenes WHERE id_eval_foto=?");
$stmt->bind_param('i', $id_eval);
$stmt->execute();
$stmt->close();

$stmt = $conn->prepare("DELETE FROM exp_evaluacion_fotos WHERE id_eval_foto=?");
$stmt->bind_param('i', $id_eval);
$stmt->execute();
$success = $stmt->affected_rows > 0;
$stmt->close();

$db->closeConnection();

echo json_encode(['success' => $success]);
