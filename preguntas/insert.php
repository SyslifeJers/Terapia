<?php
require_once '../database/conexion.php';
header('Content-Type: application/json');

$db = new Database();
$conn = $db->getConnection();

$data = json_decode(file_get_contents('php://input'), true);

$pregunta = $data['pregunta'] ?? '';
$id_seccion = isset($data['id_seccion']) ? intval($data['id_seccion']) : 0;
$opciones = $data['opciones'] ?? [];

$response = ['success' => false];

if ($pregunta && $id_seccion) {
    $stmt = $conn->prepare('INSERT INTO exp_preguntas_evaluacion (pregunta, id_seccion) VALUES (?, ?)');
    $stmt->bind_param('si', $pregunta, $id_seccion);
    if ($stmt->execute()) {
        $id_pregunta = $stmt->insert_id;
        $stmt->close();

        if (!empty($opciones)) {
            $stmtRel = $conn->prepare('INSERT INTO exp_pregunta_opcion (id_pregunta, id_opcion) VALUES (?, ?)');
            foreach ($opciones as $id_opcion) {
                $id_opcion = intval($id_opcion);
                $stmtRel->bind_param('ii', $id_pregunta, $id_opcion);
                $stmtRel->execute();
            }
            $stmtRel->close();
        }

        $response['success'] = true;
        $response['id_pregunta'] = $id_pregunta;
    } else {
        $response['error'] = $conn->error;
        $stmt->close();
    }
}

$db->closeConnection();
echo json_encode($response);
