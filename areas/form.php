<?php
include_once '../includes/head.php';
date_default_timezone_set('America/Mexico_City');
?>
            <!-- sidebar @e -->
            <!-- wrap @s -->
            <div class="nk-wrap ">
                <!-- main header @s -->
            <?php
                include_once '../includes/menu_superior.php';

                require_once '../database/conexion.php';
                $db = new Database();
                $conn = $db->getConnection();

                $id = $_GET['id'] ?? null;
                $nombre = '';
                $descripcion = '';

                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $id = $_POST['id'] ?? null;
                    $nombre = $_POST['nombre'] ?? '';
                    $descripcion = $_POST['descripcion'] ?? '';

                    if ($id) {
                        $stmt = $conn->prepare("UPDATE exp_areas_evaluacion SET nombre_area = ?, descripcion = ? WHERE id_area = ?");
                        $stmt->bind_param("ssi", $nombre, $descripcion, $id);
                        $stmt->execute();
                        $stmt->close();
                    } else {
                        $stmt = $conn->prepare("INSERT INTO exp_areas_evaluacion (nombre_area, descripcion) VALUES (?, ?)");
                        $stmt->bind_param("ss", $nombre, $descripcion);
                        $stmt->execute();
                        $stmt->close();
                    }

                    $db->closeConnection();
                    $mensaje = $id ? 'Área actualizada correctamente.' : 'Área agregada correctamente.';
                    echo "<p>$mensaje Redirigiendo en 3 segundos...</p>";
                    echo "<script>setTimeout(function(){ window.location.href='/areas/index.php'; }, 3000);</script>";
                    exit;
                } elseif ($id) {
                    $stmt = $conn->prepare("SELECT nombre_area, descripcion FROM exp_areas_evaluacion WHERE id_area = ?");
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $stmt->bind_result($nombre, $descripcion);
                    $stmt->fetch();
                    $stmt->close();
                }

                $db->closeConnection();
                ?>
                <!-- main header @e -->
                <!-- content @s -->
                <div class="nk-content nk-content-fluid">
                    <div class="container-xl wide-xl">
                        <div class="nk-content-body">
                            <div class="nk-block-head nk-block-head-sm">
                                <div class="nk-block-between">
                                    <div class="nk-block-head-content">
                                        <h3 class="nk-block-title page-title"><?php echo $id ? 'Editar Área' : 'Nueva Área'; ?></h3>
                                    </div>
                                </div>
                            </div>
                            <div class="nk-block">
                                <div class="card card-bordered">
                                    <div class="card-inner">
                                        <form method="POST">
                                            <?php if ($id): ?>
                                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
                                            <?php endif; ?>
                                            <div class="form-group">
                                                <label class="form-label" for="nombre">Nombre</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label" for="descripcion">Descripción</label>
                                                <div class="form-control-wrap">
                                                    <textarea class="form-control" id="descripcion" name="descripcion"><?php echo htmlspecialchars($descripcion); ?></textarea>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary">Guardar</button>
                                                <a href="index.php" class="btn btn-secondary">Cancelar</a>
                                            </div>
                                        </form>
                                    </div>
                                </div><!-- .card -->
                            </div><!-- .nk-block -->
                        </div>
                    </div>
                </div>
                <!-- content @e -->

            </div>
            <!-- wrap @e -->
       <?php
include_once '../includes/footer.php';
?>