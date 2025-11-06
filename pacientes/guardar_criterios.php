<?php
require_once '../database/conexion.php';

$db = new Database();
$conn = $db->getConnection();

$id_nino = intval($_POST['id_nino'] ?? 0);
$criteriosSeleccionados = $_POST['criterios'] ?? [];

if ($id_nino <= 0) {
    $db->closeConnection();
    header('Location: paciente.php?id=' . $id_nino);
    exit();
}

if (!is_array($criteriosSeleccionados)) {
    $criteriosSeleccionados = [];
}

$criterios = array_values(array_unique(array_filter(array_map('intval', $criteriosSeleccionados), static function ($id) {
    return $id > 0;
})));

try {
    $conn->begin_transaction();

    $stmtDelete = $conn->prepare('DELETE FROM exp_nino_criterio WHERE id_nino = ?');
    if ($stmtDelete) {
        $stmtDelete->bind_param('i', $id_nino);
        $stmtDelete->execute();
        $stmtDelete->close();
    }

    if (!empty($criterios)) {
        $stmtInsert = $conn->prepare('INSERT INTO exp_nino_criterio (id_nino, id_criterio) VALUES (?, ?)');
        if ($stmtInsert) {
            $criterioId = 0;
            $stmtInsert->bind_param('ii', $id_nino, $criterioId);
            foreach ($criterios as $criterio) {
                $criterioId = $criterio;
                $stmtInsert->execute();
            }
            $stmtInsert->close();
        }
    }

    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
}

$db->closeConnection();

header('Location: paciente.php?id=' . $id_nino);
exit();
