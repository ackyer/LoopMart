<?php
session_start();
$body_json =  file_get_contents('php://input');
$body = json_decode($body_json);
$username = $body->username;
$password = $body->password;
$conn = include ('inc/db_connection.php');
$sql = 'select * from users where username = "'.$username.'" and password = "'.md5($password).'"';
$result = $conn->query($sql);
if ($result->num_rows  == 1) {
    $_SESSION['username'] = $username;
    echo 'OK';
} else {
    echo 'Failed';
}
$conn->close();
