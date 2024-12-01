<?php
require_once 'config.php';

// Establecer la conexión con PostgreSQL
$conn = pg_connect("host=" . DB_HOST . " dbname=" . DB_NAME . " user=" . DB_USER . " password=" . DB_PASS);

if (!$conn) {
    error_log("Connection failed: " . pg_last_error());  // Esto te ayudará a ver el error en el log de errores.
    die("Error connecting to the database. Please try again later.");
}
?>
