<?php

declare(strict_types=1);

ini_set('display_errors', '0');
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('America/Mexico_City');

require_once __DIR__ . '/../database/conexion.php';

function calendar_json_response(int $statusCode, array $payload): void
{
    http_response_code($statusCode);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function calendar_table_exists(mysqli $conn, string $table): bool
{
    $stmt = $conn->prepare('SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? LIMIT 1');
    if (!$stmt) {
        return false;
    }

    $stmt->bind_param('s', $table);
    $stmt->execute();
    $result = $stmt->get_result();
    $exists = $result && $result->num_rows > 0;
    $stmt->close();

    return $exists;
}

function calendar_normalize_date_param($value, DateTimeZone $timezone): ?string
{
    if ($value === null) {
        return null;
    }

    $raw = trim((string) $value);
    if ($raw === '') {
        return null;
    }

    try {
        $date = new DateTime($raw);
        $date->setTimezone($timezone);
        return $date->format('Y-m-d H:i:s');
    } catch (Throwable $exception) {
        calendar_json_response(400, ['error' => 'El rango de fechas del calendario no es valido.']);
    }
}

function calendar_nth_weekday_of_month(int $year, int $month, int $weekday, int $weekNumber, DateTimeZone $timezone): DateTime
{
    $date = new DateTime(sprintf('%04d-%02d-01', $year, $month), $timezone);
    while ((int) $date->format('N') !== $weekday) {
        $date->modify('+1 day');
    }

    $candidate = clone $date;
    $candidate->modify('+' . max(0, $weekNumber - 1) . ' week');
    if ((int) $candidate->format('n') === $month) {
        return $candidate;
    }

    $last = new DateTime(sprintf('%04d-%02d-01', $year, $month), $timezone);
    $last->modify('last day of this month');
    while ((int) $last->format('N') !== $weekday) {
        $last->modify('-1 day');
    }

    return $last;
}

function calendar_apply_time(DateTime $date, string $time): DateTime
{
    $timeDate = DateTime::createFromFormat('H:i:s', $time) ?: DateTime::createFromFormat('H:i', $time);
    $result = clone $date;
    if ($timeDate instanceof DateTime) {
        $result->setTime((int) $timeDate->format('H'), (int) $timeDate->format('i'), (int) $timeDate->format('s'));
    }

    return $result;
}

$userId = isset($_SESSION['id']) ? (int) $_SESSION['id'] : 0;
if ($userId <= 0) {
    calendar_json_response(401, ['error' => 'No autenticado.']);
}

$timezone = new DateTimeZone('America/Mexico_City');
$start = calendar_normalize_date_param($_GET['start'] ?? null, $timezone);
$end = calendar_normalize_date_param($_GET['end'] ?? null, $timezone);

$db = new Database();
$conn = $db->getConnection();
$conn->set_charset('utf8mb4');

$events = [];
$hasSolicitudes = calendar_table_exists($conn, 'SolicitudReprogramacion');

$selectSolicitudes = $hasSolicitudes ? ', COALESCE(sr.solicitudes_pendientes, 0) AS solicitudes_pendientes' : ', 0 AS solicitudes_pendientes';
$joinSolicitudes = $hasSolicitudes
    ? " LEFT JOIN (SELECT cita_id, COUNT(*) AS solicitudes_pendientes FROM SolicitudReprogramacion WHERE estatus = 'pendiente' AND tipo = 'reprogramacion' GROUP BY cita_id) sr ON sr.cita_id = ci.id"
    : '';

$conditions = ['ci.IdUsuario = ?'];
$types = 'i';
$params = [$userId];

if ($start !== null) {
    $conditions[] = 'ci.Programado >= ?';
    $types .= 's';
    $params[] = $start;
}

if ($end !== null) {
    $conditions[] = 'ci.Programado < ?';
    $types .= 's';
    $params[] = $end;
}

$sqlCitas = 'SELECT ci.id, ci.IdNino AS paciente_id, ci.Programado, COALESCE(ci.Tiempo, 60) AS Tiempo,
                    ci.Estatus, es.name AS estatus_nombre, n.name AS paciente'
    . $selectSolicitudes
    . ' FROM Cita ci
        INNER JOIN nino n ON n.id = ci.IdNino
        LEFT JOIN Estatus es ON es.id = ci.Estatus'
    . $joinSolicitudes
    . ' WHERE ' . implode(' AND ', $conditions) . '
        ORDER BY ci.Programado ASC';

$stmtCitas = $conn->prepare($sqlCitas);
if (!$stmtCitas) {
    $db->closeConnection();
    calendar_json_response(500, ['error' => 'No fue posible preparar las citas del calendario.']);
}

$stmtCitas->bind_param($types, ...$params);
if (!$stmtCitas->execute()) {
    $stmtCitas->close();
    $db->closeConnection();
    calendar_json_response(500, ['error' => 'No fue posible consultar las citas del calendario.']);
}

$resultCitas = $stmtCitas->get_result();
while ($row = $resultCitas->fetch_assoc()) {
    $startDate = DateTime::createFromFormat('Y-m-d H:i:s', (string) $row['Programado'], $timezone);
    if (!$startDate) {
        continue;
    }

    $duration = max(1, (int) ($row['Tiempo'] ?? 60));
    $endDate = clone $startDate;
    $endDate->modify('+' . $duration . ' minutes');

    $statusName = trim((string) ($row['estatus_nombre'] ?? ''));
    $statusLower = function_exists('mb_strtolower') ? mb_strtolower($statusName, 'UTF-8') : strtolower($statusName);
    $isCancelled = (int) ($row['Estatus'] ?? 0) === 1 || strpos($statusLower, 'cancel') !== false;
    $isReprogrammed = strpos($statusLower, 'reprogram') !== false;
    $hasPendingReprogram = (int) ($row['solicitudes_pendientes'] ?? 0) > 0;

    $titleSuffix = '';
    if ($isCancelled) {
        $titleSuffix = ' (cancelada)';
    } elseif ($hasPendingReprogram) {
        $titleSuffix = ' (reprogramacion pendiente)';
    } elseif ($isReprogrammed) {
        $titleSuffix = ' (reprogramada)';
    }

    $classNames = ['calendar-event', 'event-type-cita'];
    if ($isCancelled) {
        $classNames[] = 'cita-cancelada';
    }
    if ($isReprogrammed || $hasPendingReprogram) {
        $classNames[] = 'cita-reprogramacion';
    }

    $events[] = [
        'id' => 'cita-' . (int) $row['id'],
        'title' => trim((string) ($row['paciente'] ?? 'Cita')) . $titleSuffix,
        'start' => $startDate->format(DateTime::ATOM),
        'end' => $endDate->format(DateTime::ATOM),
        'url' => 'pacientes/paciente.php?id=' . urlencode((string) $row['paciente_id']),
        'classNames' => $classNames,
        'backgroundColor' => $isCancelled ? '#FDECEC' : ($isReprogrammed || $hasPendingReprogram ? '#FFF7D6' : '#E3F2FD'),
        'borderColor' => $isCancelled ? '#F3A6A6' : ($isReprogrammed || $hasPendingReprogram ? '#D89B00' : '#4D8FEA'),
        'textColor' => $isCancelled ? '#8A1F1F' : '#0F172A',
        'extendedProps' => [
            'eventKind' => 'cita',
            'estatus' => $statusName,
            'reprogramacionPendiente' => $hasPendingReprogram,
        ],
    ];
}
$stmtCitas->close();

if (calendar_table_exists($conn, 'ReunionInterna') && calendar_table_exists($conn, 'ReunionInternaPsicologo')) {
    $meetingConditions = ['EXISTS (SELECT 1 FROM ReunionInternaPsicologo rip_user WHERE rip_user.reunion_id = ri.id AND rip_user.psicologo_id = ?)'];
    $meetingTypes = 'i';
    $meetingParams = [$userId];

    if ($start !== null) {
        $meetingConditions[] = 'ri.fin >= ?';
        $meetingTypes .= 's';
        $meetingParams[] = $start;
    }
    if ($end !== null) {
        $meetingConditions[] = 'ri.inicio < ?';
        $meetingTypes .= 's';
        $meetingParams[] = $end;
    }

    $sqlReuniones = 'SELECT ri.id, ri.titulo, ri.descripcion, ri.inicio, ri.fin,
                            GROUP_CONCAT(u.name ORDER BY u.name SEPARATOR ", ") AS psicologos
                     FROM ReunionInterna ri
                     INNER JOIN ReunionInternaPsicologo rip ON rip.reunion_id = ri.id
                     INNER JOIN Usuarios u ON u.id = rip.psicologo_id
                     WHERE ' . implode(' AND ', $meetingConditions) . '
                     GROUP BY ri.id, ri.titulo, ri.descripcion, ri.inicio, ri.fin
                     ORDER BY ri.inicio ASC';

    $stmtReuniones = $conn->prepare($sqlReuniones);
    if ($stmtReuniones) {
        $stmtReuniones->bind_param($meetingTypes, ...$meetingParams);
        if ($stmtReuniones->execute()) {
            $resultReuniones = $stmtReuniones->get_result();
            while ($row = $resultReuniones->fetch_assoc()) {
                $meetingStart = DateTime::createFromFormat('Y-m-d H:i:s', (string) $row['inicio'], $timezone);
                if (!$meetingStart) {
                    continue;
                }
                $meetingEnd = DateTime::createFromFormat('Y-m-d H:i:s', (string) $row['fin'], $timezone) ?: (clone $meetingStart)->modify('+1 hour');

                $participants = trim((string) ($row['psicologos'] ?? ''));
                $events[] = [
                    'id' => 'reunion-' . (int) $row['id'],
                    'title' => 'Reunion: ' . trim((string) ($row['titulo'] ?? 'Sin titulo')),
                    'start' => $meetingStart->format(DateTime::ATOM),
                    'end' => $meetingEnd->format(DateTime::ATOM),
                    'classNames' => ['calendar-event', 'event-type-reunion'],
                    'backgroundColor' => '#EDE9FE',
                    'borderColor' => '#8B5CF6',
                    'textColor' => '#312E81',
                    'extendedProps' => [
                        'eventKind' => 'reunion',
                        'description' => trim((string) ($row['descripcion'] ?? '')),
                        'participants' => $participants,
                    ],
                ];
            }
        }
        $stmtReuniones->close();
    }
}

$hasRecurringTables = calendar_table_exists($conn, 'ReunionInternaRecurrencia')
    && calendar_table_exists($conn, 'ReunionInternaRecurrenciaPsicologo');

if ($hasRecurringTables) {
    $rangeStart = $start !== null ? new DateTime($start, $timezone) : new DateTime('first day of this month', $timezone);
    $rangeEnd = $end !== null ? new DateTime($end, $timezone) : new DateTime('last day of this month 23:59:59', $timezone);
    $rangeStartDate = clone $rangeStart;
    $rangeStartDate->setTime(0, 0, 0);
    $rangeEndDate = clone $rangeEnd;
    $rangeEndDate->setTime(23, 59, 59);

    $hasRecurringExceptions = calendar_table_exists($conn, 'ReunionInternaRecurrenciaExcepcion');
    $exceptionSelect = $hasRecurringExceptions ? ', GROUP_CONCAT(DISTINCT ex.fecha_ocurrencia ORDER BY ex.fecha_ocurrencia SEPARATOR ",") AS fechas_canceladas' : ', NULL AS fechas_canceladas';
    $exceptionJoin = $hasRecurringExceptions ? " LEFT JOIN ReunionInternaRecurrenciaExcepcion ex ON ex.recurrencia_id = rir.id AND ex.accion = 'cancelada'" : '';

    $sqlRecurrentes = 'SELECT rir.id, rir.titulo, rir.descripcion, rir.fecha_inicio, rir.fecha_fin,
                              rir.hora_inicio, rir.hora_fin, rir.frecuencia, rir.intervalo,
                              rir.dia_semana, rir.semana_mes, rir.mes_anual, rir.dia_anual,
                              rir.bloquea_agenda,
                              GROUP_CONCAT(u.name ORDER BY u.name SEPARATOR ", ") AS psicologos'
        . $exceptionSelect . '
                       FROM ReunionInternaRecurrencia rir
                       LEFT JOIN ReunionInternaRecurrenciaPsicologo rirp ON rirp.recurrencia_id = rir.id
                       LEFT JOIN Usuarios u ON u.id = rirp.psicologo_id'
        . $exceptionJoin . '
                       WHERE rir.activo = 1
                         AND rir.fecha_inicio <= ?
                         AND (rir.fecha_fin IS NULL OR rir.fecha_fin >= ?)
                         AND (rir.bloquea_agenda = 0 OR EXISTS (
                             SELECT 1 FROM ReunionInternaRecurrenciaPsicologo user_rirp
                             WHERE user_rirp.recurrencia_id = rir.id AND user_rirp.psicologo_id = ?
                         ))
                       GROUP BY rir.id, rir.titulo, rir.descripcion, rir.fecha_inicio, rir.fecha_fin,
                                rir.hora_inicio, rir.hora_fin, rir.frecuencia, rir.intervalo,
                                rir.dia_semana, rir.semana_mes, rir.mes_anual, rir.dia_anual, rir.bloquea_agenda
                       ORDER BY rir.fecha_inicio ASC';

    $rangeEndSql = $rangeEndDate->format('Y-m-d');
    $rangeStartSql = $rangeStartDate->format('Y-m-d');
    $stmtRecurrentes = $conn->prepare($sqlRecurrentes);
    if ($stmtRecurrentes) {
        $stmtRecurrentes->bind_param('ssi', $rangeEndSql, $rangeStartSql, $userId);
        if ($stmtRecurrentes->execute()) {
            $resultRecurrentes = $stmtRecurrentes->get_result();
            while ($row = $resultRecurrentes->fetch_assoc()) {
                $seriesStart = DateTime::createFromFormat('Y-m-d', (string) $row['fecha_inicio'], $timezone);
                if (!$seriesStart) {
                    continue;
                }

                $seriesEnd = !empty($row['fecha_fin']) ? DateTime::createFromFormat('Y-m-d', (string) $row['fecha_fin'], $timezone) : null;
                $cancelledDates = array_flip(array_filter(explode(',', (string) ($row['fechas_canceladas'] ?? ''))));
                $frequency = (string) $row['frecuencia'];
                $interval = max(1, (int) $row['intervalo']);
                $occurrences = [];

                if ($frequency === 'semanal') {
                    $cursor = clone $seriesStart;
                    while ((int) $cursor->format('N') !== (int) $row['dia_semana']) {
                        $cursor->modify('+1 day');
                    }
                    while ($cursor <= $rangeEndDate) {
                        if ($cursor >= $rangeStartDate && ($seriesEnd === null || $cursor <= $seriesEnd)) {
                            $occurrences[] = clone $cursor;
                        }
                        $cursor->modify('+' . $interval . ' week');
                    }
                } elseif ($frequency === 'mensual_dia_semana') {
                    $cursor = new DateTime($rangeStartDate->format('Y-m-01'), $timezone);
                    $seriesMonth = ((int) $seriesStart->format('Y')) * 12 + (int) $seriesStart->format('n');
                    while ($cursor <= $rangeEndDate) {
                        $cursorMonth = ((int) $cursor->format('Y')) * 12 + (int) $cursor->format('n');
                        if ($cursorMonth >= $seriesMonth && (($cursorMonth - $seriesMonth) % $interval) === 0) {
                            $candidate = calendar_nth_weekday_of_month((int) $cursor->format('Y'), (int) $cursor->format('n'), (int) $row['dia_semana'], (int) $row['semana_mes'], $timezone);
                            if ($candidate >= $rangeStartDate && $candidate <= $rangeEndDate && $candidate >= $seriesStart && ($seriesEnd === null || $candidate <= $seriesEnd)) {
                                $occurrences[] = $candidate;
                            }
                        }
                        $cursor->modify('first day of next month');
                    }
                } elseif ($frequency === 'anual_aviso') {
                    for ($year = (int) $rangeStartDate->format('Y'); $year <= (int) $rangeEndDate->format('Y'); $year++) {
                        $candidate = DateTime::createFromFormat('Y-m-d', sprintf('%04d-%02d-%02d', $year, (int) $row['mes_anual'], (int) $row['dia_anual']), $timezone);
                        if ($candidate && $candidate >= $rangeStartDate && $candidate <= $rangeEndDate && $candidate >= $seriesStart && ($seriesEnd === null || $candidate <= $seriesEnd)) {
                            $occurrences[] = $candidate;
                        }
                    }
                }

                foreach ($occurrences as $occurrence) {
                    $occurrenceDate = $occurrence->format('Y-m-d');
                    if (isset($cancelledDates[$occurrenceDate])) {
                        continue;
                    }

                    $occurrenceStart = calendar_apply_time($occurrence, (string) $row['hora_inicio']);
                    $occurrenceEnd = calendar_apply_time($occurrence, (string) $row['hora_fin']);
                    $isAnnualNotice = (int) $row['bloquea_agenda'] !== 1;
                    $participants = trim((string) ($row['psicologos'] ?? ''));

                    $events[] = [
                        'id' => 'reunion-recurrente-' . (int) $row['id'] . '-' . $occurrenceStart->format('Ymd'),
                        'title' => ($isAnnualNotice ? 'Aviso: ' : 'Reunion recurrente: ') . trim((string) ($row['titulo'] ?? 'Sin titulo')),
                        'start' => $occurrenceStart->format(DateTime::ATOM),
                        'end' => $occurrenceEnd->format(DateTime::ATOM),
                        'classNames' => ['calendar-event', $isAnnualNotice ? 'event-type-aviso' : 'event-type-reunion-recurrente'],
                        'backgroundColor' => $isAnnualNotice ? '#E0F2FE' : '#F3E8FF',
                        'borderColor' => $isAnnualNotice ? '#0284C7' : '#A855F7',
                        'textColor' => $isAnnualNotice ? '#075985' : '#581C87',
                        'extendedProps' => [
                            'eventKind' => $isAnnualNotice ? 'aviso_anual' : 'reunion_recurrente',
                            'description' => trim((string) ($row['descripcion'] ?? '')),
                            'participants' => $participants,
                            'occurrenceDate' => $occurrenceDate,
                        ],
                    ];
                }
            }
        }
        $stmtRecurrentes->close();
    }
}

usort($events, static function (array $first, array $second): int {
    return strcmp((string) ($first['start'] ?? ''), (string) ($second['start'] ?? ''));
});

$db->closeConnection();
calendar_json_response(200, ['data' => $events]);
