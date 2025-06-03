<?php
    include_once '../inc/conn.php';
    $pdo = new Database();

    session_start();

    if (isset($_SESSION['admin'])) {
        header('location: admin/home.php');
        exit();
    }

    if (isset($_SESSION['user'])) {
        $conn = $pdo->open();

        try {
            $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
            $stmt->execute(['id' => $_SESSION['user']]);
            $user = $stmt->fetch();
        } catch (PDOException $e) {
            echo "There is some problem in connection: " . $e->getMessage();
        }

        $pdo->close();
    }
?>
