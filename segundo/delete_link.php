<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['id'];
$link_id = $_GET['id'];

// Start a transaction
$conn->begin_transaction();

try {
    // First, delete all comments associated with the link
    $delete_comments_sql = "DELETE FROM comments WHERE link_id = ?";
    $stmt = $conn->prepare($delete_comments_sql);
    $stmt->bind_param("i", $link_id);
    $stmt->execute();

    // Now, delete the link
    $delete_link_sql = "DELETE FROM links WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($delete_link_sql);
    $stmt->bind_param("ii", $link_id, $user_id);
    $stmt->execute();

    // If both operations are successful, commit the transaction
    $conn->commit();

    // Redirect back to the profile page
    header("Location: profile.php");
    exit();
} catch (Exception $e) {
    // If an error occurs, roll back the transaction
    $conn->rollback();
    
    // Log the error (you may want to use a more sophisticated logging system in production)
    error_log("Error deleting link: " . $e->getMessage());

    // Redirect to an error page or back to the profile page with an error message
    header("Location: profile.php?error=delete_failed");
    exit();
}
?>