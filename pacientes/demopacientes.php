<?php
include_once '../includes/head.php';
date_default_timezone_set('America/Mexico_City');

require_once '../database/conexion.php';
require_once __DIR__ . '/../includes/pendientes_lib.php';

$db = new Database();
$conn = $db->getConnection();
$catalog = pendientes_load_catalog($conn);

// Parameters
$perPage = 50;
$page = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
$offset = ($page - 1) * $perPage;
$busqueda = isset($_GET['busqueda']) ? trim((string)$_GET['busqueda']) : '';
$paramBusqueda = '%' . $busqueda . '%';
$terapeutaId = isset($_GET['terapeuta_id']) ? (int)$_GET['terapeuta_id'] : 0;

// Therapists list
$terapeutas = [];
$resT = $conn->query("SELECT id, name FROM Usuarios WHERE IdRol in (2,3) AND activo=1 ORDER BY name ASC");
if ($resT) {
    $terapeutas = $resT->fetch_all(MYSQLI_ASSOC);
}

// Total count
if ($terapeutaId > 0 && $busqueda !== '') {
    $stmtTotal = $conn->prepare("SELECT COUNT(DISTINCT n.id) as total FROM nino n INNER JOIN Cita c ON c.IdNino = n.id WHERE c.IdUsuario = ? AND n.name LIKE ?");
    $stmtTotal->bind_param('is', $terapeutaId, $paramBusqueda);
} elseif ($terapeutaId > 0) {
    $stmtTotal = $conn->prepare("SELECT COUNT(DISTINCT n.id) as total FROM nino n INNER JOIN Cita c ON c.IdNino = n.id WHERE c.IdUsuario = ?");
    $stmtTotal->bind_param('i', $terapeutaId);
} elseif ($busqueda !== '') {
    $stmtTotal = $conn->prepare("SELECT COUNT(*) as total FROM nino WHERE name LIKE ?");
    $stmtTotal->bind_param('s', $paramBusqueda);
} else {
    $stmtTotal = $conn->prepare("SELECT COUNT(*) as total FROM nino");
}
$stmtTotal->execute();
$resTotal = $stmtTotal->get_result();
$row = $resTotal ? $resTotal->fetch_assoc() : [];
$total = (int)($row['total'] ?? 0);

// Page data
if ($terapeutaId > 0 && $busqueda !== '') {
    $stmt = $conn->prepare("SELECT DISTINCT n.* FROM nino n INNER JOIN Cita c ON c.IdNino = n.id WHERE c.IdUsuario = ? AND n.name LIKE ? ORDER BY n.name ASC LIMIT ?, ?");
    $stmt->bind_param('isii', $terapeutaId, $paramBusqueda, $offset, $perPage);
} elseif ($terapeutaId > 0) {
    $stmt = $conn->prepare("SELECT DISTINCT n.* FROM nino n INNER JOIN Cita c ON c.IdNino = n.id WHERE c.IdUsuario = ? ORDER BY n.name ASC LIMIT ?, ?");
    $stmt->bind_param('iii', $terapeutaId, $offset, $perPage);
} elseif ($busqueda !== '') {
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

function demo_profile_dots(int $id_nino, array $catalog): string
{
    static $dbConn = null;
    if ($dbConn === null) {
        $db = new Database();
        $dbConn = $db->getConnection();
    }
    $st = pendientes_load_patient_status($dbConn, $id_nino);
    // Show profiles from active flows (usually diagnostico)
    $flows = pendientes_active_flows($catalog, $st);
    $profiles = [];
    foreach ($flows as $f) {
        $pps = isset($f['perfiles']) && is_array($f['perfiles']) ? $f['perfiles'] : [];
        foreach ($pps as $p) {
            $profiles[] = $p;
        }
    }
    usort($profiles, fn($a, $b) => ((int)($a['orden'] ?? 0)) <=> ((int)($b['orden'] ?? 0)));

    $html = '<div class="d-flex align-items-center gap-1 flex-wrap">';
    foreach ($profiles as $p) {
        $prog = pendientes_profile_progress($id_nino, $p, $st);
        $dot = pendientes_status_dot_class($prog['status']);
        $title = htmlspecialchars(($p['nombre'] ?? '') . ': ' . pendientes_status_label($prog['status']));
        $html .= '<span class="status dot dot-lg ' . $dot . '" title="' . $title . '"></span>';
    }
    $html .= '</div>';
    return $html;
}

?>

<div class="nk-wrap ">
    <?php include_once '../includes/menu_superior.php'; ?>
    <div class="nk-content nk-content-fluid">
        <div class="container-xl wide-xl">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Pacientes <span class="badge bg-outline-info">DEMO</span></h3>
                            <div class="nk-block-des text-soft">
                                <p>Listado de pacientes (sandbox para pendientes/flujo).</p>
                            </div>
                        </div>
                        <div class="nk-block-head-content">
                            <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] != 2): ?>
                                <a class="btn btn-outline-secondary" href="/pacientes/admin_perfiles.php">Configurar perfiles</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="nk-block">
                    <div class="card card-full">
                        <div class="card-inner">
                            <form method="GET" class="mb-3">
                                <div class="row g-2 align-items-end">
                                    <div class="col-12 col-md-5">
                                        <label class="form-label">Buscar por nombre</label>
                                        <input type="text" name="busqueda" class="form-control" placeholder="Buscar por nombre" value="<?php echo htmlspecialchars($busqueda); ?>">
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="form-label">Terapeuta</label>
                                        <select name="terapeuta_id" class="form-select">
                                            <option value="0">Todos</option>
                                            <?php foreach ($terapeutas as $t):
                                                $tid = (int)($t['id'] ?? 0);
                                                $sel = $tid === $terapeutaId ? 'selected' : '';
                                            ?>
                                                <option value="<?php echo $tid; ?>" <?php echo $sel; ?>><?php echo htmlspecialchars(ucwords(strtolower((string)($t['name'] ?? '')))); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-3 d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">Filtrar</button>
                                        <a class="btn btn-outline-secondary" href="/pacientes/demopacientes.php">Limpiar</a>
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
                                        <th>Pendientes</th>
                                        <th>Detalle</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pacientes as $p):
                                        $pid = (int)($p['id'] ?? ($p['Id'] ?? 0));
                                    ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars((string)$pid); ?></td>
                                            <td><?php echo htmlspecialchars((string)($p['name'] ?? '')); ?></td>
                                            <td><?php echo htmlspecialchars((string)($p['edad'] ?? '')); ?></td>
                                            <td><?php echo htmlspecialchars((string)($p['Observacion'] ?? '')); ?></td>
                                            <td><?php echo $pid > 0 ? demo_profile_dots($pid, $catalog) : ''; ?></td>
                                            <td><a class="btn btn-sm btn-primary" href="/pacientes/demopaciente.php?id=<?php echo urlencode((string)$pid); ?>">Ver</a></td>
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
                                $totalPages = max(1, (int)ceil($total / $perPage));
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>
