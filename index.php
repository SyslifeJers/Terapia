<?php
include_once 'includes/head.php';
date_default_timezone_set('America/Mexico_City');
?>
            <!-- sidebar @e -->
            <!-- wrap @s -->
            <div class="nk-wrap ">
                <!-- main header @s -->
            <?php
                include_once 'includes/menu_superior.php';

                // Conexion a la base de datos
                require_once 'database/conexion.php';
                $db = new Database();
                $conn = $db->getConnection();

                $userId = isset($_SESSION['id']) ? (int) $_SESSION['id'] : 0;

                $pacientes  = $conn->query("SELECT COUNT(*) as total FROM nino")->fetch_assoc()['total'] ?? 0;
                $citas      = $userId > 0 ? ($conn->query("SELECT COUNT(*) as total FROM Cita WHERE IdUsuario = {$userId}")->fetch_assoc()['total'] ?? 0) : 0;
                $areas      = $conn->query("SELECT COUNT(*) as total FROM exp_areas_evaluacion")->fetch_assoc()['total'] ?? 0;
                $evaluaciones = $conn->query("SELECT COUNT(*) as total FROM exp_evaluaciones")->fetch_assoc()['total'] ?? 0;

                $ninos = [];
                if ($userId > 0) {
                    $result2 = $conn->query("SELECT
    b.id,
    UPPER(b.name) AS name
FROM Cita a
INNER JOIN nino b ON a.IdNino = b.id
WHERE a.IdUsuario = {$userId}
GROUP BY b.id, b.name
ORDER BY b.name DESC;");
                    if ($result2) {
                        $ninos = $result2->fetch_all(MYSQLI_ASSOC);
                    }
                }

                $citasProximas = [];
                $citasCalendario = [];
                if ($userId > 0) {
                    $result = $conn->query("SELECT Cita.Id, Programado, nino.name, nino.id AS id_nino FROM Cita
                INNER JOIN nino ON Cita.IdNino = nino.id
                 WHERE Cita.IdUsuario = {$userId} AND Programado >= CURDATE() ORDER BY Programado ASC LIMIT 15");
                    if ($result) {
                        $citasProximas = $result->fetch_all(MYSQLI_ASSOC);
                    }

                    $resultCalendario = $conn->query("SELECT Cita.Id, Programado, nino.name, nino.id AS id_nino FROM Cita
                INNER JOIN nino ON Cita.IdNino = nino.id
                 WHERE Cita.IdUsuario = {$userId} ORDER BY Programado ASC");
                    if ($resultCalendario) {
                        $citasCalendario = $resultCalendario->fetch_all(MYSQLI_ASSOC);
                    }
                }

                $eventosCalendario = [];
                if (!empty($citasCalendario)) {
                    $tz = new DateTimeZone('America/Mexico_City');
                    foreach ($citasCalendario as $citaCalendario) {
                        if (empty($citaCalendario['Programado']) || empty($citaCalendario['id_nino'])) {
                            continue;
                        }

                        try {
                            $fechaEvento = new DateTime($citaCalendario['Programado'], $tz);
                        } catch (Exception $e) {
                            continue;
                        }

                        $eventosCalendario[] = [
                            'id' => $citaCalendario['Id'],
                            'title' => ucwords(strtolower($citaCalendario['name'] ?? 'Cita')),
                            'start' => $fechaEvento->format('Y-m-d\TH:i:s'),
                            'url' => 'pacientes/paciente.php?id=' . urlencode((string) $citaCalendario['id_nino']),
                        ];
                    }
                }

                $db->closeConnection();
                ?>
                <!-- main header @e -->
                <!-- content @s -->
                <div class="nk-content nk-content-fluid">
                    <div class="container-xl wide-xl">
                        <div class="nk-content-body">
                            <div class="nk-block-head nk-block-head-sm">
                                <div class="nk-block-between">
                                    <div class="nk-block-head-content">
                                        <?php
                                        $frases = [
                                            "¡Hoy es un gran día para ayudar a alguien a crecer!",
                                            "Cada pequeño paso cuenta en el camino al éxito.",
                                            "Tu dedicación transforma vidas.",
                                            "La empatía es la clave para sanar corazones.",
                                            "El cambio comienza con una sonrisa.",
                                            "Tu trabajo deja huella en el mundo.",
                                            "La paciencia es el arte de la esperanza.",
                                            "Cada sesión es una oportunidad para aprender.",
                                            "El esfuerzo de hoy será el logro de mañana.",
                                            "La confianza se construye con cada palabra.",
                                            "Ayudar es el mayor acto de generosidad.",
                                            "La motivación es contagiosa, compártela.",
                                            "El futuro se crea con acciones presentes.",
                                            "Tu vocación inspira a quienes te rodean.",
                                            "La perseverancia vence cualquier obstáculo.",
                                            "El respeto abre puertas al entendimiento.",
                                            "La escucha activa es el primer paso para sanar.",
                                            "El apoyo emocional es un regalo invaluable.",
                                            "La gratitud transforma la rutina en alegría.",
                                            "Cada niño es una historia de esperanza.",
                                            "El aprendizaje es un viaje, no un destino.",
                                            "La comprensión abre caminos a la solución.",
                                            "Un gesto amable puede cambiar un día.",
                                            "La creatividad es la puerta a nuevas posibilidades.",
                                            "El amor propio es el primer paso para crecer.",
                                            "La resiliencia se fortalece con cada desafío.",
                                            "El acompañamiento hace la diferencia.",
                                            "La honestidad construye relaciones sólidas.",
                                            "La alegría se multiplica cuando se comparte.",
                                            "El optimismo ilumina el camino.",
                                            "La curiosidad impulsa el descubrimiento.",
                                            "El respeto por la diversidad enriquece a todos.",
                                            "La confianza en uno mismo es fundamental.",
                                            "El apoyo familiar potencia el desarrollo.",
                                            "La comunicación efectiva une corazones.",
                                            "El esfuerzo constante trae resultados.",
                                            "La esperanza es el motor del cambio.",
                                            "La solidaridad crea comunidades fuertes.",
                                            "El aprendizaje colaborativo es poderoso.",
                                            "La humildad permite crecer y aprender.",
                                            "El reconocimiento motiva a seguir adelante."
                                        ];
                                        $fraseAleatoria = $frases[array_rand($frases)];
                                        ?>
                                        <h3 class="nk-block-title page-title"><?php echo 'Hola '. htmlspecialchars($_SESSION['name']); ?></h3>
                                        <h4><?php echo $fraseAleatoria; ?></h4>
                                    </div><!-- .nk-block-head-content -->
                                    <div class="nk-block-head-content">
                                        <div class="toggle-wrap nk-block-tools-toggle">
                                            <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                                            <div class="toggle-expand-content" data-content="pageMenu">
                                                <ul class="nk-block-tools g-3">
                                                   <!-- <li class="nk-block-tools-opt"><a href="#" class="btn btn-primary"><em class="icon ni ni-reports"></em><span>Reports</span></a></li>-->
                                                </ul>
                                            </div>
                                        </div>
                                    </div><!-- .nk-block-head-content -->
                                </div><!-- .nk-block-between -->
                            </div><!-- .nk-block-head -->
                            <div class="nk-block">
                                <div class="row g-gs">
                                    <div class="col-12">
                                        <div class="card card-full">
                                            <div class="card-inner">
                                                <div class="card-title-group align-items-center">
                                                    <div class="card-title">
                                                        <h6 class="title">Calendario de citas</h6>
                                                    </div>
                                                    <div class="card-tools">
                                                        <a href="#" class="btn btn-sm btn-primary" id="calendar-day-view">Vista del día</a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-inner pt-0">
                                                <div id="citas-calendar" class="nk-calendar"></div>
                                            </div>
                                        </div>
                                    </div><!-- .col -->

                                    <div class="col-lg-3 col-sm-6">
                                        <div class="card h-100 bg-white border border-primary shadow-sm">
                                            <div class="card-inner text-center py-4">
                                                <div class="fs-2 text-primary mb-1"><?php echo $pacientes; ?></div>
                                                <h6 class="text-muted">Pacientes</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-sm-6">
                                        <div class="card h-100 bg-white border border-info shadow-sm">
                                            <div class="card-inner text-center py-4">
                                                <div class="fs-2 text-info mb-1"><?php echo $citas; ?></div>
                                                <h6 class="text-muted">Citas</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-sm-6">
                                        <div class="card h-100 bg-white border border-warning shadow-sm">
                                            <div class="card-inner text-center py-4">
                                                <div class="fs-2 text-warning mb-1"><?php echo $areas; ?></div>
                                                <h6 class="text-muted">Áreas</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-sm-6">
                                        <div class="card h-100 bg-white border border-danger shadow-sm">
                                            <div class="card-inner text-center py-4">
                                                <div class="fs-2 text-danger mb-1"><?php echo $evaluaciones; ?></div>
                                                <h6 class="text-muted">Evaluaciones</h6>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xxl-12 col-md-12">
                                        <div class="card card-full">
                                            <div class="card-inner">
                                                <div class="card-title-group">
                                                    <div class="card-title">
                                                        <h6 class="title">Mis pacientes</h6>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-inner pt-0">
                                                <div class="table-responsive">
                                                    <table class="table table-striped table-hover align-middle">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th class="text-center" style="width: 72px;">#</th>
                                                                <th>Nombre</th>
                                                                <th class="text-end" style="width: 140px;">Acciones</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($ninos as $index => $nino):
                                                                $detallePacienteUrl = 'pacientes/paciente.php?id=' . urlencode((string)($nino['id'] ?? ''));
                                                            ?>
                                                                <tr>
                                                                    <td class="text-center fw-semibold"><?php echo $index + 1; ?></td>
                                                                    <td class="fw-medium text-capitalize text-truncate" style="max-width: 260px;">
                                                                        <?php echo htmlspecialchars($nino['name']); ?>
                                                                    </td>
                                                                    <td class="text-end">
                                                                        <a class="btn btn-sm btn-outline-primary" href="<?php echo htmlspecialchars($detallePacienteUrl, ENT_QUOTES, 'UTF-8'); ?>">
                                                                            <em class="icon ni ni-eye"></em>
                                                                            <span class="d-none d-md-inline">Ver más</span>
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                            <?php if (empty($ninos)): ?>
                                                                <tr>
                                                                    <td colspan="3" class="text-center text-muted py-4">
                                                                        <div class="d-flex flex-column align-items-center">
                                                                            <em class="icon ni ni-users fs-2 text-primary mb-1"></em>
                                                                            <span>No tienes pacientes asignados.</span>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            <?php endif; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div><!-- .card -->
                                    </div><!-- .col -->



                                </div><!-- .row -->
                            </div><!-- .nk-block -->
                        </div>
                    </div>
                </div>
                <!-- content @e -->

            </div>
            <!-- wrap @e -->
            <script src="/assets/js/libs/fullcalendar.js?ver=3.3.0"></script>
            <script>
            document.addEventListener('DOMContentLoaded', function () {
                var calendarEl = document.getElementById('citas-calendar');
                if (!calendarEl || typeof FullCalendar === 'undefined') {
                    return;
                }

                var calendarOptions = {
                    initialView: 'timeGridDay',
                    timeZone: 'local',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                    },
                    buttonText: {
                        today: 'Hoy',
                        month: 'Mes',
                        week: 'Semana',
                        day: 'Día',
                        list: 'Agenda'
                    },
                    eventTimeFormat: {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: true
                    },
                    events: <?php echo json_encode($eventosCalendario, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>,
                    eventBackgroundColor: '#E3F2FD',
                    eventBorderColor: '#4D8FEA',
                    eventTextColor: '#0F172A',
                    eventClick: function (info) {
                        if (info.event.url) {
                            info.jsEvent.preventDefault();
                            window.location.href = info.event.url;
                        }
                    }
                };

                if (FullCalendar.globalLocales && Array.isArray(FullCalendar.globalLocales)) {
                    var hasSpanishLocale = FullCalendar.globalLocales.some(function (locale) {
                        return locale.code === 'es';
                    });
                    if (hasSpanishLocale) {
                        calendarOptions.locale = 'es';
                    }
                }

                var calendar = new FullCalendar.Calendar(calendarEl, calendarOptions);

                calendar.render();

                var dayViewButton = document.getElementById('calendar-day-view');
                if (dayViewButton) {
                    dayViewButton.addEventListener('click', function (event) {
                        event.preventDefault();
                        calendar.changeView('timeGridDay');
                    });
                }
            });
            </script>
       <?php
include_once 'includes/footer.php';
?>
