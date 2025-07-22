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

                $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
                $paciente = [];

                if ($id > 0) {
                    $stmt = $conn->prepare("SELECT id_nino, nombre, edad, programa, tx, responsable, Observacion, FechaIngreso FROM nino WHERE id_nino = ? LIMIT 1");
                    $stmt->bind_param('i', $id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $paciente = $result ? $result->fetch_assoc() : [];
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
                                        <h3 class="nk-block-title page-title">Detalle de Paciente</h3>
                                        <div class="nk-block-des text-soft">
                                            <p>Información del paciente.</p>
                                        </div>
                                    </div><!-- .nk-block-head-content -->
                                </div><!-- .nk-block-between -->
                            </div><!-- .nk-block-head -->
                            <div class="nk-block">
                                <div class="card card-full">
                                    <div class="card-inner">
                                    <?php if (!empty($paciente)): ?>
                                        <table class="table table-bordered">
                                            <tbody>
                                                <tr><th>ID</th><td><?php echo htmlspecialchars($paciente['id_nino'] ?? ''); ?></td></tr>
                                                <tr><th>Nombre</th><td><?php echo htmlspecialchars($paciente['nombre'] ?? ''); ?></td></tr>
                                                <tr><th>Edad</th><td><?php echo htmlspecialchars($paciente['edad'] ?? ''); ?></td></tr>
                                                <tr><th>Programa</th><td><?php echo htmlspecialchars($paciente['programa'] ?? ''); ?></td></tr>
                                                <tr><th>Tx</th><td><?php echo htmlspecialchars($paciente['tx'] ?? ''); ?></td></tr>
                                                <tr><th>Responsable</th><td><?php echo htmlspecialchars($paciente['responsable'] ?? ''); ?></td></tr>
                                                <tr><th>Observación</th><td><?php echo htmlspecialchars($paciente['Observacion'] ?? ''); ?></td></tr>
                                                <tr><th>Fecha Ingreso</th><td><?php
                                                    if (!empty($paciente['FechaIngreso'])) {
                                                        $dt = new DateTime($paciente['FechaIngreso'], new DateTimeZone('America/Mexico_City'));
                                                        echo htmlspecialchars($dt->format('Y-m-d H:i'));
                                                    }
                                                ?></td></tr>
                                            </tbody>
                                        </table>
                                    <?php else: ?>
                                        <p>No se encontró el paciente.</p>
                                    <?php endif; ?>
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
