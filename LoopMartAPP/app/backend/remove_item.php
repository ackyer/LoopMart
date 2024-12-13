<?php
session_start();

// Asegúrate de que no haya salida antes de este punto
header('Content-Type: application/json');

// Incluye la conexión a la base de datos
$conn = include('inc/db_connection.php');
if (!$conn) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

// Verifica si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Obtén los datos enviados desde el cliente
$data = json_decode(file_get_contents('php://input'), true);

// Verifica si se proporcionó item_id
if (!isset($data['item_id'])) {
    echo json_encode(['success' => false, 'error' => 'Item ID is required']);
    exit;
}

$item_id = $data['item_id'];

// Prepara la consulta SQL para eliminar el artículo
$sql = "DELETE FROM basket WHERE buyer_id = ? AND item_id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Failed to prepare statement']);
    exit;
}

$stmt->bind_param("ii", $user_id, $item_id);
$success = $stmt->execute();

// Comprueba si la consulta se ejecutó correctamente
if (!$success) {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
    exit;
}

// Devuelve una respuesta JSON al cliente
echo json_encode(['success' => true]);
?>
