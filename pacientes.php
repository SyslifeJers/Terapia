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

                $perPage = 50;
                $page = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
                $offset = ($page - 1) * $perPage;

                $total = 0;
                $resTotal = $conn->query("SELECT COUNT(*) as total FROM nino");
                if ($resTotal) {
                    $row = $resTotal->fetch_assoc();
                    $total = $row['total'] ?? 0;
                }

                $stmt = $conn->prepare(
                    "SELECT id_nino, nombre, edad, programa, tx, responsable FROM nino ORDER BY nombre ASC LIMIT ?, ?"
                );
                $stmt->bind_param('ii', $offset, $perPage);
                $stmt->execute();
                $result = $stmt->get_result();
                $pacientes = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

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
                                        <h3 class="nk-block-title page-title">Pacientes</h3>
                                        <div class="nk-block-des text-soft">
                                            <p>Listado de pacientes.</p>
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
                                                    <th>Edad</th>
                                                    <th>Programa</th>
                                                    <th>Tx</th>
                                                    <th>Responsable</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach ($pacientes as $p): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($p['id_nino'] ?? ''); ?></td>
                                                    <td><?php echo htmlspecialchars($p['nombre'] ?? ''); ?></td>
                                                    <td><?php echo htmlspecialchars($p['edad'] ?? ''); ?></td>
                                                    <td><?php echo htmlspecialchars($p['programa'] ?? ''); ?></td>
                                                    <td><?php echo htmlspecialchars($p['tx'] ?? ''); ?></td>
                                                    <td><?php echo htmlspecialchars($p['responsable'] ?? ''); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                            <?php if (empty($pacientes)): ?>
                                                <tr><td colspan="6">No hay pacientes.</td></tr>
                                            <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="card-inner">
                                        <ul class="pagination justify-content-center">
                                            <?php $totalPages = max(1, ceil($total / $perPage));
                                            for ($i = 1; $i <= $totalPages; $i++):
                                                $active = ($i === $page) ? 'active' : ''; ?>
                                                <li class="page-item <?php echo $active; ?>">
                                                    <a class="page-link" href="?pagina=<?php echo $i; ?>"><?php echo $i; ?></a>
                                                </li>
                                            <?php endfor; ?>
                                        </ul>
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
