<?php
include 'includes/session.php';

if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $wallet = $_POST['wallet'];

    $conn = $pdo->open();

    // Check if payment method with the same name already exists
    $stmt = $conn->prepare("SELECT COUNT(*) AS numrows FROM payment_methods WHERE name = :name");
    $stmt->execute(['name' => $name]);
    $row = $stmt->fetch();

    if ($row['numrows'] > 0) {
        $_SESSION['error'] = 'Payment method already exists';
    } else {
        try {
            $stmt = $conn->prepare("INSERT INTO payment_methods (name, wallet_address) VALUES (:name, :wallet)");
            $stmt->execute(['name' => $name, 'wallet' => $wallet]);
            $_SESSION['success'] = 'Payment method added successfully';
        } catch (PDOException $e) {
            $_SESSION['error'] = $e->getMessage();
        }
    }

    $pdo->close();
} else {
    $_SESSION['error'] = 'Fill up the payment method form first';
}

header('location: payment_methods.php');
?>
