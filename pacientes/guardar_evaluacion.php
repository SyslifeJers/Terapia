<?php
require_once '../database/conexion.php';

$db = new Database();
$conn = $db->getConnection();

$id_nino = intval($_POST['id_nino'] ?? 0);
$id_usuario = intval($_POST['id_usuario'] ?? 0);
$participacion = intval($_POST['participacion'] ?? 0);
$atencion = intval($_POST['atencion'] ?? 0);
$tarea_casa = intval($_POST['tarea_casa'] ?? 0);
$observaciones = $_POST['observaciones'] ?? '';

$stmt = $conn->prepare("INSERT INTO exp_valoraciones_sesion (id_nino, id_usuario, participacion, atencion, tarea_casa, observaciones) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param('iiiiss', $id_nino, $id_usuario, $participacion, $atencion, $tarea_casa, $observaciones);
$stmt->execute();
$stmt->close();
$db->closeConnection();

header('Location: paciente.php?id=' . $id_nino);
exit();
