<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['id'];
$link_id = $_GET['id'];

// Fetch link data
$sql = "SELECT * FROM links WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $link_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$link = $result->fetch_assoc();

if (!$link) {
    header("Location: profile.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_link'])) {
    $title = $_POST['title'];
    $url = $_POST['url'];
    $description = $_POST['description'];
    
    // Handle link image upload
    if (isset($_FILES['link_image']) && $_FILES['link_image']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["link_image"]["name"]);
        if (move_uploaded_file($_FILES["link_image"]["tmp_name"], $target_file)) {
            $link_image = $target_file;
        }
    }

    $sql = "UPDATE links SET title = ?, url = ?, description = ?" . (isset($link_image) ? ", image = ?" : "") . " WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    if (isset($link_image)) {
        $stmt->bind_param("ssssii", $title, $url, $description, $link_image, $link_id, $user_id);
    } else {
        $stmt->bind_param("sssii", $title, $url, $description, $link_id, $user_id);
    }
    $stmt->execute();

    header("Location: profile.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Link</title>
    <link rel="stylesheet" href="estilos.css">
    
    <link rel="shortcut icon" href="img/1.png" type="image/png">
</head>
<body>
    <div class="container">
        <header>
            <h1>Edit Link</h1>
            <div class="neon-line"></div>
        </header>
        <div class="content">
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($link['title']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="url">URL:</label>
                    <input type="url" id="url" name="url" value="<?php echo htmlspecialchars($link['url']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" required><?php echo htmlspecialchars($link['description']); ?></textarea>
                </div>
                
                <button type="submit" name="update_link">Update Link</button>
            </form>
        </div>
    </div>
</body>
</html>

