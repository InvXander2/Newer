<?php
include 'includes/session.php';
include('../inc/config.php');

if(isset($_POST['edit'])){
    $id = $_POST['id'];
    $name = trim($_POST['name']);
    $wallet = trim($_POST['wallet']);

    $conn = $pdo->open();

    try {
        $stmt = $conn->prepare("UPDATE payment_mode SET name=:name, wallet_address=:wallet WHERE mode_id=:id");
        $stmt->execute(['name' => $name, 'wallet' => $wallet, 'id' => $id]);
        $_SESSION['success'] = 'Payment method updated successfully';
    } catch (PDOException $e) {
        $_SESSION['error'] = $e->getMessage();
    }

    $pdo->close();
} else {
    $_SESSION['error'] = 'Fill up the edit form first';
}

header('location: payment_methods.php');
