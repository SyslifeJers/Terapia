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
                $result = $conn->query("SELECT id_cita, fecha, hora FROM Cita WHERE fecha >= CURDATE() ORDER BY fecha ASC LIMIT 15");
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
                                        <h3 class="nk-block-title page-title">Expendientes</h3>
                                        <div class="nk-block-des text-soft">
                                            <p>Bienvenido al panel de gestión de expedientes.</p>
                                        </div>
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
                                                        <div class="nk-tb-col tb-col-md"><span>Fecha</span></div>
                                                        <div class="nk-tb-col tb-col-md"><span>Hora</span></div>
                                                    </div>
                                                    <?php foreach ($citasProximas as $cita):
                                                        $dt = new DateTime(($cita['fecha'] ?? '') . ' ' . ($cita['hora'] ?? ''), new DateTimeZone('America/Mexico_City'));
                                                        $fecha = $dt->format('Y-m-d');
                                                        $hora  = $dt->format('H:i');
                                                    ?>
                                                    <div class="nk-tb-item">
                                                        <div class="nk-tb-col"><span class="tb-lead"><?php echo htmlspecialchars($cita['id_cita'] ?? ''); ?></span></div>
                                                        <div class="nk-tb-col tb-col-md"><span><?php echo htmlspecialchars($fecha); ?></span></div>
                                                        <div class="nk-tb-col tb-col-md"><span><?php echo htmlspecialchars($hora); ?></span></div>
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
