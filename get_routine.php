<?php
$host = 'localhost';
$dbname = 'gym';
$user = 'root';
$pass = 'carlos27';

try {
    // Conectar a la base de datos
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener la rutina por ID
    if (isset($_GET['rutinaId'])) {
        $rutinaId = $_GET['rutinaId'];

        // Consulta para obtener los datos de la rutina y sus ejercicios
        $stmt = $pdo->prepare("
            SELECT r.id AS rutina_id, r.user_id, r.created_at, 
                   e.grupo_muscular, e.ejercicio, e.repeticiones, e.series
            FROM rutinas r
            LEFT JOIN ejercicios e ON r.id = e.rutina_id
            WHERE r.id = :rutinaId
        ");
        $stmt->execute(['rutinaId' => $rutinaId]);

        $rutina = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($rutina) {
            // Obtener los detalles del usuario
            $userId = $rutina[0]['user_id'];
            $userStmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = :userId");
            $userStmt->execute(['userId' => $userId]);
            $user = $userStmt->fetch(PDO::FETCH_ASSOC);

            // Estructura final de respuesta
            $response = [
                'success' => true,
                'rutina' => [
                    'rutina_id' => $rutina[0]['rutina_id'],
                    'user' => $user,
                    'ejercicios' => $rutina
                ]
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Rutina no encontrada.'
            ];
        }
    } else {
        $response = [
            'success' => false,
            'message' => 'ID de rutina no proporcionado.'
        ];
    }

    echo json_encode($response);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error de conexión: ' . $e->getMessage()
    ]);
}
?>