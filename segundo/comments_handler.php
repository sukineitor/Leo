<?php
include 'db_connect.php';

// Función para obtener comentarios
function fetchComments($link_id, $conn, $limit = null) {
    $comments = [];
    $sql = "SELECT c.content, u.username FROM comments c JOIN users u ON c.user_id = u.id WHERE c.link_id = ? ORDER BY c.created_at DESC";
    if ($limit !== null) {
        $sql .= " LIMIT ?";
    }
    $stmt = $conn->prepare($sql);
    if ($limit !== null) {
        $stmt->bind_param("ii", $link_id, $limit);
    } else {
        $stmt->bind_param("i", $link_id);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $comments[] = $row;
    }

    return $comments;
}

// Manejar la inserción de nuevos comentarios
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['comment'])) {
    session_start();
    if (!isset($_SESSION['id'])) {
        die(json_encode(['success' => false, 'message' => "Usuario no autenticado."]));
    }

    $link_id = $_POST['link_id'];
    $user_id = $_SESSION['id'];
    $content = trim($_POST['comment_content']);

    if (empty($content)) {
        die(json_encode(['success' => false, 'message' => "El comentario no puede estar vacío."]));
    }

    $sql = "INSERT INTO comments (link_id, user_id, content) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $link_id, $user_id, $content);

    if ($stmt->execute()) {
        $comments = fetchComments($link_id, $conn, 3);
        echo json_encode(['success' => true, 'comments' => $comments]);
    } else {
        echo json_encode(['success' => false, 'message' => "Error al guardar el comentario."]);
    }
    exit();
}

// Manejar la obtención de comentarios
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['fetch_comments'])) {
    $link_id = $_GET['link_id'];
    $all = isset($_GET['all']) && $_GET['all'] === 'true';
    
    if ($all) {
        $comments = fetchComments($link_id, $conn);
    } else {
        $comments = fetchComments($link_id, $conn, 3);
    }
    
    $total_comments = count(fetchComments($link_id, $conn));
    
    echo json_encode([
        'comments' => $comments,
        'total_comments' => $total_comments,
        'has_more' => $total_comments > 3 && !$all
    ]);
    exit();
}

// Manejar el conteo de comentarios
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['count_comments'])) {
    $link_id = $_GET['link_id'];
    $sql = "SELECT COUNT(*) as count FROM comments WHERE link_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $link_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_assoc()['count'];
    echo json_encode(['count' => $count]);
    exit();
}
?>