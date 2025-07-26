<?php
header('Content-Type: application/json');

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$note = isset($_POST['note']) ? trim($_POST['note']) : '';
if ($id <= 0 || !isset($_FILES['file'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Datos invalidos']);
    exit;
}

$uploadBase = __DIR__ . '/../uploads/exams/' . $id . '/';
if (!is_dir($uploadBase)) {
    mkdir($uploadBase, 0777, true);
}

$allowed = ['pdf', 'png', 'jpg', 'jpeg', 'gif'];
$info = pathinfo($_FILES['file']['name']);
$ext = strtolower($info['extension'] ?? '');
if (!in_array($ext, $allowed)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Tipo de archivo no permitido']);
    exit;
}

$base = preg_replace('/[^A-Za-z0-9_-]/', '_', $info['filename']);
$filename = $base . '_' . time() . '.' . $ext;
$target = $uploadBase . $filename;

if (move_uploaded_file($_FILES['file']['tmp_name'], $target)) {
    if ($note !== '') {
        file_put_contents($target . '.txt', $note);
    }
    echo json_encode(['success' => true, 'file' => $filename]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al guardar']);
}
?>
