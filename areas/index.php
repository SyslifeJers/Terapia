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

                // Conexion a la base de datos
                require_once '../database/conexion.php';
                $db = new Database();
                $conn = $db->getConnection();

                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $area_id = (int)($_POST['id_area'] ?? 0);
                    $nombre_examen = trim($_POST['nombre_examen'] ?? '');
                    $id_usuario = intval($_SESSION['id'] ?? 0);
                    if ($area_id > 0 && $nombre_examen !== '' && $id_usuario > 0) {
                        $stmt = $conn->prepare("INSERT INTO exp_examenes (id_area, id_usuario, nombre_examen) VALUES (?, ?, ?)");
                        $stmt->bind_param('iis', $area_id, $id_usuario, $nombre_examen);
                        $stmt->execute();
                        $stmt->close();
                    }
                }

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
                                    <div class="card-inner">
                                        <div class="d-flex align-items-center mb-3">
                                            <h5 class="title mb-0 me-3">Áreas</h5>
                                            <a href="/areas/form.php" class="btn btn-outline-primary btn-sm">
                                                <em class="icon ni ni-plus"></em> Nueva área
                                            </a>
                                        </div>
                                        <hr class="my-4">
                                        <form method="post" class="mb-4">
                                            <div class="row g-2 align-items-center">
                                                <div class="col-md-5">
                                                    <select name="id_area" class="form-select form-select-sm" required>
                                                        <option value="">Seleccione un área</option>
                                                        <?php foreach ($areas as $a): ?>
                                                            <option value="<?php echo $a['id_area']; ?>"><?php echo htmlspecialchars($a['nombre_area']); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-5">
                                                    <input type="text" name="nombre_examen" class="form-control form-control-sm" placeholder="Nombre del examen" required>
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="submit" class="btn btn-primary btn-sm w-100">Agregar examen</button>
                                                </div>
                                            </div>
                                        </form>
                                        <div id="areaFiles" class="nk-files nk-files-view-grid">
                                            <div class="nk-files-list">
                                                <?php foreach ($areas as $a): ?>
                                                    <div class="nk-file-item nk-file">
                                                        <div class="nk-file-info">
                                                            <div class="nk-file-title">
                                                                <div class="nk-file-icon">

                                                                    <a class="nk-file-icon-link" href="/areas/examenes.php?id=<?php echo urlencode($a['id_area']); ?>">
                                                                        <span class="nk-file-icon-type"><em class="icon ni ni-folder"></em></span>
                                                                    </a>
                                                                </div>
                                                                <div class="nk-file-name">
                                                                    <div class="nk-file-name-text">
                                                                        <a href="/areas/examenes.php?id=<?php echo urlencode($a['id_area']); ?>" class="title"><?php echo htmlspecialchars($a['nombre_area'] ?? ''); ?></a>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <ul class="nk-file-desc">
                                                                <li class="date">ID: <?php echo htmlspecialchars($a['id_area'] ?? ''); ?></li>
                                                                <li class="members"><?php echo htmlspecialchars($a['descripcion'] ?? ''); ?></li>
                                                            </ul>
                                                        </div>
                                                        <div class="nk-file-actions">
                                                            <div class="dropdown">
                                                                <a href="#" class="dropdown-toggle btn btn-sm btn-icon btn-trigger" data-bs-toggle="dropdown">
                                                                    <em class="icon ni ni-more-h"></em>
                                                                </a>
                                                                <div class="dropdown-menu dropdown-menu-end">
                                                                    <ul class="link-list-plain no-bdr">
                                                                        <li><a href="/areas/form.php?id=<?php echo urlencode($a['id_area']); ?>"><em class="icon ni ni-edit"></em><span>Editar</span></a></li>
                                                                        <?php if ($_SESSION['rol'] != 2): ?>
                                                                        <li><a href="/areas/delete.php?id=<?php echo urlencode($a['id_area']); ?>" onclick="return confirm('¿Eliminar área?');"><em class="icon ni ni-trash"></em><span>Eliminar</span></a></li>
                                                                        <?php endif; ?>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                                <?php if (empty($areas)): ?>
                                                    <p>No hay áreas.</p>
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
