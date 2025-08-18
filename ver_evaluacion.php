<?php
include_once 'includes/head.php';

$id = basename($_GET['id'] ?? '');
$baseDir = __DIR__ . '/uploads/evaluaciones/' . $id;
$metaFile = $baseDir . '/meta.json';
if (!is_file($metaFile)) {
    die('Evaluación no encontrada');
}
$meta = json_decode(file_get_contents($metaFile), true);
$archivos = $meta['archivos'] ?? ($meta['imagenes'] ?? []);
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
                                <ul class="list-group">
                                    <?php foreach ($archivos as $file): ?>
                                    <li class="list-group-item">
                                        <a href="<?php echo '/uploads/evaluaciones/' . htmlspecialchars($id) . '/' . htmlspecialchars($file); ?>" target="_blank">
                                            <?php echo htmlspecialchars($file); ?>
                                        </a>
                                    </li>
                                    <?php endforeach; ?>
                                    <?php if (empty($archivos)): ?>
                                    <li class="list-group-item">No hay archivos para esta evaluación.</li>
                                    <?php endif; ?>
                                </ul>
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
