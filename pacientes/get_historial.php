<?php
require_once '../database/conexion.php';
header('Content-Type: application/json');

$db = new Database();
$conn = $db->getConnection();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$tipo = $_GET['tipo'] ?? '';

$datos = [];

if ($id > 0 && ($tipo === 'evaluacion' || $tipo === 'progreso')) {
    if ($tipo === 'evaluacion') {
        $stmt = $conn->prepare("SELECT fecha_valoracion, participacion, atencion, tarea_casa, observaciones FROM exp_valoraciones_sesion WHERE id_nino = ? ORDER BY fecha_valoracion DESC");
    } else {
        $stmt = $conn->prepare("SELECT fecha_registro, lenguaje, motricidad, atencion, memoria, social, observaciones FROM exp_progreso_general WHERE id_nino = ? ORDER BY fecha_registro DESC");
    }
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result) {
        $datos = $result->fetch_all(MYSQLI_ASSOC);
    }
    $stmt->close();
}

$db->closeConnection();

echo json_encode($datos);
