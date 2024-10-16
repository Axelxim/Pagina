<?php
session_start(); // Iniciar la sesión

// Verificar si el usuario está autenticado
if (!isset($_SESSION['username'])) {
    header("Location: index.php"); // Redirigir a la página de inicio de sesión si no está autenticado
    exit();
}

if ($_SESSION['tipo_usuario'] !== 'entrenado') {
    header("Location: inicio.php"); // Redirigir a una página general si no es entrenado
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

    // Inicializar variables para almacenar resultados
    $results = [];
    $error = '';

    // Procesar la búsqueda
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id_rutina = $_POST['id_rutina'] ?? '';
        $fecha_rutina = $_POST['fecha_rutina'] ?? '';

        // Construir la consulta
        $query = "SELECT r.id, r.user_id, r.created_at, e.grupo_muscular, e.ejercicio, e.repeticiones, e.series 
                  FROM rutinas r 
                  LEFT JOIN ejercicios e ON r.id = e.rutina_id 
                  WHERE 1=1";

        if ($id_rutina) {
            $query .= " AND r.id = :id_rutina";
        }
        if ($fecha_rutina) {
            $query .= " AND DATE(r.created_at) = :fecha_rutina";
        }

        $stmt = $pdo->prepare($query);

        // Bind parameters
        if ($id_rutina) {
            $stmt->bindParam(':id_rutina', $id_rutina, PDO::PARAM_INT);
        }
        if ($fecha_rutina) {
            $stmt->bindParam(':fecha_rutina', $fecha_rutina);
        }

        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    $error = "Error: " . $e->getMessage();
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>

    <link rel="stylesheet" href="styles.css">
    <link rel="manifest" href="manifest.json">
    <title>Mis Consultas</title>


</head>

<body class="custom-body">
    <div class="container mt-5">
        <h1>Consulta de Rutinas</h1>

        <div class="card mt-4">
            <div class="card-body">
                <h5 class="card-title">Buscar Rutinas</h5>
                <form method="POST">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="id_rutina" class="form-label">ID de Rutina</label>
                            <input type="number" name="id_rutina" id="id_rutina" class="form-control"
                                placeholder="Ingrese el ID de la rutina">
                        </div>
                        <div class="col-md-6">
                            <label for="fecha_rutina" class="form-label">Fecha de Rutina</label>
                            <input type="date" name="fecha_rutina" id="fecha_rutina" class="form-control">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Buscar</button>
                </form>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger mt-4"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($results): ?>
            <h2 class="mt-4">Resultados de la Búsqueda</h2>
            <div class="row">
                <?php
                // Agrupar los resultados por ID de rutina
                $rutinasAgrupadas = [];

                foreach ($results as $row) {
                    $rutinasAgrupadas[$row['id']][] = $row; // Agrupando por ID
                }

                foreach ($rutinasAgrupadas as $id_rutina => $ejercicios): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">ID Rutina: <?php echo htmlspecialchars($id_rutina); ?></h5>
                                <h6 class="card-subtitle mb-2 text-muted">Ejercicios:</h6>
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
                                        <?php foreach ($ejercicios as $ejercicio): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($ejercicio['grupo_muscular']); ?></td>
                                                <td><?php echo htmlspecialchars($ejercicio['ejercicio']); ?></td>
                                                <td><?php echo htmlspecialchars($ejercicio['repeticiones']); ?></td>
                                                <td><?php echo htmlspecialchars($ejercicio['series']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <p class="card-text"><strong>Fecha de Creación:</strong>
                                    <?php echo htmlspecialchars($ejercicios[0]['created_at']); ?></p>
                                <button class="btn btn-success exportBtn"
                                    data-id="<?php echo htmlspecialchars($id_rutina); ?>">Exportar a PDF</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <div class="alert alert-warning mt-4">No se encontraron rutinas que coincidan con la búsqueda.</div>
        <?php endif; ?>
    </div>

    <!-- Navbar estilo aplicación -->
    <div class="app-navbar" id="myNavbar">
        <a href="inicio_entrenado.php">
            <i class="fas fa-home"></i>
            <span>Inicio</span>
        </a>
        <a href="mis_consultas.php" class="active">
            <i class="fas fa-comment"></i>
            <span>Consultas</span>
        </a>
        <a href="logout.php">
            <i class="fas fa-sign-out-alt"></i>
            <span>Salir</span>
        </a>
    </div>
    <script>
        document.querySelectorAll('.exportBtn').forEach(btn => {
            btn.addEventListener('click', function () {
                const rutinaId = this.getAttribute('data-id');
                exportToPDF(rutinaId);
            });
        });

        // Función para exportar a PDF
        function exportToPDF(rutinaId) {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            // Título del PDF
            doc.setFontSize(18);
            doc.text('Resumen de Rutina', 14, 20);

            // Obtener la información de la rutina
            const card = Array.from(document.querySelectorAll('.card')).find(c => {
                return c.querySelector('.card-title').innerText.includes(`ID Rutina: ${rutinaId}`);
            });

            if (card) {
                const ejercicios = card.querySelectorAll('table tbody tr');
                const rows = [];

                // Recopilar datos de la tabla de ejercicios
                ejercicios.forEach((ejercicio, index) => {
                    const cols = ejercicio.getElementsByTagName('td');
                    const rowData = [index + 1]; // Agregar número de fila
                    for (let j = 0; j < cols.length; j++) {
                        rowData.push(cols[j].innerText);
                    }
                    rows.push(rowData);
                });

                // Encabezados de la tabla
                const headers = [["#", "Grupo Muscular", "Ejercicio", "Repeticiones", "Series"]];

                // Añadir encabezados y filas al PDF
                doc.autoTable({
                    head: headers,
                    body: rows,
                    startY: 30, // Comenzar la tabla debajo del título
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