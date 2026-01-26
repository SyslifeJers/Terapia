<?php
require_once '../database/conexion.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido.'
    ]);
    exit;
}

$idValoracion = isset($_POST['id_valoracion']) ? (int)$_POST['id_valoracion'] : 0;
$observaciones = isset($_POST['observaciones']) ? trim($_POST['observaciones']) : '';
$criterios = $_POST['criterios'] ?? [];

if ($idValoracion <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Registro inválido.'
    ]);
    exit;
}

if (!is_array($criterios)) {
    $criterios = [];
}

$criteriosLimpios = [];
foreach ($criterios as $criterioId => $valor) {
    $criterioId = (int)$criterioId;
    if ($criterioId <= 0) {
        continue;
    }
    if ($valor === '' || $valor === null) {
        echo json_encode([
            'success' => false,
            'message' => 'Todos los criterios deben tener un valor.'
        ]);
        exit;
    }
    if (!is_numeric($valor)) {
        echo json_encode([
            'success' => false,
            'message' => 'Los valores de los criterios deben ser numéricos.'
        ]);
        exit;
    }
    $criteriosLimpios[$criterioId] = (float)$valor;
}

$db = new Database();
$conn = $db->getConnection();
$conn->begin_transaction();

try {
    $stmtObs = $conn->prepare("UPDATE exp_valoraciones_sesion SET observaciones = ? WHERE id_valoracion = ?");
    if (!$stmtObs) {
        throw new RuntimeException('No se pudo preparar la actualización de observaciones.');
    }
    $stmtObs->bind_param('si', $observaciones, $idValoracion);
    if (!$stmtObs->execute()) {
        throw new RuntimeException('No se pudo actualizar la observación.');
    }
    $stmtObs->close();

    if (!empty($criteriosLimpios)) {
        $stmtDet = $conn->prepare("UPDATE exp_valoracion_detalle SET valor = ? WHERE id_valoracion = ? AND id_criterio = ?");
        if (!$stmtDet) {
            throw new RuntimeException('No se pudo preparar la actualización de criterios.');
        }
        foreach ($criteriosLimpios as $criterioId => $valor) {
            $stmtDet->bind_param('dii', $valor, $idValoracion, $criterioId);
            if (!$stmtDet->execute()) {
                $stmtDet->close();
                throw new RuntimeException('No se pudo actualizar un criterio.');
            }
        }
        $stmtDet->close();
    }

    $conn->commit();
    $db->closeConnection();

    echo json_encode([
        'success' => true,
        'message' => ''
    ]);
} catch (Throwable $e) {
    $conn->rollback();
    $db->closeConnection();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
