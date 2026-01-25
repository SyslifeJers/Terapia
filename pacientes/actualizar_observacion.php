<?php
require_once '../database/conexion.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido.'
    ]);
    exit;
}

$idValoracion = isset($_POST['id_valoracion']) ? (int)$_POST['id_valoracion'] : 0;
$observaciones = isset($_POST['observaciones']) ? trim($_POST['observaciones']) : '';

if ($idValoracion <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Registro inválido.'
    ]);
    exit;
}

$db = new Database();
$conn = $db->getConnection();

$stmt = $conn->prepare("UPDATE exp_valoraciones_sesion SET observaciones = ? WHERE id_valoracion = ?");
if (!$stmt) {
    $db->closeConnection();
    echo json_encode([
        'success' => false,
        'message' => 'No se pudo preparar la consulta.'
    ]);
    exit;
}

$stmt->bind_param('si', $observaciones, $idValoracion);
$ok = $stmt->execute();
$stmt->close();
$db->closeConnection();

echo json_encode([
    'success' => $ok,
    'message' => $ok ? '' : 'No se pudo actualizar la observación.'
]);
