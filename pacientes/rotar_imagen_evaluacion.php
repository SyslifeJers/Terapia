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
$angulo = intval($_POST['angulo'] ?? 0);
if ($id_imagen <= 0 || !in_array($angulo, [0,90,180,270])) {
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
if (!is_file($path)) {
    $db->closeConnection();
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Archivo no encontrado']);
    exit;
}

$originalTime = filemtime($path);
$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
switch ($ext) {
    case 'jpg':
    case 'jpeg':
        $img = imagecreatefromjpeg($path);
        break;
    case 'png':
        $img = imagecreatefrompng($path);
        break;
    case 'gif':
        $img = imagecreatefromgif($path);
        break;
    default:
        $db->closeConnection();
        http_response_code(415);
        echo json_encode(['success' => false, 'message' => 'Formato no soportado']);
        exit;
}

if (!$img) {
    $db->closeConnection();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'No se pudo procesar la imagen']);
    exit;
}

$rotated = imagerotate($img, -$angulo, 0);
if ($rotated) {
    switch ($ext) {
        case 'jpg':
        case 'jpeg':
            imagejpeg($rotated, $path, 90);
            break;
        case 'png':
            imagepng($rotated, $path);
            break;
        case 'gif':
            imagegif($rotated, $path);
            break;
    }
    imagedestroy($img);
    imagedestroy($rotated);
    touch($path, $originalTime);
    $success = true;
} else {
    $success = false;
}

$db->closeConnection();
echo json_encode(['success' => $success]);
?>
