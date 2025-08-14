<?php
// Habilitar la visualización de errores para depuración
ini_set('display_errors', 1); // Muestra los errores directamente en la página
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); // Muestra todos los niveles de errores

require_once '../database/conexion.php';

// Establece la conexión a la base de datos
$db = new Database();
$conn = $db->getConnection();

// Ejemplo de datos del JSON
$data = json_decode(file_get_contents('php://input'), true);
$id_examen = $data['id_examen'] ?? 0;

$seccionesInsertadas = agregarSecciones($id_examen, $data['secciones'], $conn);

// Llamada al método 2: Insertar las preguntas asociadas con las secciones
$preguntasInsertadas = agregarPreguntas($seccionesInsertadas, $data['preguntas'], $conn);

// Llamada al método 1: Insertar las opciones
$opcionesInsertadas = agregarOpciones($data['opciones'],$id_examen, $conn);

// Llamada al método 3: Relacionar las preguntas con las opciones
$responseRelacion = relacionarPreguntasOpciones($preguntasInsertadas, $opcionesInsertadas, $conn);

// Devolver la respuesta final
echo json_encode(['success' => true, 'message' => 'Operación realizada correctamente']);

// Cerrar la conexión a la base de datos
$db->closeConnection();
function agregarSecciones($id_examen, $secciones, $conn)
{
    $seccionesInsertadas = [];

    foreach ($secciones as $seccionData) {
        $seccionNombre = $seccionData['nombre'] ?? '';

        if ($seccionNombre) {
            // Insertar la sección si no existe
            $stmtSeccion = $conn->prepare('INSERT INTO exp_secciones_examen (id_examen, nombre_seccion) VALUES (?, ?)');
            $stmtSeccion->bind_param('is', $id_examen, $seccionNombre);
            $stmtSeccion->execute();
            $id_seccion = $stmtSeccion->insert_id; // Obtener el ID de la base de datos
            $stmtSeccion->close();

            // Almacenamos la sección insertada en un array
            $seccionesInsertadas[] = [
                'id_json' => $seccionData['id'], // ID desde el JSON
                'nombre' => $seccionNombre,
                'id_seccion' => $id_seccion // ID desde la base de datos
            ];
        }
    }

    return $seccionesInsertadas;
}


function agregarPreguntas($seccionesInsertadas, $preguntas, $conn)
{
    $preguntasInsertadas = [];

    foreach ($preguntas as $preguntaData) {
        $preguntaId = $preguntaData['numero'] ?? '';
        $textOpciones = $preguntaData['opciones'] ?? '';
        $preguntaTexto = $preguntaData['texto'] ?? '';
        $id_seccion_json = $preguntaData['id_seccion'] ?? 0;

        // Buscar el ID de la sección en la base de datos usando el ID del JSON
        $id_seccion_db = null;
        foreach ($seccionesInsertadas as $seccion) {
            if ($seccion['id_json'] == $id_seccion_json) {
                $id_seccion_db = $seccion['id_seccion'];
                break;
            }
        }

        if ($preguntaTexto && $id_seccion_db) {
            // Insertar la pregunta en la base de datos usando el id_seccion de la base de datos
            $stmtPregunta = $conn->prepare('INSERT INTO exp_preguntas_evaluacion (pregunta, id_seccion) VALUES (?, ?)');
            $stmtPregunta->bind_param('si', $preguntaTexto, $id_seccion_db);
            if ($stmtPregunta->execute()) {
                $id_pregunta = $stmtPregunta->insert_id;
                $stmtPregunta->close();
                $preguntasInsertadas[] = [
                    'id_json' => $preguntaId, // ID desde el JSON
                    'opciones' => $textOpciones,
                    'id_pregunta' => $id_pregunta // ID desde la base de datos
                ];
            }
        }
    }


    return $preguntasInsertadas;
}
// Método 1 - Agregar todas las opciones y devolver una lista
function agregarOpciones($opcionesJson,$id_examen, $conn)
{
    $opcionesInsertadas = [];

    foreach ($opcionesJson as $opcionData) {
        $opcionTexto = $opcionData['texto'] ?? '';

        if ($opcionTexto) {
            // Insertar la opción si no existe
            $stmtOpcion = $conn->prepare('INSERT INTO exp_opciones_pregunta (texto,id_exam) VALUES (?,?)');
            $stmtOpcion->bind_param('si', $opcionTexto, $id_examen);
            $stmtOpcion->execute();
            $id_opcion = $stmtOpcion->insert_id; // Obtener el ID de la base de datos
            $stmtOpcion->close();

            // Almacenamos la opción insertada en un array
            $opcionesInsertadas[] = [
                'id_json' => $opcionData['id'], // ID desde el JSON
                'nombre' => $opcionTexto,
                'id_opcion' => $id_opcion // ID desde la base de datos
            ];
        }
    }

    return $opcionesInsertadas;
}


// Método 3 - Relacionar las preguntas con las opciones
function relacionarPreguntasOpciones($preguntas, $opcionesJson, $conn)
{
    // Recorremos cada pregunta
    foreach ($preguntas as $preguntaData) {
        // Obtener el id_pregunta insertado desde el método 2 (preguntaData['id_pregunta'] debería estar asignado aquí)
        $id_pregunta = $preguntaData['id_pregunta']; // Este id debe ser pasado desde el método 2

        $opciones = $preguntaData['opciones'] ?? [];

        // Relacionar la pregunta con las opciones
        if (!empty($opciones)) {
            //separalos de $opciones ya q esta como texto pero separados por ,
            $opcionesArray = explode(',', $opciones);
            foreach ($opcionesArray as $opcionTexto) {

                //con el $opcionTexto vamos a buscar en el opcionesJson donde coicidad el id_opcion
                $id_opcion = buscarIdOpcion($opcionTexto, $opcionesJson);
                $stmtRel = $conn->prepare('INSERT INTO exp_pregunta_opcion (id_pregunta, id_opcion) VALUES (?, ?)');
                $stmtRel->bind_param('ii', $id_pregunta, $id_opcion); // Relacionamos la pregunta con la opción
                $stmtRel->execute();
                $stmtRel->close();
            }

            // Preparamos la sentencia para insertar las relaciones

        }



    }
}


//crear el buscarIdOpcion
function buscarIdOpcion($textoOpcion, $opcionesJson)
{
    foreach ($opcionesJson as $opcion) {
        if ($opcion['id_json'] == $textoOpcion) {
            return $opcion['id_opcion'];
        }
    }
    return null;
}
