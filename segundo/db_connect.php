<?php
require_once 'config.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);  // Esto te ayuda a ver el error en el log de errores.
    die("Error connecting to the database. Please try again later.");
}
?>
