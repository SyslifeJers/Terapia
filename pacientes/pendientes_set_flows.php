<?php
header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/../includes/pendientes_lib.php';
require_once __DIR__ . '/../database/conexion.php';

if (!isset($_SESSION['id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$id_nino = isset($_POST['id_nino']) ? (int)$_POST['id_nino'] : 0;
$flows = $_POST['flows'] ?? [];
$redirect = isset($_POST['redirect']) ? (string)$_POST['redirect'] : '';

if ($id_nino <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
    exit;
}

$flows = is_array($flows) ? $flows : [$flows];
$flows = array_values(array_filter(array_map(fn($v) => trim((string)$v), $flows), fn($v) => $v !== ''));

$db = new Database();
$conn = $db->getConnection();

$catalog = pendientes_load_catalog($conn);
$valid = [];
foreach ($catalog['flows'] as $f) {
    $fid = trim((string)($f['id'] ?? ''));
    if ($fid !== '') $valid[$fid] = true;
}

$flows = array_values(array_filter($flows, fn($fid) => isset($valid[$fid])));

if (empty($flows)) {
    $flows = ['diagnostico'];
}

if (!pendientes_save_patient_flows($conn, $id_nino, $flows, (int)($_SESSION['id'] ?? 0))) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'No se pudo guardar']);
    exit;
}

$db->closeConnection();

if ($redirect !== '') {
    header('Location: ' . $redirect);
    exit;
}

echo json_encode(['success' => true, 'flows' => $flows]);
