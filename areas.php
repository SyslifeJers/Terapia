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

                $areas = [];
                $result = $conn->query("SELECT id_area, nombre_area, descripcion FROM exp_areas_evaluacion ORDER BY nombre_area ASC");
                if ($result) {
                    $areas = $result->fetch_all(MYSQLI_ASSOC);
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
                                        <h3 class="nk-block-title page-title">Áreas</h3>
                                        <div class="nk-block-des text-soft">
                                            <p>Listado de áreas de evaluación.</p>
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
                                                    <th>Nombre</th>
                                                    <th>Descripción</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach ($areas as $a): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($a['id_area'] ?? ''); ?></td>
                                                    <td><?php echo htmlspecialchars($a['nombre_area'] ?? ''); ?></td>
                                                    <td><?php echo htmlspecialchars($a['descripcion'] ?? ''); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                            <?php if (empty($areas)): ?>
                                                <tr><td colspan="3">No hay áreas.</td></tr>
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
