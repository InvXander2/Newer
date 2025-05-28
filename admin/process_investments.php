<?php
// Include database configuration
include('../inc/config.php');

// Ensure database connection is available
if (!isset($conn)) {
    error_log("Database connection not established.", 3, "errors.log");
    die("Error: Database connection not established.");
}

try {
    // Get current timestamp
    $current_date = date('Y-m-d H:i:s');

    // Find investments that are due for completion
    $stmt = $conn->prepare("SELECT i.*, ip.name 
                            FROM investment i 
                            JOIN investment_plans ip ON i.invest_plan_id = ip.id 
                            WHERE i.status = 'in progress' AND i.end_date <= ?");
    $stmt->execute(array($current_date));

    // Process each due investment
    foreach ($stmt as $investment) {
        $user_id = $investment['user_id'];
        $returns = $investment['returns'];
        $plan_name = $investment['name'];
        $invest_id = $investment['invest_id'];

        // Update investment status to completed
        $update_stmt = $conn->prepare("UPDATE investment SET status = 'completed' WHERE invest_id = ?");
        $update_stmt->execute(array($invest_id));

        // Get the user's latest balance
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

    // Handle cancelled investments (refund capital)
    $cancelled_stmt = $conn->prepare("SELECT * FROM investment 
                                      WHERE status = 'cancelled' 
                                      AND invest_id NOT IN (SELECT invest_id FROM transaction WHERE remark LIKE 'Refund%')");
    $cancelled_stmt->execute();

    foreach ($cancelled_stmt as $investment) {
        $user_id = $investment['user_id'];
        $capital = $investment['capital'];
        $invest_id = $investment['invest_id'];

        // Get the user's latest balance
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

    // Output success message
    echo "Investment processing completed successfully.";
} catch (PDOException $e) {
    // Log error to file
    error_log("Error processing investments: " . $e->getMessage(), 3, "errors.log");
    echo "An error occurred. Check errors.log for details.";
}
?>
