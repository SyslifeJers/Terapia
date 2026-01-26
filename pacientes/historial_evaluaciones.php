<?php
include_once '../includes/head.php';
require_once '../database/conexion.php';

$db = new Database();
$conn = $db->getConnection();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$paciente = [];

if ($id > 0) {
    $stmt = $conn->prepare("SELECT Id, name FROM nino WHERE Id = ? LIMIT 1");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $paciente = $result ? $result->fetch_assoc() : [];
    $stmt->close();
}

$db->closeConnection();
?>
<style>
    .hist-eval-wrapper .table-responsive {
        max-height: 70vh;
        overflow: auto;
    }

    .hist-eval-table {
        min-width: 1100px;
    }

    .hist-eval-table td,
    .hist-eval-table th {
        white-space: nowrap;
        vertical-align: top;
    }

    .hist-eval-observaciones-cell {
        min-width: 320px;
        max-width: 420px;
        white-space: normal;
    }

    .hist-eval-observaciones-text {
        white-space: pre-wrap;
        word-break: break-word;
    }

    .hist-eval-actions {
        min-width: 140px;
    }

    .hist-eval-actions .btn {
        margin-bottom: 0.35rem;
    }
</style>

<div class="nk-wrap">
    <?php include_once '../includes/menu_superior.php'; ?>
    <div class="nk-content nk-content-fluid">
        <div class="container-xl wide-xl">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">Historial de Evaluaciones</h3>
                        <div class="nk-block-des text-soft">
                            <p>Paciente: <?php echo htmlspecialchars($paciente['name'] ?? ''); ?></p>
                        </div>
                    </div>
                    <div class="nk-block-head-content">
                        <a href="paciente.php?id=<?php echo $id; ?>" class="btn btn-outline-secondary">
                            <em class="icon ni ni-arrow-left"></em> Volver
                        </a>
                    </div>
                </div>
            </div>

            <div class="nk-content-body">
                <div class="card hist-eval-wrapper">
                    <div class="card-inner">
                        <div class="table-responsive">
                            <table class="table table-striped hist-eval-table" id="histEvalTable">
                                <thead>
                                    <tr id="histEvalHeadRow">
                                        <th>Fecha</th>
                                        <th class="hist-eval-observaciones">Observaciones</th>
                                    </tr>
                                </thead>
                                <tbody id="histEvalBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditEvalObservaciones" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar evaluación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="editEvalObservacionesForm">
                    <input type="hidden" id="editEvalId" name="id_valoracion" />
                    <div class="mb-3">
                        <label class="form-label">Puntos a calificar</label>
                        <div id="editEvalCriterios" class="d-grid gap-2"></div>
                    </div>
                    <div class="mb-3">
                        <label for="editEvalObservaciones" class="form-label">Observaciones</label>
                        <textarea class="form-control" id="editEvalObservaciones" name="observaciones" rows="6"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnSaveEvalObservaciones">Guardar</button>
            </div>
        </div>
    </div>
</div>

<script>
    const idPaciente = <?php echo (int)$id; ?>;
    const histEvalTable = document.getElementById('histEvalTable');
    const modalEditEvalEl = document.getElementById('modalEditEvalObservaciones');
    const editEvalIdInput = document.getElementById('editEvalId');
    const editEvalCriterios = document.getElementById('editEvalCriterios');
    const editEvalObsInput = document.getElementById('editEvalObservaciones');
    const btnSaveEvalObs = document.getElementById('btnSaveEvalObservaciones');
    let histEvalDt = null;

    const escapeHtml = (value) => {
        const text = String(value ?? '');
        return text
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    };

    function cargarHistorialEvaluaciones() {
        fetch(`get_historial.php?tipo=evaluacion&id=${idPaciente}`)
            .then(r => r.json())
            .then(data => {
                const tbody = document.getElementById('histEvalBody');
                if (!tbody) return;
                tbody.innerHTML = '';

                const criteriosTabla = [];
                const idsActuales = new Set();
                if (Array.isArray(data)) {
                    data.forEach(row => {
                        if (Array.isArray(row.criterios)) {
                            row.criterios.forEach(c => {
                                if (!idsActuales.has(c.id_criterio)) {
                                    criteriosTabla.push(c);
                                    idsActuales.add(c.id_criterio);
                                }
                            });
                        }
                    });
                }

                const headRow = document.getElementById('histEvalHeadRow');
                if (headRow) {
                    headRow.innerHTML = '<th>Fecha</th>';
                    criteriosTabla.forEach(c => {
                        headRow.innerHTML += `<th>${escapeHtml(c.nombre)}</th>`;
                    });
                    headRow.innerHTML += '<th>Promedio</th><th class="hist-eval-observaciones">Observaciones</th><th class="hist-eval-actions">Acciones</th>';
                }

                const totalCols = headRow ? headRow.children.length : (criteriosTabla.length + 3);
                if (!Array.isArray(data) || data.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="${totalCols}">Sin registros</td></tr>`;
                } else {
                    data.forEach(row => {
                        const valores = new Map();
                        if (Array.isArray(row.criterios)) {
                            row.criterios.forEach(c => {
                                valores.set(String(c.id_criterio), c.valor);
                            });
                        }
                        let celdas = `<td>${escapeHtml(row.fecha_valoracion)}</td>`;
                        criteriosTabla.forEach(c => {
                            const key = String(c.id_criterio);
                            const valor = valores.has(key) ? valores.get(key) : '';
                            celdas += `<td>${valor !== '' ? escapeHtml(valor) : '-'}</td>`;
                        });
                        const promedio = typeof row.promedio === 'number' && !Number.isNaN(row.promedio)
                            ? row.promedio.toFixed(2)
                            : '-';
                        celdas += `<td>${promedio}</td>`;
                        const obs = row.observaciones ? row.observaciones : '';
                        const obsText = escapeHtml(obs);
                        const obsDisplay = obs ? obsText : 'Sin observaciones';
                        const obsClass = obs ? '' : ' text-muted';
                        celdas += `
                            <td class="hist-eval-observaciones-cell">
                                <div class="hist-eval-observaciones-text${obsClass}" data-empty="${obs ? '0' : '1'}">${obsDisplay}</div>
                            </td>
                            <td class="hist-eval-actions">
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-action="edit">Editar</button>
                            </td>
                        `;
                        const criteriosPayload = encodeURIComponent(JSON.stringify(row.criterios || []));
                        tbody.innerHTML += `<tr data-id="${escapeHtml(row.id_valoracion)}" data-observaciones="${obsText}" data-criterios="${criteriosPayload}">${celdas}</tr>`;
                    });
                }

                if (histEvalDt) {
                    histEvalDt.destroy();
                }
                histEvalDt = new DataTable('#histEvalTable');
            });
    }

    if (histEvalTable) {
        histEvalTable.addEventListener('click', (event) => {
            const button = event.target.closest('button[data-action="edit"]');
            if (!button) return;
            const row = button.closest('tr');
            if (!row) return;
            const id = row.dataset.id || '';
            const observaciones = row.dataset.observaciones || '';
            const criteriosRaw = row.dataset.criterios || '';
            let criterios = [];
            if (criteriosRaw) {
                try {
                    criterios = JSON.parse(decodeURIComponent(criteriosRaw));
                } catch (error) {
                    criterios = [];
                }
            }
            if (editEvalIdInput) {
                editEvalIdInput.value = id;
            }
            if (editEvalCriterios) {
                editEvalCriterios.innerHTML = '';
                if (Array.isArray(criterios) && criterios.length > 0) {
                    criterios.forEach((criterio) => {
                        const criterioId = String(criterio.id_criterio ?? '');
                        const criterioNombre = escapeHtml(criterio.nombre ?? '');
                        const criterioValor = criterio.valor ?? '';
                        const wrapper = document.createElement('div');
                        wrapper.className = 'input-group';
                        wrapper.innerHTML = `
                            <span class="input-group-text">${criterioNombre}</span>
                            <input type="number" step="0.01" class="form-control" data-criterio-id="${escapeHtml(criterioId)}" value="${escapeHtml(criterioValor)}" />
                        `;
                        editEvalCriterios.appendChild(wrapper);
                    });
                } else {
                    editEvalCriterios.innerHTML = '<p class="text-muted mb-0">Sin criterios para editar.</p>';
                }
            }
            if (editEvalObsInput) {
                editEvalObsInput.value = observaciones;
                editEvalObsInput.focus();
            }
            const editModal = bootstrap.Modal.getOrCreateInstance(modalEditEvalEl);
            editModal.show();
        });
    }

    if (btnSaveEvalObs) {
        btnSaveEvalObs.addEventListener('click', () => {
            if (!editEvalIdInput || !editEvalObsInput) return;
            const idValoracion = editEvalIdInput.value;
            const nuevaObs = editEvalObsInput.value.trim();
            const params = new URLSearchParams();
            params.append('id_valoracion', idValoracion);
            params.append('observaciones', nuevaObs);
            if (editEvalCriterios) {
                const inputs = editEvalCriterios.querySelectorAll('input[data-criterio-id]');
                inputs.forEach((input) => {
                    const criterioId = input.dataset.criterioId;
                    const valor = input.value;
                    if (criterioId !== undefined) {
                        params.append(`criterios[${criterioId}]`, valor);
                    }
                });
            }
            btnSaveEvalObs.disabled = true;
            fetch('actualizar_valoracion.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
                    },
                    body: params.toString()
                })
                .then(r => r.json())
                .then(res => {
                    if (!res.success) {
                        Swal.fire('Error', res.message || 'No se pudo actualizar la observación.', 'error');
                        return;
                    }
                    const editModal = bootstrap.Modal.getOrCreateInstance(modalEditEvalEl);
                    editModal.hide();
                    cargarHistorialEvaluaciones();
                })
                .catch(() => {
                    Swal.fire('Error', 'Ocurrió un error al actualizar la observación.', 'error');
                })
                .finally(() => {
                    btnSaveEvalObs.disabled = false;
                });
        });
    }

    cargarHistorialEvaluaciones();
</script>

<?php include_once '../includes/footer.php'; ?>
