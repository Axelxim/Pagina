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
    die(json_encode(["error" => "Error de conexión: " . $mysqli->connect_error]));
}

// Consulta para obtener usuarios
$result = $mysqli->query("SELECT id, nombres, apellido_paterno, apellido_materno FROM usuarios");

$usuarios = [];
while ($row = $result->fetch_assoc()) {
    $usuarios[] = $row;
}

echo json_encode($usuarios);
?>