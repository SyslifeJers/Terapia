<?php
include_once '../includes/head.php';
date_default_timezone_set('America/Mexico_City');
require_once __DIR__ . '/../api/flujos_lib.php';

$csrfToken = terapia_flujos_csrf_token();
?>

<div class="nk-wrap ">
    <?php include_once '../includes/menu_superior.php'; ?>
    <div class="nk-content nk-content-fluid">
        <div class="container-xl wide-xl">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Mis tareas</h3>
                            <p class="text-soft mb-0">Tareas asignadas desde Flujos de Seguimiento.</p>
                        </div>
                    </div>
                </div>

                <div class="card card-bordered mb-3">
                    <div class="card-inner">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-4"><label class="form-label">Paciente</label><input class="form-control" id="filterPatient" placeholder="Buscar paciente"></div>
                            <div class="col-md-3"><label class="form-label">Fecha limite</label><input class="form-control" id="filterDate" type="date"></div>
                            <div class="col-md-3"><label class="form-label">Estatus</label><select class="form-select" id="filterStatus"><option value="">Pendientes y en proceso</option><option value="pendiente">Pendiente</option><option value="en_proceso">En proceso</option><option value="completada">Completada</option><option value="negada">Negada</option></select></div>
                            <div class="col-md-2"><button class="btn btn-primary w-100" id="applyFilters">Filtrar</button></div>
                        </div>
                    </div>
                </div>

                <div class="card card-bordered">
                    <div class="card-inner table-responsive" id="tasksBox"><p class="text-soft mb-0">Cargando tareas...</p></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function escapeHtml(value) {
    return String(value == null ? '' : value).replace(/[&<>'"]/g, function (c) { return {'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#039;','"':'&quot;'}[c]; });
}
function loadTasks() {
    const params = new URLSearchParams();
    const paciente = document.getElementById('filterPatient').value.trim();
    const fecha = document.getElementById('filterDate').value;
    const estatus = document.getElementById('filterStatus').value;
    if (paciente) params.append('paciente', paciente);
    if (fecha) params.append('fecha', fecha);
    if (estatus) params.append('estatus', estatus);
    fetch('/api/mis_tareas.php?' + params.toString(), { credentials: 'same-origin' })
        .then(r => r.json().then(j => ({ ok: r.ok, data: j })))
        .then(result => {
            if (!result.ok || !result.data.success) throw new Error(result.data.message || 'No se pudo cargar');
            const rows = (result.data.data || []).map(t => {
                const vencida = Number(t.vencida || 0) === 1 ? '<span class="badge bg-danger ms-1">Vencida</span>' : '';
                return '<tr><td><strong>' + escapeHtml(t.nombre) + '</strong><div class="text-soft small">' + escapeHtml(t.flujo_nombre) + '</div></td><td>' + escapeHtml(t.paciente) + '</td><td>' + escapeHtml(t.fecha_limite || 'Sin fecha') + vencida + '</td><td>' + escapeHtml(t.estatus) + '</td><td><a class="btn btn-sm btn-outline-primary" href="/flujos/detalle_tarea.php?id=' + encodeURIComponent(t.id_tarea_asignada) + '">Abrir</a></td></tr>';
            }).join('');
            document.getElementById('tasksBox').innerHTML = '<table class="table table-striped align-middle"><thead><tr><th>Tarea</th><th>Paciente</th><th>Fecha limite</th><th>Estatus</th><th></th></tr></thead><tbody>' + (rows || '<tr><td colspan="5" class="text-center text-soft">Sin tareas.</td></tr>') + '</tbody></table>';
        })
        .catch(e => { document.getElementById('tasksBox').innerHTML = '<div class="alert alert-danger">' + escapeHtml(e.message) + '</div>'; });
}
document.getElementById('applyFilters').addEventListener('click', loadTasks);
loadTasks();
</script>

<?php include_once '../includes/footer.php'; ?>
