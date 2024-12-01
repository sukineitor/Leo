<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['link_id'])) {
    $link_id = $_POST['link_id'];
    $user_id = $_SESSION['id'];

    // Verificar si el usuario ya le dio like al enlace
    $sql = "SELECT * FROM likes WHERE link_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $link_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        // El usuario no ha dado like, así que lo agregamos
        $sql = "INSERT INTO likes (link_id, user_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $link_id, $user_id);
        
        if ($stmt->execute()) {
            // Actualizar el contador de likes en la tabla de enlaces
            $sql = "UPDATE links SET likes = likes + 1 WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $link_id);
            $stmt->execute();
            
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al dar like']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Ya has dado like a este enlace']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Solicitud inválida']);
}