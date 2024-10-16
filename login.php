<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Obtener datos del frontend
$data = json_decode(file_get_contents('php://input'), true);
$username = $data['username'];
$password = $data['password'];

// Datos de conexión a la base de datos
$host = 'localhost';
$dbname = 'gym';
$user = 'root';
$pass = 'carlos27';

try {
    // Conectar a la base de datos MySQL
    $pdo = new PDO("mysql:host=$host;port=3306;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Preparar la consulta para seleccionar el usuario
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    // Verificar si existe el usuario
    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar si la cuenta está activada
        if ($user['activado'] == 0) {
            echo json_encode(['success' => false, 'activado' => false, 'error' => 'Cuenta no activada. Por favor verifica tu correo.']);
            exit;
        }

        // Verificar la contraseña
        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['username'];
            $_SESSION['nombre'] = $user['nombres']; // Guardar solo el nombre en la sesión

            // Verificar si la clave 'tipo_usuario' está definida antes de usarla
            if (isset($user['tipo_usuario'])) {
                $_SESSION['tipo_usuario'] = $user['tipo_usuario']; // Guardar el tipo de usuario en la sesión

                // Verificar el tipo de usuario y redirigir a la página correspondiente
                if ($user['tipo_usuario'] == 'entrenador') {
                    echo json_encode(['success' => true, 'redirect' => 'inicio_entrenador.php']);
                } else {
                    echo json_encode(['success' => true, 'redirect' => 'inicio_entrenado.php']);
                }
            } else {
                // Manejar el caso en que 'tipo_usuario' no esté definido
                echo json_encode(['success' => false, 'error' => 'Tipo de usuario no definido.']);
            }
        } else {
            // Solo devuelve "Credenciales incorrectas" si la contraseña no coincide
            echo json_encode(['success' => false, 'activado' => true, 'error' => 'Credenciales incorrectas']);
        }
    } else {
        // Si no se encuentra el usuario, muestra "Credenciales incorrectas"
        echo json_encode(['success' => false, 'activado' => true, 'error' => 'Credenciales incorrectas']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
