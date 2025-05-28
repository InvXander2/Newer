<?php
session_start(); // Start session

// Include database configuration
$conn_file = '../inc/conn.php';
if (!file_exists($conn_file)) {
    error_log("conn.php not found at $conn_file", 3, "errors.log");
    die("Error: conn.php not found.");
}
include_once($conn_file);

// Initialize Database class and get PDO connection
$pdo = new Database();
$conn = $pdo->open(); // Get PDO connection from Database class

// Ensure database connection is available
if (!isset($conn) || !($conn instanceof PDO)) {
    error_log("Database connection not established or invalid.", 3, "errors.log");
    die("Error: Database connection not established or invalid.");
}

// Ensure user is logged in and get user ID
if (!isset($_SESSION['user_id'])) {
    error_log("User not logged in.", 3, "errors.log");
    die("Error: User not logged in.");
}
$user_id = $_SESSION['user_id'];

try {
    // Get current timestamp
    $current_date = date('Y-m-d H:i:s');

    // Process due investments for the current user
    $stmt = $conn->prepare("SELECT i.*, ip.name 
                            FROM investment i 
                            JOIN investment_plans ip ON i.invest_plan_id = ip.id 
                            WHERE i.status = 'in progress' 
                            AND i.end_date <= ? 
                            AND i.user_id = ?");
    $stmt->execute(array($current_date, $user_id));

    foreach ($stmt as $investment) {
        $returns = $investment['returns'];
        $plan_name = $investment['name'];
        $invest_id = $investment['invest_id'];

        // Update investment status to completed
        $update_stmt = $conn->prepare("UPDATE investment SET status = 'completed' WHERE invest_id = ?");
        $update_stmt->execute(array($invest_id));

        // Get user's latest balance
        $balance_stmt = $conn->prepare("SELECT balance FROM transaction WHERE user_id = ? ORDER BY trans_id DESC LIMIT 1");
        $balance_stmt->execute(array($user_id));
        $last_trans = $balance_stmt->fetch(PDO::FETCH_ASSOC);
        $current_balance = $last_trans ? $last_trans['balance'] : 0;

        // Credit returns to balance
        $new_balance = $current_balance + $returns;
        $insert_trans = $conn->prepare("INSERT INTO transaction (user_id, trans_date, type, amount, remark, balance) 
                                        VALUES (?, ?, '1', ?, ?, ?)");
        $insert_trans->execute(array(
            $user_id,
            $current_date,
            $returns,
            "Investment returns from $plan_name",
            $new_balance
        ));

        // Log completion in activity table
        $insert_activity = $conn->prepare("INSERT INTO activity (user_id, message, category, date_sent) 
                                          VALUES (?, ?, ?, ?)");
        $insert_activity->execute(array(
            $user_id,
            "Investment completed: $plan_name",
            "Investment",
            $current_date
        ));
    }

    // Process cancelled investments (refund capital) for the current user
    $cancelled_stmt = $conn->prepare("SELECT * FROM investment 
                                      WHERE status = 'cancelled' 
                                      AND invest_id NOT IN (SELECT invest_id FROM transaction WHERE remark LIKE 'Refund%') 
                                      AND user_id = ?");
    $cancelled_stmt->execute(array($user_id));

    foreach ($cancelled_stmt as $investment) {
        $capital = $investment['capital'];
        $invest_id = $investment['invest_id'];

        // Get user's latest balance
        $balance_stmt = $conn->prepare("SELECT balance FROM transaction WHERE user_id = ? ORDER BY trans_id DESC LIMIT 1");
        $balance_stmt->execute(array($user_id));
        $last_trans = $balance_stmt->fetch(PDO::FETCH_ASSOC);
        $current_balance = $last_trans ? $last_trans['balance'] : 0;

        // Refund capital to balance
        $new_balance = $current_balance + $capital;
        $insert_trans = $conn->prepare("INSERT INTO transaction (user_id, trans_date, type, amount, remark, balance) 
                                        VALUES (?, ?, '1', ?, ?, ?)");
        $insert_trans->execute(array(
            $user_id,
            $current_date,
            $capital,
            "Refund for cancelled investment #$invest_id",
            $new_balance
        ));

        // Log refund in activity table
        $insert_activity = $conn->prepare("INSERT INTO activity (user_id, message, category, date_sent) 
                                          VALUES (?, ?, ?, ?)");
        $insert_activity->execute(array(
            $user_id,
            "Refunded capital for cancelled investment #$invest_id",
            "Investment",
            $current_date
        ));
    }

    // Close the database connection
    $pdo->close();

} catch (PDOException $e) {
    error_log("Error processing investments for user $user_id: " . $e->getMessage(), 3, "errors.log");
    die("Error processing investments: " . $e->getMessage());
}
?>
