<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once '../database/conexion.php';
require_once '../libreria/SimplePDF.php';


$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$db = new Database();
$conn = $db->getConnection();


$stmt = $conn->prepare("SELECT ee.respuestas, ee.fecha, n.name AS paciente, u.name AS usuario, ex.nombre_examen FROM exp_evaluacion_examen ee JOIN nino n ON ee.id_nino=n.Id JOIN Usuarios u ON ee.id_usuario=u.id JOIN exp_examenes ex ON ee.id_examen = ex.id_examen WHERE ee.id_eval=? LIMIT 1");

$stmt->bind_param('i',$id);
$stmt->execute();
$res = $stmt->get_result();
$eval = $res ? $res->fetch_assoc() : null;
$stmt->close();
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

$i=1;
foreach($resp as $r){
    $pdf->text(40,$y,$i.'. '.$r['pregunta']);
    $y+=20;
    $pdf->text(60,$y,'Respuesta: '.$r['respuesta']);
    $y+=20;
    if(!empty($r['comentario'])){
        $pdf->text(60,$y,'Comentario: '.$r['comentario']);
        $y+=20;
    }
    $y+=10;
    $i++;
}
header('Content-Type: application/pdf');
echo $pdf->output();
?>
