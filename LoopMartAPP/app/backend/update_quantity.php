<?php
session_start();

// Habilitar la visualización de errores para depurar
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Conectar a la base de datos
$conn = include('inc/db_connection.php');

// Verifica la conexión
if (!$conn) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

header('Content-Type: application/json');

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'not_logged_in']);
    exit;
}

// Obtener datos enviados por el cliente
$data = json_decode(file_get_contents('php://input'), true);

// Verificar si los datos JSON fueron decodificados correctamente
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['error' => 'Invalid JSON data']);
    exit;
}

// Extraer el item_id y quantity del JSON
$item_id = $data['item_id'];
$quantity = $data['quantity'];

// Verificar que los datos sean válidos
if (!isset($item_id) || !isset($quantity) || !is_numeric($quantity) || $quantity < 1) {
    echo json_encode(['error' => 'Invalid item_id or quantity']);
    exit;
}

// Preparar la consulta SQL para actualizar la cantidad en el carrito
$sql = "UPDATE basket SET quantity = ? WHERE buyer_id = ? AND item_id = ?";
$stmt = $conn->prepare($sql);

// Verificar si la preparación de la consulta fue exitosa
if (!$stmt) {
    echo json_encode(['error' => 'Failed to prepare SQL statement']);
    exit;
}

// Asociar los parámetros a la consulta
$stmt->bind_param("iii", $quantity, $_SESSION['user_id'], $item_id);

// Ejecutar la consulta
$success = $stmt->execute();

// Verificar si la ejecución fue exitosa
if (!$success) {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
    exit;
}

// Devolver la respuesta en formato JSON
echo json_encode(['success' => true]);
?>
