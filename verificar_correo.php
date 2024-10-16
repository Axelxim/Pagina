<?php
$conexion = new mysqli('localhost', 'root', 'carlos27', 'gym');

if ($conexion->connect_error) {
    die(json_encode(['exists' => false]));
}

$correo = $conexion->real_escape_string($_GET['correo']);

$sql = "SELECT * FROM usuarios WHERE correo = '$correo'";
$result = $conexion->query($sql);

if ($result->num_rows > 0) {
    echo json_encode(['exists' => true]);
} else {
    echo json_encode(['exists' => false]);
}

$conexion->close();
?>