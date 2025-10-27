<?php
include_once 'includes/head.php';

require_once 'database/conexion.php';
$db = new Database();
$conn = $db->getConnection();
$id = $_SESSION['id'] ?? 0;

$stmt = $conn->prepare("SELECT name, user, telefono, correo, IdRol, registro FROM Usuarios WHERE id = ?");
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
                                        <p class="text-soft">Consulta la información asociada a tu cuenta.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-inner">
                                    <div class="row g-4">
                                        <div class="col-lg-6">
                                            <div class="nk-data data-list">
                                                <div class="data-head">
                                                    <h6 class="overline-title">Información personal</h6>
                                                </div>
                                                <div class="data-item" data-bs-toggle="tooltip" title="Nombre completo registrado">
                                                    <div class="data-col">
                                                        <span class="data-label">Nombre</span>
                                                        <span class="data-value"><?php echo htmlspecialchars($user['name'] ?? ''); ?></span>
                                                    </div>
                                                </div>
                                                <div class="data-item">
                                                    <div class="data-col">
                                                        <span class="data-label">Usuario</span>
                                                        <span class="data-value"><?php echo htmlspecialchars($user['user'] ?? ''); ?></span>
                                                    </div>
                                                </div>
                                                <div class="data-item">
                                                    <div class="data-col">
                                                        <span class="data-label">Teléfono</span>
                                                        <span class="data-value"><?php echo htmlspecialchars($user['telefono'] ?? 'Sin registrar'); ?></span>
                                                    </div>
                                                </div>
                                                <div class="data-item">
                                                    <div class="data-col">
                                                        <span class="data-label">Correo electrónico</span>
                                                        <span class="data-value"><?php echo htmlspecialchars($user['correo'] ?? 'Sin registrar'); ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="nk-data data-list">
                                                <div class="data-head">
                                                    <h6 class="overline-title">Información de la cuenta</h6>
                                                </div>
                                                <div class="data-item">
                                                    <div class="data-col">
                                                        <span class="data-label">Rol</span>
                                                        <span class="data-value"><?php echo htmlspecialchars($user['IdRol'] ?? ''); ?></span>
                                                    </div>
                                                </div>
                                                <div class="data-item">
                                                    <div class="data-col">
                                                        <span class="data-label">Fecha de registro</span>
                                                        <span class="data-value"><?php echo htmlspecialchars($user['registro'] ?? ''); ?></span>
                                                    </div>
                                                </div>
                                                <div class="data-item">
                                                    <div class="data-col">
                                                        <span class="data-label">Estado</span>
                                                        <span class="data-value"><span class="badge bg-success">Activo</span></span>
                                                    </div>
                                                </div>
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
       <?php include_once 'includes/footer.php'; ?>
