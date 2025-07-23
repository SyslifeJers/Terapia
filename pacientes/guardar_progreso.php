<?php
require_once '../database/conexion.php';

$db = new Database();
$conn = $db->getConnection();

$id_nino = intval($_POST['id_nino'] ?? 0);
$id_usuario = intval($_POST['id_usuario'] ?? 0);
$lenguaje = intval($_POST['lenguaje'] ?? 0);
$motricidad = intval($_POST['motricidad'] ?? 0);
$atencion = intval($_POST['atencion'] ?? 0);
$memoria = intval($_POST['memoria'] ?? 0);
$social = intval($_POST['social'] ?? 0);
$observaciones = $_POST['observaciones'] ?? '';

$stmt = $conn->prepare("INSERT INTO exp_progreso_general (id_nino, id_usuario, lenguaje, motricidad, atencion, memoria, social, observaciones) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param('iiiiiiis', $id_nino, $id_usuario, $lenguaje, $motricidad, $atencion, $memoria, $social, $observaciones);
$stmt->execute();
$stmt->close();
$db->closeConnection();

header('Location: paciente.php?id=' . $id_nino);
exit();
