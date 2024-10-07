<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "carlos27";
$dbname = "gym";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $routineId = isset($_GET['routineId']) ? $_GET['routineId'] : '';

    // Validar rutina ID
    if ($routineId === '') {
        echo json_encode(['success' => false, 'message' => 'ID de rutina no proporcionado.']);
        exit;
    }

    // Preparar la consulta
    $sql = "SELECT * FROM ejercicios WHERE rutina_id = :routineId";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':routineId', $routineId, PDO::PARAM_INT);
    $stmt->execute();

    $ejercicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($ejercicios) {
        echo json_encode(['success' => true, 'ejercicios' => $ejercicios]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No se encontraron ejercicios para esta rutina.']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error en la conexión: ' . $e->getMessage()]);
}
?>