<?php
include('../inc/config.php');
include 'includes/session.php';

if (isset($_POST['delete'])) {
    if (empty($_POST['id'])) {
        $_SESSION['error'] = 'No ID specified for deletion.';
        header('location: payment_methods.php');
        exit();
    }

    $id = $_POST['id'];

    try {
        $conn = $pdo->open();
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conn->prepare("DELETE FROM payment_mode WHERE id = :id");
        $stmt->execute(['id' => $id]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['success'] = 'Payment method deleted successfully.';
        } else {
            $_SESSION['error'] = 'Payment method not found or already deleted.';
        }

        $pdo->close();
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Database error: ' . $e->getMessage();
    }
} else {
    $_SESSION['error'] = 'Invalid request.';
}

header('location: payment_methods.php');
exit();
