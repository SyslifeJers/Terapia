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
                                    <div class="nk-block-head-content">
                                        <a href="subir_evaluacion.php" class="btn btn-primary">Nuevo archivo de evaluación</a>
                                    </div>
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
                            <div class="nk-block">
                                <h4 class="nk-block-title">Archivos de evaluaciones</h4>
                                <div class="card card-full">
                                    <div class="card-inner table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Título</th>
                                                    <th>Fecha</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $dir = __DIR__ . '/uploads/evaluaciones';
                                                $items = [];
                                                if (is_dir($dir)) {
                                                    foreach (scandir($dir) as $d) {
                                                        if ($d === '.' || $d === '..') continue;
                                                        $metaFile = $dir . '/' . $d . '/meta.json';
                                                        if (is_file($metaFile)) {
                                                            $meta = json_decode(file_get_contents($metaFile), true);
                                                            $items[] = ['id' => $d, 'titulo' => $meta['titulo'] ?? $d, 'fecha' => $meta['fecha'] ?? ''];
                                                        }
                                                    }
                                                }
                                                foreach ($items as $it):
                                                ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($it['titulo']); ?></td>
                                                    <td><?php echo htmlspecialchars($it['fecha']); ?></td>
                                                    <td><a href="ver_evaluacion.php?id=<?php echo urlencode($it['id']); ?>" class="btn btn-sm btn-info">Ver</a></td>
                                                </tr>
                                                <?php endforeach; ?>
                                                <?php if (empty($items)): ?>
                                                <tr><td colspan="3">No hay archivos de evaluaciones.</td></tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- content @e -->

            </div>
            <!-- wrap @e -->
       <?php
include_once 'includes/footer.php';
?>
