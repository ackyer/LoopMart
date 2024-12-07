<?php

$body_json =  file_get_contents('php://input');
$body = json_decode($body_json);

$licence_plate = $body->licence_plate;
$conn = include ('inc/db_connection.php');

//calculate price
//1.get vehilce_in time
$sql = 'Select parking_key, vehicle_in  from parking where licence_plate = "'.$licence_plate.'" and payed=0;';
$result = $conn->query($sql);
if ($result->num_rows  == 1) {
    while($row = $result->fetch_assoc()) {
        $parking_key= $row['parking_key'];
        $vehicle_in = $row['vehicle_in'];
    }
} else {
    echo 'Failed';
}

//2.get current time from DB
$sql = 'select now() as \'current_time\' from dual';
$result = $conn->query($sql);

while($row = $result->fetch_assoc()) {
    $current_time = $row['current_time'];
}

//3. calculate duration
// $current_time; 2024-11-08 11:04:25
// $vehicle_in; 2024-11-08 09:50:07
$duration = strtotime($current_time) - strtotime($vehicle_in);

//4. calculate price
$price = ceil ($duration / 3600) * 1;

//5. update the DB
$sql = 'update parking set price='.$price .' , vehilce_out = now() where  parking_key ='.$parking_key;

$result = $conn->query($sql);

$response = [
    'success' => true,
    'price' =>$price,
    'vehicle_in' => $vehicle_in,
    'vehicle_out' => $current_time,
    'license_plate' => $licence_plate,
    'parking_key' => $parking_key
];
echo json_encode($response);

$conn->close();