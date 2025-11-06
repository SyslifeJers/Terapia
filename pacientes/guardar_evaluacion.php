<?php
require_once '../database/conexion.php';

$criteriosBase = [
    'Permanencia',
    'Irritabilidad',
    'Habilidades sociales',
    'Atención conjunta',
    'Seguimiento de indicaciones',
    'Cognición',
    'Comunicación receptiva',
    'Comunicación expresiva',
];

function normalizarTextoSimple(string $texto): string
{
    $texto = mb_strtolower(trim($texto), 'UTF-8');
    $reemplazos = [
        'á' => 'a',
        'é' => 'e',
        'í' => 'i',
        'ó' => 'o',
        'ú' => 'u',
        'ü' => 'u',
        'ñ' => 'n',
    ];
    $texto = strtr($texto, $reemplazos);
    $texto = preg_replace('/[^a-z0-9\s]/u', ' ', $texto);
    $texto = preg_replace('/\s+/', ' ', $texto);
    return trim($texto);
}

function asegurarCatalogoBase(mysqli $conn, array $criteriosBase): void
{
    $conn->query("CREATE TABLE IF NOT EXISTS exp_valoracion_catalogo (
        id_catalogo INT AUTO_INCREMENT PRIMARY KEY,
        id_nino INT NULL,
        seccion VARCHAR(255) NULL,
        criterio VARCHAR(255) NOT NULL,
        puntaje_default TINYINT NOT NULL DEFAULT 5,
        orden INT NOT NULL DEFAULT 0,
        creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (id_nino) REFERENCES nino(Id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $existeIndice = $conn->query("SHOW INDEX FROM exp_valoracion_catalogo WHERE Key_name = 'idx_catalogo_nino_seccion_criterio'");
    if ($existeIndice && $existeIndice->num_rows === 0) {
        $conn->query("ALTER TABLE exp_valoracion_catalogo ADD UNIQUE KEY idx_catalogo_nino_seccion_criterio (id_nino, seccion, criterio)");
    }
    if ($existeIndice) {
        $existeIndice->free();
    }

    $conteo = 0;
    $resConteo = $conn->query("SELECT COUNT(*) AS total FROM exp_valoracion_catalogo");
    if ($resConteo) {
        $fila = $resConteo->fetch_assoc();
        $conteo = isset($fila['total']) ? (int)$fila['total'] : 0;
        $resConteo->free();
    }

    if ($conteo === 0) {
        $orden = 0;
        $stmtInsert = $conn->prepare("INSERT INTO exp_valoracion_catalogo (id_nino, seccion, criterio, puntaje_default, orden) VALUES (NULL, ?, ?, 5, ?)");
        if ($stmtInsert) {
            foreach ($criteriosBase as $criterioBase) {
                $orden++;
                $seccion = 'General';
                $criterio = mb_substr($criterioBase, 0, 255);
                $stmtInsert->bind_param('ssi', $seccion, $criterio, $orden);
                $stmtInsert->execute();
            }
            $stmtInsert->close();
        }
    }
}

function asegurarTablaDetalle(mysqli $conn): void
{
    $conn->query("CREATE TABLE IF NOT EXISTS exp_valoracion_detalle (
        id_detalle INT AUTO_INCREMENT PRIMARY KEY,
        id_valoracion INT NOT NULL,
        id_catalogo INT NULL,
        seccion VARCHAR(255) NULL,
        criterio VARCHAR(255) NOT NULL,
        puntaje TINYINT NOT NULL,
        FOREIGN KEY (id_valoracion) REFERENCES exp_valoraciones_sesion(id_valoracion) ON DELETE CASCADE,
        FOREIGN KEY (id_catalogo) REFERENCES exp_valoracion_catalogo(id_catalogo) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $columnaCatalogo = $conn->query("SHOW COLUMNS FROM exp_valoracion_detalle LIKE 'id_catalogo'");
    if ($columnaCatalogo && $columnaCatalogo->num_rows === 0) {
        $conn->query("ALTER TABLE exp_valoracion_detalle ADD COLUMN id_catalogo INT NULL AFTER id_valoracion");
        $conn->query("ALTER TABLE exp_valoracion_detalle ADD CONSTRAINT fk_detalle_catalogo FOREIGN KEY (id_catalogo) REFERENCES exp_valoracion_catalogo(id_catalogo) ON DELETE SET NULL");
    }
    if ($columnaCatalogo) {
        $columnaCatalogo->free();
    }

    $columnaSeccion = $conn->query("SHOW COLUMNS FROM exp_valoracion_detalle LIKE 'seccion'");
    if ($columnaSeccion && $columnaSeccion->num_rows === 0) {
        $conn->query("ALTER TABLE exp_valoracion_detalle ADD COLUMN seccion VARCHAR(255) NULL AFTER id_catalogo");
    }
    if ($columnaSeccion) {
        $columnaSeccion->free();
    }
}

function asegurarTablasMetricas(mysqli $conn): array
{
    $conn->query("CREATE TABLE IF NOT EXISTS exp_valoracion_metrica (
        id_metrica INT AUTO_INCREMENT PRIMARY KEY,
        clave VARCHAR(64) NOT NULL UNIQUE,
        nombre VARCHAR(100) NOT NULL,
        descripcion VARCHAR(255) NULL,
        activo TINYINT(1) NOT NULL DEFAULT 1,
        creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $conn->query("CREATE TABLE IF NOT EXISTS exp_valoracion_metrica_valor (
        id_valor INT AUTO_INCREMENT PRIMARY KEY,
        id_valoracion INT NOT NULL,
        id_metrica INT NOT NULL,
        puntaje TINYINT NOT NULL,
        creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (id_valoracion) REFERENCES exp_valoraciones_sesion(id_valoracion) ON DELETE CASCADE,
        FOREIGN KEY (id_metrica) REFERENCES exp_valoracion_metrica(id_metrica) ON DELETE CASCADE,
        UNIQUE KEY idx_valoracion_metrica (id_valoracion, id_metrica)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $metricasBase = [
        'participacion' => 'Participación',
        'atencion' => 'Atención',
        'tarea_casa' => 'Tarea en casa',
    ];

    $ids = [];
    $stmtSelect = $conn->prepare("SELECT id_metrica FROM exp_valoracion_metrica WHERE clave = ? LIMIT 1");
    $stmtInsert = $conn->prepare("INSERT INTO exp_valoracion_metrica (clave, nombre) VALUES (?, ?)");

    foreach ($metricasBase as $clave => $nombre) {
        $idEncontrado = null;
        if ($stmtSelect) {
            $stmtSelect->bind_param('s', $clave);
            $stmtSelect->execute();
            $res = $stmtSelect->get_result();
            if ($res && $fila = $res->fetch_assoc()) {
                $idEncontrado = (int)$fila['id_metrica'];
            }
            if ($res) {
                $res->free();
            }
        }
        if ($idEncontrado === null && $stmtInsert) {
            $stmtInsert->bind_param('ss', $clave, $nombre);
            if ($stmtInsert->execute()) {
                $idEncontrado = $stmtInsert->insert_id;
            }
        }
        if ($idEncontrado === null && $stmtSelect) {
            $stmtSelect->bind_param('s', $clave);
            $stmtSelect->execute();
            $res = $stmtSelect->get_result();
            if ($res && $fila = $res->fetch_assoc()) {
                $idEncontrado = (int)$fila['id_metrica'];
            }
            if ($res) {
                $res->free();
            }
        }
        if ($idEncontrado !== null) {
            $ids[$clave] = $idEncontrado;
        }
    }

    if ($stmtSelect) {
        $stmtSelect->close();
    }
    if ($stmtInsert) {
        $stmtInsert->close();
    }

    return $ids;
}

function resolverCatalogoId(mysqli $conn, int $idPaciente, ?int $catalogoId, string $seccion, string $criterio, int $puntaje): ?int
{
    $criterio = mb_substr(trim($criterio), 0, 255);
    $seccion = mb_substr(trim($seccion), 0, 255);
    $puntaje = max(1, min(10, $puntaje));

    if ($catalogoId && $catalogoId > 0) {
        $stmtVerificar = $conn->prepare("SELECT id_catalogo FROM exp_valoracion_catalogo WHERE id_catalogo = ? LIMIT 1");
        if ($stmtVerificar) {
            $stmtVerificar->bind_param('i', $catalogoId);
            $stmtVerificar->execute();
            $res = $stmtVerificar->get_result();
            if ($res && $res->num_rows > 0) {
                $stmtVerificar->close();
                return $catalogoId;
            }
            if ($res) {
                $res->free();
            }
            $stmtVerificar->close();
        }
    }

    $seccionBusqueda = $seccion;
    $criterioBusqueda = $criterio;

    if ($idPaciente > 0) {
        $stmtPaciente = $conn->prepare("SELECT id_catalogo FROM exp_valoracion_catalogo WHERE id_nino = ? AND criterio = ? AND ((seccion IS NULL AND ? = '') OR seccion = ?) LIMIT 1");
        if ($stmtPaciente) {
            $stmtPaciente->bind_param('isss', $idPaciente, $criterioBusqueda, $seccionBusqueda, $seccionBusqueda);
            $stmtPaciente->execute();
            $res = $stmtPaciente->get_result();
            if ($res && $fila = $res->fetch_assoc()) {
                $idCatalogo = (int)$fila['id_catalogo'];
                $res->free();
                $stmtPaciente->close();
                return $idCatalogo;
            }
            if ($res) {
                $res->free();
            }
            $stmtPaciente->close();
        }
    }

    $stmtGeneral = $conn->prepare("SELECT id_catalogo FROM exp_valoracion_catalogo WHERE id_nino IS NULL AND criterio = ? AND ((seccion IS NULL AND ? = '') OR seccion = ?) LIMIT 1");
    if ($stmtGeneral) {
        $stmtGeneral->bind_param('sss', $criterioBusqueda, $seccionBusqueda, $seccionBusqueda);
        $stmtGeneral->execute();
        $res = $stmtGeneral->get_result();
        if ($res && $fila = $res->fetch_assoc()) {
            $idCatalogo = (int)$fila['id_catalogo'];
            $res->free();
            $stmtGeneral->close();
            return $idCatalogo;
        }
        if ($res) {
            $res->free();
        }
        $stmtGeneral->close();
    }

    if ($idPaciente > 0) {
        $stmtInsert = $conn->prepare("INSERT INTO exp_valoracion_catalogo (id_nino, seccion, criterio, puntaje_default) VALUES (?, NULLIF(?, ''), ?, ?)");
        if ($stmtInsert) {
            $stmtInsert->bind_param('issi', $idPaciente, $seccionBusqueda, $criterioBusqueda, $puntaje);
            if ($stmtInsert->execute()) {
                $nuevoId = $stmtInsert->insert_id;
                $stmtInsert->close();
                return $nuevoId;
            }
            $stmtInsert->close();
        }
    }

    return null;
}

$db = new Database();
$conn = $db->getConnection();

asegurarCatalogoBase($conn, $criteriosBase);
asegurarTablaDetalle($conn);
$metricasIds = asegurarTablasMetricas($conn);

$id_nino = intval($_POST['id_nino'] ?? 0);
$id_usuario = intval($_POST['id_usuario'] ?? 0);
$observaciones = trim($_POST['observaciones'] ?? '');
$payloadJson = $_POST['evaluacion_json'] ?? '';
$criterios = $_POST['criterios'] ?? [];
$puntajes = $_POST['puntajes'] ?? [];

$detalles = [];

if ($payloadJson !== '') {
    $decoded = json_decode($payloadJson, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
        foreach ($decoded as $seccion) {
            $titulo = isset($seccion['titulo']) ? trim((string)$seccion['titulo']) : '';
            $items = $seccion['criterios'] ?? [];
            if (!is_array($items)) {
                continue;
            }
            foreach ($items as $item) {
                $nombre = isset($item['criterio']) ? trim((string)$item['criterio']) : '';
                $valor = $item['puntaje'] ?? null;
                $catalogoId = isset($item['catalogo_id']) ? (int)$item['catalogo_id'] : null;
                if ($nombre === '' || $valor === null || !is_numeric($valor)) {
                    continue;
                }
                $valor = (int)round($valor);
                $valor = max(1, min(10, $valor));
                $detalles[] = [
                    'seccion' => $titulo,
                    'criterio' => $nombre,
                    'puntaje' => $valor,
                    'catalogo_id' => ($catalogoId && $catalogoId > 0) ? $catalogoId : null,
                ];
            }
        }
    }
}

if (empty($detalles)) {
    foreach ($criterios as $index => $criterio) {
        $nombre = trim((string)$criterio);
        $valor = isset($puntajes[$index]) ? (int)$puntajes[$index] : null;
        if ($nombre === '' || $valor === null) {
            continue;
        }
        $valor = max(1, min(10, $valor));
        $detalles[] = [
            'seccion' => '',
            'criterio' => $nombre,
            'puntaje' => $valor,
            'catalogo_id' => null,
        ];
    }
}

if ($id_nino <= 0 || $id_usuario <= 0 || empty($detalles)) {
    $db->closeConnection();
    header('Location: paciente.php?id=' . max($id_nino, 0));
    exit();
}

foreach ($detalles as &$detalle) {
    $detalle['seccion'] = mb_substr(trim((string)($detalle['seccion'] ?? '')), 0, 255);
    $detalle['criterio'] = mb_substr(trim((string)$detalle['criterio']), 0, 255);
    $detalle['puntaje'] = max(1, min(10, (int)$detalle['puntaje']));
    $catalogoId = isset($detalle['catalogo_id']) && is_numeric($detalle['catalogo_id']) ? (int)$detalle['catalogo_id'] : null;
    $detalle['catalogo_id'] = resolverCatalogoId($conn, $id_nino, $catalogoId, $detalle['seccion'], $detalle['criterio'], $detalle['puntaje']);
}
unset($detalle);

$detalles = array_values(array_filter($detalles, static function ($detalle) {
    return isset($detalle['criterio']) && $detalle['criterio'] !== '';
}));

if (empty($detalles)) {
    $db->closeConnection();
    header('Location: paciente.php?id=' . $id_nino);
    exit();
}

$mapaMetricas = [
    'participacion' => null,
    'atencion' => null,
    'tarea_casa' => null,
];

foreach ($detalles as $detalle) {
    $claveNormalizada = normalizarTextoSimple($detalle['criterio']);
    if ($claveNormalizada === 'participacion' && $mapaMetricas['participacion'] === null) {
        $mapaMetricas['participacion'] = $detalle['puntaje'];
    } elseif ($claveNormalizada === 'atencion' && $mapaMetricas['atencion'] === null) {
        $mapaMetricas['atencion'] = $detalle['puntaje'];
    } elseif (($claveNormalizada === 'tarea en casa' || $claveNormalizada === 'tarea casa') && $mapaMetricas['tarea_casa'] === null) {
        $mapaMetricas['tarea_casa'] = $detalle['puntaje'];
    }
}

$stmt = $conn->prepare("INSERT INTO exp_valoraciones_sesion (id_nino, id_usuario, observaciones) VALUES (?, ?, ?)");
$stmt->bind_param('iis', $id_nino, $id_usuario, $observaciones);
$stmt->execute();
$id_valoracion = $stmt->insert_id;
$stmt->close();

if ($id_valoracion) {
    $stmtDetalle = $conn->prepare("INSERT INTO exp_valoracion_detalle (id_valoracion, id_catalogo, seccion, criterio, puntaje) VALUES (?, NULLIF(?, 0), NULLIF(?, ''), ?, ?)");
    if ($stmtDetalle) {
        $idValoracionParam = $id_valoracion;
        $catalogoParam = 0;
        $seccionParam = '';
        $criterioParam = '';
        $puntajeParam = 0;
        $stmtDetalle->bind_param('iissi', $idValoracionParam, $catalogoParam, $seccionParam, $criterioParam, $puntajeParam);
        foreach ($detalles as $detalle) {
            $catalogoParam = isset($detalle['catalogo_id']) && $detalle['catalogo_id'] ? (int)$detalle['catalogo_id'] : 0;
            $seccionParam = (string)($detalle['seccion'] ?? '');
            $criterioParam = (string)$detalle['criterio'];
            $puntajeParam = (int)$detalle['puntaje'];
            $stmtDetalle->execute();
        }
        $stmtDetalle->close();
    }

    $metricasRegistradas = array_filter($mapaMetricas, static fn($valor) => $valor !== null);
    if (!empty($metricasRegistradas) && !empty($metricasIds)) {
        $stmtMetricas = $conn->prepare("INSERT INTO exp_valoracion_metrica_valor (id_valoracion, id_metrica, puntaje) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE puntaje = VALUES(puntaje)");
        if ($stmtMetricas) {
            $idValoracionMetric = $id_valoracion;
            $idMetricaParam = 0;
            $puntajeMetricParam = 0;
            $stmtMetricas->bind_param('iii', $idValoracionMetric, $idMetricaParam, $puntajeMetricParam);
            foreach ($metricasRegistradas as $clave => $valor) {
                if (!isset($metricasIds[$clave])) {
                    continue;
                }
                $idMetricaParam = (int)$metricasIds[$clave];
                $puntajeMetricParam = (int)$valor;
                $stmtMetricas->execute();
            }
            $stmtMetricas->close();
        }
    }
}

$db->closeConnection();

header('Location: paciente.php?id=' . $id_nino);
exit();
