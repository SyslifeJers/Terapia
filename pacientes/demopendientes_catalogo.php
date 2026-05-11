<?php
include_once '../includes/head.php';
date_default_timezone_set('America/Mexico_City');
require_once '../database/conexion.php';
require_once __DIR__ . '/../includes/pendientes_lib.php';

$db = new Database();
$conn = $db->getConnection();

$isAdmin = isset($_SESSION['rol']) && $_SESSION['rol'] != 2;

$error = '';
$ok = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$isAdmin) {
        $error = 'No autorizado.';
    } else {
        $raw = (string)($_POST['catalog_json'] ?? '');
        $raw = trim($raw);
        $json = $raw !== '' ? json_decode($raw, true) : null;
        if (!is_array($json)) {
            $error = 'JSON inválido.';
        } elseif (!isset($json['flows']) || !is_array($json['flows'])) {
            $error = 'El catálogo debe contener "flows".';
        } else {
            if (pendientes_save_catalog($conn, $json)) {
                $ok = 'Catálogo guardado.';
            } else {
                $error = 'No se pudo guardar el catálogo.';
            }
        }
    }
}

$catalog = pendientes_load_catalog($conn);
$catalogText = json_encode($catalog, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>

<div class="nk-wrap ">
    <?php include_once '../includes/menu_superior.php'; ?>
    <div class="nk-content nk-content-fluid">
        <div class="container-xl wide-xl">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Demo: Catálogo de pendientes</h3>
                            <div class="nk-block-des text-soft">
                                <p>Edita flujos, perfiles y tareas. Se guarda en tablas `spu_flujos`, `spu_perfiles` y `spu_tareas`.</p>
                            </div>
                        </div>
                        <div class="nk-block-head-content">
                            <a class="btn btn-outline-secondary" href="/pacientes/demopacientes.php">Volver</a>
                        </div>
                    </div>
                </div>

                <?php if ($error !== ''): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <?php if ($ok !== ''): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($ok); ?></div>
                <?php endif; ?>

                <div class="nk-block">
                    <div class="card card-bordered">
                        <div class="card-inner">
                            <?php if (!$isAdmin): ?>
                                <div class="alert alert-warning mb-3">Solo administradores pueden guardar cambios. Puedes ver el JSON.</div>
                            <?php endif; ?>
                            <form method="POST">
                                <div class="mb-3">
                                    <textarea class="form-control" name="catalog_json" rows="28" spellcheck="false"><?php echo htmlspecialchars($catalogText); ?></textarea>
                                </div>
                                <?php if ($isAdmin): ?>
                                    <button type="submit" class="btn btn-primary">Guardar</button>
                                <?php endif; ?>
                            </form>
                            <div class="form-text mt-2">Iconos: usa clases NioIcon sin el prefijo `ni `. Ejemplo: `ni-file-text`. Colores: hex como `#3B82F6`.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $db->closeConnection(); ?>
<?php include_once '../includes/footer.php'; ?>
