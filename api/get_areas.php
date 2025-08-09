<?php
require_once '../database/conexion.php';
header('Content-Type: application/json');

$db = new Database();
$conn = $db->getConnection();

$areas = [];
$result = $conn->query("SELECT id_area, nombre_area, descripcion FROM exp_areas_evaluacion ORDER BY nombre_area ASC");
if ($result) {
    $areas = $result->fetch_all(MYSQLI_ASSOC);
}

$db->closeConnection();

echo json_encode($areas);

