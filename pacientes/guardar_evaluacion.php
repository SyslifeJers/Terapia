<?php
require_once '../database/conexion.php';

$db = new Database();
$conn = $db->getConnection();

$id_nino = intval($_POST['id_nino'] ?? 0);
$id_usuario = intval($_POST['id_usuario'] ?? 0);
$observaciones = trim($_POST['observaciones'] ?? '');
$criteriosPost = $_POST['criterios'] ?? [];

if ($id_nino <= 0 || $id_usuario <= 0 || !is_array($criteriosPost) || empty($criteriosPost)) {
    $db->closeConnection();
    header('Location: paciente.php?id=' . $id_nino);
    exit();
}

$criteriosValores = [];
foreach ($criteriosPost as $criterioId => $valor) {
    $criterioId = (int)$criterioId;
    if ($criterioId <= 0) {
        continue;
    }
    if (!is_numeric($valor)) {
        continue;
    }
    $valor = (int)$valor;
    $valor = max(0, min(10, $valor));
    $criteriosValores[$criterioId] = $valor;
}

if (empty($criteriosValores)) {
    $db->closeConnection();
    header('Location: paciente.php?id=' . $id_nino);
    exit();
}

try {
    $conn->begin_transaction();

    $stmt = $conn->prepare("INSERT INTO exp_valoraciones_sesion (id_nino, id_usuario, observaciones) VALUES (?, ?, ?)");
    if (!$stmt) {
        throw new Exception('No se pudo guardar la valoración.');
    }
    $stmt->bind_param('iis', $id_nino, $id_usuario, $observaciones);
    if (!$stmt->execute()) {
        throw new Exception('No se pudo guardar la valoración.');
    }
    $idValoracion = $conn->insert_id;
    $stmt->close();

    $detalleStmt = $conn->prepare("INSERT INTO exp_valoracion_detalle (id_valoracion, id_criterio, valor) VALUES (?, ?, ?)");
    if (!$detalleStmt) {
        throw new Exception('No se pudo guardar el detalle de la valoración.');
    }
    $criterioParam = 0;
    $valorParam = 0;
    $detalleStmt->bind_param('iii', $idValoracion, $criterioParam, $valorParam);
    foreach ($criteriosValores as $criterioId => $valor) {
        $criterioParam = $criterioId;
        $valorParam = $valor;
        if (!$detalleStmt->execute()) {
            throw new Exception('No se pudo guardar el detalle de la valoración.');
        }
    }
    $detalleStmt->close();

    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
}

$db->closeConnection();

header('Location: paciente.php?id=' . $id_nino);
exit();
