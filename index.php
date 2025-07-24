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

                $pacientes  = $conn->query("SELECT COUNT(*) as total FROM nino")->fetch_assoc()['total'] ?? 0;
                $citas      = $conn->query("SELECT COUNT(*) as total FROM Cita")->fetch_assoc()['total'] ?? 0;
                $areas      = $conn->query("SELECT COUNT(*) as total FROM exp_areas_evaluacion")->fetch_assoc()['total'] ?? 0;
                $evaluaciones = $conn->query("SELECT COUNT(*) as total FROM exp_evaluaciones")->fetch_assoc()['total'] ?? 0;

                $citasProximas = [];
                $result = $conn->query("SELECT Cita.Id, Programado, nino.name FROM Cita
                INNER JOIN nino ON Cita.IdNino = nino.id
                 WHERE Programado >= CURDATE() ORDER BY Programado ASC LIMIT 15");
                if ($result) {
                    $citasProximas = $result->fetch_all(MYSQLI_ASSOC);
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
                                    <!-- Tarjetas originales comentadas
                                    <div class="col-lg-3 col-sm-6"> ... </div>
                                    <div class="col-lg-3 col-sm-6"> ... </div>
                                    <div class="col-lg-3 col-sm-6"> ... </div>
                                    <div class="col-lg-3 col-sm-6"> ... </div>
                                    -->
                                    <div class="col-lg-3 col-sm-6">
                                        <div class="card h-100 bg-primary">
                                            <div class="card-inner text-center py-4">
                                                <div class="fs-2 text-white mb-1"><?php echo $pacientes; ?></div>
                                                <h6 class="text-white">Pacientes</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-sm-6">
                                        <div class="card h-100 bg-info">
                                            <div class="card-inner text-center py-4">
                                                <div class="fs-2 text-white mb-1"><?php echo $citas; ?></div>
                                                <h6 class="text-white">Citas</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-sm-6">
                                        <div class="card h-100 bg-warning">
                                            <div class="card-inner text-center py-4">
                                                <div class="fs-2 text-white mb-1"><?php echo $areas; ?></div>
                                                <h6 class="text-white">Áreas</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-sm-6">
                                        <div class="card h-100 bg-danger">
                                            <div class="card-inner text-center py-4">
                                                <div class="fs-2 text-white mb-1"><?php echo $evaluaciones; ?></div>
                                                <h6 class="text-white">Evaluaciones</h6>
                                            </div>
                                        </div>
                                    </div>
                                   
                                    <div class="col-xxl-8">
                                        <div class="card card-full">
                                            <div class="card-inner">
                                                <div class="card-title-group">
                                                    <div class="card-title">
                                                        <h6 class="title">Próximas Citas</h6>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-inner py-0 mt-n2">
                                                <div class="nk-tb-list nk-tb-flush nk-tb-dashed">
                                                    <div class="nk-tb-item nk-tb-head">
                                                        <div class="nk-tb-col"><span>ID</span></div>
                                                        <div class="nk-tb-col "><span>Fecha</span></div>
                                                        <div class="nk-tb-col "><span>Hora</span></div>
                                                        <div class="nk-tb-col "><span>Nombre</span></div>
                                                    </div>
                                                    <?php foreach ($citasProximas as $cita):
                                                        $dt = new DateTime(($cita['Programado'] ?? '') , new DateTimeZone('America/Mexico_City'));
                                                        $fecha = $dt->format('Y-m-d');
                                                        $hora  = $dt->format('H:i');
                                                    ?>
                                                    <div class="nk-tb-item">
                                                        <div class="nk-tb-col"><span class="tb-lead"><?php echo htmlspecialchars($cita['Id'] ?? ''); ?></span></div>
                                                        <div class="nk-tb-col"><span><?php echo htmlspecialchars($fecha); ?></span></div>
                                                        <div class="nk-tb-col"><span><?php echo htmlspecialchars($dt->format('h:i A')); ?></span></div>
                                                        <div class="nk-tb-col"><span><?php echo htmlspecialchars(ucwords(strtolower($cita['name'] ?? ''))); ?></span></div>
                                                    </div>
                                                    <?php endforeach; ?>
                                                    <?php if (empty($citasProximas)): ?>
                                                    <div class="nk-tb-item">
                                                        <div class="nk-tb-col">No hay citas próximas.</div>
                                                    </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div><!-- .card -->
                                    </div><!-- .col -->
                                    <div class="col-xxl-4 col-md-6">
                                        <div class="card card-full">
                                            <div class="card-inner">
                                                <div class="card-title-group">
                                                    <div class="card-title">
                                                        <h6 class="title">Key Statistics</h6>
                                                    </div>
                                                    <div class="card-tools me-n1 mt-n1">
                                                        <div class="dropdown">
                                                            <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                            <div class="dropdown-menu dropdown-menu-sm dropdown-menu-end">
                                                                <ul class="link-list-opt no-bdr">
                                                                    <li><a href="#" class="active"><span>15 Days</span></a></li>
                                                                    <li><a href="#"><span>30 Days</span></a></li>
                                                                    <li><a href="#"><span>3 Months</span></a></li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-inner pt-0">
                                                <ul class="gy-4">
                                                    <li class="border-bottom border-0 border-dashed">
                                                        <div class="mb-1">
                                                            <span class="fs-2 lh-1 mb-1 text-head">85.6K</span>
                                                            <div class="sub-text">Average Like</div>
                                                        </div>
                                                        <div class="align-center">
                                                            <div class="small text-primary me-2">54%</div>
                                                            <div class="progress progress-md rounded-pill w-100 bg-primary-dim">
                                                                <div class="progress-bar bg-primary rounded-pill" data-progress="54"></div>
                                                            </div>
                                                            <div class="dropdown ms-3">
                                                                <a class="dropdown-toggle dropdown-indicator sub-text" href="#" type="button" data-bs-toggle="dropdown" data-bs-offset="0, 10">Dec 22 - Feb 22</a>
                                                                <div class="dropdown-menu dropdown-menu-end text-right">
                                                                    <ul class="link-list-opt">
                                                                        <li><a href="#"><span>Dec 22 - Feb 22</span></a></li>
                                                                        <li><a href="#"><span>Oct 22 - Dec 22</span></a></li>
                                                                        <li><a href="#"><span>Aug 22 - Oct 22</span></a></li>
                                                                        <li><a href="#"><span>Jun 22 - Aug 22</span></a></li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li><!-- li -->
                                                    <li class="border-bottom border-0 border-dashed">
                                                        <div class="mb-1">
                                                            <span class="fs-2 lh-1 mb-1 text-head">42.7K</span>
                                                            <div class="sub-text">Average Comments</div>
                                                        </div>
                                                        <div class="align-center">
                                                            <div class="small text-danger me-2">84%</div>
                                                            <div class="progress progress-md rounded-pill w-100 bg-danger-dim">
                                                                <div class="progress-bar bg-danger rounded-pill" data-progress="84"></div>
                                                            </div>
                                                            <div class="dropdown ms-3">
                                                                <a class="dropdown-toggle dropdown-indicator sub-text" href="#" type="button" data-bs-toggle="dropdown" data-bs-offset="0, 10">Dec 22 - Feb 22</a>
                                                                <div class="dropdown-menu dropdown-menu-end text-right">
                                                                    <ul class="link-list-opt">
                                                                        <li><a href="#"><span>Dec 22 - Feb 22</span></a></li>
                                                                        <li><a href="#"><span>Oct 22 - Dec 22</span></a></li>
                                                                        <li><a href="#"><span>Aug 22 - Oct 22</span></a></li>
                                                                        <li><a href="#"><span>Jun 22 - Aug 22</span></a></li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li><!-- li -->
                                                    <li>
                                                        <div class="mb-1">
                                                            <span class="fs-2 lh-1 mb-1 text-head">25.4K</span>
                                                            <div class="sub-text">Average Shares</div>
                                                        </div>
                                                        <div class="align-center">
                                                            <div class="small text-success me-2">62%</div>
                                                            <div class="progress progress-md rounded-pill w-100 bg-success-dim">
                                                                <div class="progress-bar bg-success rounded-pill" data-progress="62"></div>
                                                            </div>
                                                            <div class="dropdown ms-3">
                                                                <a class="dropdown-toggle dropdown-indicator sub-text" href="#" type="button" data-bs-toggle="dropdown" data-bs-offset="0, 10">Dec 22 - Feb 22</a>
                                                                <div class="dropdown-menu dropdown-menu-end text-right">
                                                                    <ul class="link-list-opt">
                                                                        <li><a href="#"><span>Dec 22 - Feb 22</span></a></li>
                                                                        <li><a href="#"><span>Oct 22 - Dec 22</span></a></li>
                                                                        <li><a href="#"><span>Aug 22 - Oct 22</span></a></li>
                                                                        <li><a href="#"><span>Jun 22 - Aug 22</span></a></li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li><!-- li -->
                                                </ul>
                                            </div>
                                        </div><!-- .card -->
                                    </di                                             v><!-- .col -->
                                   

                                </div><!-- .row -->
                            </div><!-- .nk-block -->
                        </div>
                    </div>
                </div>
                <!-- content @e -->
              
            </div>
            <!-- wrap @e -->
       <?php
include_once 'includes/footer.php'; 
?>
