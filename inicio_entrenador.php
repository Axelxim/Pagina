<?php
session_start(); // Iniciar la sesión

// Verificar si el usuario está autenticado
if (!isset($_SESSION['username'])) {
    // Si no está autenticado, redirigir al login
    header("Location: index.php");
    exit();
}

if ($_SESSION['tipo_usuario'] === 'entrenado') {
    header("Location: inicio_entrenado.php"); // Redirigir a la página de entrenado
    exit();
} elseif ($_SESSION['tipo_usuario'] !== 'entrenador') {
    header("Location: inicio.php"); // Redirigir a una página general si no es entrenador
    exit();
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
    <!-- Navbar Única -->
    <nav class="navbar-unique fixed-top">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <a class="navbar-brand" href="#">
                    <img src="logo.png" alt="Logo" width="30" height="30" class="d-inline-block align-text-top">

                </a>
            </div>
            <div class="navbar-user-container d-flex align-items-center">
                <span class="navbar-username"><?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
                <div class="dropdown">
                    <button class="btn dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="fas fa-user"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="logout.php">Cerrar Sesión</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container text-center mt-5" style="margin-top: 120px;">
        <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?></h1>
    </div>

    <!-- Navbar estilo aplicación -->
    <div class="app-navbar" id="myNavbar">
        <a href="inicio_entrenador.php" class="active">
            <i class="fas fa-home"></i>
            <span>Inicio</span>
        </a>
        <a href="rutina.php">
            <i class="fa-solid fa-dumbbell"></i>
            <span>Rutinas</span>
        </a>
        <a href="consulta.php">
            <i class="fas fa-comment"></i>
            <span>Consultas</span>
        </a>

    </div>
</body>

</html>