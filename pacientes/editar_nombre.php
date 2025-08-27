<?php
include_once '../includes/head.php';
date_default_timezone_set('America/Mexico_City');
?>
<div class="nk-wrap ">
    <?php
    include_once '../includes/menu_superior.php';
    require_once '../database/conexion.php';
    $db = new Database();
    $conn = $db->getConnection();

    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nuevo_nombre = trim($_POST['name'] ?? '');
        if ($id > 0 && $nuevo_nombre !== '') {
            $stmt = $conn->prepare("UPDATE nino SET name = ? WHERE Id = ?");
            $stmt->bind_param('si', $nuevo_nombre, $id);
            $stmt->execute();
            $stmt->close();
            $db->closeConnection();
            header("Location: paciente.php?id=$id");
            exit;
        }
    }

    $nombre = '';
    if ($id > 0) {
        $stmt = $conn->prepare("SELECT name FROM nino WHERE Id = ? LIMIT 1");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            $row = $result->fetch_assoc();
            $nombre = $row['name'] ?? '';
        }
        $stmt->close();
    }
    $db->closeConnection();
    ?>
    <div class="nk-content nk-content-fluid">
        <div class="container-xl wide-xl">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Editar nombre</h3>
                            <div class="nk-block-des text-soft">
                                <p>Modificar nombre del paciente.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="nk-block">
                    <div class="card">
                        <div class="card-inner">
                            <form method="POST">
                                <div class="form-group">
                                    <label class="form-label" for="name">Nombre</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($nombre); ?>" required>
                                    </div>
                                </div>
                                <div class="form-group mt-3">
                                    <button type="submit" class="btn btn-primary">Guardar</button>
                                    <a href="paciente.php?id=<?php echo $id; ?>" class="btn btn-light">Cancelar</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include_once '../includes/footer.php'; ?>
