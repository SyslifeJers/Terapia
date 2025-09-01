<?php
include_once '../includes/head.php';
require_once '../database/conexion.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$db = new Database();
$conn = $db->getConnection();

$evaluaciones_fotos = [];
$stmt = $conn->prepare("SELECT b.titulo, b.seccion, a.ruta, c.id, c.name, a.id_eval_foto, a.id_imagen FROM exp_evaluacion_fotos_imagenes a
INNER JOIN exp_evaluacion_fotos b ON b.id_eval_foto = a.id_eval_foto
INNER JOIN nino c ON c.id = b.id_nino
WHERE a.id_eval_foto = ?");
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
                $imagenes_por_fecha = [];
                if (!empty($evaluaciones_fotos)) {
                    $seccion_dir = preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($evaluaciones_fotos[0]['seccion']));
                    foreach ($evaluaciones_fotos as $ev) {
                        $rutaAbs = __DIR__ . '/../uploads/pacientes/' . $ev['id'] . '/evaluaciones/' . $seccion_dir . '/' . $ev['id_eval_foto'] . '/' . $ev['ruta'];
                        $fechaClave = file_exists($rutaAbs) ? date('Y-m-d', filemtime($rutaAbs)) : 'desconocido';
                        $ev['hash'] = file_exists($rutaAbs) ? md5_file($rutaAbs) : '';
                        $imagenes_por_fecha[$fechaClave][] = $ev;
                    }
                    krsort($imagenes_por_fecha);
                }
                ?>
                <h3><?php echo htmlspecialchars($evaluaciones_fotos[0]['titulo'] ?? ''); ?> (<?php echo htmlspecialchars($evaluaciones_fotos[0]['name'] ?? ''); ?>)</h3>
                <?php if ($_SESSION['rol'] != 2): ?>
                <form id="add-photo-form" class="mb-3">
                    <div class="form-group">
                        <input type="file" name="fotos[]" multiple accept="image/*" required>
                        <button type="submit" class="btn btn-primary btn-sm mt-2">Agregar</button>
                    </div>
                </form>
                <?php endif; ?>
                <div class="row g-gs">
                    <?php foreach ($imagenes_por_fecha as $fecha => $imgs): ?>
                        <div class="col-12">
                            <h5><?php echo $fecha === date('Y-m-d') ? 'Hoy' : ($fecha === date('Y-m-d', strtotime('-1 day')) ? 'Ayer' : date('d/m/Y', strtotime($fecha))); ?></h5>
                        </div>
                        <?php foreach ($imgs as $ev): ?>
                        <div class="col-sm-6 col-lg-4">
                            <div class="card card-bordered">
                                <div class="card-inner">
                                    <div class="row g-2 align-items-center">
                                        <div class="col-12">
                                            <a href="../uploads/pacientes/<?php echo $ev['id']; ?>/evaluaciones/<?php echo $seccion_dir . '/' . $ev['id_eval_foto'] . '/' . $ev['ruta']; ?>?v=<?php echo $ev['hash']; ?>" target="_blank">
                                                <img id="img-<?php echo $ev['id_imagen']; ?>" src="../uploads/pacientes/<?php echo $ev['id']; ?>/evaluaciones/<?php echo $seccion_dir . '/' . $ev['id_eval_foto'] . '/' . $ev['ruta']; ?>?v=<?php echo $ev['hash']; ?>" class="img-fluid" alt="">
                                            </a>
                                        </div>
                                        <div class="col-12 mt-1">
                                            <button type="button" class="btn btn-sm btn-outline-secondary rotate-img" data-target="img-<?php echo $ev['id_imagen']; ?>">Rotar</button>
                                            <?php if ($_SESSION['rol'] != 2): ?>
                                            <button type="button" class="btn btn-sm btn-outline-success save-rotation d-none" data-id="<?php echo $ev['id_imagen']; ?>" data-target="img-<?php echo $ev['id_imagen']; ?>">Guardar</button>
                                            <button type="button" class="btn btn-sm btn-outline-danger delete-img" data-id="<?php echo $ev['id_imagen']; ?>">Eliminar</button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                    <?php if (empty($imagenes_por_fecha)): ?>
                        <div class="col-12">
                            <p>No hay evaluaciones fotográficas.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>


    <!-- wrap @e -->
    <script>
    <?php if ($_SESSION['rol'] != 2): ?>
    document.getElementById('add-photo-form')?.addEventListener('submit', function(e){
        e.preventDefault();
        const fd = new FormData(this);
        fd.append('id_eval', <?php echo $id; ?>);
        fetch('agregar_imagen_evaluacion.php', {method: 'POST', body: fd})
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    Swal.fire('Guardado', '', 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error', res.message || 'Ocurrió un error', 'error');
                }
            })
            .catch(() => Swal.fire('Error', 'Ocurrió un error', 'error'));
    });

    document.querySelectorAll('.delete-img').forEach(btn => {
        btn.addEventListener('click', function(e){
            e.preventDefault();
            const idImg = this.getAttribute('data-id');
            Swal.fire({title: '¿Eliminar imagen?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Sí, eliminar'})
                .then(res => {
                    if (res.isConfirmed) {
                        const fd = new FormData();
                        fd.append('id_imagen', idImg);
                        fetch('eliminar_imagen_evaluacion.php', {method: 'POST', body: fd})
                            .then(r => r.json())
                            .then(resp => {
                                if (resp.success) {
                                    Swal.fire('Eliminado', '', 'success').then(() => location.reload());
                                } else {
                                    Swal.fire('Error', resp.message || 'Ocurrió un error', 'error');
                                }
                            })
                            .catch(() => Swal.fire('Error', 'Ocurrió un error', 'error'));
                    }
                });
        });
    });

    document.querySelectorAll('.save-rotation').forEach(btn => {
        btn.addEventListener('click', function(){
            const idImg = this.getAttribute('data-id');
            const img = document.getElementById(this.getAttribute('data-target'));
            const angle = parseInt(img.getAttribute('data-rot') || '0');
            const fd = new FormData();
            fd.append('id_imagen', idImg);
            fd.append('angulo', angle);
            fetch('rotar_imagen_evaluacion.php', {method: 'POST', body: fd})
                .then(r => r.json())
                .then(resp => {
                    if (resp.success) {
                        Swal.fire('Guardado', '', 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Error', resp.message || 'Ocurrió un error', 'error');
                    }
                })
                .catch(() => Swal.fire('Error', 'Ocurrió un error', 'error'));
        });
    });
    <?php endif; ?>

    document.querySelectorAll('.rotate-img').forEach(btn => {
        btn.addEventListener('click', function(){
            const img = document.getElementById(this.getAttribute('data-target'));
            const current = parseInt(img.getAttribute('data-rot') || '0');
            const next = (current + 90) % 360;
            img.style.transform = 'rotate(' + next + 'deg)';
            img.setAttribute('data-rot', next);
            const saveBtn = this.parentElement.querySelector('.save-rotation');
            if (saveBtn) { saveBtn.classList.remove('d-none'); }
        });
    });
    </script>
    <?php
    include_once '../includes/footer.php';
    ?>