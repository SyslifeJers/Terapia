<?php
include_once 'includes/head.php';

$id = basename($_GET['id'] ?? '');
$baseDir = __DIR__ . '/uploads/evaluaciones/' . $id;
$metaFile = $baseDir . '/meta.json';
if (!is_file($metaFile)) {
    die('Evaluación no encontrada');
}
$meta = json_decode(file_get_contents($metaFile), true);
$imagenes = $meta['imagenes'] ?? [];
?>
            <!-- sidebar @e -->
            <!-- wrap @s -->
            <div class="nk-wrap ">
                <!-- main header @s -->
                <?php include_once 'includes/menu_superior.php'; ?>
                <!-- main header @e -->
                <!-- content @s -->
                <div class="nk-content nk-content-fluid">
                    <div class="container-xl wide-xl">
                        <div class="nk-content-body">
                            <div class="nk-block-head nk-block-head-sm">
                                <div class="nk-block-between">
                                    <div class="nk-block-head-content">
                                        <h3 class="nk-block-title page-title"><?php echo htmlspecialchars($meta['titulo'] ?? ''); ?></h3>
                                        <div class="nk-block-des text-soft">
                                            <p><?php echo htmlspecialchars($meta['fecha'] ?? ''); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="nk-block">
                                <div class="row g-gs">
                                    <?php foreach ($imagenes as $img): ?>
                                    <div class="col-sm-6 col-lg-4">
                                        <div class="card card-bordered">
                                            <img src="<?php echo '/uploads/evaluaciones/' . htmlspecialchars($id) . '/' . htmlspecialchars($img); ?>" class="card-img-top" alt="">
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                    <?php if (empty($imagenes)): ?>
                                    <div class="col-12">
                                        <p>No hay imágenes para esta evaluación.</p>
                                    </div>
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
include_once 'includes/footer.php';
?>
