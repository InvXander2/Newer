<?php
session_start();

// Initialize PDO connection
try {
    $pdo = new PDO("mysql:host=localhost;dbname=nexuvmvy_nexusinsights", "nexuvmvy_nexusinsights", "Xander24427279");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $_SESSION['error'] = 'Connection failed: ' . $e->getMessage();
    header('location: ../login.php');
    exit();
}

if (isset($_SESSION['admin'])) {
    header('location: admin/home.php');
    exit();
}

if (isset($_SESSION['user'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $_SESSION['user']]);
        $user = $stmt->fetch();
    } catch (PDOException $e) {
        $_SESSION['error'] = 'There is some problem in connection: ' . $e->getMessage();
        header('location: ../login.php');
        exit();
    }
}
?>
