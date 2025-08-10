<?php
require_once '../database/conexion.php';
header('Content-Type: application/json');

$db = new Database();
$conn = $db->getConnection();

$usuarios = [];
$result = $conn->query("SELECT `id`, `name` FROM `Usuarios` WHERE `IdRol` in (2,3) and `activo` =1 ORDER BY `name` ASC");
if ($result) {
    $usuarios = [];
    while ($row = $result->fetch_assoc()) {
        // Capitaliza la primera letra de cada palabra en 'name'
        $row['name'] = ucwords(strtolower($row['name']));
        $usuarios[] = $row;
    }
}

$db->closeConnection();

echo json_encode($usuarios);

