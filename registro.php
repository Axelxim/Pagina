<?php
// Asegúrate de incluir el archivo autoload.php de PHPMailer
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

$conexion = new mysqli('localhost', 'root', 'carlos27', 'gym');

if ($conexion->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Error de conexión a la base de datos: ' . $conexion->connect_error]);
    exit();
}

// Obtener datos del frontend
$data = json_decode(file_get_contents("php://input"));

$nombres = $conexion->real_escape_string($data->nombres);
$apellidoPaterno = $conexion->real_escape_string($data->apellidoPaterno);
$apellidoMaterno = $conexion->real_escape_string($data->apellidoMaterno);
$telefono = $conexion->real_escape_string($data->telefono);
$correo = $conexion->real_escape_string($data->correo);
$username = $conexion->real_escape_string($data->username);
$password = password_hash($conexion->real_escape_string($data->password), PASSWORD_BCRYPT);
$tipoUsuario = $conexion->real_escape_string($data->tipoUsuario); // Capturar tipo de usuario (entrenador o entrenado)

// Generar un código de activación
$codigo_activacion = bin2hex(random_bytes(16));

// Consulta para registrar el usuario con el tipo de usuario incluido
$sql = "INSERT INTO usuarios (nombres, apellido_paterno, apellido_materno, telefono, correo, username, password, activado, codigo_activacion, tipo_usuario) 
        VALUES ('$nombres', '$apellidoPaterno', '$apellidoMaterno', '$telefono', '$correo', '$username', '$password', 0, '$codigo_activacion', '$tipoUsuario')";

if ($conexion->query($sql) === TRUE) {
    // Enviar el correo de activación
    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Especificar el servidor SMTP
        $mail->SMTPAuth = true;
        $mail->Username = 'oni.mexicanog@gmail.com'; // Tu correo
        $mail->Password = 'lrbquatoqttdsxgh'; // Tu contraseña de aplicación
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Habilitar TLS
        $mail->Port = 587; // Puerto TCP para TLS

        // Remitente y destinatario
        $mail->setFrom('oni.mexicanog@gmail.com', 'Tu Nombre'); // Cambia 'Tu Nombre' al nombre que desees
        $mail->addAddress($correo); // Agregar destinatario

        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = 'Activación de Cuenta';
        $mail->Body = "Hola $nombres, <br> Gracias por registrarte. Por favor activa tu cuenta haciendo clic en el siguiente enlace: <a href='http://localhost:3000/pwa-prueba/activar.php?codigo=$codigo_activacion'>Activar cuenta</a>";

        // Enviar correo
        $mail->send();

        // Respuesta de éxito
        echo json_encode(['success' => true, 'message' => 'Registro exitoso. Revisa tu correo para activar tu cuenta.']);
    } catch (Exception $e) {
        // Respuesta de error al enviar el correo
        echo json_encode(['success' => false, 'error' => 'Error al enviar el correo: ' . $mail->ErrorInfo]);
    }
} else {
    // Respuesta de error al registrar el usuario
    echo json_encode(['success' => false, 'error' => 'Error al registrar el usuario: ' . $conexion->error]);
}

// Asegúrate de que no hay salida adicional después de esto
$conexion->close();
exit();
?>