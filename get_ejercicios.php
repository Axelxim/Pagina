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

// Obtener el grupo muscular desde la consulta
$grupoMuscularId = $_GET['grupoMuscularId'];

$sql = "SELECT id, nombre FROM ejercicios_disponibles WHERE grupo_muscular_id = ?";
$stmt = $conn->prepare($sql);
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