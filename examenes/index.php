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

                $exam_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
                $exam_name = '';
                if ($exam_id > 0) {
                    $stmt = $conn->prepare("SELECT nombre_examen FROM exp_examenes WHERE id_examen = ? LIMIT 1");
                    $stmt->bind_param('i', $exam_id);
                    $stmt->execute();
                    $res = $stmt->get_result();
                    $row = $res ? $res->fetch_assoc() : null;
                    $exam_name = $row['nombre_examen'] ?? '';
                    $stmt->close();
                }

                $sections = [];
                if ($exam_id > 0) {
                    $stmt = $conn->prepare("SELECT id_seccion, nombre_seccion FROM exp_secciones_examen WHERE id_examen = ? ORDER BY nombre_seccion ASC");
                    $stmt->bind_param('i', $exam_id);
                    $stmt->execute();
                    $res = $stmt->get_result();
                    if ($res) {
                        $sections = $res->fetch_all(MYSQLI_ASSOC);
                        foreach ($sections as &$s) {
                            $stmtQ = $conn->prepare("SELECT id_pregunta, pregunta FROM exp_preguntas_evaluacion WHERE id_seccion = ? ORDER BY id_pregunta ASC");
                            $stmtQ->bind_param('i', $s['id_seccion']);
                            $stmtQ->execute();
                            $resQ = $stmtQ->get_result();
                            $s['preguntas'] = $resQ ? $resQ->fetch_all(MYSQLI_ASSOC) : [];
                            $stmtQ->close();
                        }
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
                                        <h3 class="nk-block-title page-title">Secciones</h3>
                                        <div class="nk-block-des text-soft">
                                            <p>Secciones y preguntas del examen: <?php echo htmlspecialchars($exam_name); ?></p>
                                        </div>
                                    </div><!-- .nk-block-head-content -->
                                </div><!-- .nk-block-between -->
                            </div><!-- .nk-block-head -->
                            <div class="nk-block">
                                <div class="row g-gs">
                                    <?php foreach ($sections as $s): ?>
                                        <div class="col-md-6">
                                            <div class="card card-full">
                                                <div class="card-inner">
                                                    <h5 class="card-title"><?php echo htmlspecialchars($s['nombre_seccion']); ?></h5>
                                                    <?php if (!empty($s['preguntas'])): ?>
                                                        <ul class="list-group list-group-flush">
                                                            <?php foreach ($s['preguntas'] as $p): ?>
                                                                <li class="list-group-item"><?php echo htmlspecialchars($p['pregunta']); ?></li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    <?php else: ?>
                                                        <p>No hay preguntas.</p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                    <?php if (empty($sections)): ?>
                                        <p>No hay secciones.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- content @e -->

            </div>
            <!-- wrap @e -->
       <?php
       include_once '../includes/footer.php';
       ?>
