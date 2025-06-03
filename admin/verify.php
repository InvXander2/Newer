<?php
require_once '../inc/conn.php'; // Include the Database class
$pdo = new Database(); // Instantiate the Database class
$conn = $pdo->open(); // Open the database connection

// Example verification logic (modify based on your needs)
try {
    // Assuming verify.php checks something like a user login or token
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Example: Verify a user login
        $username = isset($_POST['username']) ? $_POST['username'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';

        if (!empty($username) && !empty($password)) {
            $stmt = $conn->prepare("SELECT * FROM users WHERE uname = :username");
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Successful verification
                session_start();
                $_SESSION['admin'] = $user['id'];
                header("Location: ../admin/dashboard.php");
                exit;
            } else {
                echo "Invalid username or password.";
            }
        } else {
            echo "Please provide username and password.";
        }
    }
} catch (PDOException $e) {
    error_log("Verification error: " . $e->getMessage() . "\n", 3, __DIR__ . "/error_log.txt");
    echo "An error occurred. Please try again.";
}

// Close the database connection
$pdo->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Verification</title>
    <link rel="stylesheet" href="../assets/css/vendor/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body>
    <div class="container">
        <h2>Admin Login</h2>
        <form method="POST" action="verify.php">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>
</body>
</html>
