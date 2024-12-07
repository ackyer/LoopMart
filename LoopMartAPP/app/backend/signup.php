<?php
session_start();
$body_json =  file_get_contents('php://input');
$body = json_decode($body_json);
$username = $body->username;
$password = $body->password;
$nickname = $body->nickname;
$conn = include ('inc/db_connection.php');
//check username
$sql = 'select * from users where username = "'.$username.'"';
$result = $conn->query($sql);
if ($result->num_rows  >= 1) {
    //send error
    echo 'User exist in DB';
    exit;
}
//put user to DB
$sql = 'insert into users values (null,"'.$username.'","'.md5($password).'", "'.$nickname.'")';
$result = $conn->query($sql);
$_SESSION['username'] = $username;
echo 'OK';
$conn->close();

