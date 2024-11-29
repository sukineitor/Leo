<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['id'];

// Fetch user data
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Fetch user's links
$sql = "SELECT * FROM links WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$links = $stmt->get_result();

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        $sql = "UPDATE users SET username = ?, email = ?";
        $params = array($username, $email);
        $types = "ss";

        if (!empty($new_password)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $sql .= ", password = ?";
            $params[] = $hashed_password;
            $types .= "s";
        }

        $sql .= " WHERE id = ?";
        $params[] = $user_id;
        $types .= "i";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();

        header("Location: profile.php?success=1");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nardelink-User</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    
    <link rel="shortcut icon" href="img/1.png" type="image/png">
</head>
<body>
    <div class="container">
        <header>
            <h1>Nardelink</h1>
            <div class="neon-line"></div>
            <nav class="profile-nav">
                <a href="index.php" class="back-to-home">Home</a>
                <div class="user-info">
                    <a href="../logout.php" class="logout-button">Logout</a>
                </div>
            </nav>
        </header>

        <?php if (isset($_GET['error']) && $_GET['error'] == 'delete_failed'): ?>
            <div class="error-message">Error deleting the link. Please try again.</div>
        <?php endif; ?>

        <?php if (isset($_GET['success']) && $_GET['success'] == '1'): ?>
            <div class="success-message">Profile updated successfully.</div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <div class="profile-grid">
            <!-- Sidebar with user information -->
            <aside class="profile-sidebar">
                <div class="user-card">
                    <div class="profile-image-placeholder">
                        <span><?php echo strtoupper(substr($user['username'], 0, 1)); ?></span>
                    </div>
                    <h2><?php echo htmlspecialchars($user['username']); ?></h2>
                    <p class="user-email"><?php echo htmlspecialchars($user['email']); ?></p>
                    
                    <button class="edit-profile-btn" onclick="toggleEditForm()">Edit Profile</button>
                    
                    <!-- Edit form (initially hidden) -->
                    <form id="edit-profile-form" action="" method="POST" style="display: none;">
                        <div class="form-group">
                            <label for="username">Username:</label>
                            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="new_password">New Password:</label>
                            <input type="password" id="new_password" name="new_password">
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirm Password:</label>
                            <input type="password" id="confirm_password" name="confirm_password">
                        </div>
                        <button type="submit" name="update_profile" class="save-profile-btn">Save Changes</button>
                    </form>
                </div>
            </aside>

            <!-- Main content with links -->
            <main class="profile-content">
                <div class="content-header">
                    <h2>My Links</h2>
                    <button onclick="window.location.href='index.php#add-link-modal'" class="add-link-btn">+ New Link</button>
                </div>

                <div class="links-grid">
                    <?php while ($link = $links->fetch_assoc()): ?>
                        <article class="link-card">
                            <?php if ($link['image']): ?>
                                <div class="link-image-container">
                                    <img src="<?php echo htmlspecialchars($link['image']); ?>" alt="Link Image" class="link-image">
                                </div>
                            <?php endif; ?>
                            <div class="link-content">
                                <h3><a href="<?php echo htmlspecialchars($link['url']); ?>" target="_blank">
                                    <?php echo htmlspecialchars($link['title']); ?>
                                </a></h3>
                                <p class="link-description"><?php echo htmlspecialchars($link['description']); ?></p>
                                <div class="link-meta">
                                    <span class="link-date">
                                        <?php echo date('d M Y', strtotime($link['created_at'])); ?>
                                    </span>
                                    <div class="link-stats">
                                        <span>👍 <?php echo $link['likes']; ?></span>
                                        <span class="comment-icon" onclick="showComments(<?php echo $link['id']; ?>)">💬 
                                            <?php
                                            $sql = "SELECT COUNT(*) as count FROM comments WHERE link_id = ?";
                                            $stmt = $conn->prepare($sql);
                                            $stmt->bind_param("i", $link['id']);
                                            $stmt->execute();
                                            $comment_count = $stmt->get_result()->fetch_assoc()['count'];
                                            echo $comment_count;
                                            ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="link-actions">
                                    <a href="edit_link.php?id=<?php echo $link['id']; ?>" class="edit-link">Edit</a>
                                    <a href="delete_link.php?id=<?php echo $link['id']; ?>" 
                                       class="delete-link" 
                                       onclick="return confirm('Are you sure you want to delete this link?');">
                                        Delete
                                    </a>
                                </div>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal to show comments -->
    <center><div id="comments-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Comments</h2>
            <div id="comments-container"></div>
        </div>
    </div>
    </center>
    <script>
        function toggleEditForm() {
            const form = document.getElementById('edit-profile-form');
            const button = document.querySelector('.edit-profile-btn');
            if (form.style.display === 'none') {
                form.style.display = 'block';
                button.textContent = 'Cancel';
            } else {
                form.style.display = 'none';
                button.textContent = 'Edit Profile';
            }
        }

        function showComments(linkId) {
            const modal = document.getElementById('comments-modal');
            const commentsContainer = document.getElementById('comments-container');
            const closeBtn = document.getElementsByClassName('close')[0];

            // Fetch comments using AJAX
            fetch(`get_comments.php?link_id=${linkId}`)
                .then(response => response.json())
                .then(comments => {
                    commentsContainer.innerHTML = '';
                    if (comments.length > 0) {
                        comments.forEach(comment => {
                            commentsContainer.innerHTML += `
                                <div class="comment">
                                    <strong>${comment.username}</strong>
                                    <p>${comment.content}</p>
                                    <small>${comment.created_at}</small>
                                </div>
                            `;
                        });
                    } else {
                        commentsContainer.innerHTML = '<p>No comments for this link.</p>';
                    }
                    modal.style.display = 'block';
                })
                .catch(error => {
                    console.error('Error:', error);
                    commentsContainer.innerHTML = '<p>Error loading comments.</p>';
                    modal.style.display = 'block';
                });

            closeBtn.onclick = function() {
                modal.style.display = 'none';
            }

            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            }
        }
    </script>
</body>
</html>
