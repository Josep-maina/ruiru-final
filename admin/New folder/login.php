<?php
// Start the session
session_start();

// Include the database connection
require_once '../db_connect.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the input values
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);

    // SQL query to fetch the admin username and password
    $query = "SELECT * FROM admins WHERE username = :username";
    $stmt = $conn->prepare($query);
    $stmt->execute([':username' => $username]);
    
    // Fetch the result
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Check if the username exists and password matches
    if ($admin && password_verify($password, $admin['password'])) {
        // Start the session and set the admin login status
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;

        // Redirect to the admin dashboard
        header('Location: admin.php');
        exit;
    } else {
        $error_message = "Invalid Username or Password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="login-container">
    <h2>Admin Login</h2>

    <!-- Display error message if credentials are wrong -->
    <?php if (isset($error_message)): ?>
        <div class="error"><?= $error_message; ?></div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <div class="input-group">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required>
        </div>
        
        <div class="input-group">
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>
        </div>
        
        <button type="submit" class="login-btn">Login</button>
    </form>
</div>

</body>
</html>
