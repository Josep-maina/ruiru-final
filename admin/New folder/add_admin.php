<?php
// Include the database connection
require_once 'db_connect.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the input values
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);

    // Hash the password using password_hash()
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // SQL query to insert the new admin
    $query = "INSERT INTO admins (username, password) VALUES (:username, :password)";
    $stmt = $conn->prepare($query);

    // Execute the query
    try {
        $stmt->execute([
            ':username' => $username,
            ':password' => $hashed_password
        ]);
        echo "Admin added successfully!";
    } catch (PDOException $e) {
        echo "Error adding admin: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h2>Add New Admin</h2>

<form action="add_admin.php" method="POST">
    <div class="input-group">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required>
    </div>
    
    <div class="input-group">
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>
    </div>

    <button type="submit" class="submit-btn">Add Admin</button>
</form>

</body>
</html>
