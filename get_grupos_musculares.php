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

// Consulta para obtener grupos musculares
$sql = "SELECT id, nombre FROM grupos_musculares";
$result = $conn->query($sql);

$gruposMusculares = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $gruposMusculares[] = $row;
    }
}

$conn->close();
header('Content-Type: application/json');
echo json_encode($gruposMusculares);
?>