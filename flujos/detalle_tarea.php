<?php
include_once '../includes/head.php';
date_default_timezone_set('America/Mexico_City');
require_once __DIR__ . '/../api/flujos_lib.php';

$csrfToken = terapia_flujos_csrf_token();
$taskId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
?>

<div class="nk-wrap ">
    <?php include_once '../includes/menu_superior.php'; ?>
    <div class="nk-content nk-content-fluid">
        <div class="container-xl wide-xl">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content"><h3 class="nk-block-title page-title">Detalle de tarea</h3></div>
                        <div class="nk-block-head-content"><a class="btn btn-outline-secondary" href="/flujos/mis_tareas.php">Volver</a></div>
                    </div>
                </div>
                <div id="detailBox" class="card card-bordered"><div class="card-inner"><p class="text-soft mb-0">Cargando...</p></div></div>
            </div>
        </div>
    </div>
</div>

<script>
const TASK_ID = <?php echo (int)$taskId; ?>;
const CSRF_TOKEN = '<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>';
function escapeHtml(value) { return String(value == null ? '' : value).replace(/[&<>'"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#039;','"':'&quot;'}[c])); }
function postJson(url, data) { return fetch(url, { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-Token':CSRF_TOKEN}, credentials:'same-origin', body:JSON.stringify(data) }).then(r => r.json().then(j => ({ok:r.ok,data:j}))); }
function renderDetail(t, docs, hist) {
    const uploadForm = Number(t.requiere_documento) === 1
        ? '<div class="card card-bordered mt-3"><div class="card-inner"><h6 class="mb-3">Subir evidencia</h6><form id="uploadForm" class="row g-3 align-items-end"><div class="col-lg-5"><label class="form-label">Documento</label><input class="form-control" type="file" name="file" accept=".pdf,.jpg,.jpeg,.png" required></div><div class="col-lg-5"><label class="form-label">Comentario</label><input class="form-control" name="comentario" placeholder="Comentario opcional"></div><div class="col-lg-2"><button class="btn btn-primary w-100" type="submit">Subir</button></div></form></div></div>'
        : '';

    return '<div class="card-inner">'
        + '<div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-3">'
        + '<div><h5 class="mb-1">' + escapeHtml(t.nombre) + '</h5><div class="text-soft">Paciente: ' + escapeHtml(t.paciente) + ' | Flujo: ' + escapeHtml(t.flujo_nombre) + '</div></div>'
        + '<div class="d-flex flex-wrap gap-1 align-items-start"><span class="badge bg-primary">' + escapeHtml(t.estatus) + '</span><span class="badge bg-light text-dark">Limite: ' + escapeHtml(t.fecha_limite || 'Sin fecha') + '</span></div>'
        + '</div>'
        + (t.descripcion ? '<p class="mb-4">' + escapeHtml(t.descripcion) + '</p>' : '')
        + '<div class="border rounded p-3 mb-4 bg-lighter">'
        + '<h6 class="mb-3">Acciones</h6>'
        + '<div class="d-flex flex-wrap gap-2"><button class="btn btn-outline-warning" onclick="setStatus(\'en_proceso\')">En proceso</button><button class="btn btn-outline-success" onclick="setStatus(\'completada\')">Completar</button><button class="btn btn-outline-danger" onclick="denyTask()">Negar</button></div>'
        + '</div>'
        + '<div class="row g-3">'
        + '<div class="col-lg-4"><div class="card card-bordered h-100"><div class="card-inner"><h6 class="mb-3">Documentos</h6><ul class="list-unstyled mb-0">' + docs + '</ul></div></div></div>'
        + '<div class="col-lg-8"><div class="card card-bordered h-100"><div class="card-inner"><h6 class="mb-3">Historial</h6><div class="table-responsive"><table class="table table-sm align-middle"><thead><tr><th>Fecha</th><th>Accion</th><th>Detalle</th></tr></thead><tbody>' + hist + '</tbody></table></div></div></div></div>'
        + '</div>'
        + uploadForm
        + '</div>';
}
function loadDetail() {
    fetch('/api/detalle_tarea.php?id_tarea_asignada=' + encodeURIComponent(TASK_ID), { credentials:'same-origin' })
        .then(r => r.json().then(j => ({ok:r.ok,data:j})))
        .then(result => {
            if (!result.ok || !result.data.success) throw new Error(result.data.message || 'No se pudo cargar');
            const t = result.data.data;
            const docs = (t.documentos || []).map(d => '<li class="mb-2"><a href="' + escapeHtml(d.ruta) + '" target="_blank">' + escapeHtml(d.nombre_original) + '</a><div class="small text-soft">' + escapeHtml(d.comentario || '') + '</div></li>').join('') || '<li class="text-soft">Sin documentos.</li>';
            const hist = (t.historial || []).map(h => '<tr><td>' + escapeHtml(h.fecha) + '</td><td>' + escapeHtml(h.accion) + '</td><td>' + escapeHtml(h.descripcion || '') + '</td></tr>').join('') || '<tr><td colspan="3" class="text-soft">Sin historial.</td></tr>';
            document.getElementById('detailBox').innerHTML = renderDetail(t, docs, hist);
            const form = document.getElementById('uploadForm');
            if (form) form.addEventListener('submit', uploadDocument);
        })
        .catch(e => { document.getElementById('detailBox').innerHTML = '<div class="card-inner"><div class="alert alert-danger">' + escapeHtml(e.message) + '</div></div>'; });
}
function setStatus(status) {
    postJson('/api/actualizar_tarea.php', { id_tarea_asignada: TASK_ID, estatus: status, observaciones: '' }).then(r => { if (!r.ok || !r.data.success) throw new Error(r.data.message || 'No se pudo actualizar'); loadDetail(); }).catch(e => alert(e.message));
}
function denyTask() {
    const motivo = prompt('Motivo de negacion');
    if (!motivo) return;
    const observaciones = prompt('Observaciones');
    if (!observaciones) return;
    postJson('/api/negar_tarea.php', { id_tarea_asignada: TASK_ID, motivo: motivo, observaciones: observaciones }).then(r => { if (!r.ok || !r.data.success) throw new Error(r.data.message || 'No se pudo negar'); loadDetail(); }).catch(e => alert(e.message));
}
function uploadDocument(event) {
    event.preventDefault();
    const data = new FormData(event.currentTarget);
    data.append('id_tarea_asignada', TASK_ID);
    fetch('/api/subir_documento_tarea.php', { method:'POST', headers:{'X-CSRF-Token':CSRF_TOKEN}, credentials:'same-origin', body:data }).then(r => r.json().then(j => ({ok:r.ok,data:j}))).then(r => { if (!r.ok || !r.data.success) throw new Error(r.data.message || 'No se pudo subir'); loadDetail(); }).catch(e => alert(e.message));
}
loadDetail();
</script>

<?php include_once '../includes/footer.php'; ?>
