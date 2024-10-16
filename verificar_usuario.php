<?php
$conexion = new mysqli('localhost', 'root', 'carlos27', 'gym');

if ($conexion->connect_error) {
    die(json_encode(['exists' => false]));
}

$username = $conexion->real_escape_string($_GET['username']);

$sql = "SELECT * FROM usuarios WHERE username = '$username'";
$result = $conexion->query($sql);

if ($result->num_rows > 0) {
    echo json_encode(['exists' => true]);
} else {
    echo json_encode(['exists' => false]);
}

$conexion->close();
?>