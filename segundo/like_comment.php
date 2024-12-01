<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['id'])) {
        die(json_encode(['success' => false, 'message' => "Usuario no autenticado."]));
    }

    $user_id = $_SESSION['id'];

    // Procesar like
    if (isset($_POST['like'])) {
        $link_id = $_POST['link_id'];

        $sql = "SELECT * FROM likes WHERE link_id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $link_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            $sql = "INSERT INTO likes (link_id, user_id) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $link_id, $user_id);
            $stmt->execute();

            $sql = "UPDATE links SET likes = likes + 1 WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $link_id);
            $stmt->execute();

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => "Ya has dado like a este enlace."]);
        }
    }
}
?>