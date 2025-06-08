<?php
// session.php

// Include database connection
include_once 'conn.php';

// Start session
session_start();

// Initialize database connection
$pdo = new Database();

// Visitor tracking logic (logs every page visit)
try {
    // Get visitor data
    $page_name = basename($_SERVER['PHP_SELF']); // Current page name (e.g., index.php)
    $visit_time = date('Y-m-d H:i:s');
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown IP';

    // Get approximate location using ip-api.com
    $context = stream_context_create(['http' => ['timeout' => 5]]);
    $geolocation = @json_decode(file_get_contents("http://ip-api.com/json/{$ip_address}", false, $context));
    $location = $geolocation ? "{$geolocation->city}, {$geolocation->region}, {$geolocation->country}" : 'Location not available';

    // Get user ID if logged in
    $user_id = isset($_SESSION['user']) ? $_SESSION['user'] : null;

    // Open database connection
    $conn = $pdo->open();

    // Prepare and execute INSERT query
    $stmt = $conn->prepare("INSERT INTO visitor_logs (page_name, visit_time, location, ip_address, user_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$page_name, $visit_time, $location, $ip_address, $user_id]);

    // Close connection
    $pdo->close();

    // Optional: Log success (remove in production if not needed)
    error_log("Visitor data logged successfully for page: $page_name", 3, "visitor.log");
} catch (PDOException $e) {
    error_log("Error logging visitor: " . $e->getMessage(), 3, "errors.log");
    // Continue execution even if logging fails
}

// Session management logic
if (isset($_SESSION['admin'])) {
    header('location: admin/home.php');
    exit();
}

if (isset($_SESSION['user'])) {
    try {
        $conn = $pdo->open();

        $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $_SESSION['user']]);
        $user = $stmt->fetch();

        $pdo->close();
    } catch (PDOException $e) {
        error_log("Connection error: " . $e->getMessage(), 3, "errors.log");
        // Optionally, handle the error (e.g., unset session or redirect)
    }
}
?>
