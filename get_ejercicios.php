<?php
$servername = "localhost";
$username = "root";
$password = "carlos27";
$dbname = "gym";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Comprobar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verificar si se recibió el ID del grupo muscular
if (!isset($_GET['grupoMuscularId']) || empty($_GET['grupoMuscularId'])) {
    echo json_encode([]);
    exit();
}

$grupoMuscularId = $_GET['grupoMuscularId'];

// Preparar y ejecutar la consulta
$sql = "SELECT id, nombre FROM ejercicios_disponibles WHERE grupo_muscular_id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['error' => 'Error en la consulta: ' . $conn->error]);
    exit();
}

$stmt->bind_param("i", $grupoMuscularId);
$stmt->execute();
$result = $stmt->get_result();

$ejercicios = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $ejercicios[] = $row;
    }
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($ejercicios);
?>