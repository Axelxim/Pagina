<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Obtener datos del frontend
$data = json_decode(file_get_contents('php://input'), true);
$correo = $data['correo'];

// Datos de conexión a la base de datos
$host = 'localhost'; // Cambia esto si es necesario
$dbname = 'gym';
$user = 'root'; // Reemplaza con tu usuario de MySQL
$pass = 'carlos27'; // Reemplaza con tu contraseña de MySQL

try {
    // Conectar a la base de datos MySQL
    $pdo = new PDO("mysql:host=$host;port=3306;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Preparar la consulta para verificar el correo
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE correo = :correo");
    $stmt->bindParam(':correo', $correo);
    $stmt->execute();

    // Comprobar si el correo existe
    $exists = $stmt->fetchColumn() > 0;

    echo json_encode(['exists' => $exists]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>