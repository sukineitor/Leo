<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['id']) || !isset($_GET['link_id'])) {
    header('HTTP/1.1 403 Forbidden');
    exit('Access denied');
}

$link_id = $_GET['link_id'];

$sql = "SELECT c.*, u.username FROM comments c 
        JOIN users u ON c.user_id = u.id 
        WHERE c.link_id = ? 
        ORDER BY c.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $link_id);
$stmt->execute();
$result = $stmt->get_result();

$comments = array();
while ($row = $result->fetch_assoc()) {
    $comments[] = array(
        'username' => htmlspecialchars($row['username']),
        'content' => htmlspecialchars($row['content']),
        'created_at' => date('d M Y H:i', strtotime($row['created_at']))
    );
}

header('Content-Type: application/json');
echo json_encode($comments);

