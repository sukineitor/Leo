<?php
include 'db_connect.php';

$sql = "SELECT l.*, u.username, COUNT(c.id) as comment_count, l.likes FROM links l 
        JOIN users u ON l.user_id = u.id 
        LEFT JOIN comments c ON l.id = c.link_id 
        GROUP BY l.id 
        ORDER BY l.created_at DESC 
        LIMIT 10";
$result = $conn->query($sql);

$links = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $links[] = $row;
    }
}

echo json_encode($links); // Devolver los datos en formato JSON
?>
