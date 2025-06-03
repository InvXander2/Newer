<?php
ob_start(); // Start output buffering to prevent header issues
include 'session.php';

// Initialize PDO connection
try {
    $pdo = new PDO("mysql:host=localhost;dbname=nexuvmvy_nexusinsights", "nexuvmvy_nexusinsights", "Xander24427279");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $_SESSION['error'] = 'Connection failed: ' . $e->getMessage();
    header('location: ../login.php');
    exit();
}

$conn = $pdo; // Use $pdo directly

if (isset($_POST['login'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    try {
        $stmt = $conn->prepare("SELECT *, COUNT(*) AS numrows FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();
        if ($row['numrows'] > 0) {
            if ($row['status']) {
                if (password_verify($password, $row['password'])) {
                    if ($row['type']) {
                        $_SESSION['admin'] = $row['id'];
                    } else {
                        $_SESSION['user'] = $row['id'];
                    }
                } else {
                    $_SESSION['error'] = 'Incorrect Password';
                }
            } else {
                $_SESSION['error'] = 'Account not activated.';
            }
        } else {
            $_SESSION['error'] = 'Email not found';
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = 'There is some problem in connection: ' . $e->getMessage();
    }
} else {
    $_SESSION['error'] = 'Input login credentials first';
}

$pdo = null; // Close connection (optional)
header('location: ../login.php');
exit();
?>
