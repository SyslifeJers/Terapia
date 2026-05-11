<?php
include_once '../includes/head.php';
date_default_timezone_set('America/Mexico_City');

require_once '../database/conexion.php';
require_once __DIR__ . '/../includes/pendientes_lib.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] == 2) {
    http_response_code(403);
    echo '<div class="p-4">No autorizado.</div>';
    exit;
}

function spu_slugify(string $text): string
{
    $text = trim($text);
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/', '_', $text);
    $text = trim((string)$text, '_');
    return $text !== '' ? substr($text, 0, 80) : 'item_' . time();
}

$db = new Database();
$conn = $db->getConnection();
pendientes_ensure_schema($conn);

$message = '';
$error = '';
$viewProfileId = (int)($_REQUEST['view_profile'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = trim((string)($_POST['action'] ?? ''));
    $viewProfileId = (int)($_POST['view_profile'] ?? $viewProfileId);

    if ($action === 'delete_flow') {
        $id = (int)($_POST['id_flujo'] ?? 0);
        if ($id > 0) {
            $stmt = $conn->prepare('DELETE FROM spu_flujos WHERE id_flujo = ?');
            $stmt->bind_param('i', $id);
            if ($stmt && $stmt->execute()) {
                $message = 'Flujo eliminado.';
            } else {
                $error = 'No se pudo eliminar el flujo.';
            }
            if ($stmt) {
                $stmt->close();
            }
        }
    }

    if ($action === 'delete_profile') {
        $id = (int)($_POST['id_perfil'] ?? 0);
        if ($id > 0) {
            $stmt = $conn->prepare('DELETE FROM spu_perfiles WHERE id_perfil = ?');
            $stmt->bind_param('i', $id);
            if ($stmt && $stmt->execute()) {
                $message = 'Perfil eliminado.';
            } else {
                $error = 'No se pudo eliminar el perfil.';
            }
            if ($stmt) {
                $stmt->close();
            }
        }
    }

    if ($action === 'delete_task') {
        $id = (int)($_POST['id_tarea'] ?? 0);
        if ($id > 0) {
            $stmt = $conn->prepare('DELETE FROM spu_tareas WHERE id_tarea = ?');
            $stmt->bind_param('i', $id);
            if ($stmt && $stmt->execute()) {
                $message = 'Tarea eliminada.';
            } else {
                $error = 'No se pudo eliminar la tarea.';
            }
            if ($stmt) {
                $stmt->close();
            }
        }
    }

    if ($action === 'save_flow') {
        $id = (int)($_POST['id_flujo'] ?? 0);
        $nombre = trim((string)($_POST['nombre'] ?? ''));
        $slug = trim((string)($_POST['slug'] ?? ''));
        $slug = $slug !== '' ? spu_slugify($slug) : spu_slugify($nombre);
        $descripcion = trim((string)($_POST['descripcion'] ?? ''));
        $icon = trim((string)($_POST['icon'] ?? 'ni-activity-round'));
        $color = trim((string)($_POST['color'] ?? '#6D5BFF'));
        $orden = (int)($_POST['orden'] ?? 0);
        $activo = isset($_POST['activo']) ? 1 : 0;

        if ($nombre === '') {
            $error = 'El flujo requiere nombre.';
        } else {
            if ($id > 0) {
                $stmt = $conn->prepare('UPDATE spu_flujos SET slug = ?, nombre = ?, descripcion = ?, icon = ?, color = ?, orden = ?, activo = ? WHERE id_flujo = ?');
                $stmt->bind_param('sssssiii', $slug, $nombre, $descripcion, $icon, $color, $orden, $activo, $id);
            } else {
                $stmt = $conn->prepare('INSERT INTO spu_flujos (slug, nombre, descripcion, icon, color, orden, activo) VALUES (?, ?, ?, ?, ?, ?, ?)');
                $stmt->bind_param('sssssii', $slug, $nombre, $descripcion, $icon, $color, $orden, $activo);
            }
            if ($stmt && $stmt->execute()) {
                $message = 'Flujo guardado.';
            } else {
                $error = 'No se pudo guardar el flujo.';
            }
            if ($stmt) {
                $stmt->close();
            }
        }
    }

    if ($action === 'save_profile') {
        $id = (int)($_POST['id_perfil'] ?? 0);
        $idFlujo = (int)($_POST['id_flujo'] ?? 0);
        $nombre = trim((string)($_POST['nombre'] ?? ''));
        $slug = trim((string)($_POST['slug'] ?? ''));
        $slug = $slug !== '' ? spu_slugify($slug) : spu_slugify($nombre);
        $descripcion = trim((string)($_POST['descripcion'] ?? ''));
        $icon = trim((string)($_POST['icon'] ?? 'ni-clipboad-check'));
        $color = trim((string)($_POST['color'] ?? '#94A3B8'));
        $orden = (int)($_POST['orden'] ?? 0);
        $activo = isset($_POST['activo']) ? 1 : 0;

        if ($nombre === '' || $idFlujo <= 0) {
            $error = 'El perfil requiere flujo y nombre.';
        } else {
            if ($id > 0) {
                $stmt = $conn->prepare('UPDATE spu_perfiles SET id_flujo = ?, slug = ?, nombre = ?, descripcion = ?, icon = ?, color = ?, orden = ?, activo = ? WHERE id_perfil = ?');
                $stmt->bind_param('isssssiii', $idFlujo, $slug, $nombre, $descripcion, $icon, $color, $orden, $activo, $id);
            } else {
                $stmt = $conn->prepare('INSERT INTO spu_perfiles (id_flujo, slug, nombre, descripcion, icon, color, orden, activo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
                $stmt->bind_param('isssssii', $idFlujo, $slug, $nombre, $descripcion, $icon, $color, $orden, $activo);
            }
            if ($stmt && $stmt->execute()) {
                $message = 'Perfil guardado.';
                if ($id === 0) {
                    $viewProfileId = (int)$conn->insert_id;
                }
            } else {
                $error = 'No se pudo guardar el perfil.';
            }
            if ($stmt) {
                $stmt->close();
            }
        }
    }

    if ($action === 'save_task') {
        $id = (int)($_POST['id_tarea'] ?? 0);
        $idPerfil = (int)($_POST['id_perfil'] ?? 0);
        $titulo = trim((string)($_POST['titulo'] ?? ''));
        $slug = trim((string)($_POST['slug'] ?? ''));
        $slug = $slug !== '' ? spu_slugify($slug) : spu_slugify($titulo);
        $descripcion = trim((string)($_POST['descripcion'] ?? ''));
        $evidencia = trim((string)($_POST['evidencia'] ?? 'none'));
        if (!in_array($evidencia, ['none', 'optional', 'required'], true)) {
            $evidencia = 'none';
        }
        $alertaTipo = trim((string)($_POST['alerta_tipo'] ?? 'none'));
        if (!in_array($alertaTipo, ['none', 'citas'], true)) {
            $alertaTipo = 'none';
        }
        $alertaCantidad = max(0, (int)($_POST['alerta_cantidad'] ?? 0));
        $tiposPermitidos = trim((string)($_POST['tipos_permitidos'] ?? ''));
        $tipos = array_values(array_filter(array_map('trim', explode(',', $tiposPermitidos))));
        $orden = (int)($_POST['orden'] ?? 0);
        $activo = isset($_POST['activo']) ? 1 : 0;

        if ($titulo === '' || $idPerfil <= 0) {
            $error = 'La tarea requiere perfil y título.';
        } else {
            $tiposJson = json_encode($tipos, JSON_UNESCAPED_UNICODE);
            if ($id > 0) {
                $stmt = $conn->prepare('UPDATE spu_tareas SET id_perfil = ?, slug = ?, titulo = ?, descripcion = ?, evidencia = ?, alerta_tipo = ?, alerta_cantidad = ?, tipos_permitidos = ?, orden = ?, activo = ? WHERE id_tarea = ?');
                $stmt->bind_param('isssssisiii', $idPerfil, $slug, $titulo, $descripcion, $evidencia, $alertaTipo, $alertaCantidad, $tiposJson, $orden, $activo, $id);
            } else {
                $stmt = $conn->prepare('INSERT INTO spu_tareas (id_perfil, slug, titulo, descripcion, evidencia, alerta_tipo, alerta_cantidad, tipos_permitidos, orden, activo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
                $stmt->bind_param('isssssisii', $idPerfil, $slug, $titulo, $descripcion, $evidencia, $alertaTipo, $alertaCantidad, $tiposJson, $orden, $activo);
            }
            if ($stmt && $stmt->execute()) {
                $message = 'Tarea guardada.';
                $viewProfileId = $idPerfil;
            } else {
                $error = 'No se pudo guardar la tarea.';
            }
            if ($stmt) {
                $stmt->close();
            }
        }
    }
}

$catalog = pendientes_load_catalog($conn);

if ($viewProfileId <= 0) {
    foreach ($catalog['flows'] as $flow) {
        if (!empty($flow['perfiles'][0]['db_id'])) {
            $viewProfileId = (int)$flow['perfiles'][0]['db_id'];
            break;
        }
    }
}

$viewProfile = null;
$viewFlow = null;
foreach ($catalog['flows'] as $flow) {
    foreach (($flow['perfiles'] ?? []) as $profile) {
        if ((int)$profile['db_id'] === $viewProfileId) {
            $viewProfile = $profile;
            $viewFlow = $flow;
            break 2;
        }
    }
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
                            <h3 class="nk-block-title page-title">Administrador de perfiles</h3>
                            <div class="nk-block-des text-soft">
                                <p>Configura flujos, perfiles y tareas del seguimiento.</p>
                            </div>
                        </div>
                        <div class="nk-block-head-content">
                            <a class="btn btn-outline-secondary" href="/pacientes/demopacientes.php">Volver</a>
                        </div>
                    </div>
                </div>

                <style>
                    .spu-flow-card { border-top-width: 4px; }
                    .spu-flow-badge { border-radius: 999px; padding: 4px 10px; font-size: 12px; font-weight: 600; }
                    .spu-profile-item { border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 12px; padding: 14px; margin-bottom: 12px; transition: 0.2s ease; }
                    .spu-profile-item.is-active { border-color: rgba(109, 91, 255, 0.35); background: rgba(109, 91, 255, 0.05); }
                    .spu-profile-link { color: inherit; text-decoration: none; display: block; }
                    .spu-profile-link:hover { color: inherit; }
                    .spu-task-item { border-top: 1px dashed rgba(15, 23, 42, 0.12); padding-top: 10px; margin-top: 10px; }
                    .spu-actions { display: flex; gap: 6px; justify-content: flex-end; flex-wrap: wrap; }
                    .spu-actions form { margin: 0; }
                    .spu-meta-badges { display: flex; gap: 6px; flex-wrap: wrap; margin-top: 8px; }
                    .spu-toolbar { display: flex; gap: 8px; flex-wrap: wrap; }
                </style>

                <?php if ($error !== ''): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <?php if ($message !== ''): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>

                <div class="nk-block-head nk-block-head-sm px-0">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h5 class="title mb-0">Configuración dinámica</h5>
                        </div>
                        <div class="nk-block-head-content">
                            <div class="spu-toolbar">
                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#flowModal" data-mode="create">Nuevo flujo</button>
                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#profileModal" data-mode="create" data-flow-id="<?php echo (int)($viewFlow['db_id'] ?? ($catalog['flows'][0]['db_id'] ?? 0)); ?>">Nuevo perfil</button>
                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#taskModal" data-mode="create" data-profile-id="<?php echo (int)($viewProfile['db_id'] ?? 0); ?>">Nueva tarea</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="nk-block">
                    <?php foreach ($catalog['flows'] as $flow): ?>
                        <div class="card card-bordered mb-3 spu-flow-card" style="border-top-color: <?php echo htmlspecialchars((string)($flow['color'] ?? '#6D5BFF')); ?>;">
                            <div class="card-inner">
                                <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 mb-3">
                                    <div>
                                        <h5 class="title mb-1"><?php echo htmlspecialchars((string)$flow['nombre']); ?></h5>
                                        <p class="text-soft mb-2"><?php echo htmlspecialchars((string)($flow['descripcion'] ?? '')); ?></p>
                                        <span class="spu-flow-badge" style="background: <?php echo htmlspecialchars((string)$flow['color']); ?>; color: #fff;"><?php echo count($flow['perfiles'] ?? []); ?> perfiles</span>
                                    </div>
                                    <div class="spu-actions">
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#flowModal" data-mode="edit" data-id="<?php echo (int)$flow['db_id']; ?>" data-nombre="<?php echo htmlspecialchars((string)$flow['nombre'], ENT_QUOTES); ?>" data-slug="<?php echo htmlspecialchars((string)$flow['id'], ENT_QUOTES); ?>" data-descripcion="<?php echo htmlspecialchars((string)($flow['descripcion'] ?? ''), ENT_QUOTES); ?>" data-icon="<?php echo htmlspecialchars((string)($flow['icon'] ?? ''), ENT_QUOTES); ?>" data-color="<?php echo htmlspecialchars((string)($flow['color'] ?? '#6D5BFF'), ENT_QUOTES); ?>" data-orden="<?php echo (int)($flow['orden'] ?? 0); ?>">Editar flujo</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#profileModal" data-mode="create" data-flow-id="<?php echo (int)$flow['db_id']; ?>">Agregar perfil</button>
                                        <form method="POST" onsubmit="return confirm('Eliminar flujo y sus perfiles/tareas relacionadas?');">
                                            <input type="hidden" name="action" value="delete_flow">
                                            <input type="hidden" name="id_flujo" value="<?php echo (int)$flow['db_id']; ?>">
                                            <button class="btn btn-sm btn-outline-danger" type="submit">Eliminar</button>
                                        </form>
                                    </div>
                                </div>

                                <?php if (empty($flow['perfiles'])): ?>
                                    <p class="text-soft mb-0">Este flujo no tiene perfiles todavía.</p>
                                <?php else: ?>
                                    <?php foreach (($flow['perfiles'] ?? []) as $profile): ?>
                                        <div class="spu-profile-item <?php echo (int)$profile['db_id'] === $viewProfileId ? 'is-active' : ''; ?>">
                                            <div class="row g-2 align-items-start">
                                                <div class="col-12 col-lg-7">
                                                    <a class="spu-profile-link" href="?view_profile=<?php echo (int)$profile['db_id']; ?>">
                                                        <h6 class="title mb-1"><?php echo htmlspecialchars((string)$profile['nombre']); ?></h6>
                                                        <p class="text-soft mb-0"><?php echo htmlspecialchars((string)($profile['descripcion'] ?? '')); ?></p>
                                                        <div class="spu-meta-badges">
                                                            <span class="badge bg-light text-dark"><?php echo count($profile['tareas'] ?? []); ?> tareas</span>
                                                            <span class="badge bg-light text-dark"><?php echo htmlspecialchars((string)($profile['icon'] ?? '')); ?></span>
                                                        </div>
                                                    </a>
                                                </div>
                                                <div class="col-12 col-lg-5">
                                                    <div class="spu-actions">
                                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#profileModal" data-mode="edit" data-id="<?php echo (int)$profile['db_id']; ?>" data-flow-id="<?php echo (int)$flow['db_id']; ?>" data-nombre="<?php echo htmlspecialchars((string)$profile['nombre'], ENT_QUOTES); ?>" data-slug="<?php echo htmlspecialchars((string)$profile['id'], ENT_QUOTES); ?>" data-descripcion="<?php echo htmlspecialchars((string)($profile['descripcion'] ?? ''), ENT_QUOTES); ?>" data-icon="<?php echo htmlspecialchars((string)($profile['icon'] ?? ''), ENT_QUOTES); ?>" data-color="<?php echo htmlspecialchars((string)($profile['color'] ?? '#94A3B8'), ENT_QUOTES); ?>" data-orden="<?php echo (int)($profile['orden'] ?? 0); ?>" data-view-profile="<?php echo (int)$profile['db_id']; ?>">Editar perfil</button>
                                                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#taskModal" data-mode="create" data-profile-id="<?php echo (int)$profile['db_id']; ?>" data-view-profile="<?php echo (int)$profile['db_id']; ?>">Agregar tarea</button>
                                                        <form method="POST" onsubmit="return confirm('Eliminar perfil y sus tareas relacionadas?');">
                                                            <input type="hidden" name="action" value="delete_profile">
                                                            <input type="hidden" name="id_perfil" value="<?php echo (int)$profile['db_id']; ?>">
                                                            <input type="hidden" name="view_profile" value="<?php echo (int)$viewProfileId; ?>">
                                                            <button class="btn btn-sm btn-outline-danger" type="submit">Eliminar</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div class="card card-bordered">
                        <div class="card-inner">
                            <?php if ($viewProfile): ?>
                                <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 mb-3">
                                    <div>
                                        <h5 class="title mb-1">Tareas de <?php echo htmlspecialchars((string)$viewProfile['nombre']); ?></h5>
                                        <p class="text-soft mb-0"><?php echo htmlspecialchars((string)($viewFlow['nombre'] ?? '')); ?><?php echo !empty($viewProfile['descripcion']) ? ' · ' . htmlspecialchars((string)$viewProfile['descripcion']) : ''; ?></p>
                                    </div>
                                    <div class="spu-actions">
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#profileModal" data-mode="edit" data-id="<?php echo (int)$viewProfile['db_id']; ?>" data-flow-id="<?php echo (int)($viewFlow['db_id'] ?? 0); ?>" data-nombre="<?php echo htmlspecialchars((string)$viewProfile['nombre'], ENT_QUOTES); ?>" data-slug="<?php echo htmlspecialchars((string)$viewProfile['id'], ENT_QUOTES); ?>" data-descripcion="<?php echo htmlspecialchars((string)($viewProfile['descripcion'] ?? ''), ENT_QUOTES); ?>" data-icon="<?php echo htmlspecialchars((string)($viewProfile['icon'] ?? ''), ENT_QUOTES); ?>" data-color="<?php echo htmlspecialchars((string)($viewProfile['color'] ?? '#94A3B8'), ENT_QUOTES); ?>" data-orden="<?php echo (int)($viewProfile['orden'] ?? 0); ?>" data-view-profile="<?php echo (int)$viewProfile['db_id']; ?>">Editar perfil</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#taskModal" data-mode="create" data-profile-id="<?php echo (int)$viewProfile['db_id']; ?>" data-view-profile="<?php echo (int)$viewProfile['db_id']; ?>">Agregar tarea</button>
                                    </div>
                                </div>
                                <?php if (empty($viewProfile['tareas'])): ?>
                                    <p class="text-soft mb-0">Este perfil no tiene tareas todavía.</p>
                                <?php else: ?>
                                    <?php foreach (($viewProfile['tareas'] ?? []) as $task): ?>
                                        <div class="spu-task-item <?php echo $task === reset($viewProfile['tareas']) ? 'mt-0 pt-0 border-top-0' : ''; ?>">
                                            <div class="row g-2 align-items-start">
                                                <div class="col-12 col-lg-8">
                                                    <div class="fw-semibold"><?php echo htmlspecialchars((string)$task['titulo']); ?></div>
                                                    <div class="text-soft small mt-1"><?php echo htmlspecialchars((string)($task['descripcion'] ?? '')); ?></div>
                                                    <div class="spu-meta-badges mt-2">
                                                        <span class="badge bg-light text-dark">Evidencia: <?php echo htmlspecialchars((string)($task['evidencia'] ?? 'none')); ?></span>
                                                        <?php if (($task['alerta_tipo'] ?? 'none') === 'citas'): ?>
                                                            <span class="badge bg-warning text-dark">Alerta tras <?php echo (int)($task['alerta_cantidad'] ?? 0); ?> citas</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-4">
                                                    <div class="spu-actions">
                                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#taskModal" data-mode="edit" data-id="<?php echo (int)$task['db_id']; ?>" data-profile-id="<?php echo (int)$viewProfile['db_id']; ?>" data-titulo="<?php echo htmlspecialchars((string)$task['titulo'], ENT_QUOTES); ?>" data-slug="<?php echo htmlspecialchars((string)$task['id'], ENT_QUOTES); ?>" data-descripcion="<?php echo htmlspecialchars((string)($task['descripcion'] ?? ''), ENT_QUOTES); ?>" data-evidencia="<?php echo htmlspecialchars((string)($task['evidencia'] ?? 'none'), ENT_QUOTES); ?>" data-alerta-tipo="<?php echo htmlspecialchars((string)($task['alerta_tipo'] ?? 'none'), ENT_QUOTES); ?>" data-alerta-cantidad="<?php echo (int)($task['alerta_cantidad'] ?? 0); ?>" data-tipos="<?php echo htmlspecialchars(implode(', ', is_array($task['tipos'] ?? null) ? $task['tipos'] : []), ENT_QUOTES); ?>" data-orden="<?php echo (int)($task['orden'] ?? 0); ?>" data-view-profile="<?php echo (int)$viewProfile['db_id']; ?>">Editar tarea</button>
                                                        <form method="POST" onsubmit="return confirm('Eliminar tarea?');">
                                                            <input type="hidden" name="action" value="delete_task">
                                                            <input type="hidden" name="id_tarea" value="<?php echo (int)$task['db_id']; ?>">
                                                            <input type="hidden" name="view_profile" value="<?php echo (int)$viewProfile['db_id']; ?>">
                                                            <button class="btn btn-sm btn-outline-danger" type="submit">Eliminar</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            <?php else: ?>
                                <h5 class="title mb-1">Tareas del perfil</h5>
                                <p class="text-soft mb-0">Selecciona un perfil para ver sus tareas.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="flowModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Flujo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="save_flow">
                    <input type="hidden" name="id_flujo" id="flow_id_flujo" value="0">
                    <input type="hidden" name="view_profile" value="<?php echo (int)$viewProfileId; ?>">
                    <div class="row g-2">
                        <div class="col-12"><label class="form-label">Nombre</label><input class="form-control" id="flow_nombre" name="nombre"></div>
                        <div class="col-12"><label class="form-label">Slug</label><input class="form-control" id="flow_slug" name="slug"></div>
                        <div class="col-12"><label class="form-label">Descripción</label><textarea class="form-control" id="flow_descripcion" name="descripcion"></textarea></div>
                        <div class="col-md-6"><label class="form-label">Icono</label><input class="form-control" id="flow_icon" name="icon"></div>
                        <div class="col-md-3"><label class="form-label">Color</label><input type="color" class="form-control form-control-color" id="flow_color" name="color" value="#6D5BFF"></div>
                        <div class="col-md-3"><label class="form-label">Orden</label><input type="number" class="form-control" id="flow_orden" name="orden" value="0"></div>
                        <div class="col-12"><div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="flow_activo" name="activo" checked><label class="custom-control-label" for="flow_activo">Activo</label></div></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar flujo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="profileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Perfil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="save_profile">
                    <input type="hidden" name="id_perfil" id="profile_id_perfil" value="0">
                    <input type="hidden" name="view_profile" id="profile_view_profile" value="<?php echo (int)$viewProfileId; ?>">
                    <div class="row g-2">
                        <div class="col-12"><label class="form-label">Flujo</label><select class="form-select" id="profile_id_flujo" name="id_flujo"><?php foreach ($catalog['flows'] as $flow): ?><option value="<?php echo (int)$flow['db_id']; ?>"><?php echo htmlspecialchars((string)$flow['nombre']); ?></option><?php endforeach; ?></select></div>
                        <div class="col-12"><label class="form-label">Nombre</label><input class="form-control" id="profile_nombre" name="nombre"></div>
                        <div class="col-12"><label class="form-label">Slug</label><input class="form-control" id="profile_slug" name="slug"></div>
                        <div class="col-12"><label class="form-label">Descripción</label><textarea class="form-control" id="profile_descripcion" name="descripcion"></textarea></div>
                        <div class="col-md-6"><label class="form-label">Icono/logo</label><input class="form-control" id="profile_icon" name="icon"></div>
                        <div class="col-md-3"><label class="form-label">Color</label><input type="color" class="form-control form-control-color" id="profile_color" name="color" value="#94A3B8"></div>
                        <div class="col-md-3"><label class="form-label">Orden</label><input type="number" class="form-control" id="profile_orden" name="orden" value="0"></div>
                        <div class="col-12"><div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="profile_activo" name="activo" checked><label class="custom-control-label" for="profile_activo">Activo</label></div></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar perfil</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="taskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tarea</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="save_task">
                    <input type="hidden" name="id_tarea" id="task_id_tarea" value="0">
                    <input type="hidden" name="view_profile" id="task_view_profile" value="<?php echo (int)$viewProfileId; ?>">
                    <div class="row g-2">
                        <div class="col-12"><label class="form-label">Perfil</label><select class="form-select" id="task_id_perfil" name="id_perfil"><?php foreach ($catalog['flows'] as $flow): foreach (($flow['perfiles'] ?? []) as $profile): ?><option value="<?php echo (int)$profile['db_id']; ?>"><?php echo htmlspecialchars((string)$flow['nombre'] . ' / ' . (string)$profile['nombre']); ?></option><?php endforeach; endforeach; ?></select></div>
                        <div class="col-12"><label class="form-label">Título</label><input class="form-control" id="task_titulo" name="titulo"></div>
                        <div class="col-12"><label class="form-label">Slug</label><input class="form-control" id="task_slug" name="slug"></div>
                        <div class="col-12"><label class="form-label">Descripción</label><textarea class="form-control" id="task_descripcion" name="descripcion"></textarea></div>
                        <div class="col-md-4"><label class="form-label">Evidencia</label><select class="form-select" id="task_evidencia" name="evidencia"><option value="none">No</option><option value="optional">Opcional</option><option value="required">Obligatoria</option></select></div>
                        <div class="col-md-4"><label class="form-label">Alerta</label><select class="form-select" id="task_alerta_tipo" name="alerta_tipo"><option value="none">Sin alerta</option><option value="citas">Por citas</option></select></div>
                        <div class="col-md-4"><label class="form-label">Núm. citas</label><input type="number" min="0" class="form-control" id="task_alerta_cantidad" name="alerta_cantidad" value="0"></div>
                        <div class="col-md-8"><label class="form-label">Tipos permitidos</label><input class="form-control" id="task_tipos" name="tipos_permitidos" placeholder="pdf, png, jpg"></div>
                        <div class="col-md-2"><label class="form-label">Orden</label><input type="number" class="form-control" id="task_orden" name="orden" value="0"></div>
                        <div class="col-md-2 d-flex align-items-end"><div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="task_activo" name="activo" checked><label class="custom-control-label" for="task_activo">Activa</label></div></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar tarea</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var flowModal = document.getElementById('flowModal');
    if (flowModal) {
        flowModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            document.getElementById('flow_id_flujo').value = button?.getAttribute('data-id') || '0';
            document.getElementById('flow_nombre').value = button?.getAttribute('data-nombre') || '';
            document.getElementById('flow_slug').value = button?.getAttribute('data-slug') || '';
            document.getElementById('flow_descripcion').value = button?.getAttribute('data-descripcion') || '';
            document.getElementById('flow_icon').value = button?.getAttribute('data-icon') || 'ni-activity-round';
            document.getElementById('flow_color').value = button?.getAttribute('data-color') || '#6D5BFF';
            document.getElementById('flow_orden').value = button?.getAttribute('data-orden') || '0';
            document.getElementById('flow_activo').checked = true;
        });
    }

    var profileModal = document.getElementById('profileModal');
    if (profileModal) {
        profileModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            document.getElementById('profile_id_perfil').value = button?.getAttribute('data-id') || '0';
            document.getElementById('profile_id_flujo').value = button?.getAttribute('data-flow-id') || document.getElementById('profile_id_flujo').value;
            document.getElementById('profile_nombre').value = button?.getAttribute('data-nombre') || '';
            document.getElementById('profile_slug').value = button?.getAttribute('data-slug') || '';
            document.getElementById('profile_descripcion').value = button?.getAttribute('data-descripcion') || '';
            document.getElementById('profile_icon').value = button?.getAttribute('data-icon') || 'ni-clipboad-check';
            document.getElementById('profile_color').value = button?.getAttribute('data-color') || '#94A3B8';
            document.getElementById('profile_orden').value = button?.getAttribute('data-orden') || '0';
            document.getElementById('profile_view_profile').value = button?.getAttribute('data-view-profile') || '<?php echo (int)$viewProfileId; ?>';
            document.getElementById('profile_activo').checked = true;
        });
    }

    var taskModal = document.getElementById('taskModal');
    if (taskModal) {
        taskModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            document.getElementById('task_id_tarea').value = button?.getAttribute('data-id') || '0';
            document.getElementById('task_id_perfil').value = button?.getAttribute('data-profile-id') || document.getElementById('task_id_perfil').value;
            document.getElementById('task_titulo').value = button?.getAttribute('data-titulo') || '';
            document.getElementById('task_slug').value = button?.getAttribute('data-slug') || '';
            document.getElementById('task_descripcion').value = button?.getAttribute('data-descripcion') || '';
            document.getElementById('task_evidencia').value = button?.getAttribute('data-evidencia') || 'none';
            document.getElementById('task_alerta_tipo').value = button?.getAttribute('data-alerta-tipo') || 'none';
            document.getElementById('task_alerta_cantidad').value = button?.getAttribute('data-alerta-cantidad') || '0';
            document.getElementById('task_tipos').value = button?.getAttribute('data-tipos') || '';
            document.getElementById('task_orden').value = button?.getAttribute('data-orden') || '0';
            document.getElementById('task_view_profile').value = button?.getAttribute('data-view-profile') || '<?php echo (int)$viewProfileId; ?>';
            document.getElementById('task_activo').checked = true;
        });
    }
});
</script>

<?php $db->closeConnection(); ?>
<?php include_once '../includes/footer.php'; ?>
