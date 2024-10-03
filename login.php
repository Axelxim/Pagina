<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Obtener datos del frontend
$data = json_decode(file_get_contents('php://input'), true);
$username = $data['username'];
$password = $data['password'];

// Datos de conexión a la base de datos
$host = 'localhost'; // Cambia esto si es necesario
$dbname = 'gym';
$user = 'root'; // Reemplaza con tu usuario de MySQL
$pass = 'carlos27'; // Reemplaza con tu contraseña de MySQL

try {
    // Conectar a la base de datos MySQL
    $pdo = new PDO("mysql:host=$host;port=3306;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Preparar la consulta para seleccionar el usuario
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    // Verificar si se encontraron resultados
    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        // Verificar la contraseña
        if (password_verify($password, $user['password'])) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    } else {
        echo json_encode(['success' => false]);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>