<?php
header('Content-Type: application/json');
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] == 2) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}
require_once '../database/conexion.php';

$id_imagen = intval($_POST['id_imagen'] ?? 0);
if ($id_imagen <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Datos invalidos']);
    exit;
}

$db = new Database();
$conn = $db->getConnection();

$stmt = $conn->prepare("SELECT ei.id_eval_foto, ei.ruta, ef.id_nino, ef.seccion FROM exp_evaluacion_fotos_imagenes ei JOIN exp_evaluacion_fotos ef ON ei.id_eval_foto = ef.id_eval_foto WHERE ei.id_imagen=?");
$stmt->bind_param('i', $id_imagen);
$stmt->execute();
$info = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$info) {
    $db->closeConnection();
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Imagen no encontrada']);
    exit;
}

$seccion_dir = preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($info['seccion']));
if ($seccion_dir === '') {
    $seccion_dir = 'general';
}
$path = __DIR__ . '/../uploads/pacientes/' . $info['id_nino'] . '/evaluaciones/' . $seccion_dir . '/' . $info['id_eval_foto'] . '/' . $info['ruta'];
if (is_file($path)) {
    @unlink($path);
}

$stmt = $conn->prepare("DELETE FROM exp_evaluacion_fotos_imagenes WHERE id_imagen=?");
$stmt->bind_param('i', $id_imagen);
$stmt->execute();
$success = $stmt->affected_rows > 0;
$stmt->close();

$db->closeConnection();

echo json_encode(['success' => $success]);
