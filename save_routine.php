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

// Obtener datos de la solicitud
$data = json_decode(file_get_contents("php://input"), true);
$userId = $data['userId'];
$ejercicios = $data['ejercicios'];

// Insertar la rutina en la base de datos
$stmt = $mysqli->prepare("INSERT INTO rutinas (user_id) VALUES (?)");
$stmt->bind_param("i", $userId);

if ($stmt->execute()) {
    $rutinaId = $stmt->insert_id; // Obtener el ID de la rutina recién insertada
    $stmt->close();

    // Insertar los ejercicios relacionados con la rutina
    foreach ($ejercicios as $ejercicio) {
        $grupoMuscular = $ejercicio['grupoMuscular'];
        $ejercicioNombre = $ejercicio['ejercicio'];
        $repeticiones = $ejercicio['repeticiones'];
        $series = $ejercicio['series'];

        // Aquí debes tener la lógica para obtener el id del grupo muscular y el ejercicio en base a su nombre
        $stmt = $mysqli->prepare("INSERT INTO ejercicios (rutina_id, grupo_muscular, ejercicio, repeticiones, series) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issii", $rutinaId, $grupoMuscular, $ejercicioNombre, $repeticiones, $series);

        if (!$stmt->execute()) {
            echo json_encode(["success" => false, "message" => "Error al guardar ejercicio: " . $stmt->error]);
            exit;
        }
        $stmt->close();
    }

    // Respuesta de éxito
    echo json_encode(["success" => true, "rutinaId" => $rutinaId]); // Devuelve el ID de la rutina
} else {
    echo json_encode(["success" => false, "message" => "Error al guardar rutina: " . $stmt->error]);
}

$mysqli->close();
?>