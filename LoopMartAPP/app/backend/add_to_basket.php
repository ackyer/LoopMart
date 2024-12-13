<?php
session_start();
ini_set('display_errors', 0); // No mostrar errores al cliente
ini_set('log_errors', 1); // Registrar errores en un archivo de log
error_reporting(E_ALL);

header('Content-Type: application/json');

// Conexión a la base de datos
$conn = include('inc/db_connection.php');

// Verifica la conexión
if ($conn->connect_error) {
    error_log("Database connection error: " . $conn->connect_error);
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

// Decodificar los datos JSON enviados desde JavaScript
$data = json_decode(file_get_contents('php://input'), true);

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'not_logged_in']);
    exit;
}

// Validar el item_id enviado
if (isset($data['item_id'])) {
    $user_id = $_SESSION['user_id'];
    $item_id = intval($data['item_id']); // Asegura que item_id es un entero

    // Comprobar si el producto ya está en el carrito
    $query_check = "SELECT quantity FROM basket WHERE buyer_id = ? AND item_id = ?";
    $stmt_check = $conn->prepare($query_check);
    if (!$stmt_check) {
        error_log("Failed to prepare statement: " . $conn->error);
        echo json_encode(['success' => false, 'error' => 'Server error']);
        exit;
    }
    $stmt_check->bind_param('ii', $user_id, $item_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        // Si el producto ya está en el carrito, incrementar la cantidad
        $row = $result_check->fetch_assoc();
        $new_quantity = $row['quantity'] + 1;

        $query_update = "UPDATE basket SET quantity = ? WHERE buyer_id = ? AND item_id = ?";
        $stmt_update = $conn->prepare($query_update);
        if (!$stmt_update) {
            error_log("Failed to prepare update statement: " . $conn->error);
            echo json_encode(['success' => false, 'error' => 'Server error']);
            exit;
        }
        $stmt_update->bind_param('iii', $new_quantity, $user_id, $item_id);

        if ($stmt_update->execute()) {
            echo json_encode(['success' => true, 'message' => 'Quantity updated']);
        } else {
            error_log("Failed to execute update statement: " . $stmt_update->error);
            echo json_encode(['success' => false, 'error' => 'Failed to update quantity']);
        }
        $stmt_update->close();
    } else {
        // Si no está en el carrito, insertar un nuevo registro
        $query_insert = "INSERT INTO basket (buyer_id, item_id, quantity) VALUES (?, ?, 1)";
        $stmt_insert = $conn->prepare($query_insert);
        if (!$stmt_insert) {
            error_log("Failed to prepare insert statement: " . $conn->error);
            echo json_encode(['success' => false, 'error' => 'Server error']);
            exit;
        }
        $stmt_insert->bind_param('ii', $user_id, $item_id);

        if ($stmt_insert->execute()) {
            echo json_encode(['success' => true, 'message' => 'Item added to basket']);
        } else {
            error_log("Failed to execute insert statement: " . $stmt_insert->error);
            echo json_encode(['success' => false, 'error' => 'Failed to add item to basket']);
        }
        $stmt_insert->close();
    }

    $stmt_check->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid data']);
}

$conn->close();
?>
