<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['username'])) {
    header("Location: index.php"); // Redirigir a la página de inicio de sesión si no está autenticado
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Rutina Reciente</title>
</head>

<body>
    <div class="container mt-5">
        <h1>Rutina Más Reciente</h1>

        <?php if ($routine): ?>
            <h3>ID de Rutina: <?php echo $routine['id']; ?></h3>
            <h5>Fecha de Creación: <?php echo $routine['created_at']; ?></h5>

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

        <a href="inicio_entrenado.php" class="btn btn-primary">Volver</a>
    </div>
</body>


</html>