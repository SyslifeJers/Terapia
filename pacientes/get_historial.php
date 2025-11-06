<?php
require_once '../database/conexion.php';
header('Content-Type: application/json');

$db = new Database();
$conn = $db->getConnection();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$tipo = $_GET['tipo'] ?? '';

$datos = [];

if ($id > 0 && ($tipo === 'evaluacion' || $tipo === 'progreso')) {
    if ($tipo === 'evaluacion') {
        $stmt = $conn->prepare("SELECT id_valoracion, fecha_valoracion, observaciones FROM exp_valoraciones_sesion WHERE id_nino = ? ORDER BY fecha_valoracion DESC");
        if ($stmt) {
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $valoraciones = [];
            $ids = [];
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $row['id_valoracion'] = (int)$row['id_valoracion'];
                    $row['observaciones'] = $row['observaciones'] ?? '';
                    $row['detalles'] = [];
                    $row['promedio'] = null;
                    $valoraciones[] = $row;
                    $ids[] = $row['id_valoracion'];
                }
            }
            $stmt->close();

            if (!empty($ids)) {
                $listaIds = implode(',', array_map('intval', $ids));
                $sqlDetalles = "SELECT vd.id_valoracion, vd.id_criterio, vd.valor, c.nombre FROM exp_valoracion_detalle vd INNER JOIN exp_criterios_evaluacion c ON c.id_criterio = vd.id_criterio WHERE vd.id_valoracion IN ($listaIds) ORDER BY c.nombre ASC";
                $resDet = $conn->query($sqlDetalles);
                if ($resDet) {
                    $indexMap = array_flip($ids);
                    while ($det = $resDet->fetch_assoc()) {
                        $idVal = (int)$det['id_valoracion'];
                        if (!isset($indexMap[$idVal])) {
                            continue;
                        }
                        $valoraciones[$indexMap[$idVal]]['detalles'][] = [
                            'id_criterio' => (int)$det['id_criterio'],
                            'nombre' => $det['nombre'],
                            'valor' => (float)$det['valor'],
                        ];
                    }
                }
            }

            foreach ($valoraciones as $valoracion) {
                $suma = 0;
                $conteo = 0;
                foreach ($valoracion['detalles'] as $detalle) {
                    $suma += (float)$detalle['valor'];
                    $conteo++;
                }
                $promedio = $conteo ? round($suma / $conteo, 2) : null;
                $datos[] = [
                    'fecha_valoracion' => $valoracion['fecha_valoracion'],
                    'observaciones' => $valoracion['observaciones'],
                    'criterios' => $valoracion['detalles'],
                    'promedio' => $promedio,
                ];
            }
        }
    } else {
        $stmt = $conn->prepare("SELECT fecha_registro, lenguaje, motricidad, atencion, memoria, social, observaciones FROM exp_progreso_general WHERE id_nino = ? ORDER BY fecha_registro DESC");
        if ($stmt) {
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result) {
                $datos = $result->fetch_all(MYSQLI_ASSOC);
            }
            $stmt->close();
        }
    }
}

$db->closeConnection();

echo json_encode($datos);
