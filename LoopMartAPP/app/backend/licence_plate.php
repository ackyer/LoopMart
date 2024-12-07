<?php

$body_json =  file_get_contents('php://input');
$body = json_decode($body_json);

$licence_plate = $body->licence_plate;
$conn = include ('inc/db_connection.php');
$sql = 'Select parking_key from parking where licence_plate = "'.$licence_plate.'" and payed=0;';
$result = $conn->query($sql);
if ($result->num_rows  == 1) {
    echo 'OK';
} else {
    echo 'Failed';
}
$conn->close();