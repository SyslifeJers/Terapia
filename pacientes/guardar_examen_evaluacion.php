<?php
require_once '../database/conexion.php';
session_start();

$db = new Database();
$conn = $db->getConnection();

$id_nino = intval($_POST['id_nino'] ?? 0);
$id_examen = intval($_POST['id_examen'] ?? 0);
$id_eval = intval($_POST['id_eval'] ?? 0);
$id_usuario = intval($_SESSION['id'] ?? 0);
$respuestas = $_POST['respuestas'] ?? '';
$status = intval($_POST['status'] ?? 0);
$autosave = isset($_POST['autosave']);

if ($id_eval > 0) {
    if ($status === 1) {
        $stmt = $conn->prepare("UPDATE exp_evaluacion_examen SET respuestas=?, id_usuario=?, status=1 WHERE id_eval=? AND id_examen=? AND id_nino=? AND status=0");
        $stmt->bind_param('siiii', $respuestas, $id_usuario, $id_eval, $id_examen, $id_nino);
        $stmt->execute();
        $stmt->close();
    } else {
        $stmt = $conn->prepare("UPDATE exp_evaluacion_examen SET respuestas=?, id_usuario=? WHERE id_eval=? AND id_examen=? AND id_nino=? AND status=0");
        $stmt->bind_param('siiii', $respuestas, $id_usuario, $id_eval, $id_examen, $id_nino);
        $stmt->execute();
        $stmt->close();
    }
} else {
    $stmt = $conn->prepare("INSERT INTO exp_evaluacion_examen (id_examen, id_nino, id_usuario, respuestas, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('iiisi', $id_examen, $id_nino, $id_usuario, $respuestas, $status);
    $stmt->execute();
    $id_eval = $conn->insert_id;
    $stmt->close();
}

$db->closeConnection();

if ($autosave) {
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'id_eval' => $id_eval]);
} else {
    header('Location: paciente.php?id=' . $id_nino);
}
exit();
?>
