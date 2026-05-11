<?php
include_once '../includes/head.php';
date_default_timezone_set('America/Mexico_City');

require_once '../database/conexion.php';
require_once __DIR__ . '/../includes/pendientes_lib.php';

$db = new Database();
$conn = $db->getConnection();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$paciente = [];
if ($id > 0) {
    // Note: DB uses both Id and id in this codebase; keep this query aligned with paciente.php.
    $stmt = $conn->prepare('SELECT Id, name, edad, Observacion FROM nino WHERE Id = ? LIMIT 1');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $paciente = $res ? $res->fetch_assoc() : [];
}

$catalog = pendientes_load_catalog($conn);
$patientStatus = pendientes_load_patient_status($conn, $id);
$activeFlows = pendientes_active_flows($catalog, $patientStatus);
$overall = pendientes_overall_progress($id, $activeFlows, $patientStatus);

$allFlows = isset($catalog['flows']) && is_array($catalog['flows']) ? $catalog['flows'] : [];
usort($allFlows, fn($a, $b) => ((int)($a['orden'] ?? 0)) <=> ((int)($b['orden'] ?? 0)));

function demo_profile_card(array $profile, array $progress): string
{
    $name = htmlspecialchars((string)($profile['nombre'] ?? ''));
    $desc = htmlspecialchars((string)($profile['descripcion'] ?? ''));
    $icon = trim((string)($profile['icon'] ?? 'ni-clipboad-check'));
    $color = trim((string)($profile['color'] ?? '#3B82F6'));
    $pct = (int)($progress['pct'] ?? 0);
    $status = (string)($progress['status'] ?? 'no_iniciado');
    $label = pendientes_status_label($status);
    $pill = pendientes_status_badge_class($status);
    $completed = (int)($progress['completed'] ?? 0);
    $total = (int)($progress['total'] ?? 0);
    $alerts = (int)($progress['alerts'] ?? 0);

    $btnText = 'Comenzar';
    if ($status === 'en_proceso') $btnText = 'Continuar';
    if ($status === 'completado') $btnText = 'Ver / Editar';

    $html = '';
    $html .= '<div class="demo-profile-card card card-bordered" style="--profile-color:' . htmlspecialchars($color) . ';">';
    $html .= '<div class="card-inner">';
    $html .= '<div class="demo-profile-title">' . $name . '</div>';
    $html .= '<div class="demo-profile-sub text-soft">' . $desc . '</div>';
    $html .= '<div class="demo-ring" style="--pct:' . $pct . '; --ring:' . htmlspecialchars($color) . ';">';
    $html .= '<div class="demo-ring-inner">';
    $html .= '<em class="icon ni ' . htmlspecialchars($icon) . '"></em>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '<div class="mt-2 d-flex align-items-center justify-content-between gap-2 flex-wrap">';
    $html .= '<span class="badge ' . $pill . '">' . htmlspecialchars($label) . '</span>';
    $html .= '<span class="small text-soft">' . $completed . ' de ' . $total . ' tareas</span>';
    $html .= '</div>';
    if ($alerts > 0) {
        $html .= '<div class="mt-2"><span class="badge bg-danger">' . $alerts . ' alerta' . ($alerts === 1 ? '' : 's') . '</span></div>';
    }
    $html .= '<div class="mt-3"><a class="btn btn-outline-primary w-100" href="#profile-' . htmlspecialchars((string)($profile['id'] ?? '')) . '">' . htmlspecialchars($btnText) . '</a></div>';
    $html .= '</div>';
    $html .= '</div>';
    return $html;
}

?>

<div class="nk-wrap ">
    <?php include_once '../includes/menu_superior.php'; ?>
    <div class="nk-content nk-content-fluid">
        <div class="container-xl wide-xl">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Paciente <span class="badge bg-outline-info">DEMO</span></h3>
                            <div class="nk-block-des text-soft">
                                <p><?php echo htmlspecialchars((string)($paciente['name'] ?? '')); ?> (ID: <?php echo htmlspecialchars((string)($paciente['Id'] ?? $id)); ?>)</p>
                            </div>
                        </div>
                        <div class="nk-block-head-content">
                            <a class="btn btn-outline-secondary" href="/pacientes/demopacientes.php">Volver</a>
                        </div>
                    </div>
                </div>

                <style>
                    .demo-progress-wrap { display:flex; gap:12px; align-items:stretch; flex-wrap:wrap; }
                    .demo-progress-card { flex: 1 1 320px; }
                    .demo-progress-main { border-top: 4px solid #6D5BFF; }
                    .demo-flow-card { border-top: 4px solid #4F46E5; }
                    @media (max-width: 1200px) { .demo-profile-grid { grid-template-columns: repeat(2, minmax(220px, 1fr)); } }
                    @media (max-width: 576px) { .demo-profile-grid { grid-template-columns: 1fr; } }
                    .demo-flow-dot { width: 12px; height: 12px; border-radius: 999px; background: var(--flow-color); display:inline-block; margin-right: 8px; box-shadow: 0 0 0 4px color-mix(in srgb, var(--flow-color) 18%, white); }
                    .demo-flow-title { font-weight: 700; color: var(--flow-color); }
                    .demo-profile-title { font-weight: 600; line-height: 1.2; }
                    .demo-profile-sub { font-size: 0.9rem; min-height: 2.4em; }
                    .demo-profile-card { border-top: 4px solid var(--profile-color, #CBD5E1); box-shadow: 0 10px 24px rgba(15, 23, 42, 0.05); }
                    .demo-profile-grid { display:grid; grid-template-columns: repeat(4, minmax(220px, 1fr)); gap: 12px; }
                    .demo-ring {
                        width: 140px; height: 140px; border-radius: 999px;
                        margin: 12px auto 0;
                        background: conic-gradient(var(--ring) calc(var(--pct) * 1%), rgba(156, 163, 175, 0.25) 0);
                        display:flex; align-items:center; justify-content:center;
                        position: relative;
                    }
                    .demo-ring-inner {
                        width: 112px; height: 112px; border-radius: 999px;
                        background: #fff;
                        display:flex; align-items:center; justify-content:center;
                        box-shadow: 0 1px 2px rgba(0,0,0,0.06);
                    }
                    .demo-ring-inner .icon { font-size: 44px; opacity: 0.9; }
                    html.dark .demo-ring-inner { background: #1f2937; }
                    .demo-flow-chip { border: 1px solid color-mix(in srgb, var(--flow-color) 30%, white); background: color-mix(in srgb, var(--flow-color) 8%, white); border-radius: 10px; padding: 10px 12px; min-height: 58px; }
                    .demo-flow-chip label { font-weight: 600; }
                    .demo-accordion-button:not(.collapsed) { background: color-mix(in srgb, var(--profile-color) 10%, white); color: #0f172a; }
                    .demo-summary-badge { border: 1px solid rgba(109, 91, 255, 0.18); background: rgba(109, 91, 255, 0.08); color: #5b47ff; border-radius: 999px; padding: 4px 10px; font-size: 12px; font-weight: 600; }
                </style>

                <div class="nk-block">
                    <div class="demo-progress-wrap">
                        <div class="card card-bordered demo-progress-card demo-progress-main">
                            <div class="card-inner">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="fw-semibold">Progreso general</div>
                                        <div class="text-soft small"><?php echo htmlspecialchars((string)$overall['completed']); ?> de <?php echo htmlspecialchars((string)$overall['total']); ?> completados</div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-semibold"><?php echo htmlspecialchars((string)$overall['pct']); ?>%</div>
                                    </div>
                                </div>
                                <div class="mt-3 d-flex flex-wrap gap-2">
                                    <span class="demo-summary-badge"><?php echo count($activeFlows); ?> flujo<?php echo count($activeFlows) === 1 ? '' : 's'; ?> activo<?php echo count($activeFlows) === 1 ? '' : 's'; ?></span>
                                    <span class="demo-summary-badge"><?php echo pendientes_patient_appointments_count($conn, $id); ?> citas registradas</span>
                                    <?php if (!empty($overall['alerts'])): ?>
                                        <span class="demo-summary-badge" style="background: rgba(226, 29, 72, 0.08); border-color: rgba(226, 29, 72, 0.18); color: #be123c;"><?php echo (int)$overall['alerts']; ?> alerta<?php echo (int)$overall['alerts'] === 1 ? '' : 's'; ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="progress mt-3" style="height: 12px; background: rgba(109, 91, 255, 0.12);">
                                    <div class="progress-bar" role="progressbar" style="width: <?php echo (int)$overall['pct']; ?>%; background: linear-gradient(90deg, #6D5BFF 0%, #8B7CFF 100%);"></div>
                                </div>
                            </div>
                        </div>
                        <div class="card card-bordered demo-progress-card demo-flow-card">
                            <div class="card-inner">
                                <div class="d-flex align-items-start gap-2">
                                    <em class="icon ni ni-info" style="font-size: 18px;"></em>
                                    <div>
                                        <div class="fw-semibold">Flujo activo</div>
                                        <div class="text-soft small">Asigna uno o varios flujos. Cada flujo agrega perfiles y tareas al seguimiento del paciente.</div>
                                    </div>
                                </div>

                                <form method="POST" action="/pacientes/pendientes_set_flows.php" class="mt-2">
                                    <input type="hidden" name="id_nino" value="<?php echo (int)$id; ?>">
                                    <input type="hidden" name="redirect" value="<?php echo htmlspecialchars('/pacientes/demopaciente.php?id=' . $id); ?>">
                                    <div class="row g-2 mt-1">
                                        <?php
                                        $selected = isset($patientStatus['flows']) && is_array($patientStatus['flows']) ? $patientStatus['flows'] : ['diagnostico'];
                                        foreach ($allFlows as $f):
                                            $fid = (string)($f['id'] ?? '');
                                            if ($fid === '') continue;
                                            $checked = in_array($fid, $selected, true) ? 'checked' : '';
                                            $flowColor = htmlspecialchars((string)($f['color'] ?? '#6D5BFF'));
                                        ?>
                                            <div class="col-12 col-md-6">
                                                <div class="demo-flow-chip" style="--flow-color: <?php echo $flowColor; ?>;">
                                                    <input type="checkbox" class="custom-control-input" id="flow-<?php echo htmlspecialchars($fid); ?>" name="flows[]" value="<?php echo htmlspecialchars($fid); ?>" <?php echo $checked; ?>>
                                                    <label class="custom-control-label" for="flow-<?php echo htmlspecialchars($fid); ?>"><?php echo htmlspecialchars((string)($f['nombre'] ?? $fid)); ?></label>
                                                </div>
                                                <div class="small text-soft mt-1 ms-1"><?php echo htmlspecialchars((string)($f['descripcion'] ?? '')); ?></div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-outline-primary" type="submit">Asignar al paciente</button>
                                        <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] != 2): ?>
                                            <a class="btn btn-sm btn-outline-secondary ms-1" href="/pacientes/admin_perfiles.php">Configurar perfiles</a>
                                        <?php endif; ?>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="nk-block">
                    <?php
                    foreach ($activeFlows as $flow) {
                        $profiles = isset($flow['perfiles']) && is_array($flow['perfiles']) ? $flow['perfiles'] : [];
                        usort($profiles, fn($a, $b) => ((int)($a['orden'] ?? 0)) <=> ((int)($b['orden'] ?? 0)));
                    ?>
                        <div class="card card-bordered mb-3" style="border-top: 4px solid <?php echo htmlspecialchars((string)($flow['color'] ?? '#6D5BFF')); ?>;">
                            <div class="card-inner">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                                    <div>
                                        <h6 class="title mb-1">
                                            <span class="demo-flow-dot" style="--flow-color: <?php echo htmlspecialchars((string)($flow['color'] ?? '#6D5BFF')); ?>;"></span>
                                            <span class="demo-flow-title" style="--flow-color: <?php echo htmlspecialchars((string)($flow['color'] ?? '#6D5BFF')); ?>;"><?php echo htmlspecialchars((string)($flow['nombre'] ?? 'Flujo')); ?></span>
                                        </h6>
                                        <p class="text-soft mb-0"><?php echo htmlspecialchars((string)($flow['descripcion'] ?? '')); ?></p>
                                    </div>
                                </div>
                                <div class="demo-profile-grid">
                                    <?php foreach ($profiles as $p) {
                                        $prog = pendientes_profile_progress($id, $p, $patientStatus);
                                        echo demo_profile_card($p, $prog);
                                    } ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>

                <div class="nk-block">
                    <div class="card card-bordered">
                        <div class="card-inner">
                            <h5 class="title">Pendientes (tareas + evidencias)</h5>
                            <div class="accordion" id="accPendientes">
                                <?php
                                $accIdx = 0;
                                foreach ($activeFlows as $flow) {
                                    $profiles = isset($flow['perfiles']) && is_array($flow['perfiles']) ? $flow['perfiles'] : [];
                                    usort($profiles, fn($a, $b) => ((int)($a['orden'] ?? 0)) <=> ((int)($b['orden'] ?? 0)));
                                    foreach ($profiles as $p) {
                                        $pid = (string)($p['id'] ?? ('p' . $accIdx));
                                        $collapseId = 'collapse-' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $pid);
                                        $headId = 'head-' . $collapseId;
                                        $progress = pendientes_profile_progress($id, $p, $patientStatus);
                                        $dot = pendientes_status_dot_class($progress['status']);
                                        $profileColor = htmlspecialchars((string)($p['color'] ?? '#94A3B8'));
                                ?>
                                        <div class="accordion-item" id="profile-<?php echo htmlspecialchars($pid); ?>">
                                            <h2 class="accordion-header" id="<?php echo htmlspecialchars($headId); ?>">
                                                <button class="accordion-button <?php echo $accIdx === 0 ? '' : 'collapsed'; ?> demo-accordion-button" style="--profile-color: <?php echo $profileColor; ?>;" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo htmlspecialchars($collapseId); ?>" aria-expanded="<?php echo $accIdx === 0 ? 'true' : 'false'; ?>">
                                                    <span class="status dot dot-lg <?php echo $dot; ?> me-2"></span>
                                                    <?php echo htmlspecialchars((string)($p['nombre'] ?? 'Perfil')); ?>
                                                    <span class="ms-2 badge <?php echo pendientes_status_badge_class($progress['status']); ?>"><?php echo htmlspecialchars(pendientes_status_label($progress['status'])); ?></span>
                                                    <span class="ms-auto text-soft small"><?php echo (int)$progress['completed']; ?> de <?php echo (int)$progress['total']; ?> tareas</span>
                                                </button>
                                            </h2>
                                            <div id="<?php echo htmlspecialchars($collapseId); ?>" class="accordion-collapse collapse <?php echo $accIdx === 0 ? 'show' : ''; ?>" data-bs-parent="#accPendientes">
                                                <div class="accordion-body">
                                                    <?php
                                                    $tasks = isset($p['tareas']) && is_array($p['tareas']) ? $p['tareas'] : [];
                                                    usort($tasks, fn($a, $b) => ((int)($a['orden'] ?? 0)) <=> ((int)($b['orden'] ?? 0)));
                                                    if (empty($tasks)) {
                                                        echo '<p class="text-soft mb-0">Sin tareas.</p>';
                                                    }
                                                    foreach ($tasks as $t) {
                                                        $taskId = (string)($t['id'] ?? '');
                                                        if ($taskId === '') continue;
                                                        $taskTitle = (string)($t['titulo'] ?? $taskId);
                                                        $taskEvidence = strtolower((string)($t['evidencia'] ?? 'none'));
                                                        $saved = $patientStatus['tasks'][$taskId]['status'] ?? 'no_iniciado';
                                                        $saved = pendientes_normalize_status((string)$saved);
                                                        if ($saved === 'no_iniciado' && pendientes_task_has_files($id, $taskId)) {
                                                            $saved = 'en_proceso';
                                                        }
                                                        $files = pendientes_list_task_files($id, $taskId);
                                                        $alert = pendientes_task_alert_state($conn, $id, $t, $patientStatus);
                                                    ?>
                                                        <div class="card card-bordered mb-2" style="border-left: 4px solid <?php echo $profileColor; ?>;">
                                                            <div class="card-inner">
                                                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                                                    <div>
                                                                        <div class="fw-semibold"><?php echo htmlspecialchars($taskTitle); ?></div>
                                                                        <div class="small text-soft">
                                                                            <span class="badge <?php echo pendientes_status_badge_class($saved); ?>"><?php echo htmlspecialchars(pendientes_status_label($saved)); ?></span>
                                                                            <?php if ($taskEvidence !== 'none'): ?>
                                                                                <span class="ms-1">Evidencia: <?php echo htmlspecialchars($taskEvidence); ?></span>
                                                                            <?php endif; ?>
                                                                            <?php if (!empty($alert['active'])): ?>
                                                                                <span class="badge bg-danger ms-1"><?php echo htmlspecialchars((string)$alert['message']); ?></span>
                                                                            <?php endif; ?>
                                                                            <?php if (!empty($t['descripcion'])): ?>
                                                                                <span class="d-block mt-1"><?php echo htmlspecialchars((string)$t['descripcion']); ?></span>
                                                                            <?php endif; ?>
                                                                            <?php if (($t['alerta_tipo'] ?? 'none') === 'citas' && (int)($t['alerta_cantidad'] ?? 0) > 0): ?>
                                                                                <span class="d-block mt-1">Alerta si sigue pendiente después de <?php echo (int)$t['alerta_cantidad']; ?> citas.</span>
                                                                            <?php endif; ?>
                                                                        </div>
                                                                    </div>
                                                                    <div class="d-flex flex-wrap gap-1">
                                                                        <form method="POST" action="/pacientes/pendientes_set_status.php">
                                                                            <input type="hidden" name="id_nino" value="<?php echo (int)$id; ?>">
                                                                            <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($taskId); ?>">
                                                                            <input type="hidden" name="status" value="no_iniciado">
                                                                            <input type="hidden" name="redirect" value="<?php echo htmlspecialchars('/pacientes/demopaciente.php?id=' . $id); ?>">
                                                                            <button class="btn btn-sm btn-outline-secondary" type="submit">Reiniciar</button>
                                                                        </form>
                                                                        <form method="POST" action="/pacientes/pendientes_set_status.php">
                                                                            <input type="hidden" name="id_nino" value="<?php echo (int)$id; ?>">
                                                                            <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($taskId); ?>">
                                                                            <input type="hidden" name="status" value="en_proceso">
                                                                            <input type="hidden" name="redirect" value="<?php echo htmlspecialchars('/pacientes/demopaciente.php?id=' . $id); ?>">
                                                                            <button class="btn btn-sm btn-outline-warning" type="submit">En proceso</button>
                                                                        </form>
                                                                        <form method="POST" action="/pacientes/pendientes_set_status.php">
                                                                            <input type="hidden" name="id_nino" value="<?php echo (int)$id; ?>">
                                                                            <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($taskId); ?>">
                                                                            <input type="hidden" name="status" value="completado">
                                                                            <input type="hidden" name="redirect" value="<?php echo htmlspecialchars('/pacientes/demopaciente.php?id=' . $id); ?>">
                                                                            <button class="btn btn-sm btn-outline-success" type="submit">Completar</button>
                                                                        </form>
                                                                    </div>
                                                                </div>

                                                                <?php if ($taskEvidence !== 'none'): ?>
                                                                    <div class="mt-3">
                                                                        <form method="POST" action="/pacientes/pendientes_upload.php" enctype="multipart/form-data" class="row g-2 align-items-end">
                                                                            <input type="hidden" name="id_nino" value="<?php echo (int)$id; ?>">
                                                                            <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($taskId); ?>">
                                                                            <input type="hidden" name="redirect" value="<?php echo htmlspecialchars('/pacientes/demopaciente.php?id=' . $id); ?>">
                                                                            <div class="col-12 col-md-5">
                                                                                <label class="form-label">Archivo</label>
                                                                                <input type="file" class="form-control" name="file" required>
                                                                            </div>
                                                                            <div class="col-12 col-md-5">
                                                                                <label class="form-label">Nota (opcional)</label>
                                                                                <input type="text" class="form-control" name="note" placeholder="Nota">
                                                                            </div>
                                                                            <div class="col-12 col-md-2">
                                                                                <button class="btn btn-primary w-100" type="submit">Subir</button>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                <?php endif; ?>

                                                                <div class="mt-3">
                                                                    <div class="small text-soft mb-1">Evidencias: <?php echo count($files); ?></div>
                                                                    <?php if (!empty($files)): ?>
                                                                        <div class="d-flex flex-column gap-1">
                                                                            <?php
                                                                            foreach ($files as $fn) {
                                                                                $ext = strtolower(pathinfo($fn, PATHINFO_EXTENSION));
                                                                                $isImg = in_array($ext, ['png', 'jpg', 'jpeg', 'gif'], true);
                                                                                $url = '/uploads/pendientes/' . $id . '/evidencias/' . rawurlencode($taskId) . '/' . rawurlencode($fn);
                                                                            ?>
                                                                                <div class="d-flex align-items-center justify-content-between gap-2">
                                                                                    <a href="<?php echo htmlspecialchars($url); ?>" target="_blank" class="link-primary">
                                                                                        <?php echo htmlspecialchars($fn); ?>
                                                                                    </a>
                                                                                    <div class="d-flex gap-1">
                                                                                        <?php if ($isImg): ?>
                                                                                            <a class="btn btn-sm btn-outline-info" href="<?php echo htmlspecialchars($url); ?>" target="_blank">Ver</a>
                                                                                        <?php else: ?>
                                                                                            <a class="btn btn-sm btn-outline-info" href="<?php echo htmlspecialchars($url); ?>" target="_blank">Abrir</a>
                                                                                        <?php endif; ?>
                                                                                        <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] != 2): ?>
                                                                                            <form method="POST" action="/pacientes/pendientes_delete.php">
                                                                                                <input type="hidden" name="id_nino" value="<?php echo (int)$id; ?>">
                                                                                                <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($taskId); ?>">
                                                                                                <input type="hidden" name="filename" value="<?php echo htmlspecialchars($fn); ?>">
                                                                                                <input type="hidden" name="redirect" value="<?php echo htmlspecialchars('/pacientes/demopaciente.php?id=' . $id); ?>">
                                                                                                <button class="btn btn-sm btn-outline-danger" type="submit">Borrar</button>
                                                                                            </form>
                                                                                        <?php endif; ?>
                                                                                    </div>
                                                                                </div>
                                                                            <?php } ?>
                                                                        </div>
                                                                    <?php else: ?>
                                                                        <div class="text-soft small">Sin archivos.</div>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                <?php
                                        $accIdx++;
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $db->closeConnection(); ?>
<?php include_once '../includes/footer.php'; ?>
