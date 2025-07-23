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

                $citasProximas = [];
                $result = $conn->query("SELECT Cita.Id, Programado, nino.name, nino.id as id_nino FROM Cita
                INNER JOIN nino ON Cita.IdNino = nino.id
                 WHERE Programado >= CURDATE() ORDER BY Programado ASC");
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
                                        <h3 class="nk-block-title page-title">Citas</h3>
                                        <div class="nk-block-des text-soft">
                                            <p>Listado de citas próximas.</p>
                                        </div>
                                    </div><!-- .nk-block-head-content -->
                                </div><!-- .nk-block-between -->
                            </div><!-- .nk-block-head -->
                            <div class="nk-block">
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
                                                <div class="nk-tb-col tb-col-md"><span>Nombre</span></div>
                                                <div class="nk-tb-col tb-col-md"><span>Ver más</span></div>
                                            </div>
                                            <?php foreach ($citasProximas as $cita):
                                                $dt = new DateTime(($cita['Programado'] ?? '') , new DateTimeZone('America/Mexico_City'));
                                                $fecha = $dt->format('Y-m-d');
                                                $hora  = $dt->format('H:i');
                                            ?>
                                            <div class="nk-tb-item">
                                                <div class="nk-tb-col"><span class="tb-lead"><?php echo htmlspecialchars($cita['Id']); ?></span></div>
                                                <div class="nk-tb-col tb-col-md"><span><?php echo htmlspecialchars($fecha); ?></span></div>
                                                <div class="nk-tb-col tb-col-md"><span><?php echo htmlspecialchars($hora); ?></span></div>
                                                <div class="nk-tb-col tb-col-md"><span><?php echo htmlspecialchars($cita['name'] ?? ''); ?></span></div>
                                                <div class="nk-tb-col tb-col-md"><a class="btn btn-sm btn-primary" href="ver_cita.php?id=<?php echo htmlspecialchars($cita['id_nino']); ?>">Ver</a></div>
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

