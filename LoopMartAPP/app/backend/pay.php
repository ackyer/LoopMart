<?php
session_start();
$body_json =  file_get_contents('php://input');
$body = json_decode($body_json);
$parking_key = $body->parking_key;

$conn = include ('inc/db_connection.php');
$sql = 'update parking set payed =1 where parking_key = '.$parking_key;
$result = $conn->query($sql);
echo 'OK';
$conn->close();
