<?php
// inc/header.php
// Ensure $settings, $conn, and $id are available (loaded via config.php or session.php)
if (!isset($settings)) {
    include('../inc/config.php');
}
if (!isset($id)) {
    $id = $_SESSION['user'] ?? 0; // Fallback to 0 if user not logged in
}

// Use PDO ($conn) for consistency
try {
    $stmt0 = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt0->execute([$id]);
    $row0 = $stmt0->fetch(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare("SELECT *, COUNT(*) AS numrows FROM direct_message WHERE user_id = 0 OR (user_id = ? AND status = 0)");
    $stmt->execute([$id]);
    $drow = $stmt->fetch(PDO::FETCH_ASSOC);
    $no_of_msg = $drow['numrows'];

    $stmtQuery = $conn->prepare("SELECT * FROM direct_message WHERE (user_id = ? OR user_id = 0) AND status < 2 ORDER BY msg_id DESC LIMIT 4");
    $stmtQuery->execute([$id]);
    $dmrow = $stmtQuery->fetchAll(PDO::FETCH_OBJ);
} catch (PDOException $e) {
    error_log("Error in header.php queries: " . $e->getMessage(), 3, "../errors.log");
    $row0 = ['full_name' => 'Guest', 'photo' => 'profile.jpg'];
    $no_of_msg = 0;
    $dmrow = [];
}
?>

<div class="topbar">            
    <!-- Navbar -->
    <nav class="navbar-custom" style="display: flex; justify-content: space-between; align-items: center;">    
        <ul class="list-unstyled topbar-nav mb-0" style="display: flex; align-items: center;">                        
            <li>
                <button class="nav-link button-menu-mobile">
                    <i data-feather="menu" class="align-self-center topbar-icon"></i>
                </button>
            </li>
            <li class="topbar-logo" style="margin
