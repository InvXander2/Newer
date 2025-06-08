<?php
// track_visitor.php

// Include database connection
// Assumes PDO connection in $pdo

// Check if already tracked in this session
if (!isset($_SESSION['tracked'])) {
    try {
        // Get visitor data
        $page_name = basename($_SERVER['PHP_SELF']); // Current page name (e.g., index.php)
        $visit_time = date('Y-m-d H:i:s');
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown IP';

        // Get approximate location using ip-api.com
        $geolocation = @json_decode(file_get_contents("http://ip-api.com/json/{$ip_address}"));
        $location = $geolocation ? "{$geolocation->city}, {$geolocation->region}, {$geolocation->country}" : 'Location not available';

        // Get user ID if logged in (adjust based on your session variable)
        $user_id = isset($_SESSION['user']) ? $_SESSION['user'] : null; // Assuming $_SESSION['user'] holds the user ID

        // Prepare and execute INSERT query
        $stmt = $pdo->prepare("INSERT INTO visitor_logs (page_name, visit_time, location, ip_address, user_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$page_name, $visit_time, $location, $ip_address, $user_id]);

        // Mark as tracked for this session
        $_SESSION['tracked'] = true;

        echo "Visitor data logged successfully.";
    } catch (PDOException $e) {
        error_log("Error logging visitor: " . $e->getMessage(), 3, "errors.log");
        echo "Failed to log visitor data.";
    }
}
?>
