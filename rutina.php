<?php
session_start(); // Iniciar la sesión

// Verificar si el usuario está autenticado
if (!isset($_SESSION['username'])) {
    header("Location: index.php"); // Redirigir a la página de inicio de sesión si no está autenticado
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.13/jspdf.plugin.autotable.min.js"></script>
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

    <!-- Contenedor de la tarjeta con margen superior para evitar invasión del navbar -->
    <div class="container" style="margin-top: 90px;"> <!-- Ajusta este valor si es necesario -->
        <div class="card routine-card text-center">
            <div class="card-header bg-primary text-white">
                Crear Rutina
            </div>
            <div class="card-body">
                <!-- Desplegable de usuarios -->
                <div class="mb-3">
                    <label for="userSelect" class="form-label">Seleccionar Usuario</label>
                    <select class="form-select" id="userSelect">
                        <option value="">Seleccionar usuario</option> <!-- Opción predeterminada -->
                    </select>
                </div>

                <!-- Contenedor donde se irán agregando los ejercicios -->
                <div id="ejerciciosContainer"></div>

                <!-- Botón pequeño para agregar ejercicio -->
                <button type="button" class="btn btn-outline-primary add-exercise-btn" id="addExerciseBtn">
                    + Agregar Ejercicio
                </button>

                <!-- Sección para ejercicios guardados -->
                <div class="saved-exercises">
                    <h5>Ejercicios Guardados</h5>
                    <div id="savedExercisesContainer"></div>

                    <button id="saveRoutineBtn" class="btn btn-success mt-3">Guardar Rutina</button>

                    <!-- Sección para mostrar el resumen de la rutina -->
                    <div id="summaryContainer" class="mt-4" style="display: none;">
                        <h5 id="rutinaTitle">Rutina</h5> <!-- Aquí aparecerá el identificador de la rutina -->
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Grupo Muscular</th>
                                    <th>Ejercicio</th>
                                    <th>Repeticiones</th>
                                    <th>Series</th>
                                </tr>
                            </thead>
                            <tbody id="summaryBody">
                                <!-- Aquí se agregarán las filas con los ejercicios guardados -->
                            </tbody>
                        </table>

                        <!-- Botones para exportar y limpiar el resumen -->
                        <div class="d-flex justify-content-between mt-3">
                            <button id="exportPdfBtn" class="btn btn-primary">Exportar PDF</button>
                            <button id="clearSummaryBtn" class="btn btn-danger">Limpiar Resumen</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navbar estilo aplicación -->
    <div class="app-navbar" id="myNavbar">
        <a href="inicio_entrenador.php">
            <i class="fas fa-home"></i>
            <span>Inicio</span>
        </a>
        <a href="rutina.php" class="active">
            <i class="fa-solid fa-dumbbell"></i>
            <span>Rutinas</span>
        </a>
        <a href="consulta.php">
            <i class="fas fa-comment"></i>
            <span>Consultas</span>
        </a>

    </div>
    <script src="script.js"></script>
</body>

</html>