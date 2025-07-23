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

    // Parámetros de paginación y búsqueda
    $perPage = 50;
    $page = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
    $offset = ($page - 1) * $perPage;
    $busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
    $paramBusqueda = '%' . $busqueda . '%';

    // Total de registros
    if (!empty($busqueda)) {
        $stmtTotal = $conn->prepare("SELECT COUNT(*) as total FROM nino WHERE name LIKE ?");
        $stmtTotal->bind_param('s', $paramBusqueda);
    } else {
        $stmtTotal = $conn->prepare("SELECT COUNT(*) as total FROM nino");
    }
    $stmtTotal->execute();
    $resTotal = $stmtTotal->get_result();
    $row = $resTotal->fetch_assoc();
    $total = $row['total'] ?? 0;

    // Datos paginados
    if (!empty($busqueda)) {
        $stmt = $conn->prepare("SELECT * FROM nino WHERE name LIKE ? ORDER BY name ASC LIMIT ?, ?");
        $stmt->bind_param('sii', $paramBusqueda, $offset, $perPage);
    } else {
        $stmt = $conn->prepare("SELECT * FROM nino ORDER BY name ASC LIMIT ?, ?");
        $stmt->bind_param('ii', $offset, $perPage);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $pacientes = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

    $db->closeConnection();
    ?>
    <div class="nk-content nk-content-fluid">
        <div class="container-xl wide-xl">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Pacientes</h3>
                            <div class="nk-block-des text-soft">
                                <p>Listado de pacientes.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="nk-block">
                    <div class="card card-full">
                        <div class="card-inner">
                            <!-- Formulario de búsqueda -->
                            <form method="GET" class="mb-3">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <input type="text" name="busqueda" class="form-control" placeholder="Buscar por nombre" value="<?php echo htmlspecialchars($busqueda); ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-primary">Buscar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card-inner table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Edad</th>
                                        <th>Programa</th>
                                        <th>Detalle</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pacientes as $p): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($p['id'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($p['name'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($p['edad'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($p['Observacion'] ?? ''); ?></td>
                                            <td><a class="btn btn-sm btn-primary" href="/pacientes/paciente.php?id=<?php echo urlencode($p['id']); ?>">Ver</a></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($pacientes)): ?>
                                        <tr><td colspan="6">No hay pacientes.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-inner">
                            <ul class="pagination justify-content-center">
                                <?php
                                $totalPages = max(1, ceil($total / $perPage));
                                for ($i = 1; $i <= $totalPages; $i++):
                                    $active = ($i === $page) ? 'active' : '';
                                    $params = $_GET;
                                    $params['pagina'] = $i;
                                    $query = http_build_query($params);
                                ?>
                                    <li class="page-item <?php echo $active; ?>">
                                        <a class="page-link" href="?<?php echo $query; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                            </ul>
                        </div>
                    </div><!-- .card -->
                </div><!-- .nk-block -->
            </div>
        </div>
    </div>
</div>
<?php include_once '../includes/footer.php'; ?>
