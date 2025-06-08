<?php
include 'includes/session.php';
include '../account/connect.php'; // MySQLi connection

if (isset($_POST['delete'])) {
    $id = $_POST['id'];

    try {
        $stmt = $conne->prepare("DELETE FROM visitor_logs WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $_SESSION['success'] = 'Visitor log deleted successfully';
        } else {
            $_SESSION['error'] = 'No visitor log found with the specified ID';
        }
        $stmt->close();
    } catch (Exception $e) {
        $_SESSION['error'] = 'Error deleting log: ' . $e->getMessage();
    }

    $conne->close();
} else {
    $_SESSION['error'] = 'Select visitor log to delete first';
}

header('location: visitor_logs.php');
?>
