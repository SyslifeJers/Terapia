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

                $question_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
                $question_text = '';
                $exam_id = 0;
                $section_id = 0;

                if ($question_id > 0) {
                    $stmt = $conn->prepare("SELECT pregunta, id_seccion FROM exp_preguntas_evaluacion WHERE id_pregunta = ? LIMIT 1");
                    $stmt->bind_param('i', $question_id);
                    $stmt->execute();
                    $res = $stmt->get_result();
                    $row = $res ? $res->fetch_assoc() : null;
                    $question_text = $row['pregunta'] ?? '';
                    $section_id = $row['id_seccion'] ?? 0;
                    $stmt->close();

                    if ($section_id > 0) {
                        $stmt = $conn->prepare("SELECT id_examen FROM exp_secciones_examen WHERE id_seccion = ? LIMIT 1");
                        $stmt->bind_param('i', $section_id);
                        $stmt->execute();
                        $res = $stmt->get_result();
                        $row = $res ? $res->fetch_assoc() : null;
                        $exam_id = $row['id_examen'] ?? 0;
                        $stmt->close();
                    }
                }

                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $action = $_POST['action'] ?? '';
                    switch ($action) {
                        case 'add_option':
                            $text = trim($_POST['option_text'] ?? '');
                            if ($question_id > 0 && $text !== '' && $exam_id > 0) {
                                $stmt = $conn->prepare("INSERT INTO exp_opciones_pregunta (texto, id_exam) VALUES (?, ?)");
                                $stmt->bind_param('si', $text, $exam_id);
                                $stmt->execute();
                                $option_id = $conn->insert_id;
                                $stmt->close();

                                $stmt = $conn->prepare("INSERT INTO exp_pregunta_opcion (id_pregunta, id_opcion) VALUES (?, ?)");
                                $stmt->bind_param('ii', $question_id, $option_id);
                                $stmt->execute();
                                $stmt->close();
                            }
                            break;
                        case 'add_existing_option':
                            $option_id = (int)($_POST['existing_option_id'] ?? 0);
                            if ($question_id > 0 && $option_id > 0) {
                                $stmt = $conn->prepare("INSERT INTO exp_pregunta_opcion (id_pregunta, id_opcion) VALUES (?, ?)");
                                $stmt->bind_param('ii', $question_id, $option_id);
                                $stmt->execute();
                                $stmt->close();
                            }
                            break;
                        case 'delete_option':
                            $option_id = (int)($_POST['option_id'] ?? 0);
                            if ($question_id > 0 && $option_id > 0) {
                                $stmt = $conn->prepare("DELETE FROM exp_pregunta_opcion WHERE id_pregunta = ? AND id_opcion = ?");
                                $stmt->bind_param('ii', $question_id, $option_id);
                                $stmt->execute();
                                $stmt->close();
                            }
                            break;
                    }
                }

                if ($question_id > 0) {
                    $stmt = $conn->prepare("SELECT o.id_opcion, o.texto, o.id_exam FROM exp_opciones_pregunta o JOIN exp_pregunta_opcion po ON o.id_opcion = po.id_opcion WHERE po.id_pregunta = ? ORDER BY o.id_opcion ASC");
                    $stmt->bind_param('i', $question_id);
                    $stmt->execute();
                    $res = $stmt->get_result();
                    $options = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
                    $stmt->close();

                    if ($exam_id > 0) {
                        $stmt = $conn->prepare("SELECT id_opcion, texto FROM exp_opciones_pregunta WHERE id_exam = ? AND id_opcion NOT IN (SELECT id_opcion FROM exp_pregunta_opcion WHERE id_pregunta = ?)");
                        $stmt->bind_param('ii', $exam_id, $question_id);
                        $stmt->execute();
                        $res = $stmt->get_result();
                        $available_options = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
                        $stmt->close();
                    } else {
                        $available_options = [];
                    }
                } else {
                    $options = [];
                    $available_options = [];
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
                                        <h3 class="nk-block-title page-title">Opciones de la pregunta</h3>
                                        <div class="nk-block-des text-soft">
                                            <p><?php echo htmlspecialchars($question_text); ?></p>
                                        </div>
                                    </div><!-- .nk-block-head-content -->
                                </div><!-- .nk-block-between -->
                            </div><!-- .nk-block-head -->
                            <div class="nk-block">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Opción</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($options as $o): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($o['texto']); ?></td>
                                                <td>
                                                    <form method="post" class="d-inline" onsubmit="return confirm('¿Eliminar opción?');">
                                                        <input type="hidden" name="action" value="delete_option">
                                                        <input type="hidden" name="option_id" value="<?php echo $o['id_opcion']; ?>">
                                                        <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($options)): ?>
                                            <tr><td colspan="2">No hay opciones.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                                <form method="post" class="mt-3">
                                    <input type="hidden" name="action" value="add_existing_option">
                                    <div class="input-group">
                                        <select name="existing_option_id" class="form-select form-select-sm" <?php echo empty($available_options) ? 'disabled' : ''; ?>>
                                            <?php foreach ($available_options as $ao): ?>
                                                <option value="<?php echo $ao['id_opcion']; ?>"><?php echo htmlspecialchars($ao['texto']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="submit" class="btn btn-secondary btn-sm" <?php echo empty($available_options) ? 'disabled' : ''; ?>>Agregar existente</button>
                                    </div>
                                    <?php if (empty($available_options)): ?>
                                        <div class="form-text">No hay opciones existentes para este examen.</div>
                                    <?php endif; ?>
                                </form>
                                <form method="post" class="mt-3">
                                    <input type="hidden" name="action" value="add_option">
                                    <div class="input-group">
                                        <input type="text" name="option_text" class="form-control form-control-sm" placeholder="Nueva opción">
                                        <button type="submit" class="btn btn-primary btn-sm">Agregar opción</button>
                                    </div>
                                </form>
                                <div class="mt-3">
                                    <a href="index.php?id=<?php echo $exam_id; ?>" class="btn btn-light">Regresar</a>
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
