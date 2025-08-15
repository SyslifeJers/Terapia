<?php
include_once '../includes/head.php';

require_once '../database/conexion.php';

$id_nino = isset($_GET['id']) ? intval($_GET['id']) : 0;
$id_examen = isset($_GET['examen']) ? intval($_GET['examen']) : 0;
$id_eval = isset($_GET['eval']) ? intval($_GET['eval']) : 0;

$db = new Database();
$conn = $db->getConnection();

$nombre_nino = '';
if ($id_nino > 0) {
    $stmtN = $conn->prepare("SELECT name FROM nino WHERE Id=? LIMIT 1");
    $stmtN->bind_param('i', $id_nino);
    $stmtN->execute();
    $stmtN->bind_result($nombre_nino);
    $stmtN->fetch();
    $stmtN->close();
}
?>
<div class="nk-wrap ">
    <?php
    include_once '../includes/menu_superior.php';
    ?>
    <div class="nk-content nk-content-fluid">
        <div class="container-xl wide-xl">
            <div class="nk-content-body">
<?php
if ($id_examen === 0) {
    $areas = [];
    $res = $conn->query("SELECT id_area, nombre_area FROM exp_areas_evaluacion ORDER BY nombre_area ASC");
    if ($res) {
        $areas = $res->fetch_all(MYSQLI_ASSOC);
    }
    echo '<h3 class="nk-block-title page-title mb-4">Selecciona evaluación para ' . htmlspecialchars($nombre_nino) . '</h3>';
    if (!empty($areas)) {
        foreach ($areas as $area) {
            echo '<h5 class="mb-3">' . htmlspecialchars($area['nombre_area']) . '</h5>';

            $stmtEx = $conn->prepare("SELECT id_examen, nombre_examen FROM exp_examenes WHERE id_area=? ORDER BY nombre_examen ASC");
            $stmtEx->bind_param('i', $area['id_area']);
            $stmtEx->execute();
            $resEx = $stmtEx->get_result();
            $examenes = $resEx ? $resEx->fetch_all(MYSQLI_ASSOC) : [];
            $stmtEx->close();

            if (!empty($examenes)) {
                echo '<div class="row g-gs mb-4">';
                foreach ($examenes as $ex) {
                    $stmtEval = $conn->prepare("SELECT id_eval, status FROM exp_evaluacion_examen WHERE id_nino=? AND id_examen=? ORDER BY fecha DESC LIMIT 1");
                    $stmtEval->bind_param('ii', $id_nino, $ex['id_examen']);
                    $stmtEval->execute();
                    $resEval = $stmtEval->get_result();
                    $eval = $resEval ? $resEval->fetch_assoc() : null;
                    $stmtEval->close();
                    $id_eval = $eval['id_eval'] ?? 0;
                    $status_eval = intval($eval['status'] ?? 0);

                    echo '<div class="col-md-6">';
                    echo '<div class="card card-full">';
                    echo '<div class="card-inner d-flex justify-content-between align-items-center">';
                    echo '<div>' . htmlspecialchars($ex['nombre_examen']);
                    if ($id_eval > 0) {
                        if ($status_eval === 1) {
                            echo ' <span class="badge bg-success ms-1">Finalizado</span>';
                        } else {
                            echo ' <span class="badge bg-warning ms-1">En progreso</span>';
                        }
                    }
                    echo '</div>';
                    if ($id_eval > 0) {
                        echo '<div class="d-flex gap-1">';
                        if ($status_eval !== 1) {
                            echo '<a class="btn btn-warning btn-sm" href="evaluacion_examen.php?id=' . $id_nino . '&examen=' . $ex['id_examen'] . '&eval=' . $id_eval . '">Editar</a>';
                        }
                        echo '<a class="btn btn-success btn-sm" target="_blank" href="pdf_evaluacion_examen.php?id=' . $id_eval . '">Ver</a>';
                        echo '</div>';
                    } else {
                        echo '<a class="btn btn-primary btn-sm" href="evaluacion_examen.php?id=' . $id_nino . '&examen=' . $ex['id_examen'] . '">Iniciar</a>';
                    }
                    echo '</div></div></div>';
                }
                echo '</div>';
            } else {
                echo '<p class="mb-4">No hay evaluaciones en esta área.</p>';
            }
        }
    } else {
        echo '<p>No hay áreas disponibles.</p>';
    }
} else {
    $exam_name = '';
    $stmt = $conn->prepare("SELECT nombre_examen FROM exp_examenes WHERE id_examen=? LIMIT 1");
    $stmt->bind_param('i', $id_examen);
    $stmt->execute();
    $stmt->bind_result($exam_name);
    $stmt->fetch();
    $stmt->close();

    $sections = [];
    $stmt = $conn->prepare("SELECT id_seccion, nombre_seccion FROM exp_secciones_examen WHERE id_examen=? ORDER BY id_seccion ASC");
    $stmt->bind_param('i', $id_examen);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res) {
        $sections = $res->fetch_all(MYSQLI_ASSOC);
        foreach ($sections as &$s) {
            $stmtQ = $conn->prepare("SELECT id_pregunta, pregunta FROM exp_preguntas_evaluacion WHERE id_seccion=? ORDER BY id_pregunta ASC");
            $stmtQ->bind_param('i', $s['id_seccion']);
            $stmtQ->execute();
            $resQ = $stmtQ->get_result();
            $s['preguntas'] = $resQ ? $resQ->fetch_all(MYSQLI_ASSOC) : [];
            foreach ($s['preguntas'] as &$p) {
                $stmtO = $conn->prepare("SELECT op.texto FROM exp_pregunta_opcion po JOIN exp_opciones_pregunta op ON po.id_opcion=op.id_opcion WHERE po.id_pregunta=? ORDER BY op.texto ASC");
                $stmtO->bind_param('i', $p['id_pregunta']);
                $stmtO->execute();
                $resO = $stmtO->get_result();
                $p['opciones'] = $resO ? $resO->fetch_all(MYSQLI_ASSOC) : [];
                $stmtO->close();
            }
            unset($p);
            $stmtQ->close();
        }
        unset($s);
    }
    $stmt->close();

    $saved = [];
    $status_eval = 0;
    if ($id_eval > 0) {
        $stmt = $conn->prepare("SELECT respuestas, status FROM exp_evaluacion_examen WHERE id_eval=? AND id_examen=? AND id_nino=? LIMIT 1");
        $stmt->bind_param('iii', $id_eval, $id_examen, $id_nino);
        $stmt->execute();
        $stmt->bind_result($respJson, $status_eval);
        if ($stmt->fetch()) {
            $arr = json_decode($respJson, true);
            if (is_array($arr)) {
                foreach ($arr as $item) {
                    if (isset($item['id'])) {
                        $saved[$item['id']] = $item;
                    }
                }
            }
        }
        $stmt->close();
    }

    echo '<h3 class="nk-block-title page-title mb-1">' . htmlspecialchars($exam_name) . '</h3>';
    echo '<p class="mb-4">Paciente: ' . htmlspecialchars($nombre_nino) . '</p>';
    if ($status_eval === 1) {
        echo '<p>Esta evaluación ha sido finalizada y no puede editarse.</p>';
        echo '<a class="btn btn-success" target="_blank" href="pdf_evaluacion_examen.php?id=' . $id_eval . '">Ver</a>';
    } else {
        echo '<form id="evalForm" method="POST" action="guardar_examen_evaluacion.php">';
        echo '<input type="hidden" name="id_nino" value="' . $id_nino . '">';
        echo '<input type="hidden" name="id_examen" value="' . $id_examen . '">';
        echo '<input type="hidden" name="id_eval" id="id_eval" value="' . $id_eval . '">';
        echo '<input type="hidden" name="status" id="status" value="0">';
        echo '<input type="hidden" name="respuestas" id="respuestas">';
        $secIndex = 1;
        $totalSections = count($sections);
        foreach ($sections as $section) {
            $display = ($secIndex === 1) ? '' : 'style="display:none;"';
            echo '<div id="sec' . $secIndex . '" ' . $display . '>';
            echo '<h5 class="mb-3">' . htmlspecialchars($section['nombre_seccion']) . '</h5>';
        foreach ($section['preguntas'] as $q) {
            $qid = 'q' . $q['id_pregunta'];
            $selVal = htmlspecialchars($saved[$qid]['respuesta'] ?? '', ENT_QUOTES);
            $txtVal = htmlspecialchars($saved[$qid]['comentario'] ?? '', ENT_QUOTES);
            echo '<div class="form-group">';
            echo '<label class="form-label">' . htmlspecialchars($q['pregunta']) . '</label>';
            echo '<select class="form-select" id="' . $qid . '" data-pregunta="' . htmlspecialchars($q['pregunta']) . '">';
            echo '<option value=""' . ($selVal === '' ? ' selected' : '') . '>Selecciona</option>';
            if (!empty($q['opciones'])) {
                foreach ($q['opciones'] as $op) {
                    $opt = htmlspecialchars($op['texto']);
                    $sel = ($selVal === $opt) ? ' selected' : '';
                    echo '<option value="' . $opt . '"' . $sel . '>' . $opt . '</option>';
                }
            } else {
                echo '<option value="Si"' . ($selVal === 'Si' ? ' selected' : '') . '>Sí</option>';
                echo '<option value="Parcial"' . ($selVal === 'Parcial' ? ' selected' : '') . '>Parcial</option>';
                echo '<option value="No"' . ($selVal === 'No' ? ' selected' : '') . '>No</option>';
            }
            echo '</select>';
            echo '<textarea id="' . $qid . 'c" class="form-control mt-2" placeholder="Comentario">' . $txtVal . '</textarea>';
            echo '</div>';
        }
        echo '<div class="mt-3">';
        if ($secIndex > 1) {
            echo '<button type="button" class="btn btn-secondary me-2" onclick="prevSec(' . $secIndex . ')">Anterior</button>';
        }
        if ($secIndex < $totalSections) {
            echo '<button type="button" class="btn btn-primary" onclick="nextSec(' . $secIndex . ')">Siguiente</button>';
        } else {
            echo '<button type="submit" class="btn btn-success me-2">Guardar</button>';
            echo '<button type="button" class="btn btn-danger" onclick="finalizeExam()">Finalizar</button>';
        }
        echo '</div>';
        echo '</div>';
        $secIndex++;
    }
      echo '</form>';
      }
  }
  $db->closeConnection();
?>
            </div>

        </div>
    </div>
</div>
<script>
const form=document.getElementById('evalForm');
function collectData(){const data=[];document.querySelectorAll('[data-pregunta]').forEach(sel=>{const id=sel.id;data.push({id,pregunta:sel.getAttribute('data-pregunta'),respuesta:sel.value,comentario:document.getElementById(id+'c').value});});return data;}
function saveProgress(cb){const data=collectData();const params=new URLSearchParams();params.append('autosave','1');params.append('id_eval',document.getElementById('id_eval').value);params.append('id_nino','<?php echo $id_nino; ?>');params.append('id_examen','<?php echo $id_examen; ?>');params.append('respuestas',JSON.stringify(data));fetch('guardar_examen_evaluacion.php',{method:'POST',body:params}).then(r=>r.json()).then(res=>{if(res.id_eval){document.getElementById('id_eval').value=res.id_eval;}if(cb)cb();}).catch(()=>{if(cb)cb();});}
function nextSec(n){saveProgress(()=>{document.getElementById('sec'+n).style.display='none';document.getElementById('sec'+(n+1)).style.display='block';});}
function prevSec(n){saveProgress(()=>{document.getElementById('sec'+n).style.display='none';document.getElementById('sec'+(n-1)).style.display='block';});}
function finalizeExam(){document.getElementById('status').value=1;const data=collectData();document.getElementById('respuestas').value=JSON.stringify(data);form.submit();}
if(form){form.addEventListener('submit',function(){const data=collectData();document.getElementById('respuestas').value=JSON.stringify(data);});}
</script>
<?php include_once '../includes/footer.php'; ?>
