<?php
include_once 'includes/head.php';

require_once 'database/conexion.php';
$db = new Database();
$conn = $db->getConnection();
$id = $_SESSION['id'] ?? 0;

$message = '';
$messageClass = 'alert-info';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPass = trim($_POST['pass'] ?? '');
    $confirmPass = trim($_POST['pass_confirm'] ?? '');

    if ($newPass === '' || $confirmPass === '') {
        $message = 'Debes ingresar y confirmar la nueva contraseña.';
        $messageClass = 'alert-danger';
    } elseif ($newPass !== $confirmPass) {
        $message = 'Las contraseñas no coinciden. Verifica e inténtalo nuevamente.';
        $messageClass = 'alert-danger';
    } else {
        $stmt = $conn->prepare("UPDATE Usuarios SET pass = ? WHERE id = ?");
        $stmt->bind_param('si', $newPass, $id);
        if ($stmt->execute()) {
            $message = 'Contraseña actualizada correctamente.';
            $messageClass = 'alert-success';
        } else {
            $message = 'Ocurrió un error al actualizar la contraseña.';
            $messageClass = 'alert-danger';
        }
        $stmt->close();
    }
}

$stmt = $conn->prepare("SELECT user FROM Usuarios WHERE id = ?");
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
                                        <h3 class="nk-block-title page-title">Configuración de la cuenta</h3>
                                        <p class="text-soft">Gestiona la seguridad de tu cuenta actualizando tu contraseña.</p>
                                    </div>
                                </div>
                            </div>
                            <?php if ($message): ?>
                                <div class="alert <?php echo $messageClass; ?>">
                                    <?php echo htmlspecialchars($message); ?>
                                </div>
                            <?php endif; ?>
                            <div class="card">
                                <div class="card-inner">
                                    <form method="POST">
                                        <div class="row g-4">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">Usuario</label>
                                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['user'] ?? ''); ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label" for="pass">Nueva contraseña</label>
                                                    <input type="password" name="pass" id="pass" class="form-control" required minlength="6">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label" for="pass_confirm">Confirmar contraseña</label>
                                                    <input type="password" name="pass_confirm" id="pass_confirm" class="form-control" required minlength="6">
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
