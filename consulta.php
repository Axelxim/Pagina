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
    <title>Consultas de Rutinas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf@latest/dist/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.13/jspdf.plugin.autotable.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.13/jspdf.plugin.autotable.min.js"></script>
    <link rel="stylesheet" href="styles.css">
    <link rel="manifest" href="manifest.json">
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

    <div class="container mt-5">
        <h1>Consultas de Rutinas</h1>
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Buscar Rutinas</h5>
                <form id="searchForm">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="username" class="form-label">Nombre de Usuario</label>
                            <input type="text" class="form-control" id="username" placeholder="Nombre de Usuario">
                        </div>
                        <div class="col-md-4">
                            <label for="date" class="form-label">Fecha</label>
                            <input type="date" class="form-control" id="date">
                        </div>
                        <div class="col-md-4">
                            <label for="routineId" class="form-label">ID de Rutina</label>
                            <input type="number" class="form-control" id="routineId" placeholder="ID de Rutina">
                        </div>
                    </div>
                    <!-- Añadimos un margen superior al botón -->
                    <button type="submit" class="btn btn-primary mt-3">Buscar</button>
                </form>
            </div>
        </div>

        <div id="resultsContainer"></div> <!-- Contenedor para mostrar resultados -->

    </div>


    <!-- Navbar estilo aplicación -->
    <div class="app-navbar" id="myNavbar">
        <a href="inicio_entrenador.php">
            <i class="fas fa-home"></i>
            <span>Inicio</span>
        </a>
        <a href="rutina.php">
            <i class="fa-solid fa-dumbbell"></i>
            <span>Rutinas</span>
        </a>
        <a href="consulta.php" class="active">
            <i class="fas fa-comment"></i>
            <span>Consultas</span>
        </a>
    </div>

    <script>
        // Manejar la búsqueda
        document.getElementById('searchForm').addEventListener('submit', function (event) {
            event.preventDefault();

            const username = document.getElementById('username').value.trim();
            const date = document.getElementById('date').value;
            const routineId = document.getElementById('routineId').value;

            // Validar que al menos un campo esté lleno
            if (!username && !date && !routineId) {
                alert('Por favor, complete al menos un campo de búsqueda.');
                return;
            }

            fetch(`get_rutinas.php?username=${encodeURIComponent(username)}&date=${encodeURIComponent(date)}&routineId=${encodeURIComponent(routineId)}`)
                .then(response => response.json())
                .then(data => {
                    const resultsContainer = document.getElementById('resultsContainer');
                    resultsContainer.innerHTML = '';

                    if (data.success) {
                        data.rutinas.forEach(rutina => {
                            const card = document.createElement('div');
                            card.className = 'card mb-3';
                            card.innerHTML = `
                                <div class="card-body">
                                    <h5 class="card-title">ID Rutina: ${rutina.id}</h5>
                                    <h6 class="card-subtitle mb-2 text-muted">Usuario: ${rutina.nombre_completo}</h6>
                                    <p class="card-text">Fecha: ${rutina.created_at}</p>
                                    <h6 class="card-subtitle mb-2">Ejercicios:</h6>
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
                                            ${rutina.ejercicios.map(ejercicio => `
                                                <tr>
                                                    <td>${ejercicio.grupo_muscular}</td>
                                                    <td>${ejercicio.ejercicio}</td>
                                                    <td>${ejercicio.repeticiones}</td>
                                                    <td>${ejercicio.series}</td>
                                                </tr>
                                            `).join('')}
                                        </tbody>
                                    </table>
                                    <button class="btn btn-success exportBtn" data-id="${rutina.id}">Exportar a PDF</button>
                                </div>
                            `;
                            resultsContainer.appendChild(card);
                        });

                        // Agregar eventos a los botones de exportación
                        document.querySelectorAll('.exportBtn').forEach(btn => {
                            btn.addEventListener('click', function () {
                                const rutinaId = this.getAttribute('data-id');
                                exportToPDF(rutinaId);
                            });
                        });
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
        });

        // Función para exportar a PDF
        function exportToPDF(rutinaId) {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            // Título del PDF
            doc.setFontSize(18);
            doc.text('Resumen de Rutina', 14, 20);

            // Obtener el nombre del usuario
            const card = Array.from(document.querySelectorAll('#resultsContainer .card')).find(c => {
                return c.querySelector('.card-title').innerText.includes(`ID Rutina: ${rutinaId}`);
            });

            if (card) {
                const userName = card.querySelector('.card-subtitle').innerText; // Nombre completo del usuario
                const fecha = card.querySelector('.card-text').innerText; // Fecha

                // Agregar el nombre del usuario y la fecha
                doc.setFontSize(12);
                doc.text(`Usuario: ${userName}`, 14, 30);
                doc.text(`Fecha: ${fecha}`, 14, 40);

                // Encabezados de la tabla
                const headers = [["#", "Grupo Muscular", "Ejercicio", "Repeticiones", "Series"]];
                const rows = [];

                // Recopilar datos de la tabla de ejercicios
                const exercises = card.querySelectorAll('table tbody tr');
                exercises.forEach((exercise, index) => {
                    const cols = exercise.getElementsByTagName('td');
                    const rowData = [index + 1]; // Agregar número de fila
                    for (let j = 0; j < cols.length; j++) {
                        rowData.push(cols[j].innerText);
                    }
                    rows.push(rowData);
                });

                // Añadir encabezados y filas al PDF
                doc.autoTable({
                    head: headers,
                    body: rows,
                    startY: 50, // Comenzar la tabla debajo de la fecha
                    margin: { horizontal: 10 },
                });

                // Guardar el PDF
                doc.save(`rutina_${rutinaId}.pdf`);
            } else {
                alert('No se encontró la rutina para exportar.');
            }
        }
    </script>
</body>

</html>