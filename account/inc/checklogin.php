<?php
function check_login() {
    if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
        $_SESSION['error'] = "Please log in to access the dashboard.";
        header('location: ../login.php');
        exit();
    }
}
?>
