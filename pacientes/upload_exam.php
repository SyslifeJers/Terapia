<?php
header('Content-Type: application/json');

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$note = isset($_POST['note']) ? trim($_POST['note']) : '';
$seccion = isset($_POST['seccion']) ? trim((string)$_POST['seccion']) : '';

if ($seccion === '') {
    $seccion = 'General';
}

if ($id <= 0 || !isset($_FILES['file'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Datos invalidos']);
    exit;
}

$uploadBase = __DIR__ . '/../uploads/exams/' . $id . '/';
if (!is_dir($uploadBase)) {
    mkdir($uploadBase, 0777, true);
}

// Seccion -> carpeta (excepto 'General' que queda en raiz)
$seccionDir = '';
if (strcasecmp($seccion, 'General') !== 0) {
    $tmp = strtolower($seccion);
    $tmp = preg_replace('/[^a-z0-9]+/', '_', $tmp);
    $tmp = trim($tmp, '_');
    if ($tmp !== '') {
        // limite simple para evitar rutas gigantes
        $seccionDir = substr($tmp, 0, 64);
    }
}

$finalDir = $uploadBase;
if ($seccionDir !== '') {
    $finalDir = $uploadBase . $seccionDir . '/';
    if (!is_dir($finalDir)) {
        mkdir($finalDir, 0777, true);
    }

    // Guardar meta de la seccion para mostrar nombre original
    $metaPath = $finalDir . 'section.json';
    if (!is_file($metaPath)) {
        $meta = [
            'name' => $seccion,
            'dir' => $seccionDir,
            'created_at' => date('c')
        ];
        @file_put_contents($metaPath, json_encode($meta, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }
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
$target = $finalDir . $filename;

if (move_uploaded_file($_FILES['file']['tmp_name'], $target)) {
    if ($note !== '') {
        file_put_contents($target . '.txt', $note);
    }

    echo json_encode([
        'success' => true,
        'file' => $filename,
        'seccion' => $seccion,
        'seccion_dir' => $seccionDir
    ]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al guardar']);
}
?>
