<?php
include_once 'includes/head.php';
date_default_timezone_set('America/Mexico_City');
?>
            <!-- sidebar @e -->
            <!-- wrap @s -->
            <div class="nk-wrap ">
                <!-- main header @s -->
            <?php
                include_once 'includes/menu_superior.php';
            ?>
                <!-- main header @e -->
                <!-- content @s -->
                <div class="nk-content nk-content-fluid">
                    <div class="container-xl wide-xl">
                        <div class="nk-content-body">
                            <div class="nk-block-head nk-block-head-sm">
                                <div class="nk-block-between">
                                    <div class="nk-block-head-content">
                                        <h3 class="nk-block-title page-title">Archivos</h3>
                                        <div class="nk-block-des text-soft">
                                            <p>Listado de archivos disponibles.</p>
                                        </div>
                                    </div><!-- .nk-block-head-content -->
                                    <div class="nk-block-head-content">
                                        <a href="subir_evaluacion.php" class="btn btn-primary">Subir archivo</a>
                                    </div>
                                </div><!-- .nk-block-between -->
                            </div><!-- .nk-block-head -->
                            <div class="nk-block">
                                <h4 class="nk-block-title">Archivos</h4>
                                <div class="card card-full">
                                    <div class="card-inner table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Título</th>
                                                    <th>Fecha</th>
                                                    <th>Archivos</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $dir = __DIR__ . '/uploads/evaluaciones';
                                                $items = [];
                                                if (is_dir($dir)) {
                                                    foreach (scandir($dir) as $d) {
                                                        if ($d === '.' || $d === '..') continue;
                                                        $metaFile = $dir . '/' . $d . '/meta.json';
                                                        if (is_file($metaFile)) {
                                                            $meta = json_decode(file_get_contents($metaFile), true);
                                                            $items[] = [
                                                                'id' => $d,
                                                                'titulo' => $meta['titulo'] ?? $d,
                                                                'fecha' => $meta['fecha'] ?? '',
                                                                'archivos' => $meta['archivos'] ?? ($meta['imagenes'] ?? [])
                                                            ];
                                                        }
                                                    }
                                                }
                                                foreach ($items as $it):
                                                ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($it['titulo']); ?></td>
                                                    <td><?php echo htmlspecialchars($it['fecha']); ?></td>
                                                    <td>
                                                        <?php foreach ($it['archivos'] as $file): ?>
                                                            <a href="<?php echo '/uploads/evaluaciones/' . rawurlencode($it['id']) . '/' . rawurlencode($file); ?>" target="_blank"><?php echo htmlspecialchars($file); ?></a><br>
                                                        <?php endforeach; ?>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                                <?php if (empty($items)): ?>
                                                <tr><td colspan="3">No hay archivos.</td></tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
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
