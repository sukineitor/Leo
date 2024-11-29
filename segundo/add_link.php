<?php
session_start();
include 'db_connect.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['id'])) {
        echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
        exit;
    }

    $title = $_POST['title'];
    $url = $_POST['url'];
    $description = $_POST['description'];
    $user_id = $_SESSION['id'];

    if (empty($title) || empty($url) || empty($description)) {
        echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
        exit;
    }

    $link_image = null;
    if (isset($_FILES['link_image']) && $_FILES['link_image']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["link_image"]["name"]);
        if (move_uploaded_file($_FILES["link_image"]["tmp_name"], $target_file)) {
            $link_image = $target_file;
        }
    }

    // Calcula la fecha de expiración (por defecto, 4 días después de la creación)
    $expires_at = date('Y-m-d H:i:s', strtotime('+2 days'));

    $sql = "INSERT INTO links (user_id, url, title, description, image, expires_at) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssss", $user_id, $url, $title, $description, $link_image, $expires_at);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al agregar el enlace']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
?>
