<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "carlos27";
$dbname = "gym";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $usernameFilter = isset($_GET['username']) ? $_GET['username'] : '';
    $dateFilter = isset($_GET['date']) ? $_GET['date'] : '';
    $routineIdFilter = isset($_GET['routineId']) ? $_GET['routineId'] : '';

    // Preparar la consulta
    $sql = "SELECT rutinas.id, 
                   CONCAT(usuarios.nombres, ' ', usuarios.apellido_paterno, ' ', usuarios.apellido_materno) AS nombre_completo, 
                   rutinas.created_at 
            FROM rutinas 
            JOIN usuarios ON rutinas.user_id = usuarios.id 
            WHERE 1=1";

    // Agregar filtros si existen
    if ($usernameFilter !== '') {
        $sql .= " AND usuarios.username LIKE :username";
    }
    if ($dateFilter !== '') {
        $sql .= " AND DATE(rutinas.created_at) = :date";
    }
    if ($routineIdFilter !== '') {
        $sql .= " AND rutinas.id = :routineId";
    }

    $stmt = $conn->prepare($sql);

    // Vincular parámetros
    if ($usernameFilter !== '') {
        $stmt->bindValue(':username', '%' . $usernameFilter . '%');
    }
    if ($dateFilter !== '') {
        $stmt->bindValue(':date', $dateFilter);
    }
    if ($routineIdFilter !== '') {
        $stmt->bindValue(':routineId', $routineIdFilter, PDO::PARAM_INT);
    }

    $stmt->execute();

    $rutinas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Agregar ejercicios a cada rutina
    foreach ($rutinas as &$rutina) {
        $sqlEjercicios = "SELECT grupo_muscular, ejercicio, repeticiones, series 
                          FROM ejercicios 
                          WHERE rutina_id = :rutinaId";
        $stmtEjercicios = $conn->prepare($sqlEjercicios);
        $stmtEjercicios->bindValue(':rutinaId', $rutina['id'], PDO::PARAM_INT);
        $stmtEjercicios->execute();
        $rutina['ejercicios'] = $stmtEjercicios->fetchAll(PDO::FETCH_ASSOC);
    }

    echo json_encode(['success' => true, 'rutinas' => $rutinas]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error en la conexión: ' . $e->getMessage()]);
}
?>