<?php

function pendientes_catalog_file_path(): string
{
    return __DIR__ . '/data/pendientes_catalog.json';
}

function pendientes_default_catalog(): array
{
    return [
        'version' => 1,
        'flows' => [
            [
                'id' => 'diagnostico',
                'nombre' => 'Diagnóstico',
                'descripcion' => 'Flujo inicial de valoración y documentación clínica.',
                'icon' => 'ni-activity-round',
                'color' => '#6D5BFF',
                'orden' => 1,
                'perfiles' => [
                    [
                        'id' => 'anamnesis',
                        'nombre' => 'Anamnesis',
                        'descripcion' => 'Historia clínica inicial',
                        'icon' => 'ni-clipboad-check',
                        'color' => '#34C38F',
                        'orden' => 1,
                        'tareas' => [
                            [
                                'id' => 'anamnesis_completar',
                                'titulo' => 'Completar anamnesis',
                                'descripcion' => 'Registro base de antecedentes y contexto.',
                                'evidencia' => 'optional',
                                'alerta_tipo' => 'none',
                                'alerta_cantidad' => 0,
                                'tipos' => ['pdf', 'png', 'jpg', 'jpeg', 'gif'],
                                'orden' => 1,
                            ],
                        ],
                    ],
                    [
                        'id' => 'acuerdo_servicios',
                        'nombre' => 'Acuerdo de prestación de servicios',
                        'descripcion' => 'Consentimiento y firma',
                        'icon' => 'ni-file-text',
                        'color' => '#F5A623',
                        'orden' => 2,
                        'tareas' => [
                            [
                                'id' => 'acuerdo_subir',
                                'titulo' => 'Subir acuerdo firmado',
                                'descripcion' => 'Documento de aceptación del servicio.',
                                'evidencia' => 'required',
                                'alerta_tipo' => 'none',
                                'alerta_cantidad' => 0,
                                'tipos' => ['pdf', 'png', 'jpg', 'jpeg', 'gif'],
                                'orden' => 1,
                            ],
                        ],
                    ],
                    [
                        'id' => 'perfil_alimenticio',
                        'nombre' => 'Perfil alimenticio',
                        'descripcion' => 'Hábitos y preferencias',
                        'icon' => 'ni-apple',
                        'color' => '#3B82F6',
                        'orden' => 3,
                        'tareas' => [
                            [
                                'id' => 'alimenticio_registrar',
                                'titulo' => 'Registrar perfil alimenticio',
                                'descripcion' => 'Captura inicial de hábitos de alimentación.',
                                'evidencia' => 'none',
                                'alerta_tipo' => 'none',
                                'alerta_cantidad' => 0,
                                'tipos' => [],
                                'orden' => 1,
                            ],
                        ],
                    ],
                    [
                        'id' => 'perfil_sensorial',
                        'nombre' => 'Perfil sensorial',
                        'descripcion' => 'Sensibilidades y respuestas',
                        'icon' => 'ni-user-alt',
                        'color' => '#94A3B8',
                        'orden' => 4,
                        'tareas' => [
                            [
                                'id' => 'sensorial_registrar',
                                'titulo' => 'Registrar perfil sensorial',
                                'descripcion' => 'Identificación de estímulos y respuestas frecuentes.',
                                'evidencia' => 'none',
                                'alerta_tipo' => 'none',
                                'alerta_cantidad' => 0,
                                'tipos' => [],
                                'orden' => 1,
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ];
}

function pendientes_patient_base_dir(int $id_nino): string
{
    return __DIR__ . '/../uploads/pendientes/' . $id_nino;
}

function pendientes_allowed_task_id(string $task_id): bool
{
    return $task_id !== '' && preg_match('/^[a-zA-Z0-9_-]{1,80}$/', $task_id) === 1;
}

function pendientes_task_evidence_dir(int $id_nino, string $task_id): string
{
    return pendientes_patient_base_dir($id_nino) . '/evidencias/' . $task_id;
}

function pendientes_list_task_files(int $id_nino, string $task_id): array
{
    if (!pendientes_allowed_task_id($task_id)) {
        return [];
    }
    $dir = pendientes_task_evidence_dir($id_nino, $task_id);
    if (!is_dir($dir)) {
        return [];
    }
    $items = array_diff(scandir($dir), ['.', '..']);
    $out = [];
    foreach ($items as $it) {
        if (substr($it, -4) === '.txt') {
            continue;
        }
        $p = $dir . '/' . $it;
        if (is_file($p)) {
            $out[] = $it;
        }
    }
    sort($out, SORT_NATURAL | SORT_FLAG_CASE);
    return $out;
}

function pendientes_task_has_files(int $id_nino, string $task_id): bool
{
    return count(pendientes_list_task_files($id_nino, $task_id)) > 0;
}

function pendientes_normalize_status(string $status): string
{
    $s = strtolower(trim($status));
    $map = [
        'no iniciado' => 'no_iniciado',
        'no_iniciado' => 'no_iniciado',
        'pendiente' => 'no_iniciado',
        'en proceso' => 'en_proceso',
        'en_proceso' => 'en_proceso',
        'completado' => 'completado',
        'completo' => 'completado',
    ];
    return $map[$s] ?? 'no_iniciado';
}

function pendientes_status_label(string $status): string
{
    $s = pendientes_normalize_status($status);
    if ($s === 'completado') {
        return 'Completado';
    }
    if ($s === 'en_proceso') {
        return 'En proceso';
    }
    return 'No iniciado';
}

function pendientes_status_badge_class(string $status): string
{
    $s = pendientes_normalize_status($status);
    if ($s === 'completado') {
        return 'bg-success';
    }
    if ($s === 'en_proceso') {
        return 'bg-warning';
    }
    return 'bg-secondary';
}

function pendientes_status_dot_class(string $status): string
{
    $s = pendientes_normalize_status($status);
    if ($s === 'completado') {
        return 'dot-success';
    }
    if ($s === 'en_proceso') {
        return 'dot-warning';
    }
    return 'dot-gray';
}

function pendientes_array_json(array $value): string
{
    return json_encode(array_values($value), JSON_UNESCAPED_UNICODE);
}

function pendientes_conn_escape(mysqli $conn, string $value): string
{
    return $conn->real_escape_string($value);
}

function pendientes_ensure_column(mysqli $conn, string $table, string $column, string $definition): void
{
    $table = preg_replace('/[^a-zA-Z0-9_]/', '', $table);
    $column = preg_replace('/[^a-zA-Z0-9_]/', '', $column);
    if ($table === '' || $column === '') {
        return;
    }
    $result = $conn->query("SHOW COLUMNS FROM `{$table}` LIKE '{$column}'");
    if ($result && $result->num_rows === 0) {
        $conn->query("ALTER TABLE `{$table}` ADD COLUMN {$definition}");
    }
}

function pendientes_ensure_schema(mysqli $conn): void
{
    static $initialized = false;
    if ($initialized) {
        return;
    }

    $sql = [];
    $sql[] = "CREATE TABLE IF NOT EXISTS spu_flujos (
        id_flujo INT AUTO_INCREMENT PRIMARY KEY,
        slug VARCHAR(80) NOT NULL UNIQUE,
        nombre VARCHAR(150) NOT NULL,
        descripcion TEXT NULL,
        icon VARCHAR(80) NULL,
        color VARCHAR(20) NULL,
        orden INT NOT NULL DEFAULT 0,
        activo TINYINT(1) NOT NULL DEFAULT 1,
        creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        actualizado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $sql[] = "CREATE TABLE IF NOT EXISTS spu_perfiles (
        id_perfil INT AUTO_INCREMENT PRIMARY KEY,
        id_flujo INT NOT NULL,
        slug VARCHAR(80) NOT NULL UNIQUE,
        nombre VARCHAR(150) NOT NULL,
        descripcion TEXT NULL,
        icon VARCHAR(80) NULL,
        color VARCHAR(20) NULL,
        orden INT NOT NULL DEFAULT 0,
        activo TINYINT(1) NOT NULL DEFAULT 1,
        creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        actualizado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        CONSTRAINT fk_spu_perfiles_flujo FOREIGN KEY (id_flujo) REFERENCES spu_flujos(id_flujo) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $sql[] = "CREATE TABLE IF NOT EXISTS spu_tareas (
        id_tarea INT AUTO_INCREMENT PRIMARY KEY,
        id_perfil INT NOT NULL,
        slug VARCHAR(80) NOT NULL UNIQUE,
        titulo VARCHAR(180) NOT NULL,
        descripcion TEXT NULL,
        evidencia ENUM('none','optional','required') NOT NULL DEFAULT 'none',
        alerta_tipo ENUM('none','citas') NOT NULL DEFAULT 'none',
        alerta_cantidad INT NOT NULL DEFAULT 0,
        tipos_permitidos TEXT NULL,
        orden INT NOT NULL DEFAULT 0,
        activo TINYINT(1) NOT NULL DEFAULT 1,
        creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        actualizado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        CONSTRAINT fk_spu_tareas_perfil FOREIGN KEY (id_perfil) REFERENCES spu_perfiles(id_perfil) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $sql[] = "CREATE TABLE IF NOT EXISTS spu_paciente_flujos (
        id_paciente_flujo INT AUTO_INCREMENT PRIMARY KEY,
        id_nino INT NOT NULL,
        id_flujo INT NOT NULL,
        activo TINYINT(1) NOT NULL DEFAULT 1,
        actualizado_por INT NULL,
        creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        actualizado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY uk_spu_paciente_flujo (id_nino, id_flujo),
        CONSTRAINT fk_spu_paciente_flujos_flujo FOREIGN KEY (id_flujo) REFERENCES spu_flujos(id_flujo) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $sql[] = "CREATE TABLE IF NOT EXISTS spu_paciente_tareas (
        id_paciente_tarea INT AUTO_INCREMENT PRIMARY KEY,
        id_nino INT NOT NULL,
        id_tarea INT NOT NULL,
        status ENUM('no_iniciado','en_proceso','completado') NOT NULL DEFAULT 'no_iniciado',
        actualizado_por INT NULL,
        completado_en DATETIME NULL,
        creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        actualizado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY uk_spu_paciente_tarea (id_nino, id_tarea),
        CONSTRAINT fk_spu_paciente_tareas_tarea FOREIGN KEY (id_tarea) REFERENCES spu_tareas(id_tarea) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    foreach ($sql as $stmt) {
        $conn->query($stmt);
    }

    pendientes_ensure_column($conn, 'spu_tareas', 'alerta_tipo', "`alerta_tipo` ENUM('none','citas') NOT NULL DEFAULT 'none' AFTER `evidencia`");
    pendientes_ensure_column($conn, 'spu_tareas', 'alerta_cantidad', "`alerta_cantidad` INT NOT NULL DEFAULT 0 AFTER `alerta_tipo`");

    $needsSeed = false;
    $res = $conn->query("SELECT COUNT(*) AS total FROM spu_flujos");
    if ($res) {
        $row = $res->fetch_assoc();
        $needsSeed = ((int)($row['total'] ?? 0) === 0);
    }

    $initialized = true;

    if ($needsSeed) {
        pendientes_save_catalog($conn, pendientes_default_catalog());
    }
}

function pendientes_save_catalog(mysqli $conn, array $catalog): bool
{
    pendientes_ensure_schema($conn);
    if (!isset($catalog['flows']) || !is_array($catalog['flows'])) {
        return false;
    }

    $conn->begin_transaction();
    try {
        $conn->query('DELETE FROM spu_paciente_tareas');
        $conn->query('DELETE FROM spu_paciente_flujos');
        $conn->query('DELETE FROM spu_tareas');
        $conn->query('DELETE FROM spu_perfiles');
        $conn->query('DELETE FROM spu_flujos');

        foreach ($catalog['flows'] as $flow) {
            $flowSlug = trim((string)($flow['id'] ?? ''));
            if ($flowSlug === '') {
                continue;
            }
            $stmtFlow = $conn->prepare('INSERT INTO spu_flujos (slug, nombre, descripcion, icon, color, orden, activo) VALUES (?, ?, ?, ?, ?, ?, 1)');
            $flowName = trim((string)($flow['nombre'] ?? $flowSlug));
            $flowDesc = trim((string)($flow['descripcion'] ?? ''));
            $flowIcon = trim((string)($flow['icon'] ?? 'ni-activity-round'));
            $flowColor = trim((string)($flow['color'] ?? '#6D5BFF'));
            $flowOrder = (int)($flow['orden'] ?? 0);
            $stmtFlow->bind_param('sssssi', $flowSlug, $flowName, $flowDesc, $flowIcon, $flowColor, $flowOrder);
            if (!$stmtFlow->execute()) {
                throw new RuntimeException('No se pudo guardar flujo');
            }
            $flowDbId = (int)$conn->insert_id;
            $stmtFlow->close();

            $profiles = isset($flow['perfiles']) && is_array($flow['perfiles']) ? $flow['perfiles'] : [];
            foreach ($profiles as $profile) {
                $profileSlug = trim((string)($profile['id'] ?? ''));
                if ($profileSlug === '') {
                    continue;
                }
                $stmtProfile = $conn->prepare('INSERT INTO spu_perfiles (id_flujo, slug, nombre, descripcion, icon, color, orden, activo) VALUES (?, ?, ?, ?, ?, ?, ?, 1)');
                $profileName = trim((string)($profile['nombre'] ?? $profileSlug));
                $profileDesc = trim((string)($profile['descripcion'] ?? ''));
                $profileIcon = trim((string)($profile['icon'] ?? 'ni-clipboad-check'));
                $profileColor = trim((string)($profile['color'] ?? $flowColor));
                $profileOrder = (int)($profile['orden'] ?? 0);
                $stmtProfile->bind_param('isssssi', $flowDbId, $profileSlug, $profileName, $profileDesc, $profileIcon, $profileColor, $profileOrder);
                if (!$stmtProfile->execute()) {
                    throw new RuntimeException('No se pudo guardar perfil');
                }
                $profileDbId = (int)$conn->insert_id;
                $stmtProfile->close();

                $tasks = isset($profile['tareas']) && is_array($profile['tareas']) ? $profile['tareas'] : [];
                foreach ($tasks as $task) {
                    $taskSlug = trim((string)($task['id'] ?? ''));
                    if ($taskSlug === '') {
                        continue;
                    }
                    $stmtTask = $conn->prepare('INSERT INTO spu_tareas (id_perfil, slug, titulo, descripcion, evidencia, alerta_tipo, alerta_cantidad, tipos_permitidos, orden, activo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)');
                    $taskTitle = trim((string)($task['titulo'] ?? $taskSlug));
                    $taskDesc = trim((string)($task['descripcion'] ?? ''));
                    $taskEvidence = strtolower(trim((string)($task['evidencia'] ?? 'none')));
                    if (!in_array($taskEvidence, ['none', 'optional', 'required'], true)) {
                        $taskEvidence = 'none';
                    }
                    $alertType = strtolower(trim((string)($task['alerta_tipo'] ?? 'none')));
                    if (!in_array($alertType, ['none', 'citas'], true)) {
                        $alertType = 'none';
                    }
                    $alertCount = max(0, (int)($task['alerta_cantidad'] ?? 0));
                    $taskTypes = isset($task['tipos']) && is_array($task['tipos']) ? pendientes_array_json($task['tipos']) : '[]';
                    $taskOrder = (int)($task['orden'] ?? 0);
                    $stmtTask->bind_param('isssssisi', $profileDbId, $taskSlug, $taskTitle, $taskDesc, $taskEvidence, $alertType, $alertCount, $taskTypes, $taskOrder);
                    if (!$stmtTask->execute()) {
                        throw new RuntimeException('No se pudo guardar tarea');
                    }
                    $stmtTask->close();
                }
            }
        }

        $conn->commit();
        return true;
    } catch (Throwable $e) {
        $conn->rollback();
        return false;
    }
}

function pendientes_load_catalog(mysqli $conn): array
{
    pendientes_ensure_schema($conn);

    $catalog = [
        'version' => 1,
        'flows' => [],
    ];

    $flowsById = [];
    $resFlows = $conn->query("SELECT * FROM spu_flujos WHERE activo = 1 ORDER BY orden ASC, nombre ASC");
    if ($resFlows) {
        while ($row = $resFlows->fetch_assoc()) {
            $idFlujo = (int)$row['id_flujo'];
            $flow = [
                'db_id' => $idFlujo,
                'id' => (string)$row['slug'],
                'nombre' => (string)$row['nombre'],
                'descripcion' => (string)($row['descripcion'] ?? ''),
                'icon' => (string)($row['icon'] ?? 'ni-activity-round'),
                'color' => (string)($row['color'] ?? '#6D5BFF'),
                'orden' => (int)$row['orden'],
                'perfiles' => [],
            ];
            $catalog['flows'][] = $flow;
            $flowsById[$idFlujo] = count($catalog['flows']) - 1;
        }
    }

    $profilesById = [];
    $resProfiles = $conn->query("SELECT * FROM spu_perfiles WHERE activo = 1 ORDER BY orden ASC, nombre ASC");
    if ($resProfiles) {
        while ($row = $resProfiles->fetch_assoc()) {
            $idFlujo = (int)$row['id_flujo'];
            if (!isset($flowsById[$idFlujo])) {
                continue;
            }
            $idPerfil = (int)$row['id_perfil'];
            $profile = [
                'db_id' => $idPerfil,
                'id' => (string)$row['slug'],
                'nombre' => (string)$row['nombre'],
                'descripcion' => (string)($row['descripcion'] ?? ''),
                'icon' => (string)($row['icon'] ?? 'ni-clipboad-check'),
                'color' => (string)($row['color'] ?? '#94A3B8'),
                'orden' => (int)$row['orden'],
                'tareas' => [],
            ];
            $catalog['flows'][$flowsById[$idFlujo]]['perfiles'][] = $profile;
            $profilesById[$idPerfil] = [
                'flow_index' => $flowsById[$idFlujo],
                'profile_index' => count($catalog['flows'][$flowsById[$idFlujo]]['perfiles']) - 1,
            ];
        }
    }

    $resTasks = $conn->query("SELECT * FROM spu_tareas WHERE activo = 1 ORDER BY orden ASC, titulo ASC");
    if ($resTasks) {
        while ($row = $resTasks->fetch_assoc()) {
            $idPerfil = (int)$row['id_perfil'];
            if (!isset($profilesById[$idPerfil])) {
                continue;
            }
            $pos = $profilesById[$idPerfil];
            $tipos = json_decode((string)($row['tipos_permitidos'] ?? '[]'), true);
            if (!is_array($tipos)) {
                $tipos = [];
            }
            $catalog['flows'][$pos['flow_index']]['perfiles'][$pos['profile_index']]['tareas'][] = [
                'db_id' => (int)$row['id_tarea'],
                'id' => (string)$row['slug'],
                'titulo' => (string)$row['titulo'],
                'descripcion' => (string)($row['descripcion'] ?? ''),
                'evidencia' => (string)$row['evidencia'],
                'alerta_tipo' => (string)($row['alerta_tipo'] ?? 'none'),
                'alerta_cantidad' => (int)($row['alerta_cantidad'] ?? 0),
                'tipos' => $tipos,
                'orden' => (int)$row['orden'],
            ];
        }
    }

    return $catalog;
}

function pendientes_find_flow_id(mysqli $conn, string $slug): int
{
    pendientes_ensure_schema($conn);
    $slug = trim($slug);
    if ($slug === '') {
        return 0;
    }
    $stmt = $conn->prepare('SELECT id_flujo FROM spu_flujos WHERE slug = ? LIMIT 1');
    $stmt->bind_param('s', $slug);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res ? $res->fetch_assoc() : null;
    $stmt->close();
    return (int)($row['id_flujo'] ?? 0);
}

function pendientes_find_task(mysqli $conn, string $slug): ?array
{
    pendientes_ensure_schema($conn);
    $slug = trim($slug);
    if ($slug === '') {
        return null;
    }
    $stmt = $conn->prepare('SELECT * FROM spu_tareas WHERE slug = ? LIMIT 1');
    $stmt->bind_param('s', $slug);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res ? $res->fetch_assoc() : null;
    $stmt->close();
    return is_array($row) ? $row : null;
}

function pendientes_load_patient_status(mysqli $conn, int $id_nino): array
{
    pendientes_ensure_schema($conn);

    $status = [
        'flows' => [],
        'tasks' => [],
        'updated_at' => null,
    ];

    $stmtFlows = $conn->prepare('SELECT f.slug, pf.actualizado_en FROM spu_paciente_flujos pf INNER JOIN spu_flujos f ON f.id_flujo = pf.id_flujo WHERE pf.id_nino = ? AND pf.activo = 1 ORDER BY f.orden ASC');
    $stmtFlows->bind_param('i', $id_nino);
    $stmtFlows->execute();
    $resFlows = $stmtFlows->get_result();
    while ($resFlows && ($row = $resFlows->fetch_assoc())) {
        $status['flows'][] = (string)$row['slug'];
        $status['updated_at'] = (string)($row['actualizado_en'] ?? $status['updated_at']);
    }
    $stmtFlows->close();

    if (empty($status['flows'])) {
        $defaultFlowId = pendientes_find_flow_id($conn, 'diagnostico');
        if ($defaultFlowId > 0) {
            $userId = (int)($_SESSION['id'] ?? 0);
            $stmtInsert = $conn->prepare('INSERT INTO spu_paciente_flujos (id_nino, id_flujo, activo, actualizado_por) VALUES (?, ?, 1, ?) ON DUPLICATE KEY UPDATE activo = VALUES(activo), actualizado_por = VALUES(actualizado_por), actualizado_en = CURRENT_TIMESTAMP');
            $stmtInsert->bind_param('iii', $id_nino, $defaultFlowId, $userId);
            $stmtInsert->execute();
            $stmtInsert->close();
            $status['flows'][] = 'diagnostico';
        }
    }

    $stmtTasks = $conn->prepare('SELECT t.slug, pt.status, pt.actualizado_en, pt.completado_en, pt.actualizado_por FROM spu_paciente_tareas pt INNER JOIN spu_tareas t ON t.id_tarea = pt.id_tarea WHERE pt.id_nino = ?');
    $stmtTasks->bind_param('i', $id_nino);
    $stmtTasks->execute();
    $resTasks = $stmtTasks->get_result();
    while ($resTasks && ($row = $resTasks->fetch_assoc())) {
        $status['tasks'][(string)$row['slug']] = [
            'status' => (string)$row['status'],
            'updated_at' => (string)($row['actualizado_en'] ?? ''),
            'completed_at' => (string)($row['completado_en'] ?? ''),
            'updated_by' => (int)($row['actualizado_por'] ?? 0),
        ];
        $status['updated_at'] = (string)($row['actualizado_en'] ?? $status['updated_at']);
    }
    $stmtTasks->close();

    return $status;
}

function pendientes_save_patient_flows(mysqli $conn, int $id_nino, array $flowSlugs, int $updatedBy): bool
{
    pendientes_ensure_schema($conn);
    $flowSlugs = array_values(array_filter(array_map('trim', $flowSlugs), fn($v) => $v !== ''));
    if (empty($flowSlugs)) {
        $flowSlugs = ['diagnostico'];
    }

    $flowIds = [];
    foreach ($flowSlugs as $slug) {
        $flowId = pendientes_find_flow_id($conn, $slug);
        if ($flowId > 0) {
            $flowIds[$flowId] = true;
        }
    }
    if (empty($flowIds)) {
        return false;
    }

    $conn->begin_transaction();
    try {
        $stmtDelete = $conn->prepare('DELETE FROM spu_paciente_flujos WHERE id_nino = ?');
        $stmtDelete->bind_param('i', $id_nino);
        $stmtDelete->execute();
        $stmtDelete->close();

        $stmtInsert = $conn->prepare('INSERT INTO spu_paciente_flujos (id_nino, id_flujo, activo, actualizado_por) VALUES (?, ?, 1, ?)');
        foreach (array_keys($flowIds) as $flowId) {
            $stmtInsert->bind_param('iii', $id_nino, $flowId, $updatedBy);
            $stmtInsert->execute();
        }
        $stmtInsert->close();

        $conn->commit();
        return true;
    } catch (Throwable $e) {
        $conn->rollback();
        return false;
    }
}

function pendientes_save_patient_task_status(mysqli $conn, int $id_nino, string $taskSlug, string $status, int $updatedBy): bool
{
    pendientes_ensure_schema($conn);
    $task = pendientes_find_task($conn, $taskSlug);
    if (!$task) {
        return false;
    }
    $normalized = pendientes_normalize_status($status);
    $idTarea = (int)$task['id_tarea'];
    $completedAt = $normalized === 'completado' ? date('Y-m-d H:i:s') : null;
    $stmt = $conn->prepare('INSERT INTO spu_paciente_tareas (id_nino, id_tarea, status, actualizado_por, completado_en) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE status = VALUES(status), actualizado_por = VALUES(actualizado_por), completado_en = VALUES(completado_en), actualizado_en = CURRENT_TIMESTAMP');
    $stmt->bind_param('iisis', $id_nino, $idTarea, $normalized, $updatedBy, $completedAt);
    $ok = $stmt->execute();
    $stmt->close();
    return (bool)$ok;
}

function pendientes_active_flows(array $catalog, array $patientStatus): array
{
    $active = [];
    $ids = isset($patientStatus['flows']) && is_array($patientStatus['flows']) ? $patientStatus['flows'] : [];
    $ids = array_values(array_filter(array_map('strval', $ids), fn($v) => $v !== ''));
    if (empty($ids)) {
        $ids = ['diagnostico'];
    }

    foreach ($catalog['flows'] as $flow) {
        $fid = (string)($flow['id'] ?? '');
        if ($fid !== '' && in_array($fid, $ids, true)) {
            $active[] = $flow;
        }
    }
    usort($active, fn($a, $b) => ((int)($a['orden'] ?? 0)) <=> ((int)($b['orden'] ?? 0)));
    return $active;
}

function pendientes_profile_progress(int $id_nino, array $profile, array $patientStatus): array
{
    $tasks = isset($profile['tareas']) && is_array($profile['tareas']) ? $profile['tareas'] : [];
    usort($tasks, fn($a, $b) => ((int)($a['orden'] ?? 0)) <=> ((int)($b['orden'] ?? 0)));
    $total = 0;
    $completed = 0;
    $anyStarted = false;
    $alerts = 0;
    $appointmentsCount = null;

    foreach ($tasks as $t) {
        $taskId = (string)($t['id'] ?? '');
        if ($taskId === '') {
            continue;
        }
        $total++;
        $st = $patientStatus['tasks'][$taskId]['status'] ?? 'no_iniciado';
        $st = pendientes_normalize_status((string)$st);
        if ($st === 'completado') {
            $completed++;
            $anyStarted = true;
            continue;
        }
        if ($st === 'en_proceso' || pendientes_task_has_files($id_nino, $taskId)) {
            $anyStarted = true;
        }
        $alertType = strtolower((string)($t['alerta_tipo'] ?? 'none'));
        $alertCount = (int)($t['alerta_cantidad'] ?? 0);
        if ($alertType === 'citas' && $alertCount > 0 && $st !== 'completado') {
            if ($appointmentsCount === null) {
                $appointmentsCount = pendientes_patient_appointments_count(null, $id_nino);
            }
            if ($appointmentsCount >= $alertCount) {
                $alerts++;
            }
        }
    }

    $pct = $total > 0 ? (int)round(($completed / $total) * 100) : 0;
    $status = 'no_iniciado';
    if ($total > 0 && $completed === $total) {
        $status = 'completado';
    } elseif ($anyStarted) {
        $status = 'en_proceso';
    }

    return [
        'total' => $total,
        'completed' => $completed,
        'pct' => $pct,
        'status' => $status,
        'alerts' => $alerts,
    ];
}

function pendientes_overall_progress(int $id_nino, array $flows, array $patientStatus): array
{
    $total = 0;
    $completed = 0;
    $alerts = 0;
    foreach ($flows as $flow) {
        foreach (($flow['perfiles'] ?? []) as $profile) {
            $profileProgress = pendientes_profile_progress($id_nino, $profile, $patientStatus);
            $alerts += (int)($profileProgress['alerts'] ?? 0);
            foreach (($profile['tareas'] ?? []) as $task) {
                $taskId = (string)($task['id'] ?? '');
                if ($taskId === '') {
                    continue;
                }
                $total++;
                $st = $patientStatus['tasks'][$taskId]['status'] ?? 'no_iniciado';
                if (pendientes_normalize_status((string)$st) === 'completado') {
                    $completed++;
                }
            }
        }
    }
    $pct = $total > 0 ? (int)round(($completed / $total) * 100) : 0;
    return ['total' => $total, 'completed' => $completed, 'pct' => $pct, 'alerts' => $alerts];
}

function pendientes_patient_appointments_count(?mysqli $conn, int $id_nino): int
{
    static $cache = [];
    if (isset($cache[$id_nino])) {
        return $cache[$id_nino];
    }
    if (!$conn) {
        require_once __DIR__ . '/../database/conexion.php';
        $db = new Database();
        $conn = $db->getConnection();
    }
    $count = 0;
    $stmt = $conn->prepare('SELECT COUNT(*) AS total FROM Cita WHERE IdNino = ?');
    if ($stmt) {
        $stmt->bind_param('i', $id_nino);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : [];
        $count = (int)($row['total'] ?? 0);
        $stmt->close();
    }
    $cache[$id_nino] = $count;
    return $count;
}

function pendientes_task_alert_state(mysqli $conn, int $id_nino, array $task, array $patientStatus): array
{
    $taskId = (string)($task['id'] ?? '');
    $status = pendientes_normalize_status((string)($patientStatus['tasks'][$taskId]['status'] ?? 'no_iniciado'));
    $alertType = strtolower((string)($task['alerta_tipo'] ?? 'none'));
    $alertCount = (int)($task['alerta_cantidad'] ?? 0);
    $appointments = pendientes_patient_appointments_count($conn, $id_nino);
    $active = false;
    $message = '';
    if ($alertType === 'citas' && $alertCount > 0 && $status !== 'completado' && $appointments >= $alertCount) {
        $active = true;
        $message = 'Pendiente después de ' . $alertCount . ' citas';
    }
    return [
        'active' => $active,
        'message' => $message,
        'appointments' => $appointments,
        'threshold' => $alertCount,
    ];
}
