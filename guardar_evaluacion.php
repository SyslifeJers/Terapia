<?php
// Procesa el formulario de subir evaluaciones fotográficas
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
        die('No se pudo crear el directorio de la evaluación');
    }

    $imagenes = [];
    if (!empty($_FILES['fotos']['name'][0])) {
        foreach ($_FILES['fotos']['tmp_name'] as $i => $tmpName) {
            if ($_FILES['fotos']['error'][$i] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['fotos']['name'][$i], PATHINFO_EXTENSION));
                $filename = 'img' . ($i + 1) . '.' . $ext;
                move_uploaded_file($tmpName, $folder . '/' . $filename);
                $imagenes[] = $filename;
            }
        }
    }

    $meta = [
        'titulo' => $titulo,
        'fecha' => date('Y-m-d H:i:s'),
        'imagenes' => $imagenes
    ];
    file_put_contents($folder . '/meta.json', json_encode($meta, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

    header('Location: evaluaciones.php');
    exit;
} else {
    header('Location: subir_evaluacion.php');
    exit;
}
