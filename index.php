<?php
session_start();
include 'segundo/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Receive form data
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Search for the user in the database
    $sql = "SELECT id, username, password FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username); // Using "s" because username is a string
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the user exists
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        // Check if the password is correct
        if (password_verify($password, $row['password'])) {
            // Start session and redirect user
            $_SESSION['id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            header("Location: segundo/index.php"); // Redirect to the home page
            exit();
        } else {
            // If the password is incorrect
            $error = "Incorrect username or password.";
        }
    } else {
        // If the user does not exist
        $error = "Incorrect username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login in Nardelink</title>
    <link rel="stylesheet" href="style.css">
    
    <link rel="shortcut icon" href="segundo/img/1.png" type="image/png">
</head>
<body>
    <div class="container">
        <h2>Nardelink</h2>

        <!-- Display error message if exists -->
        <?php if(isset($error)) { echo "<p class='error'>$error</p>"; } ?>

        <!-- Login form -->
        <form method="post" action="">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="submit" value="Login">
        </form>

        <!-- Link to the registration page -->
        <p>Don't have an account? <a href="registro.php">Register here</a></p>
    </div>
</body>
</html>
