<?php
include_once 'includes/head.php';

require_once 'database/conexion.php';
$db = new Database();
$conn = $db->getConnection();
$message = '';
$id = $_SESSION['id'] ?? 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPass = trim($_POST['pass'] ?? '');
    if ($newPass !== '') {
        $stmt = $conn->prepare("UPDATE Usuarios SET pass = ? WHERE id = ?");
        $stmt->bind_param('si', $newPass, $id);
        if ($stmt->execute()) {
            $message = 'Contraseña actualizada correctamente';
        } else {
            $message = 'Error al actualizar la contraseña';
        }
        $stmt->close();
    } else {
        $message = 'La contraseña no puede estar vacía';
    }
}
$stmt = $conn->prepare("SELECT id, name, user, pass, token, activo, registro, telefono, correo, IdRol FROM Usuarios WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();
$db->closeConnection();
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
                                        <h3 class="nk-block-title page-title">Perfil de usuario</h3>
                                    </div>
                                </div>
                            </div>
                            <?php if ($message): ?>
                                <div class="alert alert-info"><?php echo $message; ?></div>
                            <?php endif; ?>
                            <div class="card">
                                <div class="card-inner">
                                    <form method="POST">
                                        <div class="row g-4">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">Nombre</label>
                                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">Usuario</label>
                                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['user'] ?? ''); ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label" for="pass">Nueva contraseña</label>
                                                    <input type="text" name="pass" id="pass" class="form-control" required>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <button type="submit" class="btn btn-primary">Actualizar contraseña</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- content @e -->
            </div>
            <!-- wrap @e -->
       <?php include_once 'includes/footer.php'; ?>
