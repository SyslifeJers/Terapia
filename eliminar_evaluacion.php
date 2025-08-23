<?php
// Script para eliminar una evaluación cargada.
// Recibe el identificador del directorio de la evaluación a través del parámetro GET "id".

$id = isset($_GET['id']) ? basename($_GET['id']) : '';
if ($id === '') {
    header('Location: evaluaciones.php');
    exit;
}

$dir = __DIR__ . '/uploads/evaluaciones/' . $id;

// Función recursiva para eliminar un directorio completo
function rrmdir(string $dirPath): void {
    if (!is_dir($dirPath)) {
        return;
    }
    foreach (scandir($dirPath) as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }
        $fullPath = $dirPath . '/' . $item;
        if (is_dir($fullPath)) {
            rrmdir($fullPath);
        } else {
            @unlink($fullPath);
        }
    }
    @rmdir($dirPath);
}

if (is_dir($dir)) {
    rrmdir($dir);
}

header('Location: evaluaciones.php');
exit;
