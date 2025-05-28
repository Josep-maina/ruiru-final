<?php
// Start the session
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Redirect to login page if not logged in
    header('Location: login.php');
    exit;
}

// Include the database connection
require_once '../db_connect.php';

// Fetch admission records from the database
$query = "SELECT * FROM admissions";
$stmt = $conn->query($query);
$admissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - View Applications</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- Admin Navbar -->
<div class="navbar">
    <a href="admin.php">Dashboard</a>
    <a href="logout.php">Logout</a>
</div>

<!-- Admin Dashboard Content -->
<h2>Applications</h2>

<!-- Search Form -->
<form method="GET" action="admin.php">
    <input type="text" name="search" placeholder="Search by name or ID">
    <button type="submit">Search</button>
</form>

<!-- Admissions Table -->
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Course</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($admissions as $admission): ?>
        <tr>
            <td><?= $admission['id']; ?></td>
            <td><?= $admission['full_name']; ?></td>
            <td><?= $admission['email']; ?></td>
            <td><?= $admission['phone_number']; ?></td>
            <td><?= $admission['course_interest']; ?></td>
            <td>
                <form method="POST" action="update_status.php">
                    <select name="status">
                        <option value="Not Attended" <?= $admission['status'] === 'Not Attended' ? 'selected' : ''; ?>>Not Attended</option>
                        <option value="Attended" <?= $admission['status'] === 'Attended' ? 'selected' : ''; ?>>Attended</option>
                        <option value="On Process" <?= $admission['status'] === 'On Process' ? 'selected' : ''; ?>>On Process</option>
                    </select>
                    <input type="hidden" name="id" value="<?= $admission['id']; ?>">
                    <button type="submit">Update</button>
                </form>
            </td>
            <td>
                <a href="edit_application.php?id=<?= $admission['id']; ?>">Edit</a>
                <a href="delete_application.php?id=<?= $admission['id']; ?>">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
