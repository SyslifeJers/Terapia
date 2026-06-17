<?php

declare(strict_types=1);

date_default_timezone_set('America/Mexico_City');

require_once __DIR__ . '/../database/conexion.php';

function terapia_flujos_response(int $statusCode, array $payload): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function terapia_flujos_session(): array
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $usuarioId = isset($_SESSION['id']) ? (int) $_SESSION['id'] : 0;
    if ($usuarioId <= 0) {
        terapia_flujos_response(401, ['success' => false, 'message' => 'No autenticado.']);
    }

    if (empty($_SESSION['flujos_csrf'])) {
        $_SESSION['flujos_csrf'] = bin2hex(random_bytes(16));
    }

    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
        terapia_flujos_require_csrf();
    }

    return ['id' => $usuarioId, 'rol' => isset($_SESSION['rol']) ? (int) $_SESSION['rol'] : 0];
}

function terapia_flujos_csrf_token(): string
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (empty($_SESSION['flujos_csrf'])) {
        $_SESSION['flujos_csrf'] = bin2hex(random_bytes(16));
    }
    return (string) $_SESSION['flujos_csrf'];
}

function terapia_flujos_require_csrf(): void
{
    $expected = (string) ($_SESSION['flujos_csrf'] ?? '');
    $provided = (string) ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($_POST['csrf_token'] ?? ''));
    if ($expected === '' || $provided === '' || !hash_equals($expected, $provided)) {
        terapia_flujos_response(419, ['success' => false, 'message' => 'Token de seguridad invalido.']);
    }
}

function terapia_flujos_detect_mime(string $path): string
{
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo) {
            $mime = (string) finfo_file($finfo, $path);
            finfo_close($finfo);
            return $mime;
        }
    }
    return '';
}

function terapia_flujos_input(): array
{
    $raw = (string) file_get_contents('php://input');
    $json = json_decode($raw, true);
    return is_array($json) ? $json : $_POST;
}

function terapia_flujos_db(): array
{
    $db = new Database();
    $conn = $db->getConnection();
    $conn->set_charset('utf8mb4');
    return [$db, $conn];
}

function terapia_flujos_task_access(mysqli $conn, int $taskId, int $userId): ?array
{
    $stmt = $conn->prepare("SELECT ta.*, n.name AS paciente, f.nombre AS flujo_nombre
        FROM exp_flujo_tareas_asignadas ta
        INNER JOIN exp_flujo_asignaciones a ON a.id_asignacion = ta.id_asignacion
        INNER JOIN exp_flujos f ON f.id_flujo = a.id_flujo
        INNER JOIN nino n ON n.id = ta.id_nino
        WHERE ta.id_tarea_asignada = ?
          AND ta.activo = 1
          AND ta.responsable = 'psicologa'
          AND ta.responsable_usuario_id = ?
        LIMIT 1");
    if (!$stmt) {
        return null;
    }
    $stmt->bind_param('ii', $taskId, $userId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return is_array($row) ? $row : null;
}

function terapia_flujos_historial(mysqli $conn, ?int $asignacionId, ?int $taskId, string $accion, string $descripcion, int $usuarioId): void
{
    $stmt = $conn->prepare('INSERT INTO exp_flujo_historial (id_asignacion, id_tarea_asignada, accion, descripcion, usuario_id) VALUES (?, ?, ?, ?, ?)');
    if (!$stmt) {
        return;
    }
    $stmt->bind_param('iissi', $asignacionId, $taskId, $accion, $descripcion, $usuarioId);
    $stmt->execute();
    $stmt->close();
}

function terapia_flujos_actualizar_avance(mysqli $conn, int $asignacionId): void
{
    $stmt = $conn->prepare("SELECT
        COUNT(*) AS total,
        SUM(CASE WHEN estatus = 'completada' THEN 1 ELSE 0 END) AS completadas,
        SUM(CASE WHEN estatus = 'negada' AND motivo_negacion IS NOT NULL AND motivo_negacion <> '' THEN 1 ELSE 0 END) AS negadas
        FROM exp_flujo_tareas_asignadas
        WHERE id_asignacion = ? AND obligatoria = 1 AND activo = 1 AND estatus <> 'cancelada'");
    if (!$stmt) {
        return;
    }
    $stmt->bind_param('i', $asignacionId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc() ?: [];
    $stmt->close();

    $total = (int)($row['total'] ?? 0);
    $completadas = (int)($row['completadas'] ?? 0);
    $negadas = (int)($row['negadas'] ?? 0);
    $avance = $total > 0 ? round(($completadas / $total) * 100, 2) : 0.0;
    $estatus = 'activo';
    if ($total > 0 && $completadas === $total) {
        $estatus = 'completado';
    } elseif ($total > 0 && ($completadas + $negadas) === $total) {
        $estatus = 'completado_excepciones';
    }

    $stmtUpdate = $conn->prepare("UPDATE exp_flujo_asignaciones SET avance = ?, estatus = CASE WHEN estatus = 'cancelado' THEN estatus ELSE ? END WHERE id_asignacion = ?");
    if ($stmtUpdate) {
        $stmtUpdate->bind_param('dsi', $avance, $estatus, $asignacionId);
        $stmtUpdate->execute();
        $stmtUpdate->close();
    }
}

function terapia_flujos_upload_dir(int $taskId): string
{
    return __DIR__ . '/../uploads/flujos/tareas/' . $taskId;
}
