<?php
include('../inc/config.php');

try {
    $current_date = date('Y-m-d H:i:s');
    $user_id = 8; // Test with a known user
    $returns = 240; // Test with a known return amount
    $plan_name = "Starter Plan"; // Test plan name
    $invest_id = 1; // Test investment ID
    $current_balance = 1000; // Test balance

    // Update investment status
    $update_stmt = $conn->prepare("UPDATE investment SET status = 'completed' WHERE invest_id = ?");
    $update_stmt->execute([$invest_id]);

    // Credit returns to balance
    $new_balance = $current_balance + $returns;
    $insert_trans = $conn->prepare("INSERT INTO transaction (user_id, trans_date, type, amount, remark, balance) 
                                    VALUES (?, ?, '1', ?, ?, ?)");
    $insert_trans->execute([$user_id, $current_date, $returns, "Investment returns from $plan_name", $new_balance]);

    echo "Test completed successfully.";
} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage(), 3, "errors.log");
    echo "Error occurred. Check errors.log.";
}
?>
