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

    $sql = "INSERT INTO links (user_id, url, title, description) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $user_id, $url, $title, $description);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al agregar el enlace']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}