<?php
require_once __DIR__ . '/../database/conexion.php';

$criteriosBase = [
    'Permanencia',
    'Irritabilidad',
    'Habilidades sociales',
    'Atención conjunta',
    'Seguimiento de indicaciones',
    'Cognición',
    'Comunicación receptiva',
    'Comunicación expresiva',
];

$db = new Database();
$conn = $db->getConnection();

$conn->query("CREATE TABLE IF NOT EXISTS exp_valoracion_catalogo (
    id_catalogo INT AUTO_INCREMENT PRIMARY KEY,
    id_nino INT NULL,
    seccion VARCHAR(255) NULL,
    criterio VARCHAR(255) NOT NULL,
    puntaje_default TINYINT NOT NULL DEFAULT 5,
    orden INT NOT NULL DEFAULT 0,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_nino) REFERENCES nino(Id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$existeIndiceCatalogo = $conn->query("SHOW INDEX FROM exp_valoracion_catalogo WHERE Key_name = 'idx_catalogo_nino_seccion_criterio'");
if ($existeIndiceCatalogo && $existeIndiceCatalogo->num_rows === 0) {
    $conn->query("ALTER TABLE exp_valoracion_catalogo ADD UNIQUE KEY idx_catalogo_nino_seccion_criterio (id_nino, seccion, criterio)");
}
if ($existeIndiceCatalogo) {
    $existeIndiceCatalogo->free();
}

$idPaciente = isset($id) ? (int)$id : 0;
$plantillaPaciente = [];
$plantillaGeneral = [];

if ($conn) {
    $conteoCatalogo = 0;
    $resultadoConteo = $conn->query("SELECT COUNT(*) AS total FROM exp_valoracion_catalogo");
    if ($resultadoConteo) {
        $conteoFila = $resultadoConteo->fetch_assoc();
        $conteoCatalogo = isset($conteoFila['total']) ? (int)$conteoFila['total'] : 0;
        $resultadoConteo->free();
    }

    if ($conteoCatalogo === 0) {
        $orden = 0;
        $stmtInsertBase = $conn->prepare("INSERT INTO exp_valoracion_catalogo (id_nino, seccion, criterio, puntaje_default, orden) VALUES (NULL, ?, ?, 5, ?)");
        if ($stmtInsertBase) {
            foreach ($criteriosBase as $criterioBase) {
                $seccionBase = 'General';
                $orden++;
                $criterioNombre = mb_substr($criterioBase, 0, 255);
                $stmtInsertBase->bind_param('ssi', $seccionBase, $criterioNombre, $orden);
                $stmtInsertBase->execute();
            }
            $stmtInsertBase->close();
        }
    }

    $stmt = $conn->prepare("SELECT id_catalogo, id_nino, seccion, criterio, puntaje_default FROM exp_valoracion_catalogo WHERE id_nino IS NULL OR id_nino = ? ORDER BY CASE WHEN id_nino = ? THEN 0 ELSE 1 END, orden, seccion, id_catalogo");
    if ($stmt) {
        $stmt->bind_param('ii', $idPaciente, $idPaciente);
        $stmt->execute();
        $resultado = $stmt->get_result();
        while ($fila = $resultado->fetch_assoc()) {
            $nombreSeccion = isset($fila['seccion']) && trim($fila['seccion']) !== '' ? trim($fila['seccion']) : 'General';
            $puntaje = isset($fila['puntaje_default']) ? (int)$fila['puntaje_default'] : 5;
            $puntaje = max(1, min(10, $puntaje));
            $registro = [
                'catalogo_id' => isset($fila['id_catalogo']) ? (int)$fila['id_catalogo'] : null,
                'criterio' => mb_substr(trim($fila['criterio']), 0, 255),
                'puntaje' => $puntaje,
            ];
            if ((int)$fila['id_nino'] === $idPaciente && $idPaciente > 0) {
                if (!isset($plantillaPaciente[$nombreSeccion])) {
                    $plantillaPaciente[$nombreSeccion] = [
                        'titulo' => $nombreSeccion,
                        'criterios' => [],
                    ];
                }
                $plantillaPaciente[$nombreSeccion]['criterios'][] = $registro;
            } elseif ($fila['id_nino'] === null) {
                if (!isset($plantillaGeneral[$nombreSeccion])) {
                    $plantillaGeneral[$nombreSeccion] = [
                        'titulo' => $nombreSeccion,
                        'criterios' => [],
                    ];
                }
                $plantillaGeneral[$nombreSeccion]['criterios'][] = $registro;
            }
        }
        $stmt->close();
    }
}

if (empty($plantillaGeneral) && empty($plantillaPaciente)) {
    $plantillaGeneral['General'] = [
        'titulo' => 'General',
        'criterios' => array_map(fn($nombre) => [
            'catalogo_id' => null,
            'criterio' => $nombre,
            'puntaje' => 5
        ], $criteriosBase),
    ];
}

$plantillasDisponibles = [];

if (!empty($plantillaPaciente)) {
    $plantillasDisponibles[] = [
        'key' => 'paciente',
        'label' => 'Plantilla asignada al paciente',
        'descripcion' => 'Criterios configurados específicamente para este paciente desde la tabla exp_valoracion_catalogo.',
        'secciones' => array_values($plantillaPaciente),
    ];
}

if (!empty($plantillaGeneral)) {
    $plantillasDisponibles[] = [
        'key' => 'general',
        'label' => 'Plantilla general',
        'descripcion' => 'Configuración predeterminada tomada de la base de datos.',
        'secciones' => array_values($plantillaGeneral),
    ];
}

$plantillaSeleccionada = $plantillasDisponibles[0]['key'] ?? null;

$db->closeConnection();
?>
<div class="modal fade" id="modalForm" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="guardar_evaluacion.php" class="form-validate" id="formNuevaEvaluacion">
                <div class="modal-header">
                    <h5 class="modal-title">Nueva evaluación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_nino" value="<?php echo $id ?? 0; ?>">
                    <input type="hidden" name="id_usuario" value="<?php echo $_SESSION['id'] ?? 0; ?>">
                    <input type="hidden" name="evaluacion_json" id="evaluacionPayload">

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label mb-0">Tablas de evaluación</label>
                        <button type="button" class="btn btn-outline-primary btn-sm" id="addSectionBtn">
                            <em class="icon ni ni-plus"></em> Agregar tabla
                        </button>
                    </div>

                    <div class="card card-bordered border-dashed mb-3">
                        <div class="card-inner py-3">
                            <div class="row g-2 align-items-end">
                                <div class="col-sm-8">
                                    <label class="form-label" for="plantillaSelector">Plantilla de base de datos</label>
                                    <select class="form-select" id="plantillaSelector" <?php echo empty($plantillasDisponibles) ? 'disabled' : ''; ?>>
                                        <option value="" data-descripcion="Inicia desde cero sin plantilla predefinida." <?php echo $plantillaSeleccionada === null ? 'selected' : ''; ?>>Construir manualmente</option>
                                        <?php foreach ($plantillasDisponibles as $plantilla): ?>
                                            <option value="<?php echo htmlspecialchars($plantilla['key'], ENT_QUOTES, 'UTF-8'); ?>" data-descripcion="<?php echo htmlspecialchars($plantilla['descripcion'], ENT_QUOTES, 'UTF-8'); ?>" <?php echo $plantillaSeleccionada === $plantilla['key'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($plantilla['label'], ENT_QUOTES, 'UTF-8'); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text" id="plantillaDescripcion"></div>
                                </div>
                                <div class="col-sm-4 text-sm-end">
                                    <button type="button" class="btn btn-outline-secondary w-100" id="applyPlantillaBtn" <?php echo empty($plantillasDisponibles) ? 'disabled' : ''; ?>>Aplicar plantilla</button>
                                </div>
                            </div>
                            <div class="form-text mt-2">
                                Gestiona las opciones desde la tabla <strong>exp_valoracion_catalogo</strong> para agregar o quitar criterios y asignarlos por paciente.
                            </div>
                        </div>
                    </div>

                    <div id="seccionesContainer" class="mb-3"></div>

                    <div class="alert alert-light border" role="alert">
                        <div class="d-flex align-items-start">
                            <em class="icon ni ni-info me-2"></em>
                            <div>
                                Puedes agregar o quitar tablas y puntos según sea necesario. Cada tabla puede representar un área o criterio diferente para evaluar.
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="observaciones">Observaciones</label>
                        <div class="form-control-wrap">
                            <textarea class="form-control" id="observaciones" name="observaciones" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<template id="sectionTemplate">
    <div class="card card-bordered evaluacion-section mb-3">
        <div class="card-inner">
            <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
                <div class="flex-grow-1">
                    <label class="form-label mb-1">Nombre de la tabla</label>
                    <input type="text" class="form-control section-title-input" placeholder="Ej. Comunicación">
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary btn-sm add-criterio-btn">
                        <em class="icon ni ni-plus"></em> Punto
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-sm remove-section-btn">
                        <em class="icon ni ni-trash"></em> Tabla
                    </button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Descripción</th>
                            <th class="text-center" style="width: 130px;">Puntaje</th>
                            <th class="text-center" style="width: 70px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="criterios-body"></tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<template id="rowTemplate">
    <tr class="criterio-row">
        <td>
            <input type="text" class="form-control criterio-desc" placeholder="Descripción del punto" required>
        </td>
        <td class="text-center">
            <input type="number" class="form-control criterio-score" value="5" min="1" max="10" required>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger remove-criterio" aria-label="Eliminar punto">
                <em class="icon ni ni-trash"></em>
            </button>
        </td>
    </tr>
</template>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const seccionesContainer = document.getElementById('seccionesContainer');
    const addSectionBtn = document.getElementById('addSectionBtn');
    const sectionTemplate = document.getElementById('sectionTemplate');
    const rowTemplate = document.getElementById('rowTemplate');
    const payloadInput = document.getElementById('evaluacionPayload');
    const form = document.getElementById('formNuevaEvaluacion');
    const plantillaSelector = document.getElementById('plantillaSelector');
    const applyPlantillaBtn = document.getElementById('applyPlantillaBtn');
    const plantillasDisponibles = <?php echo json_encode($plantillasDisponibles, JSON_UNESCAPED_UNICODE); ?>;
    const plantillaDescripcion = document.getElementById('plantillaDescripcion');

    function createRow(tbody, descripcion = '', puntaje = 5, catalogoId = null) {
        const fragment = rowTemplate.content.cloneNode(true);
        const rowEl = fragment.querySelector('.criterio-row');
        const descInput = rowEl.querySelector('.criterio-desc');
        const scoreInput = rowEl.querySelector('.criterio-score');
        if (descripcion) {
            descInput.value = descripcion;
        }
        if (typeof puntaje === 'number' && !Number.isNaN(puntaje)) {
            scoreInput.value = puntaje;
        }
        if (catalogoId !== null && catalogoId !== undefined && catalogoId !== '') {
            rowEl.dataset.catalogoId = String(catalogoId);
            rowEl.dataset.catalogoOriginal = descripcion || '';
        }
        if (descInput) {
            descInput.addEventListener('input', () => {
                const original = rowEl.dataset.catalogoOriginal ? rowEl.dataset.catalogoOriginal.trim() : '';
                const actual = descInput.value.trim();
                if (rowEl.dataset.catalogoId && original !== '' && original !== actual) {
                    delete rowEl.dataset.catalogoId;
                }
            });
        }
        tbody.appendChild(rowEl);
    }

    function createSection(data = {}) {
        const fragment = sectionTemplate.content.cloneNode(true);
        const sectionEl = fragment.querySelector('.evaluacion-section');
        const titleInput = sectionEl.querySelector('.section-title-input');
        const tbody = sectionEl.querySelector('.criterios-body');

        if (typeof data.titulo === 'string') {
            titleInput.value = data.titulo;
        }

        const criterios = Array.isArray(data.criterios) ? data.criterios : null;
        if (criterios && criterios.length) {
            criterios.forEach(item => {
                const descripcion = typeof item.criterio === 'string' ? item.criterio : '';
                const puntaje = typeof item.puntaje === 'number' ? item.puntaje : 5;
                const catalogoId = typeof item.catalogo_id === 'number' ? item.catalogo_id : (item.catalogo_id ? Number(item.catalogo_id) : null);
                createRow(tbody, descripcion, puntaje, Number.isFinite(catalogoId) && catalogoId > 0 ? catalogoId : null);
            });
        } else {
            createRow(tbody);
        }

        seccionesContainer.appendChild(sectionEl);
    }

    function ensureSectionPresence() {
        if (seccionesContainer.children.length === 0) {
            createSection();
        }
    }

    seccionesContainer.addEventListener('click', (event) => {
        const addRowBtn = event.target.closest('.add-criterio-btn');
        if (addRowBtn) {
            const section = addRowBtn.closest('.evaluacion-section');
            const tbody = section ? section.querySelector('.criterios-body') : null;
            if (tbody) {
                createRow(tbody);
            }
            return;
        }

        const removeRowBtn = event.target.closest('.remove-criterio');
        if (removeRowBtn) {
            const row = removeRowBtn.closest('.criterio-row');
            if (row) {
                row.remove();
                const tbody = removeRowBtn.closest('.criterios-body');
                if (tbody && tbody.children.length === 0) {
                    createRow(tbody);
                }
            }
            return;
        }

        const removeSectionBtn = event.target.closest('.remove-section-btn');
        if (removeSectionBtn) {
            const section = removeSectionBtn.closest('.evaluacion-section');
            if (section) {
                section.remove();
                ensureSectionPresence();
            }
        }
    });

    if (addSectionBtn) {
        addSectionBtn.addEventListener('click', () => {
            createSection();
        });
    }

    function aplicarPlantilla(key) {
        if (!key) {
            seccionesContainer.innerHTML = '';
            ensureSectionPresence();
            return;
        }
        const plantilla = plantillasDisponibles.find(item => item.key === key);
        if (!plantilla) {
            return;
        }
        seccionesContainer.innerHTML = '';
        plantilla.secciones.forEach(section => {
            createSection(section);
        });
        ensureSectionPresence();
    }

    function actualizarDescripcion(key) {
        if (!plantillaDescripcion) {
            return;
        }
        let option = null;
        if (plantillaSelector) {
            option = Array.from(plantillaSelector.options).find(opt => opt.value === (key ?? '')) || null;
        }
        const texto = option && option.dataset && option.dataset.descripcion ? option.dataset.descripcion : '';
        plantillaDescripcion.textContent = texto;
    }

    if (applyPlantillaBtn) {
        applyPlantillaBtn.addEventListener('click', () => {
            const key = plantillaSelector ? plantillaSelector.value : null;
            aplicarPlantilla(key);
        });
    }

    if (plantillaSelector) {
        plantillaSelector.addEventListener('change', () => {
            actualizarDescripcion(plantillaSelector.value);
            if (!applyPlantillaBtn) {
                aplicarPlantilla(plantillaSelector.value);
            }
        });
        actualizarDescripcion(plantillaSelector.value);
    }

    if (form) {
        form.addEventListener('submit', (event) => {
            const payload = [];
            const sections = seccionesContainer.querySelectorAll('.evaluacion-section');
            sections.forEach(section => {
                const titleInput = section.querySelector('.section-title-input');
                const titulo = titleInput ? titleInput.value.trim() : '';
                const filas = [];
                section.querySelectorAll('.criterio-row').forEach(row => {
                    const descInput = row.querySelector('.criterio-desc');
                    const scoreInput = row.querySelector('.criterio-score');
                    const descripcion = descInput ? descInput.value.trim() : '';
                    const valor = scoreInput ? Number(scoreInput.value) : null;
                    if (descripcion !== '' && valor !== null && !Number.isNaN(valor)) {
                        const catalogoAttr = row.dataset.catalogoId ? Number(row.dataset.catalogoId) : null;
                        const catalogoId = (typeof catalogoAttr === 'number' && Number.isFinite(catalogoAttr) && catalogoAttr > 0) ? Math.trunc(catalogoAttr) : null;
                        filas.push({
                            criterio: descripcion,
                            puntaje: Math.max(1, Math.min(10, Math.round(valor))),
                            catalogo_id: catalogoId
                        });
                    }
                });
                if (filas.length) {
                    payload.push({
                        titulo,
                        criterios: filas
                    });
                }
            });

            if (!payload.length) {
                event.preventDefault();
                alert('Agrega al menos un punto de evaluación antes de guardar.');
                return;
            }

            payloadInput.value = JSON.stringify(payload);
        });
    }

    if (plantillaSelector && plantillaSelector.value) {
        aplicarPlantilla(plantillaSelector.value);
    } else if (plantillasDisponibles.length) {
        aplicarPlantilla(plantillasDisponibles[0].key);
    } else {
        ensureSectionPresence();
    }
});
</script>
