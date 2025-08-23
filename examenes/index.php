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

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        switch ($action) {
            case 'add_section':
                $name = trim($_POST['section_name'] ?? '');
                if ($name !== '' && $exam_id > 0) {
                    $stmt = $conn->prepare("INSERT INTO exp_secciones_examen (id_examen, nombre_seccion) VALUES (?, ?)");
                    $stmt->bind_param('is', $exam_id, $name);
                    $stmt->execute();
                    $stmt->close();
                }
                break;
            case 'edit_section':
                $section_id = (int)($_POST['section_id'] ?? 0);
                $name = trim($_POST['section_name'] ?? '');
                if ($section_id > 0 && $name !== '') {
                    $stmt = $conn->prepare("UPDATE exp_secciones_examen SET nombre_seccion = ? WHERE id_seccion = ?");
                    $stmt->bind_param('si', $name, $section_id);
                    $stmt->execute();
                    $stmt->close();
                }
                break;
            case 'delete_section':
                if ($_SESSION['rol'] != 2) {
                    $section_id = (int)($_POST['section_id'] ?? 0);
                    if ($section_id > 0) {
                        $stmt = $conn->prepare("DELETE FROM exp_secciones_examen WHERE id_seccion = ?");
                        $stmt->bind_param('i', $section_id);
                        $stmt->execute();
                        $stmt->close();
                    }
                }
                break;
            case 'add_question':
                $section_id = (int)($_POST['section_id'] ?? 0);
                $text = trim($_POST['question_text'] ?? '');
                if ($section_id > 0 && $text !== '') {
                    $stmt = $conn->prepare("INSERT INTO exp_preguntas_evaluacion (id_seccion, pregunta) VALUES (?, ?)");
                    $stmt->bind_param('is', $section_id, $text);
                    $stmt->execute();
                    $stmt->close();
                }
                break;
            case 'edit_question':
                $question_id = (int)($_POST['question_id'] ?? 0);
                $text = trim($_POST['question_text'] ?? '');
                if ($question_id > 0 && $text !== '') {
                    $stmt = $conn->prepare("UPDATE exp_preguntas_evaluacion SET pregunta = ? WHERE id_pregunta = ?");
                    $stmt->bind_param('si', $text, $question_id);
                    $stmt->execute();
                    $stmt->close();
                }
                break;
            case 'delete_question':
                if ($_SESSION['rol'] != 2) {
                    $question_id = (int)($_POST['question_id'] ?? 0);
                    if ($question_id > 0) {
                        $stmt = $conn->prepare("DELETE FROM exp_preguntas_evaluacion WHERE id_pregunta = ?");
                        $stmt->bind_param('i', $question_id);
                        $stmt->execute();
                        $stmt->close();
                    }
                }
                break;
        }
    }

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
                $stmtQ = $conn->prepare("SELECT id_pregunta, pregunta, (SELECT COUNT(*) FROM exp_pregunta_opcion WHERE id_pregunta = exp_preguntas_evaluacion.id_pregunta) AS num_opciones FROM exp_preguntas_evaluacion WHERE id_seccion = ? ORDER BY id_pregunta ASC");
                $stmtQ->bind_param('i', $s['id_seccion']);
                $stmtQ->execute();
                $resQ = $stmtQ->get_result();
                $s['preguntas'] = $resQ ? $resQ->fetch_all(MYSQLI_ASSOC) : [];
                $stmtQ->close();
            }
            unset($s);
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
                    <form method="post" class="mb-4">
                        <input type="hidden" name="action" value="add_section">
                        <div class="input-group">
                            <input type="text" name="section_name" class="form-control form-control-sm" placeholder="Nueva sección">
                            <button type="submit" class="btn btn-primary btn-sm">Agregar sección</button>
                        </div>
                    </form>
                    <div class="row g-gs">
                        <?php foreach ($sections as $s): ?>
                            <div class="col-md-12">
                                <div class="card card-full">
                                    <div class="card-inner">
                                        <div class="mb-2 d-flex align-items-center">
                                            <form method="post" class="d-flex flex-grow-1">
                                                <input type="hidden" name="action" value="edit_section">
                                                <input type="hidden" name="section_id" value="<?php echo $s['id_seccion']; ?>">
                                                <input type="text" name="section_name" value="<?php echo htmlspecialchars($s['nombre_seccion']); ?>" class="form-control form-control-sm">
                                                <button type="submit" class="btn btn-success btn-sm ms-2">Guardar</button>
                                            </form>
                                            <?php if ($_SESSION['rol'] != 2): ?>
                                            <form method="post" class="ms-2" onsubmit="return confirm('¿Eliminar sección?');">
                                                <input type="hidden" name="action" value="delete_section">
                                                <input type="hidden" name="section_id" value="<?php echo $s['id_seccion']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                            </form>
                                            <?php endif; ?>
                                        </div>
                                        <?php if (!empty($s['preguntas'])): ?>
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Pregunta</th>
                                                        <th>Tipo</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($s['preguntas'] as $p): ?>
                                                        <tr>
                                                            <td><a href="pregunta_opciones.php?id=<?php echo $p['id_pregunta']; ?>"><?php echo htmlspecialchars($p['pregunta']); ?></a></td>
                                                            <td><?php echo ($p['num_opciones'] > 1) ? 'Múltiple' : 'Única'; ?></td>
                                                            <td>
                                                                <div class="nk-file-actions">
                                                                    <div class="dropdown">
                                                                        <a href="#" class="dropdown-toggle btn btn-sm btn-icon btn-trigger" data-bs-toggle="dropdown">
                                                                            <em class="icon ni ni-more-h"></em>
                                                                        </a>
                                                                        <div class="dropdown-menu dropdown-menu-end">
                                                                            <ul class="link-list-plain no-bdr">
                                                                                <li>
                                                                                    <form method="post" class="d-inline-flex align-items-center p-2 bg-light rounded shadow-sm">
                                                                                        <input type="hidden" name="action" value="edit_question">
                                                                                        <input type="hidden" name="question_id" value="<?php echo $p['id_pregunta']; ?>">
                                                                                        <input type="text" name="question_text" value="<?php echo htmlspecialchars($p['pregunta']); ?>" class="form-control form-control-sm me-2" style="width:180px;" placeholder="Editar pregunta">
                                                                                        <button type="submit" class="btn btn-success btn-sm">
                                                                                            <em class="icon ni ni-save"></em> Guardar
                                                                                        </button>
                                                                                    </form>
                                                                                </li>
                                                                                <?php if ($_SESSION['rol'] != 2): ?>
                                                                                <li class="divider"></li>
                                                                                <li>                                                                <form method="post" class="d-inline ms-2" onsubmit="return confirm('¿Eliminar pregunta?');">
                                                                    <input type="hidden" name="action" value="delete_question">
                                                                    <input type="hidden" name="question_id" value="<?php echo $p['id_pregunta']; ?>">
                                                                    <button type="submit" class="btn btn-danger btn-sm"> <em class="icon ni ni-download"></em> Eliminar</button>
                                                                </form></li>
                                                                                <?php endif; ?>
                                                                              
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                

                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        <?php else: ?>
                                            <p>No hay preguntas.</p>
                                        <?php endif; ?>
                                        <form method="post" class="mt-2">
                                            <input type="hidden" name="action" value="add_question">
                                            <input type="hidden" name="section_id" value="<?php echo $s['id_seccion']; ?>">
                                            <div class="input-group">
                                                <input type="text" name="question_text" class="form-control form-control-sm" placeholder="Nueva pregunta">
                                                <button type="submit" class="btn btn-primary btn-sm">Agregar pregunta</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php if (empty($sections)): ?>
                            <p>No hay secciones.</p>
                        <?php endif; ?>

                    </div>
                </div>
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