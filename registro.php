<?php
session_start();
include 'segundo/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Receive form data
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate that the passwords match
    if ($password != $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Hash the password
        $password_hashed = password_hash($password, PASSWORD_DEFAULT);

        // Check if the username or email already exists
        $sql = "SELECT id FROM users WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Username or email is already registered.";
        } else {
            // Insert the new user into the database
            $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $username, $email, $password_hashed);
            if ($stmt->execute()) {
                // Redirect the user to the login page after successful registration
                $_SESSION['username'] = $username;
                header("Location: index.php");
                exit();
            } else {
                $error = "There was an error registering the user. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register in Nardelink</title>
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="segundo/img/1.png" type="image/png">
</head>
<body>
    <div class="container">
        <h2>Register</h2>

        <!-- Display error message if exists -->
        <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>

        <!-- Registration form -->
        <form method="post" action="">
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <input type="submit" value="Register">
        </form>

        <!-- Link to the login page -->
        <p>Already have an account? <a href="index.php">Log in here</a></p>
    </div>
</body>
</html>
