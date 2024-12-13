<?php
session_start();
error_reporting(0); // Opcional: Para deshabilitar errores en la salida (¡solo en producción!).
ini_set('display_errors', 0);
header('Content-Type: application/json'); // Asegúrate de que el contenido siempre sea JSON


// Conexión a la base de datos
$conn = include('inc/db_connection.php');
if (!$conn) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'not_logged_in']);
    exit;
}

// Tu lógica para obtener los elementos del carrito
$query = "SELECT basket.item_id, item.item_name, item.price, basket.quantity
          FROM basket
          JOIN item ON basket.item_id = item.item_id
          WHERE basket.buyer_id = " . intval($_SESSION['user_id']);

$result = $conn->query($query);

if (!$result) {
    echo json_encode(['success' => false, 'error' => 'Query failed: ' . $conn->error]);
    exit;
}

$basket_items = [];
$total_price = 0;

while ($row = $result->fetch_assoc()) {
    $basket_items[] = [
        'item_id' => $row['item_id'],
        'item_name' => $row['item_name'],
        'price' => floatval($row['price']),
        'quantity' => intval($row['quantity'])
    ];
    $total_price += floatval($row['price']) * intval($row['quantity']);
}

echo json_encode([
    'success' => true,
    'basket_items' => $basket_items,
    'total_price' => $total_price
]);

$conn->close();
?>
