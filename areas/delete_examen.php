<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] == 2) {
    header('Location: examenes.php');
    exit;
}
require_once '../database/conexion.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$area_id = isset($_GET['area_id']) ? (int)$_GET['area_id'] : 0;
if ($id > 0) {
    $db = new Database();
    $conn = $db->getConnection();
    $stmt = $conn->prepare('DELETE FROM exp_examenes WHERE id_examen = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    $db->closeConnection();
}

$redirect = $area_id > 0 ? 'examenes.php?id=' . urlencode($area_id) : 'index.php';
header('Location: ' . $redirect);
exit;
?>
