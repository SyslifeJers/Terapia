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

                $evaluaciones = [];
                $result = $conn->query("SELECT id_evaluacion, id_nino, id_usuario, id_area, fecha, observaciones FROM exp_evaluaciones ORDER BY fecha DESC");
                if ($result) {
                    $evaluaciones = $result->fetch_all(MYSQLI_ASSOC);
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
                                        <h3 class="nk-block-title page-title">Evaluaciones</h3>
                                        <div class="nk-block-des text-soft">
                                            <p>Listado de evaluaciones realizadas.</p>
                                        </div>
                                    </div><!-- .nk-block-head-content -->
                                </div><!-- .nk-block-between -->
                            </div><!-- .nk-block-head -->
                            <div class="nk-block">
                                <div class="card card-full">
                                    <div class="card-inner table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Niño</th>
                                                    <th>Usuario</th>
                                                    <th>Área</th>
                                                    <th>Fecha</th>
                                                    <th>Observaciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach ($evaluaciones as $e):
                                                $dt = new DateTime($e['fecha'] ?? '', new DateTimeZone('America/Mexico_City'));
                                                $fecha = $dt->format('Y-m-d');
                                            ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($e['id_evaluacion'] ?? ''); ?></td>
                                                    <td><?php echo htmlspecialchars($e['id_nino'] ?? ''); ?></td>
                                                    <td><?php echo htmlspecialchars($e['id_usuario'] ?? ''); ?></td>
                                                    <td><?php echo htmlspecialchars($e['id_area'] ?? ''); ?></td>
                                                    <td><?php echo htmlspecialchars($fecha); ?></td>
                                                    <td><?php echo htmlspecialchars($e['observaciones'] ?? ''); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                            <?php if (empty($evaluaciones)): ?>
                                                <tr><td colspan="6">No hay evaluaciones.</td></tr>
                                            <?php endif; ?>
                                            </tbody>
                                        </table>
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
