<?php
// Conexión a la base de datos
include 'db_connect.php';

try {
    // Consulta para eliminar enlaces con más de 2 días de antigüedad
    $sql = "DELETE FROM links WHERE created_at < NOW() - INTERVAL 2 DAY";

    // Ejecutar la consulta
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    // Mensaje de confirmación
    echo "Se han eliminado los enlaces con más de 2 días de antigüedad.";
} catch (PDOException $e) {
    // Mostrar error en caso de fallo
    echo "Error: " . $e->getMessage();
}

$conn = null; // Cerrar la conexión
?>
