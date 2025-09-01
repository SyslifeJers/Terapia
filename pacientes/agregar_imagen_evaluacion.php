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
if ($id_eval <= 0 || empty($_FILES['fotos']['name'][0])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Datos invalidos']);
    exit;
}

$db = new Database();
$conn = $db->getConnection();

$stmt = $conn->prepare("SELECT id_nino, seccion FROM exp_evaluacion_fotos WHERE id_eval_foto=?");
$stmt->bind_param('i', $id_eval);
$stmt->execute();
$info = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$info) {
    $db->closeConnection();
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Evaluación no encontrada']);
    exit;
}

$id_nino = $info['id_nino'];
$seccion_dir = preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($info['seccion']));
if ($seccion_dir === '') {
    $seccion_dir = 'general';
}
$baseDir = __DIR__ . '/../uploads/pacientes/' . $id_nino . '/evaluaciones/' . $seccion_dir . '/' . $id_eval;
if (!is_dir($baseDir)) {
    mkdir($baseDir, 0777, true);
}

$success = true;
foreach ($_FILES['fotos']['tmp_name'] as $i => $tmpName) {
    if ($_FILES['fotos']['error'][$i] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['fotos']['name'][$i], PATHINFO_EXTENSION));
        $filename = uniqid('img_') . '.' . $ext;
        if (move_uploaded_file($tmpName, $baseDir . '/' . $filename)) {
            $stmt = $conn->prepare("INSERT INTO exp_evaluacion_fotos_imagenes (id_eval_foto, ruta) VALUES (?, ?)");
            $stmt->bind_param('is', $id_eval, $filename);
            $stmt->execute();
            $stmt->close();
        } else {
            $success = false;
        }
    }
}

$db->closeConnection();
echo json_encode(['success' => $success]);
