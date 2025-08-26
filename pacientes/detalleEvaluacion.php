<?php
include_once '../includes/head.php';
require_once '../database/conexion.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$db = new Database();
$conn = $db->getConnection();

$evaluaciones_fotos = [];
$stmt = $conn->prepare("SELECT b.titulo, b.seccion, a.`ruta`, c.id, c.name, a.id_eval_foto FROM `exp_evaluacion_fotos_imagenes` a
inner join exp_evaluacion_fotos b on b.id_eval_foto = a.id_eval_foto
inner join nino c on c.id = b.id_nino
WHERE a.`id_eval_foto` = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result) {
    $evaluaciones_fotos = $result->fetch_all(MYSQLI_ASSOC);
}
$stmt->close();

$db->closeConnection();
?>
<!-- sidebar @e -->
<!-- wrap @s -->
<div class="nk-wrap ">
    <!-- main header @s -->
    <?php include_once '../includes/menu_superior.php'; ?>

    <div class="nk-content nk-content-fluid">
        <div class="container-xl wide-xl">
            <div class="nk-content-body">
                <?php
                $seccion_dir = '';
                if (!empty($evaluaciones_fotos)) {
                    $seccion_dir = preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($evaluaciones_fotos[0]['seccion']));
                }
                ?>
                <h3><?php echo htmlspecialchars($evaluaciones_fotos[0]['titulo'] ?? ''); ?> (<?php echo htmlspecialchars($evaluaciones_fotos[0]['name'] ?? ''); ?>)</h3>
                <div class="row g-gs">
                    <?php foreach ($evaluaciones_fotos as $ev): ?>
                        <div class="col-sm-6 col-lg-4">
                            <div class="card card-bordered">
                                <div class="card-inner">
                                    <h6 class="title mb-2"></h6>

                                    <div class="row g-2">
                                        <div class="col-6">
                                            <a href="../uploads/pacientes/<?php echo $ev['id']; ?>/evaluaciones/<?php echo $seccion_dir . '/' . $ev['id_eval_foto'] . '/' . $ev['ruta']; ?>" target="_blank">
                                                <img src="../uploads/pacientes/<?php echo $ev['id']; ?>/evaluaciones/<?php echo $seccion_dir . '/' . $ev['id_eval_foto'] . '/' . $ev['ruta']; ?>" class="img-fluid" alt="">
                                            </a></div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($evaluaciones_fotos)): ?>
                        <div class="col-12">
                            <p>No hay evaluaciones fotográficas.</p>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>


    <!-- wrap @e -->
    <?php
    include_once '../includes/footer.php';
    ?>