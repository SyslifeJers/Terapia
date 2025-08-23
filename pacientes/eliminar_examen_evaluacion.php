<?php
header('Content-Type: application/json');
require_once '../database/conexion.php';

$id_eval = intval($_POST['id_eval'] ?? 0);
$id_nino = intval($_POST['id_nino'] ?? 0);

if ($id_eval <= 0 || $id_nino <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false]);
    exit;
}

$db = new Database();
$conn = $db->getConnection();

$stmt = $conn->prepare("DELETE FROM exp_evaluacion_examen WHERE id_eval=? AND id_nino=? AND status=0");
$stmt->bind_param('ii', $id_eval, $id_nino);
$stmt->execute();
$success = $stmt->affected_rows > 0;
$stmt->close();
$db->closeConnection();

echo json_encode(['success' => $success]);
?>
