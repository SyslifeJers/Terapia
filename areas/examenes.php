<?php
include_once '../includes/head.php';
date_default_timezone_set('America/Mexico_City');
?>
            <!-- sidebar @e -->
            <!-- wrap @s -->
            <div class="nk-wrap ">
                <!-- main header @s -->
            <?php
                include_once '../includes/menu_superior.php';

                require_once '../database/conexion.php';
                $db = new Database();
                $conn = $db->getConnection();

                $area_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
                $area_nombre = '';
                if ($area_id > 0) {
                    $stmt = $conn->prepare("SELECT nombre_area FROM exp_areas_evaluacion WHERE id_area = ? LIMIT 1");
                    $stmt->bind_param('i', $area_id);
                    $stmt->execute();
                    $res = $stmt->get_result();
                    $row = $res ? $res->fetch_assoc() : null;
                    $area_nombre = $row['nombre_area'] ?? '';
                    $stmt->close();
                }

                $examenes = [];
                if ($area_id > 0) {
                    $stmt = $conn->prepare("SELECT id_examen, nombre_examen FROM exp_examenes WHERE id_area = ? ORDER BY nombre_examen ASC");
                    $stmt->bind_param('i', $area_id);
                    $stmt->execute();
                    $res = $stmt->get_result();
                    if ($res) {
                        $examenes = $res->fetch_all(MYSQLI_ASSOC);
                    }
                    $stmt->close();
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
                                        <h3 class="nk-block-title page-title">Exámenes</h3>
                                        <div class="nk-block-des text-soft">
                                            <p>Exámenes del área: <?php echo htmlspecialchars($area_nombre); ?></p>
                                        </div>
                                    </div><!-- .nk-block-head-content -->
                                    <div class="nk-block-head-content">
                                        <a href="javascript:history.back()" class="btn btn-secondary"><em class="icon ni ni-arrow-left"></em><span>Atrás</span></a>
                                    </div>
                                </div><!-- .nk-block-between -->
                            </div><!-- .nk-block-head -->
                            <div class="nk-block">
                                <div class="card card-full">
                                    <div class="card-inner">
                                        <div class="d-flex align-items-center mb-3">
                                            <h5 class="title mb-0 me-3">Exámenes</h5>
                                        </div>
                                        <hr class="my-4">
                                        <div id="examFiles" class="nk-files nk-files-view-grid">
                                            <div class="nk-files-list">
                                                <?php foreach ($examenes as $e): ?>
                                                    <div class="nk-file-item nk-file">
                                                        <div class="nk-file-info">
                                                            <div class="nk-file-title">
                                                                <div class="nk-file-icon">
                                                                    <a class="nk-file-icon-link" href="/examenes/index.php?id=<?php echo urlencode($e['id_examen']); ?>">
                                                                        <span class="nk-file-icon-type"><em class="icon ni ni-file-text"></em></span>
                                                                    </a>
                                                                </div>
                                                                <div class="nk-file-name">
                                                                    <div class="nk-file-name-text">
                                                                        <a href="/examenes/index.php?id=<?php echo urlencode($e['id_examen']); ?>" class="title"><?php echo htmlspecialchars($e['nombre_examen']); ?></a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <ul class="nk-file-desc">
                                                                <li class="date">ID: <?php echo htmlspecialchars($e['id_examen']); ?></li>
                                                            </ul>
                                                        </div>
                                                        <?php if ($_SESSION['rol'] != 2): ?>
                                                        <div class="nk-file-actions">
                                                            <div class="dropdown">
                                                                <a href="#" class="dropdown-toggle btn btn-sm btn-icon btn-trigger" data-bs-toggle="dropdown">
                                                                    <em class="icon ni ni-more-h"></em>
                                                                </a>
                                                                <div class="dropdown-menu dropdown-menu-end">
                                                                    <ul class="link-list-plain no-bdr">
                                                                        <li><a href="/areas/delete_examen.php?id=<?php echo urlencode($e['id_examen']); ?>&area_id=<?php echo urlencode($area_id); ?>" onclick="return confirm('¿Eliminar examen?');"><em class="icon ni ni-trash"></em><span>Eliminar</span></a></li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endforeach; ?>
                                                <?php if (empty($examenes)): ?>
                                                    <p>No hay exámenes.</p>
                                                <?php endif; ?>
                                            </div>
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
       include_once '../includes/footer.php';
       ?>
