<?php
$host = "localhost";
$db_username = "loopmartuser";
$db_password = "loopmartpassword";
$dbname = "loopmart";

// Create connection
$conn = new \mysqli($host, $db_username, $db_password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    // Registrar el error en lugar de usar `die()` para evitar salida HTML
    error_log("Database connection failed: " . $conn->connect_error);
    return false; // Retornar `false` en caso de fallo
}

return $conn;