<?php
session_start();
$body_json =  file_get_contents('php://input');
$body = json_decode($body_json);
$email = $body->email;
$password = $body->password;
$name = $body->name;
$conn = include ('inc/db_connection.php');
if (!$conn) {
    // Responder con un JSON si falla la conexión
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}
//check username
$sql = 'select * from user where email = "'.$email.'"';
$result = $conn->query($sql);
if ($result->num_rows  >= 1) {
    //send error
    echo 'User exist in DB';
    exit;
}
//put user to DB
$sql = 'INSERT INTO user (name, email, password) VALUES ("'.$name.'", "'.$email.'", "'.md5($password).'")';
$result = $conn->query($sql);
$last_id = $conn->insert_id;
$_SESSION['name'] = $name;
$_SESSION['email'] = $email;
$_SESSION['user_id'] = $last_id;

echo 'OK';
$conn->close();

