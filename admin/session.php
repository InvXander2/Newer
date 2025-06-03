<?php
// Include the Database class
require_once '../inc/conn.php'; // Ensure this path is correct

// Start session
session_start();

// Instantiate the Database class
$pdo = new Database();

// Redirect admin users to admin dashboard
if (isset($_SESSION['admin'])) {
    header('location: admin/home.php');
    exit;
}

// If user is logged in, fetch their details
if (isset($_SESSION['user'])) {
    try {
        $conn = $pdo->open(); // Get PDO connection
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $_SESSION['user']]);
        $user = $stmt->fetch();
        $pdo->close(); // Optional, as PDO closes automatically at script end
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage() . "\n", 3, __DIR__ . "/error_log.txt");
        echo "There is some problem in connection: " . $e->getMessage();
    }
}
?>
