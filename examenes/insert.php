<?php
require_once '../database/conexion.php';
header('Content-Type: application/json');

$db = new Database();
$conn = $db->getConnection();

$data = json_decode(file_get_contents('php://input'), true);

$id_area = isset($data['id_area']) ? intval($data['id_area']) : 0;
$id_usuario = isset($data['id_usuario']) ? intval($data['id_usuario']) : 0;
$nombre_examen = $data['nombre_examen'] ?? '';

$response = ['success' => false];

if ($id_area && $id_usuario && $nombre_examen) {
    $stmt = $conn->prepare('INSERT INTO exp_examenes (id_area, id_usuario, nombre_examen) VALUES (?, ?, ?)');
    $stmt->bind_param('iis', $id_area, $id_usuario, $nombre_examen);
    if ($stmt->execute()) {
        $response['success'] = true;
        $response['id_examen'] = $stmt->insert_id;
    } else {
        $response['error'] = $conn->error;
    }
    $stmt->close();
}

$db->closeConnection();
echo json_encode($response);
