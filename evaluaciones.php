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
                                        <h3 class="nk-block-title page-title">Evaluaciones</h3>
                                        <div class="nk-block-des text-soft">
                                            <p>Listado de evaluaciones.</p>
                                        </div>
                                    </div><!-- .nk-block-head-content -->
                                </div><!-- .nk-block-between -->
                            </div><!-- .nk-block-head -->
                            <div class="nk-block">
                                <div class="card card-full">
                                    <div class="card-inner">
                                        <div class="d-flex align-items-center mb-3">
                                            <h5 class="title mb-0 me-3">Evaluaciones</h5>
                                            <a href="subir_evaluacion.php" class="btn btn-outline-primary btn-sm">
                                                <em class="icon ni ni-plus"></em> Subir archivo
                                            </a>
                                        </div>
                                        <hr class="my-4">
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
                                        ?>
                                        <div id="evaluacionFiles" class="nk-files nk-files-view-grid">
                                            <div class="nk-files-list">
                                                <?php foreach ($items as $it): ?>
                                                <div class="nk-file-item nk-file">
                                                    <div class="nk-file-info">
                                                        <div class="nk-file-title">
                                                            <div class="nk-file-icon">
                                                                <a class="nk-file-icon-link" href="/ver_evaluacion.php?id=<?php echo urlencode($it['id']); ?>">
                                                                    <span class="nk-file-icon-type"><em class="icon ni ni-folder"></em></span>
                                                                </a>
                                                            </div>
                                                            <div class="nk-file-name">
                                                                <div class="nk-file-name-text">
                                                                    <a href="/ver_evaluacion.php?id=<?php echo urlencode($it['id']); ?>" class="title"><?php echo htmlspecialchars($it['titulo']); ?></a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <ul class="nk-file-desc">
                                                            <li class="date"><?php echo htmlspecialchars($it['fecha']); ?></li>
                                                            <li class="members"><?php echo count($it['archivos']); ?> archivo(s)</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <?php endforeach; ?>
                                                <?php if (empty($items)): ?>
                                                <p>No hay archivos.</p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
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
