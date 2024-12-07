<?php
$host = "localhost";
$db_username = "parkoidusername";
$db_password = "parkoidpassword";
$dbname = "parkoid";

// Create connection
$conn = new \mysqli($host, $db_username, $db_password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

return $conn;