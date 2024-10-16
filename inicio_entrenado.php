<?php
session_start(); // Iniciar la sesión

// Verificar si el usuario está autenticado
if (!isset($_SESSION['username'])) {
    // Si no está autenticado, redirigir al login
    header("Location: index.php");
    exit();
}

if ($_SESSION['tipo_usuario'] === 'entrenador') {
    header("Location: inicio_entrenador.php"); // Redirigir a la página de entrenador
    exit();
} elseif ($_SESSION['tipo_usuario'] !== 'entrenado') {
    header("Location: inicio.php"); // Redirigir a una página general si no es entrenador
    exit();
}

// Datos de conexión a la base de datos
$host = 'localhost';
$dbname = 'gym';
$user = 'root';
$pass = 'carlos27';

try {
    // Conectar a la base de datos MySQL
    $pdo = new PDO("mysql:host=$host;port=3306;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener el id del usuario desde la sesión
    $username = $_SESSION['username'];

    // Obtener el id del usuario desde la tabla de usuarios
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $user_id = $user['id'];

        // Consultar la rutina más reciente del usuario
        $stmt = $pdo->prepare("SELECT * FROM rutinas WHERE user_id = :user_id ORDER BY created_at DESC LIMIT 1");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        $routine = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($routine) {
            $rutina_id = $routine['id'];

            // Consultar los ejercicios de la rutina
            $stmt = $pdo->prepare("SELECT * FROM ejercicios WHERE rutina_id = :rutina_id");
            $stmt->bindParam(':rutina_id', $rutina_id);
            $stmt->execute();

            $exercises = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $routine = null; // No hay rutinas
        }
    } else {
        // Manejar el caso donde el usuario no se encuentra
        echo "Usuario no encontrado.";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="manifest" href="manifest.json">
    <title>Mi Primera PWA</title>
</head>

<body class="custom-body">
    <div class="container text-center mt-5">
        <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?></h1>

        <h2>Rutina Más Reciente</h2>

        <div class="card mt-4">
            <div class="card-body">
                <?php if ($routine): ?>
                    <h5 class="card-title">ID de Rutina: <?php echo $routine['id']; ?></h5>
                    <h6 class="card-subtitle mb-2 text-muted">Fecha de Creación: <?php echo $routine['created_at']; ?></h6>

                    <table class="table">
                        <thead>
                            <tr>
                                <th>Grupo Muscular</th>
                                <th>Ejercicio</th>
                                <th>Repeticiones</th>
                                <th>Series</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($exercises as $exercise): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($exercise['grupo_muscular']); ?></td>
                                    <td><?php echo htmlspecialchars($exercise['ejercicio']); ?></td>
                                    <td><?php echo htmlspecialchars($exercise['repeticiones']); ?></td>
                                    <td><?php echo htmlspecialchars($exercise['series']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No tienes rutinas guardadas.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Navbar estilo aplicación -->
    <div class="app-navbar" id="myNavbar">
        <a href="inicio_entrenado.php" class="active">
            <i class="fas fa-home"></i>
            <span>Inicio</span>
        </a>
        <a href="mis_consultas.php">
            <i class="fas fa-comment"></i>
            <span>Consultas</span>
        </a>
        <a href="logout.php">
            <i class="fas fa-sign-out-alt"></i>
            <span>Salir</span>
        </a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>