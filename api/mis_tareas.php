<?php
declare(strict_types=1);
require_once __DIR__ . '/flujos_lib.php';

$session = terapia_flujos_session();
[$db, $conn] = terapia_flujos_db();

$estatus = trim((string)($_GET['estatus'] ?? ''));
$paciente = trim((string)($_GET['paciente'] ?? ''));
$fecha = trim((string)($_GET['fecha'] ?? ''));

$conditions = ["ta.responsable = 'psicologa'", 'ta.responsable_usuario_id = ?', 'ta.activo = 1'];
$types = 'i';
$params = [$session['id']];

if ($estatus !== '') {
    $conditions[] = 'ta.estatus = ?';
    $types .= 's';
    $params[] = $estatus;
} else {
    $conditions[] = "ta.estatus IN ('pendiente','en_proceso')";
}
if ($paciente !== '') {
    $conditions[] = 'LOWER(n.name) LIKE ?';
    $types .= 's';
    $params[] = '%' . strtolower($paciente) . '%';
}
if ($fecha !== '') {
    $conditions[] = 'ta.fecha_limite = ?';
    $types .= 's';
    $params[] = $fecha;
}

$sql = 'SELECT ta.*, n.name AS paciente, f.nombre AS flujo_nombre,
               CASE WHEN ta.fecha_limite < CURDATE() AND ta.estatus IN (\'pendiente\',\'en_proceso\') THEN 1 ELSE 0 END AS vencida
        FROM exp_flujo_tareas_asignadas ta
        INNER JOIN exp_flujo_asignaciones a ON a.id_asignacion = ta.id_asignacion
        INNER JOIN exp_flujos f ON f.id_flujo = a.id_flujo
        INNER JOIN nino n ON n.id = ta.id_nino
        WHERE ' . implode(' AND ', $conditions) . '
        ORDER BY vencida DESC, ta.fecha_limite ASC, ta.orden ASC';
$stmt = $conn->prepare($sql);
if (!$stmt) {
    terapia_flujos_response(500, ['success' => false, 'message' => 'No fue posible consultar tareas.']);
}
$stmt->bind_param($types, ...$params);
$stmt->execute();
$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$db->closeConnection();

terapia_flujos_response(200, ['success' => true, 'data' => $rows]);
