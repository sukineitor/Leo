<?php
session_start();
include 'db_connect.php';
include 'comments_handler.php';

// Obtener el número de enlaces enviados hoy
$today = date('Y-m-d');
$user_id = $_SESSION['id'] ?? 0;
$sql = "SELECT COUNT(*) as link_count FROM links WHERE user_id = ? AND DATE(created_at) = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $user_id, $today);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$links_submitted_today = $row['link_count'];
$links_left = 3 - $links_submitted_today;

// Consulta para obtener todos los enlaces
$sql = "SELECT l.id, l.title, l.url, l.description, l.likes, l.image, u.username, u.profile_image, 
        (SELECT COUNT(*) FROM comments WHERE link_id = l.id) AS comment_count 
        FROM links l 
        JOIN users u ON l.user_id = u.id 
        WHERE l.expires_at > NOW() 
        ORDER BY l.created_at DESC";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nardelink</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="img/1.png" type="image/png">
</head>
<body>
    <div id="loading-screen">
        <div class="loader">
            <div class="circle"></div>
            <div class="circle"></div>
            <div class="circle"></div>
        </div>
        <h2 class="loading-text">Starting Nardelink...</h2>
    </div>

    <div class="container">
        <header>
            <h1>Nardelink</h1>
            <div class="neon-line"></div>
            <nav>
                <?php if (isset($_SESSION['id'])): ?>
                    <a href="profile.php" class="profile-link">
                        <?php
                        $user_id = $_SESSION['id'];
                        $sql = "SELECT profile_image FROM users WHERE id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $user_id);
                        $stmt->execute();
                        $result_user = $stmt->get_result();
                        $user = $result_user->fetch_assoc();
                        if ($user['profile_image']): ?>
                            <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Profile" class="profile-image-small">
                        <?php else: ?>
                            <span class="profile-icon">👤</span>
                        <?php endif; ?>
                        My Profile
                    </a>
                <?php else: ?>
                    <div class="nav-links">
                        <a href="../index.php">Login</a>
                        <a href="../registro.php">Sign Up</a>
                    </div>
                <?php endif; ?>
            </nav>
        </header>

        <?php if (isset($_SESSION['id'])): ?>
            <div class="user-info">
                <p>Links left today: <?php echo $links_left; ?></p>
                <button id="add-link-btn" class="add-link-btn" <?php echo $links_left <= 0 ? 'disabled' : ''; ?>>Add Link</button>
            </div>
        <?php endif; ?>

        <div class="content">
            <main class="main-content">
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<article class='link' data-id='" . $row['id'] . "'>";
                        echo "<div class='link-header'>";
                        echo "<h3><a href='" . htmlspecialchars($row['url']) . "' target='_blank'>" . htmlspecialchars($row['title']) . "</a></h3>";
                        echo "<div class='link-meta'>";
                        if ($row['profile_image']) {
                            echo "<img src='" . htmlspecialchars($row['profile_image']) . "' alt='Profile' class='profile-image-tiny'>";
                        }
                        echo "<span>Posted by: " . htmlspecialchars($row['username']) . "</span>";
                        echo "<span class='like-button' data-id='" . $row['id'] . "'>👍 <span class='like-count'>" . $row['likes'] . "</span></span>";
                        echo "<span>Comments: <span class='comment-count'>" . $row['comment_count'] . "</span></span>";
                        echo "</div>";
                        echo "</div>";
                        echo "<p>" . htmlspecialchars($row['description']) . "</p>";
                        if ($row['image']) {
                            echo "<img src='" . htmlspecialchars($row['image']) . "' alt='Link Image' class='link-image'>";
                        }

                        echo "<div class='comments-section'>";
                        echo "<h4>Comments</h4>";
                        echo "<div class='comments-list' id='comments-" . $row['id'] . "'></div>";
                        echo "<button class='load-more-comments' data-id='" . $row['id'] . "' style='display:none;'>See more comments</button>";
                        if (isset($_SESSION['id'])) {
                            echo "<form class='comment-form' data-id='" . $row['id'] . "'>
                                    <textarea name='comment_content' placeholder='Write your comment'></textarea>
                                    <button type='submit'>Comment</button>
                                  </form>";
                        }
                        echo "</div>";

                        echo "</article>";
                    }
                } else {
                    echo "<p>No links to show.</p>";
                }
                ?>
            </main>
            <aside class="sidebar">
                <h2>Recent Links</h2>
                <ul id="recent-links"></ul>
            </aside>
        </div>
    </div>

    <!-- Modal for adding a new link -->
    <div id="add-link-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Add New Link</h2>
            <form id="add-link-form" enctype="multipart/form-data">
                <input type="text" id="link-title" name="title" placeholder="Link Title" required>
                <input type="url" id="link-url" name="url" placeholder="Link URL" required>
                <textarea id="link-description" name="description" placeholder="Link Description" required></textarea>
                
                <button type="submit">Add Link</button>
            </form>
        </div>
    </div>

    <!-- Social Media -->
    <footer class="social-media">
        <h3>Follow me on: </h3>
        <div class="social-icons">
            <a href="https://www.instagram.com/leojhon.v" target="_blank" class="social-icon"><i class="fab fa-instagram"></i></a>
            <a href="https://youtube.com/@leojhon.v?si=j--83WeKAnMWHYm2" target="_blank" class="social-icon"><i class="fab fa-youtube"></i></a>
            <a href="https://www.tiktok.com/@leojhonv?_t=8rjSsxuEgMC&_r=1" target="_blank" class="social-icon"><i class="fab fa-tiktok"></i></a>
        </div>
    </footer>

    <script src="script.js"></script>
</body>
</html>

