<?php
include 'includes/session.php';
include 'includes/slugify.php';

if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];

    // Debugging: Log input values
    error_log("investments_edit.php: id=$id, status=$status, time=" . date('Y-m-d H:i:s'), 3, 'debug.log');

    $conn = $pdo->open();

    try {
        // Fetch investment details
        $stmt = $conn->prepare("SELECT i.*, ip.name AS plan_name, u.id AS user_id 
                                FROM investment i 
                                LEFT JOIN investment_plans ip ON ip.id = i.invest_plan_id 
                                LEFT JOIN users u ON u.id = i.user_id 
                                WHERE i.invest_id = :id");
        $stmt->execute(['id' => $id]);
        $investment = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$investment) {
            throw new Exception('Investment not found.');
        }

        $user_id = $investment['user_id'];
        $capital = $investment['capital'];
        $returns = $investment['returns'];
        $plan_name = $investment['plan_name'] ?? 'Investment Plan';
        $act_time = date('Y-m-d h:i A');

        // Debugging: Log investment details
        error_log("investments_edit.php: user_id=$user_id, capital=$capital, returns=$returns, plan_name=$plan_name", 3, 'debug.log');

        // Begin transaction
        $conn->beginTransaction();

        // Update investment status
        $stmt = $conn->prepare("UPDATE investment SET status = :status WHERE invest_id = :id");
        $stmt->execute(['status' => $status, 'id' => $id]);

        // Only process balance, remark, and activity for 'completed' or 'cancelled'
        if ($status === 'completed' || $status === 'cancelled') {
            // Get user's current balance from the latest transaction
            $stmt = $conn->prepare("SELECT balance FROM transaction WHERE user_id = :user_id ORDER BY trans_id DESC LIMIT 1");
            $stmt->execute(['user_id' => $user_id]);
            $current_balance = $stmt->fetchColumn() ?: 0;

            // Debugging: Log current balance
            error_log("investments_edit.php: current_balance=$current_balance", 3, 'debug.log');

            if ($status === 'completed') {
                // Credit capital + returns to user's balance
                $amount = $returns;
                $new_balance = $current_balance + $amount;
                $message = "Your investment of $$capital for $plan_name has been completed, and $$amount has been credited to your account.";

                // Insert transaction with remark
                $stmt = $conn->prepare("INSERT INTO transaction (user_id, amount, type, balance, trans_date, remark) 
                                        VALUES (:user_id, :amount, :type, :balance, NOW(), :remark)");
                $stmt->execute([
                    'user_id' => $user_id,
                    'amount' => $amount,
                    'type' => 1, // Credit
                    'balance' => $new_balance,
                    'remark' => $message
                ]);

                // Log activity
                $stmt = $conn->prepare("INSERT INTO activity (user_id, message, category, date_sent) 
                                        VALUES (:user_id, :message, :category, :date_sent)");
                $stmt->execute([
                    'user_id' => $user_id,
                    'message' => $message,
                    'category' => 'Investment Completion',
                    'date_sent' => $act_time
                ]);
            } elseif ($status === 'cancelled') {
                // Credit only capital to user's balance
                $amount = $capital;
                $new_balance = $current_balance + $amount;
                $message = "Your investment of $$capital for $plan_name has been cancelled, and $$capital has been refunded to your account.";

                // Insert transaction with remark
                $stmt = $conn->prepare("INSERT INTO transaction (user_id, amount, type, balance, trans_date, remark) 
                                        VALUES (:user_id, :amount, :type, :balance, NOW(), :remark)");
                $stmt->execute([
                    'user_id' => $user_id,
                    'amount' => $amount,
                    'type' => 1, // Credit
                    'balance' => $new_balance,
                    'remark' => $message
                ]);

                // Log activity
                $stmt = $conn->prepare("INSERT INTO activity (user_id, message, category, date_sent) 
                                        VALUES (:user_id, :message, :category, :date_sent)");
                $stmt->execute([
                    'user_id' => $user_id,
                    'message' => $message,
                    'category' => 'Investment Cancellation',
                    'date_sent' => $act_time
                ]);
            }

            // Debugging: Log transaction and activity details
            error_log("investments_edit.php: status=$status, amount=$amount, new_balance=$new_balance, message=$message", 3, 'debug.log');
        }

        // Commit transaction
        $conn->commit();
        $_SESSION['success'] = 'Investment status updated successfully';
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollBack();
        $_SESSION['error'] = 'Error updating investment: ' . $e->getMessage();
        error_log("investments_edit.php: Error - " . $e->getMessage(), 3, 'debug.log');
    }

    $pdo->close();
} else {
    $_SESSION['error'] = 'Please select a status to update.';
}

header('location: investments.php');
?>
