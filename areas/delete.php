<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] == 2) {
    header('Location: index.php');
    exit;
}
require_once '../database/conexion.php';

$id = $_GET['id'] ?? null;
if ($id) {
    $db = new Database();
    $conn = $db->getConnection();
    $stmt = $conn->prepare('DELETE FROM exp_areas_evaluacion WHERE id_area = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    $db->closeConnection();
}

header('Location: index.php');
exit;
?>
