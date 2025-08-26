<?php
include_once '../includes/head.php';
date_default_timezone_set('America/Mexico_City');
?>
<div class="nk-wrap ">
    <?php
    include_once '../includes/menu_superior.php';
    require_once '../database/conexion.php';
    $db = new Database();
    $conn = $db->getConnection();

    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $paciente = [];

    if ($id > 0) {
        $stmt = $conn->prepare("SELECT Id, name, edad, Observacion FROM nino WHERE Id = ? LIMIT 1");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $paciente = $result ? $result->fetch_assoc() : [];
    }

    $citas  = $conn->query("SELECT COUNT(*) as total FROM Cita WHERE IdNino = $id")->fetch_assoc()['total'] ?? 0;
    $evaluaciones  = $conn->query("SELECT COUNT(*) as total FROM exp_valoraciones_sesion WHERE id_nino = $id")->fetch_assoc()['total'] ?? 0;
    $examenes = $conn->query("SELECT COUNT(*) as total FROM exp_evaluacion_examen WHERE id_nino = $id")->fetch_assoc()['total'] ?? 0;

    $grafica_data = [
        'primera' => null,
        'ultima' => null,
        'promedio' => [
            'lenguaje' => 0,
            'motricidad' => 0,
            'atencion' => 0,
            'memoria' => 0,
            'social' => 0,
        ],
    ];

    $sql = "SELECT lenguaje, motricidad, atencion, memoria, social, fecha_registro
        FROM exp_progreso_general
        WHERE id_nino = ?
        ORDER BY fecha_registro ASC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    $total = 0;
    $suma = [
        'lenguaje' => 0,
        'motricidad' => 0,
        'atencion' => 0,
        'memoria' => 0,
        'social' => 0,
    ];

    while ($row = $result->fetch_assoc()) {
        if (!$grafica_data['primera']) {
            $grafica_data['primera'] = $row;
        }
        $grafica_data['ultima'] = $row;

        $suma['lenguaje'] += (int)$row['lenguaje'];
        $suma['motricidad'] += (int)$row['motricidad'];
        $suma['atencion'] += (int)$row['atencion'];
        $suma['memoria'] += (int)$row['memoria'];
        $suma['social'] += (int)$row['social'];
        $total++;
    }

    if ($total > 0) {
        $grafica_data['promedio'] = [
            'lenguaje' => round($suma['lenguaje'] / $total, 2),
            'motricidad' => round($suma['motricidad'] / $total, 2),
            'atencion' => round($suma['atencion'] / $total, 2),
            'memoria' => round($suma['memoria'] / $total, 2),
            'social' => round($suma['social'] / $total, 2),
            'fecha_registro' => 'Promedio'
        ];
    } else {
        $grafica_data['primera'] = [
            'lenguaje' => 0,
            'motricidad' => 0,
            'atencion' => 0,
            'memoria' => 0,
            'social' => 0,
            'fecha_registro' => 'Sin datos'
        ];
        $grafica_data['ultima'] = $grafica_data['primera'];
    }

    $ultimas_evaluaciones = [];

    $stmt = $conn->prepare("
    SELECT participacion, atencion, tarea_casa, fecha_valoracion 
    FROM exp_valoraciones_sesion 
    WHERE id_nino = ?
    ORDER BY fecha_valoracion DESC 
    LIMIT 15
");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Construir arreglo con promedio y ordenar cronológicamente
    while ($row = $result->fetch_assoc()) {
        $row['participacion'] = (float)$row['participacion'];
        $row['atencion'] = (float)$row['atencion'];
        $row['tarea_casa'] = (float)$row['tarea_casa'];
        $row['promedio'] = round((
            $row['participacion'] +
            $row['atencion'] +
            $row['tarea_casa']
        ) / 3, 2);

        $ultimas_evaluaciones[] = $row;
    }

    // Ordenar por fecha ascendente para que se grafique cronológicamente
    $ultimas_evaluaciones = array_reverse($ultimas_evaluaciones);

    $lista_examenes = [];

    $stmt = $conn->prepare("SELECT ee.id_eval, ee.fecha, u.name AS usuario, ex.nombre_examen FROM exp_evaluacion_examen ee JOIN Usuarios u ON ee.id_usuario = u.id JOIN exp_examenes ex ON ee.id_examen = ex.id_examen WHERE ee.id_nino = ? ORDER BY ee.fecha DESC");

    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result) {
        $lista_examenes = $result->fetch_all(MYSQLI_ASSOC);
    }

    $evaluaciones_fotos = [];
    $lista_secciones = [];
    $stmt = $conn->prepare("SELECT ef.id_eval_foto, ef.titulo, ef.seccion, ef.fecha, GROUP_CONCAT(ei.ruta) AS imagenes FROM exp_evaluacion_fotos ef LEFT JOIN exp_evaluacion_fotos_imagenes ei ON ef.id_eval_foto = ei.id_eval_foto WHERE ef.id_nino = ? GROUP BY ef.id_eval_foto ORDER BY ef.fecha DESC");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $sec = $row['seccion'] ?: 'General';
            $evaluaciones_fotos[$sec][] = $row;
            $lista_secciones[$sec] = true;
        }
    }
    $stmt->close();
    $lista_secciones = array_keys($lista_secciones);

    $db->closeConnection();
    ?>
    <div class="nk-content nk-content-fluid">
        <div class="container-xl wide-xl">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">Detalle de Paciente</h3>
                        <div class="nk-block-des text-soft">
                            <p>Información del paciente.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="nk-content-body">
                <div class="row g-gs">
                    <div class="col-xxl-6 col-lg-6">
                        <div class="card">
                            <div class="card-inner">
                                <div class="team">
                                    <div class="team-options">
                                        <div class="drodown">
                                            <a href="#" class="dropdown-toggle btn btn-sm btn-icon btn-trigger" data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <ul class="link-list-opt no-bdr">
                                                    <li><a href="#"><em class="icon ni ni-focus"></em><span>Actualizar observaciones</span></a></li>
                                                    <li><a href="#"><em class="icon ni ni-edit"></em><span>Editar paciente</span></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="user-card user-card-s2">
                                        <div class="user-avatar md bg-info">
                                            <img src="/assets/imagen/dinosaurio.png" alt="">
                                            <div class="status dot dot-lg dot-success"></div>
                                        </div>
                                        <div class="user-info">
                                            <h6><?php echo ucwords(strtolower(htmlspecialchars($paciente['name'] ?? ''))); ?></h6>
                                            <span class="sub-text">ID:<?php echo htmlspecialchars($paciente['Id'] ?? ''); ?></span>
                                        </div>
                                    </div>
                                    <div class="team-details">
                                        <p><?php echo htmlspecialchars($paciente['Observacion'] ?? ''); ?></p>
                                    </div>
                                    <ul class="team-statistics">
                                        <li><span><?php echo $citas; ?></span><span>Sesiones</span></li>
                                        <li><span><?php echo $evaluaciones; ?></span><span>Evaluaciones</span></li>
                                        <li><span><?php echo $examenes; ?></span><span>Exámenes</span></li>
                                    </ul>
                                    <div class="team-view">
                                        <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#modalForm">Nueva evaluación</button>
                                        <a href="evaluacion_examen.php?id=<?php echo $id; ?>" class="btn btn-warning">Agregar examen</a>
                                    </div>

                                    <div class="team-view mt-4">
                                        <h6>Promedio de las últimas 15 evaluaciones</h6>
                                    </div>

                                    <div class="team-statistics">
                                        <?php
                                        $evalCount = count($ultimas_evaluaciones);
                                        $avgPart = $evalCount ? round(array_sum(array_column($ultimas_evaluaciones, 'participacion')) / $evalCount, 2) : 0;
                                        $avgAt = $evalCount ? round(array_sum(array_column($ultimas_evaluaciones, 'atencion')) / $evalCount, 2) : 0;
                                        $avgTarea = $evalCount ? round(array_sum(array_column($ultimas_evaluaciones, 'tarea_casa')) / $evalCount, 2) : 0;
                                        ?>
                                        <p>Participación: <?php echo $avgPart; ?></p>
                                        <p>Atención: <?php echo $avgAt; ?></p>
                                        <p>Tarea en casa: <?php echo $avgTarea; ?></p>
                                    </div>
                                    <div class="team-view mt-2">
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalProgreso">Nuevo progreso</button>
                                    </div>
                                    <div class="team-statistics">
                                        <div class="team-view mt-2">
                                            <button type="button" class="btn btn-outline-info" id="btnHistEval">Historial de evaluación</button>
                                        </div>
                                        <div class="team-view mt-2">
                                            <button type="button" class="btn btn-outline-info" id="btnHistProg">Historial de progreso</button>
                                        </div>

                                        <div class="team-view mt-2">
                                            <a href="/pacientes/reporte_paciente.php?id=<?php echo $id; ?>" class="btn btn-outline-success">Descargar reporte</a>
                                        </div>

                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-6 col-lg-6">
                        <div class="card">
                            <div class="card-inner">
                                <h5 class="title">Gráfica de evolución 🧠</h5>
                                <canvas id="graficaRadar" width="400" height="400"></canvas>
                            </div>
                        </div>
                    </div>
                </div>



                <!-- 🧠 Gráfica Radar -->

                <div class="card mt-4">
                    <div class="card-inner">
                        <h5 class="title">Evolución de las últimas 15 evaluaciones</h5>
                        <canvas id="graficaLineal" height="300"></canvas>
                    </div>
                </div>
            <div class="card mt-4">
                <div class="card-inner">
                    <h5 class="title mb-3">Evaluaciones</h5>
                    <form action="guardar_evaluacion_fotos.php" method="POST" enctype="multipart/form-data" class="mb-4">
                        <input type="hidden" name="id_nino" value="<?php echo $id; ?>">
                        <div class="row g-4">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label" for="titulo_eval">Título</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control" id="titulo_eval" name="titulo" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label" for="seccion_eval">Sección</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control" id="seccion_eval" name="seccion" list="secciones_list" required>
                                        <datalist id="secciones_list">
                                            <?php foreach ($lista_secciones as $sec): ?>
                                                <option value="<?php echo htmlspecialchars($sec); ?>"></option>
                                            <?php endforeach; ?>
                                        </datalist>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label" for="fotos_eval">Fotos</label>
                                    <div class="form-control-wrap">
                                        <input type="file" class="form-control" id="fotos_eval" name="fotos[]" accept="image/*" multiple required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </div>
                        </div>
                    </form>
                    <div class="row g-gs">
                        <?php foreach ($evaluaciones_fotos as $sec => $lista): ?>
                            <div class="col-12"><h5 class="mt-2"><?php echo htmlspecialchars($sec); ?></h5></div>
                            <?php foreach ($lista as $ev): ?>
                            <div class="col-sm-6 col-lg-4">
                                <div class="card card-bordered">
                                    <div class="card-inner">
                                        <h6 class="title mb-2"><?php echo htmlspecialchars($ev['titulo']); ?></h6>
                                        <a href="detalleEvaluacion.php?id=<?php echo $ev['id_eval_foto']; ?>" class="btn btn-sm btn-outline-secondary">
                                            <em class="icon ni ni-eye"></em> Ver
                                        </a>
                                        <?php if ($_SESSION['rol'] != 2): ?>
                                        <a href="#" class="btn btn-sm btn-outline-danger delete-eval-foto" data-id="<?php echo $ev['id_eval_foto']; ?>">
                                            <em class="icon ni ni-trash"></em> Eliminar
                                        </a>
                                        <?php endif; ?>
                                        <!--
                                        <div class="row g-2">
                                            <?php //foreach (array_filter(explode(',', $ev['imagenes'])) as $img): ?>
                                            <div class="col-6">
                                                <img src="../uploads/pacientes/<?php //echo $id; ?>/evaluaciones/<?php //echo $ev['id_eval_foto'] . '/' . htmlspecialchars($img); ?>" class="img-fluid" alt="">
                                            </div>
                                            <?php //endforeach; ?>
                                        </div>
                                        -->
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                        <?php if (empty($evaluaciones_fotos)): ?>
                        <div class="col-12"><p>No hay evaluaciones fotográficas.</p></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
                <div class="card mt-4">
                    <div class="card-inner">
                        <!-- Botón para abrir el modal -->
                        <div class="d-flex align-items-center mb-3">
                            <h5 class="title mb-0 me-3">Archivos</h5>
                            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalExamUpload">
                                <em class="icon ni ni-plus"></em> Subir archivo
                            </button>
                        </div>
                        <hr class="my-4">
                        <div id="examFiles" class="nk-files nk-files-view-grid">
                            <div class="nk-files-list">
                                <?php
                                $dir = __DIR__ . '/../uploads/exams/' . $id;
                                if (is_dir($dir)) {
                                    $files = array_diff(scandir($dir), ['.', '..']);
                                    foreach ($files as $f) {
                                        if (substr($f, -4) === '.txt') continue;
                                        $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
                                        $icon = in_array($ext, ['png', 'jpg', 'jpeg', 'gif']) ? 'ni-file-img' : 'ni-file-pdf';
                                        $url = '/uploads/exams/' . $id . '/' . rawurlencode($f);
                                        $note = '';
                                        $noteFile = $dir . '/' . $f . '.txt';
                                        if (is_file($noteFile)) {
                                            $note = '<div class="mt-1 small text-muted">Nota: ' . nl2br(htmlspecialchars(trim(file_get_contents($noteFile)))) . '</div>';
                                        } else {
                                            $note = '';
                                        }

                                        echo '
<div class="nk-file-item nk-file">
    <div class="nk-file-info">
        <div class="nk-file-title">
            <div class="nk-file-icon">
                <a class="nk-file-icon-link" href="' . $url . '" target="_blank">
                    <span class="nk-file-icon-type">
                        <em class="icon ni ' . $icon . '"></em>
                    </span>
                </a>
            </div>
            <div class="nk-file-name">
                <div class="nk-file-name-text">
                    <a href="' . $url . '" class="title" target="_blank">' . htmlspecialchars($f) . '</a>
                </div>
            </div>
        </div>
        <ul class="nk-file-desc">
            <li class="date">' . date("d M", filemtime($noteFile)) . '</li>
            <li class="size">' . round(filesize($noteFile) / 1048576, 2) . ' MB</li>
            <li class="members">1 Usuario</li>
        </ul>
        ' . $note . '
    </div>
    <div class="nk-file-actions">
        <div class="dropdown">
            <a href="#" class="dropdown-toggle btn btn-sm btn-icon btn-trigger" data-bs-toggle="dropdown">
                <em class="icon ni ni-more-h"></em>
            </a>
            <div class="dropdown-menu dropdown-menu-end">
                <ul class="link-list-plain no-bdr">
                    <li><a href="' . $url . '" target="_blank"><em class="icon ni ni-eye"></em><span>Ver</span></a></li>
                    <li><a href="' . $url . '" download><em class="icon ni ni-download"></em><span>Descargar</span></a></li>
                    ' . (($_SESSION['rol'] != 2) ? '<li><a href="#" class="delete-exam" data-file="' . htmlspecialchars($f) . '"><em class="icon ni ni-trash"></em><span>Eliminar</span></a></li>' : '') . '
                </ul>
            </div>
        </div>
    </div>
</div>';
                                    }
                                } else {
                                    echo '<p>No hay archivos.</p>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mt-4">
                <div class="card-inner">
                    <h5 class="title mb-3">Evaluaciones de examen</h5>
                    <table class="table table-striped">
                        <thead>

                            <tr><th>Fecha</th><th>Evaluación</th><th>Usuario</th><th>PDF</th></tr>

                        </thead>
                        <tbody>
                        <?php foreach ($lista_examenes as $ex): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($ex['fecha']); ?></td>
                                <td><?php echo htmlspecialchars($ex['nombre_examen']); ?></td>

                                <td><?php echo htmlspecialchars($ex['usuario']); ?></td>
                                <td><a class="btn btn-sm btn-primary" target="_blank" href="pdf_evaluacion_examen.php?id=<?php echo $ex['id_eval']; ?>">Descargar</a></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($lista_examenes)): ?>
                            <tr><td colspan="4">Sin evaluaciones</td></tr>

                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>


        </div>
    </div>
</div>
<!-- Agregar el <div class="loading-animation tri-ring"></div> para cuando este cargando la subida del archivo -->
<div class="modal fade" id="modalExamUpload" tabindex="-1" aria-labelledby="modalExamUploadLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="min-height: 14em;">
            <div id="examUploadLoading" class="loading-animation tri-ring" style="display:none; margin: 0 auto; position: static !important;"></div>
            <form id="examUploadForm" class="mb-0">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalExamUploadLabel">Subir archivo de examen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <input type="file" name="file" id="examFile" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <textarea name="note" id="examNote" class="form-control" placeholder="Nota"></textarea>
                    </div>
                    <!-- Animación de carga al subir archivo -->
                
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        <em class="icon ni ni-upload-cloud"></em> <span class="ms-1">Subir archivo</span>
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const datosLineales = <?php echo json_encode($ultimas_evaluaciones); ?>;

    const fechas = datosLineales.map(e => e.fecha_valoracion);
    const participacion = datosLineales.map(e => parseFloat(e.participacion));
    const atencion = datosLineales.map(e => parseFloat(e.atencion));
    const tarea = datosLineales.map(e => parseFloat(e.tarea_casa));
    const promedio = datosLineales.map(e => parseFloat(e.promedio));

    const configLineal = {
        type: 'line',
        data: {
            labels: fechas,
            datasets: [{
                    label: 'Participación',
                    data: participacion,
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    tension: 0.3
                },
                {
                    label: 'Atención',
                    data: atencion,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.3
                },
                {
                    label: 'Tarea en casa',
                    data: tarea,
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    tension: 0.3
                },
                {
                    label: 'Calificación general',
                    data: promedio,
                    borderColor: 'rgba(255, 206, 86, 1)',
                    backgroundColor: 'rgba(255, 206, 86, 0.2)',
                    tension: 0.3,
                    borderDash: [5, 5]
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Tendencia de las últimas 10 evaluaciones'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    suggestedMin: 0,
                    suggestedMax: 10
                }
            }
        }
    };

    new Chart(document.getElementById('graficaLineal'), configLineal);
    // Aquí va tu configuración de Chart.js con los 4 datasets
    console.log(datosLineales);
</script>

<script>
    const datos = <?php echo json_encode($grafica_data); ?>;

    const labels = ["Lenguaje", "Motricidad", "Atención", "Memoria", "Social"];

    const dataRadar = {
        labels: labels,
        datasets: [{
                label: "Primera sesión (" + datos.primera.fecha_registro + ")",
                data: [
                    parseFloat(datos.primera.lenguaje),
                    parseFloat(datos.primera.motricidad),
                    parseFloat(datos.primera.atencion),
                    parseFloat(datos.primera.memoria),
                    parseFloat(datos.primera.social)
                ],
                borderColor: "rgba(54, 162, 235, 1)",
                backgroundColor: "rgba(54, 162, 235, 0.2)",
                fill: true
            },
            {
                label: "Promedio",
                data: [
                    parseFloat(datos.promedio.lenguaje),
                    parseFloat(datos.promedio.motricidad),
                    parseFloat(datos.promedio.atencion),
                    parseFloat(datos.promedio.memoria),
                    parseFloat(datos.promedio.social)
                ],
                borderColor: "rgba(75, 192, 192, 1)",
                backgroundColor: "rgba(75, 192, 192, 0.2)",
                fill: true
            },
            {
                label: "Última sesión (" + datos.ultima.fecha_registro + ")",
                data: [
                    parseFloat(datos.ultima.lenguaje),
                    parseFloat(datos.ultima.motricidad),
                    parseFloat(datos.ultima.atencion),
                    parseFloat(datos.ultima.memoria),
                    parseFloat(datos.ultima.social)
                ],
                borderColor: "rgba(255, 99, 132, 1)",
                backgroundColor: "rgba(255, 99, 132, 0.2)",
                fill: true
            }
        ]
    };

    const configRadar = {
        type: 'radar',
        data: dataRadar,
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Comparativa de Primera, Última y Promedio de Evaluaciones'
                }
            },
            scales: {
                r: {
                    suggestedMin: 0,
                    suggestedMax: 10,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    };

    new Chart(document.getElementById('graficaRadar'), configRadar);
</script>
<script>
    const idPaciente = <?php echo $id; ?>;
    const btnHistEval = document.getElementById('btnHistEval');
    const btnHistProg = document.getElementById('btnHistProg');
    const examForm = document.getElementById('examUploadForm');
    const examNote = document.getElementById('examNote');
    const examLoading = document.getElementById('examUploadLoading');
    let lastFocusedElement = null;

    function cargarHistorial(tipo, tbodyId, modalId) {
        fetch(`get_historial.php?tipo=${tipo}&id=${idPaciente}`)
            .then(r => r.json())
            .then(data => {
                const tbody = document.getElementById(tbodyId);
                tbody.innerHTML = '';
                if (data.length === 0) {
                    const cols = tbodyId === 'histEvalBody' ? 5 : 7;
                    tbody.innerHTML = `<tr><td colspan="${cols}">Sin registros</td></tr>`;
                } else {
                    data.forEach(row => {
                        if (tipo === 'evaluacion') {
                            tbody.innerHTML += `<tr><td>${row.fecha_valoracion}</td><td>${row.participacion}</td><td>${row.atencion}</td><td>${row.tarea_casa}</td><td>${row.observaciones || ''}</td></tr>`;
                            new DataTable('#histEvalTable');

                        } else {
                            tbody.innerHTML += `<tr><td>${row.fecha_registro}</td><td>${row.lenguaje}</td><td>${row.motricidad}</td><td>${row.atencion}</td><td>${row.memoria}</td><td>${row.social}</td><td>${row.observaciones || ''}</td></tr>`;
                            new DataTable('#histProgTable');
                        }
                    });
                }
                const modalEl = document.getElementById(modalId);
                const modal = new bootstrap.Modal(modalEl);
                lastFocusedElement = document.activeElement;
                modal.show();
            });
    }

    const modalHistEvalEl = document.getElementById('modalHistEval');
    const modalHistProgEl = document.getElementById('modalHistProg');

    [modalHistEvalEl, modalHistProgEl].forEach(modalEl => {
        if (modalEl) {
            modalEl.addEventListener('hidden.bs.modal', () => {
                if (lastFocusedElement) {
                    lastFocusedElement.focus();
                }
            });
        }
    });

    if (btnHistEval) {
        btnHistEval.addEventListener('click', () => {
            cargarHistorial('evaluacion', 'histEvalBody', 'modalHistEval');
        });
    }

    if (btnHistProg) {
        btnHistProg.addEventListener('click', () => {
            cargarHistorial('progreso', 'histProgBody', 'modalHistProg');
        });
    }

    if (examForm) {
        examForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const input = document.getElementById('examFile');
            if (!input.files.length) return;
            const data = new FormData();
            data.append('file', input.files[0]);
            data.append('id', idPaciente);
            if (examNote) {
                data.append('note', examNote.value);
            }
            // Mostrar animación de carga
            if (examLoading){
                examLoading.style.display = 'block';
                examForm.style.display = 'none';
            } 

            fetch('upload_exam.php', {
                    method: 'POST',
                    body: data
                })
                .then(r => r.json())
                .then(res => {
                    // Ocultar animación de carga
                    if (examLoading) examLoading.style.display = 'none';
                    if (res.success) {
                        Swal.fire('Archivo subido', '', 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Error', res.message || 'Ocurrió un error', 'error');
                    }
                })
                .catch(() => {
                    if (examLoading) examLoading.style.display = 'none';
                    Swal.fire('Error', 'Ocurrió un error', 'error');
                })
                .finally(() => {
                    if (examLoading){
                        examLoading.style.display = 'none';
                        examForm.style.display = 'block';
                    } 
                });
        });
    }

    <?php if ($_SESSION['rol'] != 2): ?>
    document.querySelectorAll('.delete-eval-foto').forEach(btn => {
        btn.addEventListener('click', function(e){
            e.preventDefault();
            const idEval = this.getAttribute('data-id');
            Swal.fire({
                title: '¿Eliminar evaluación?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar'
            }).then(res => {
                if (res.isConfirmed) {
                    const fd = new FormData();
                    fd.append('id_eval', idEval);
                    fetch('eliminar_evaluacion_fotos.php', {
                        method: 'POST',
                        body: fd
                    })
                    .then(r => r.json())
                    .then(resp => {
                        if (resp.success) {
                            Swal.fire('Eliminado', '', 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Error', resp.message || 'Ocurrió un error', 'error');
                        }
                    })
                    .catch(() => Swal.fire('Error', 'Ocurrió un error', 'error'));
                }
            });
        });
    });

    document.querySelectorAll('.delete-exam').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const file = this.getAttribute('data-file');
            Swal.fire({
                title: '¿Eliminar archivo?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar'
            }).then(res => {
                if (res.isConfirmed) {
                    const fd = new FormData();
                    fd.append('id', idPaciente);
                    fd.append('file', file);
                    fetch('delete_exam.php', {
                            method: 'POST',
                            body: fd
                        })
                        .then(r => r.json())
                        .then(resp => {
                            if (resp.success) {
                                Swal.fire('Eliminado', '', 'success').then(() => location.reload());
                            } else {
                                Swal.fire('Error', resp.message || 'Ocurrió un error', 'error');
                            }
                        })
                        .catch(() => Swal.fire('Error', 'Ocurrió un error', 'error'));
                }
            });
        });
    });
    <?php endif; ?>
</script>
<?php include_once '../includes/modalEvaluacion.php'; ?>
<?php include_once '../includes/modalProgreso.php'; ?>
<?php include_once '../includes/modalHistorial.php'; ?>

<?php include_once '../includes/footer.php'; ?>