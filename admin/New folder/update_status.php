<?php
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $status = $_POST['status'] ?? null;

    if ($id && $status) {
        $stmt = $conn->prepare("UPDATE admissions SET status = :status WHERE id = :id");
        $stmt->execute([':status' => $status, ':id' => $id]);
        echo "Updated";
    } else {
        echo "Invalid input.";
    }
}
