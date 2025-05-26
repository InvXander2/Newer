<?php
require_once('../inc/conn.php'); // This defines $pdo as a Database instance
include('inc/session.php'); // Starts session and sets $_SESSION['user']

$conn = $pdo->open(); // Get PDO connection

$id = $_SESSION['user'] ?? null;

if ($id) {
    try {
        $stmt = $conn->prepare("UPDATE users SET date_view = NOW() WHERE id = :id");
        $stmt->execute(['id' => $id]);
    } catch (PDOException $e) {
        error_log("Logout update error: " . $e->getMessage(), 3, __DIR__ . "/error_log.txt");
    }
}

$pdo->close(); // Close the connection
session_unset();
session_destroy();
header('Location: ../login.php');
exit;
?>
