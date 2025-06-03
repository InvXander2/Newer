<?php
// Include database connection (remove @ to allow error reporting)
require_once '../inc/conn.php'; // Use require_once to ensure file is included and stop if it fails
session_start();

// Redirect admin users to admin dashboard
if (isset($_SESSION['admin'])) {
    header('location: admin/home.php');
    exit; // Ensure script stops after redirect
}

// If user is logged in, fetch their details
if (isset($_SESSION['user'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $_SESSION['user']]);
        $user = $stmt->fetch();
    } catch (PDOException $e) {
        echo "There is some problem in connection: " . $e->getMessage();
    }
}
// Note: No need for $pdo->close(), as PDO connections close automatically
?>
