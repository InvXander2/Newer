<?php
// Use require_once instead of @include to ensure the file is included and errors are reported
$connFile = __DIR__ . '/../inc/conn.php';
if (!file_exists($connFile)) {
    die("Error: conn.php not found at $connFile");
}
require_once $connFile;

session_start();

// Debug: Confirm $pdo is defined
if (!isset($pdo) || !($pdo instanceof Database)) {
    die("Error: \$pdo is not defined in session.php");
}

if (isset($_SESSION['admin'])) {
    header('location: admin/home.php');
    exit;
}

if (isset($_SESSION['user'])) {
    $conn = $pdo->open();
    if ($conn === null) {
        die("Error: Database connection failed in session.php");
    }

    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $_SESSION['user']]);
        $user = $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Connection error in session.php: " . $e->getMessage());
        // Optionally set an error message or redirect
        $_SESSION['error'] = 'Database error occurred';
    }

    $pdo->close();
}
?>
