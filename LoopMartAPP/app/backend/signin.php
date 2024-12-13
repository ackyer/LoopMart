<?php
session_start();
$body_json =  file_get_contents('php://input');
$body = json_decode($body_json);
$email = $body->email;
$password = $body->password;
$conn = include ('inc/db_connection.php');
if (!$conn) {
    // Responder con un JSON si falla la conexión
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}
$sql = 'select * from user where email = "'.$email.'" and password = "'.md5($password).'"';
$result = $conn->query($sql);
if ($result->num_rows  == 1) {
    $row = $result->fetch_row();
    $_SESSION['email'] = $email;
    $_SESSION['name'] = $row[1];
    $_SESSION['user_id'] = $row[0];
    echo 'OK';
} else {
    echo 'Failed';
}
$conn->close();
