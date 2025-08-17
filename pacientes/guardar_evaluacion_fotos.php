<?php
require_once '../database/conexion.php';

$db = new Database();
$conn = $db->getConnection();

$id_nino = intval($_POST['id_nino'] ?? 0);
$titulo = trim($_POST['titulo'] ?? '');

if ($id_nino <= 0 || $titulo === '') {
    $db->closeConnection();
    header('Location: paciente.php?id=' . $id_nino);
    exit();
}

// Insertar evaluacion
$stmt = $conn->prepare("INSERT INTO exp_evaluacion_fotos (id_nino, titulo) VALUES (?, ?)");
$stmt->bind_param('is', $id_nino, $titulo);
$stmt->execute();
$id_eval = $stmt->insert_id;
$stmt->close();

$baseDir = __DIR__ . '/../uploads/pacientes/' . $id_nino . '/evaluaciones/' . $id_eval;
if (!is_dir($baseDir)) {
    mkdir($baseDir, 0777, true);
}

if (!empty($_FILES['fotos']['name'][0])) {
    foreach ($_FILES['fotos']['tmp_name'] as $i => $tmpName) {
        if ($_FILES['fotos']['error'][$i] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['fotos']['name'][$i], PATHINFO_EXTENSION));
            $filename = 'img' . ($i + 1) . '.' . $ext;
            if (move_uploaded_file($tmpName, $baseDir . '/' . $filename)) {
                $stmt = $conn->prepare("INSERT INTO exp_evaluacion_fotos_imagenes (id_eval_foto, ruta) VALUES (?, ?)");
                $stmt->bind_param('is', $id_eval, $filename);
                $stmt->execute();
                $stmt->close();
            }
        }
    }
}

$db->closeConnection();
header('Location: paciente.php?id=' . $id_nino);
exit();
?>
