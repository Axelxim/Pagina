<?php
// Datos de conexión a la base de datos
$host = 'localhost';
$dbname = 'gym';
$user = 'root';
$pass = 'carlos27';

if (isset($_GET['codigo'])) {
    $codigoActivacion = $_GET['codigo'];

    try {
        // Conectar a la base de datos MySQL
        $pdo = new PDO("mysql:host=$host;port=3306;dbname=$dbname", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Actualizar el usuario para activar la cuenta
        $stmt = $pdo->prepare("UPDATE usuarios SET activado = 1 WHERE codigo_activacion = :codigo_activacion");
        $stmt->bindParam(':codigo_activacion', $codigoActivacion);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo "Cuenta activada con éxito.";
        } else {
            echo "Código de activación no válido.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Código de activación no proporcionado.";
}
?>