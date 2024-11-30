<?php
require_once 'config.php';

try {
    // Usar PDO para conectar a PostgreSQL
    $dsn = "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME;
    $conn = new PDO($dsn, DB_USER, DB_PASS);
    
    // Configuración para reportar errores
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Prueba exitosa
    echo "Conexión exitosa a PostgreSQL"; // Mensaje de prueba
} catch (PDOException $e) {
    error_log("Connection failed: " . $e->getMessage()); // Registro de error
    die("Error en la conexión a la base de datos.");
}
?>
