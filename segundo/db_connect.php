<?php
require_once 'config.php';

// Conexión a la base de datos MySQL usando MySQLi
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Verificar la conexión
if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);  // Registra el error
    die("Error connecting to the database. Please try again later.");
}
?>
