<?php
header('Content-Type: application/json'); // Establecer el tipo de contenido a JSON

// Conexión a la base de datos
$host = 'localhost';
$dbname = 'gym';
$user = 'root';
$pass = 'carlos27';

// Crear conexión
$mysqli = new mysqli($host, $user, $pass, $dbname);

// Verificar conexión
if ($mysqli->connect_error) {
    die(json_encode(["success" => false, "message" => "Error de conexión: " . $mysqli->connect_error]));
}

// Obtener el ID de la rutina de la solicitud GET
$rutinaId = isset($_GET['rutinaId']) ? intval($_GET['rutinaId']) : 0;

if ($rutinaId > 0) {
    // Preparar la consulta para obtener la rutina y ejercicios relacionados
    $stmt = $mysqli->prepare("SELECT e.grupo_muscular, e.ejercicio, e.repeticiones, e.series FROM ejercicios e WHERE e.rutina_id = ?");
    $stmt->bind_param("i", $rutinaId);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $ejercicios = [];

        // Recuperar todos los ejercicios en un arreglo
        while ($row = $result->fetch_assoc()) {
            $ejercicios[] = $row;
        }

        // Respuesta de éxito
        echo json_encode(["success" => true, "ejercicios" => $ejercicios]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al recuperar ejercicios: " . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "ID de rutina inválido."]);
}

$mysqli->close();
?>