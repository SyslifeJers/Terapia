<?php
// Procesa el formulario de subir archivos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    if ($titulo === '') {
        die('El título es obligatorio');
    }

    $baseDir = __DIR__ . '/uploads/evaluaciones';
    if (!is_dir($baseDir)) {
        mkdir($baseDir, 0777, true);
    }

    $folder = $baseDir . '/' . time();
    if (!mkdir($folder, 0777, true)) {
        die('No se pudo crear el directorio de los archivos');
    }

    $archivos = [];
    if (!empty($_FILES['archivos']['name'][0])) {
        foreach ($_FILES['archivos']['tmp_name'] as $i => $tmpName) {
            if ($_FILES['archivos']['error'][$i] === UPLOAD_ERR_OK) {
                $info = pathinfo($_FILES['archivos']['name'][$i]);
                $base = preg_replace('/[^A-Za-z0-9_-]/', '_', $info['filename']);
                $ext = strtolower($info['extension'] ?? '');
                $filename = $base . '_' . time() . '_' . $i . '.' . $ext;
                move_uploaded_file($tmpName, $folder . '/' . $filename);
                $archivos[] = $filename;
            }
        }
    }

    $meta = [
        'titulo' => $titulo,
        'fecha' => date('Y-m-d H:i:s'),
        'archivos' => $archivos
    ];
    file_put_contents($folder . '/meta.json', json_encode($meta, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

    header('Location: evaluaciones.php');
    exit;
} else {
    header('Location: subir_evaluacion.php');
    exit;
}
