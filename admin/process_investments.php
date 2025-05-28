<?php
include('../inc/config.php'); // Ensure this includes your database connection ($conn)

try {
    $current_date = date('Y-m-d H:i:s');

    // Find investments that are due
    $stmt = $conn->prepare("SELECT i.*, ip.name 
                            FROM investment i 
                            JOIN investment_plans ip ON i.invest_plan_id = ip.id 
                            WHERE i.status = 'in progress' AND i.end_date <= ?");
    $stmt->execute([$current_date]);

    foreach ($stmt as $investment) {
        $user_id = $investment['user_id'];
        $returns = $investment['returns'];
        $plan_name = $investment['name'];
        $invest_id = $investment['invest_id'];

        // Update investment status to completed
        $update_stmt = $conn->prepare("UPDATE investment SET status = 'completed' WHERE invest_id = ?");
        $update_stmt->execute([$invest_id]);

        // Get the user's latest balance
        $balance_stmt = $conn->prepare("SELECT balance FROM transaction WHERE user_id = ? ORDER BY trans_id DESC LIMIT 1");
        $balance_stmt->execute([$user_id]);
        $last_trans = $balance_stmt->fetch(PDO::FETCH_ASSOC);
        $current_balance = $last_trans ? $last_trans['balance'] : 0;

        // Credit returns to balance
        $new_balance = $current_balance + $returns;
        $insert_trans = $conn->prepare("INSERT INTO transaction (user_id, trans_date, type, amount, remark, balance) 
                                        VALUES (?, ?, '1', ?, ?, ?)");
        $insert_trans->execute([$user_id, $current_date, $returns, "
