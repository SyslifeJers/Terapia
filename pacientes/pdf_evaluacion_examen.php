<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once '../database/conexion.php';
require_once '../libreria/SimplePDF.php';


$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$db = new Database();
$conn = $db->getConnection();


$stmt = $conn->prepare("SELECT ee.respuestas, ee.fecha, ee.id_examen, n.name AS paciente, u.name AS usuario, ex.nombre_examen FROM exp_evaluacion_examen ee JOIN nino n ON ee.id_nino=n.Id JOIN Usuarios u ON ee.id_usuario=u.id JOIN exp_examenes ex ON ee.id_examen = ex.id_examen WHERE ee.id_eval=? LIMIT 1");

$stmt->bind_param('i',$id);
$stmt->execute();
$res = $stmt->get_result();
$eval = $res ? $res->fetch_assoc() : null;
$stmt->close();

// Map question ids to their section names
$sectionMap = [];
if($eval){
    $stmt = $conn->prepare("SELECT pe.id_pregunta, se.nombre_seccion FROM exp_preguntas_evaluacion pe JOIN exp_secciones_examen se ON pe.id_seccion = se.id_seccion WHERE se.id_examen=? ORDER BY se.id_seccion, pe.id_pregunta");
    $stmt->bind_param('i',$eval['id_examen']);
    $stmt->execute();
    $res = $stmt->get_result();
    if($res){
        while($row = $res->fetch_assoc()){
            $sectionMap[$row['id_pregunta']] = $row['nombre_seccion'];
        }
    }
    $stmt->close();
}
$db->closeConnection();

if(!$eval){
    die('Evaluación no encontrada');
}

$resp = json_decode($eval['respuestas'], true) ?: [];
$pdf = new SimplePDF();
$pdf->addPage();


$pdf->text(40,40,'Evaluación: '.$eval['nombre_examen']);
$pdf->text(40,60,'Paciente: '.$eval['paciente']);
$pdf->text(40,80,'Fecha: '.$eval['fecha']);
$pdf->text(40,100,'Aplicó: '.$eval['usuario']);
$y = 130;
$pageLimit = 800; // Approximate bottom margin of the page

$currentSection = '';
foreach($resp as $r){
    $qid = isset($r['id']) ? intval(preg_replace('/\D/','', $r['id'])) : 0;
    $secName = $sectionMap[$qid] ?? '';
    if($secName !== $currentSection){
        if($y > $pageLimit){
            $pdf->addPage();
            $y = 40;
        }
        $pdf->text(40,$y,'Sección: '.$secName);
        $y += 20;
        $currentSection = $secName;
    }

    if($y > $pageLimit){
        $pdf->addPage();
        $y = 40;
    }
    $pdf->text(40,$y,$r['pregunta']);
    $y += 20;

    if($y > $pageLimit){
        $pdf->addPage();
        $y = 40;
    }
    $pdf->text(60,$y,'R: '.$r['respuesta']);
    $y += 20;

    if(!empty($r['comentario'])){
        if($y > $pageLimit){
            $pdf->addPage();
            $y = 40;
        }
        $pdf->text(60,$y,'Comentario: '.$r['comentario']);
        $y += 20;
    }

    $y += 10;
}
header('Content-Type: application/pdf');
echo $pdf->output();
?>
