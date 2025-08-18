<?php
include_once 'includes/head.php';
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
                                        <h3 class="nk-block-title page-title">Subir archivos</h3>
                                        <div class="nk-block-des text-soft">
                                            <p>Seleccione uno o varios archivos y asigne un título.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="nk-block">
                                <div class="card card-bordered">
                                    <div class="card-inner">
                                        <form action="guardar_evaluacion.php" method="POST" enctype="multipart/form-data">
                                            <div class="row g-4">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label class="form-label" for="titulo">Título</label>
                                                        <div class="form-control-wrap">
                                                            <input
                                                                type="text"
                                                                class="form-control"
                                                                id="titulo"
                                                                name="titulo"
                                                                required
                                                            >
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label class="form-label" for="archivos">Archivos</label>
                                                        <div class="form-control-wrap">
                                                            <input
                                                                type="file"
                                                                class="form-control"
                                                                id="archivos"
                                                                name="archivos[]"
                                                                multiple
                                                                required
                                                            >
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <button type="submit" class="btn btn-primary">Guardar</button>
                                                </div>
                                            </div>
                                        </form>
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
