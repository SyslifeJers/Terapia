<?php
require_once '../database/conexion.php';
session_start();

$db = new Database();
$conn = $db->getConnection();

$id_nino = intval($_POST['id_nino'] ?? 0);

$id_examen = intval($_POST['id_examen'] ?? 0);
$id_usuario = intval($_SESSION['id'] ?? 0);
$respuestas = $_POST['respuestas'] ?? '';

$stmt = $conn->prepare("INSERT INTO exp_evaluacion_examen (id_examen, id_nino, id_usuario, respuestas) VALUES (?, ?, ?, ?)");
$stmt->bind_param('iiis', $id_examen, $id_nino, $id_usuario, $respuestas);

$stmt->execute();
$stmt->close();
$db->closeConnection();

header('Location: paciente.php?id=' . $id_nino);
exit();
?>
