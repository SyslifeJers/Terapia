<?php
require_once '../database/conexion.php';
header('Content-Type: application/json');

$db = new Database();
$conn = $db->getConnection();

$data = json_decode(file_get_contents('php://input'), true);

$id_examen = isset($data['id_examen']) ? intval($data['id_examen']) : 0;
$nombre_seccion = $data['nombre_seccion'] ?? '';

$response = ['success' => false];

if ($id_examen && $nombre_seccion) {
    $stmt = $conn->prepare('INSERT INTO exp_secciones_examen (id_examen, nombre_seccion) VALUES (?, ?)');
    $stmt->bind_param('is', $id_examen, $nombre_seccion);
    if ($stmt->execute()) {
        $response['success'] = true;
        $response['id_seccion'] = $stmt->insert_id;
    } else {
        $response['error'] = $conn->error;
    }
    $stmt->close();
}

$db->closeConnection();
echo json_encode($response);
