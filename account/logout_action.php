<?php
// Includes
include('../inc/config.php');   // defines $conne
include('inc/session.php');     // already starts session and sets $_SESSION['user']

// Use the session user ID
$id = $_SESSION['user'] ?? null;

if ($id) {
    // Update last active time
    $stmt = $conne->prepare("UPDATE users SET date_view = NOW() WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

// Destroy session
session_unset();
session_destroy();

// Redirect to login
header('Location: ../login.php');
exit;
?>
