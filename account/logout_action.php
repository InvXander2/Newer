<?php
// Include the database connection and session check
include('../inc/config.php');
include('inc/session.php');

// Get the user ID from the session
session_start();
if (isset($_SESSION['user'])) {
    $id = $_SESSION['user'];

    // Update user's last activity time
    $stmt = "UPDATE users SET date_view = NOW() WHERE id = ?";
    $query = $conne->prepare($stmt);
    $query->bind_param("i", $id);
    $query->execute();

    // Destroy session
    session_unset();
    session_destroy();
}

// Redirect to login page
header('Location: ../login.php');
exit;
?>
