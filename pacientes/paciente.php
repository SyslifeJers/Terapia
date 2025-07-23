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

    $grafica_data = [
        'primera' => null,
        'ultima' => null,
        'promedio' => ['participacion' => 0, 'atencion' => 0, 'tarea_casa' => 0],
    ];

    $sql = "SELECT participacion, atencion, tarea_casa, fecha_valoracion 
        FROM exp_valoraciones_sesion 
        WHERE id_nino = ?
        ORDER BY fecha_valoracion ASC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    $total = 0;
    $suma = ['participacion' => 0, 'atencion' => 0, 'tarea_casa' => 0];

    while ($row = $result->fetch_assoc()) {
        if (!$grafica_data['primera']) {
            $grafica_data['primera'] = $row;
        }
        $grafica_data['ultima'] = $row;

        $suma['participacion'] += (int)$row['participacion'];
        $suma['atencion'] += (int)$row['atencion'];
        $suma['tarea_casa'] += (int)$row['tarea_casa'];
        $total++;
    }

    if ($total > 0) {
        $grafica_data['promedio'] = [
            'participacion' => round($suma['participacion'] / $total, 2),
            'atencion' => round($suma['atencion'] / $total, 2),
            'tarea_casa' => round($suma['tarea_casa'] / $total, 2),
            'fecha_valoracion' => 'Promedio'
        ];
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
                                        <li><span>0</span><span>Exámenes</span></li>
                                    </ul>
                                    <div class="team-view">
                                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalForm">Nueva evaluación</button>
                                    </div>
                                    <div class="team-view mt-4">
                                        <h6>Promedio de las últimas 15 evaluaciones</h6>
                                    </div>

                                    <div class="team-statistics">

                                        <p>Participación: <?php echo round(array_sum(array_column($ultimas_evaluaciones, 'participacion')) / count($ultimas_evaluaciones), 2); ?></p>
                                        <p>Atención: <?php echo round(array_sum(array_column($ultimas_evaluaciones, 'atencion')) / count($ultimas_evaluaciones), 2); ?></p>
                                        <p>Tarea en casa: <?php echo round(array_sum(array_column($ultimas_evaluaciones, 'tarea_casa')) / count($ultimas_evaluaciones), 2); ?></p>

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
            </div>
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

    const labels = ["Participación", "Atención", "Tarea en casa"];

    const dataRadar = {
        labels: labels,
        datasets: [{
                label: "Primera sesión (" + datos.primera.fecha_valoracion + ")",
                data: [
                    parseFloat(datos.primera.participacion),
                    parseFloat(datos.primera.atencion),
                    parseFloat(datos.primera.tarea_casa)
                ],
                borderColor: "rgba(54, 162, 235, 1)",
                backgroundColor: "rgba(54, 162, 235, 0.2)",
                fill: true
            },
            {
                label: "Promedio",
                data: [
                    parseFloat(datos.promedio.participacion),
                    parseFloat(datos.promedio.atencion),
                    parseFloat(datos.promedio.tarea_casa)
                ],
                borderColor: "rgba(75, 192, 192, 1)",
                backgroundColor: "rgba(75, 192, 192, 0.2)",
                fill: true
            },
            {
                label: "Última sesión (" + datos.ultima.fecha_valoracion + ")",
                data: [
                    parseFloat(datos.ultima.participacion),
                    parseFloat(datos.ultima.atencion),
                    parseFloat(datos.ultima.tarea_casa)
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
<?php include_once '../includes/modalEvaluacion.php'; ?>

<?php include_once '../includes/footer.php'; ?>

